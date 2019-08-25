<?php

namespace SUPT;

use Exception;
use PHPMailer;
use function apply_filters;

require_once __DIR__ . '/include/FormModel.php';
require_once __DIR__ . '/include/SubmissionModel.php';

/**
 * handle the form submission according to FormType fields
 */
class FormSubmission {

	const ACTION_BASE_NAME = 'supt-form';
	const NEXT_ACTION_ID_DEFAULT = - 1;

	const SUBMISSION_LIMIT_MINUTE_IP_FORM_AGENT = 2;
	const SUBMISSION_LIMIT_MINUTE_IP_FORM = 5;
	const SUBMISSION_LIMIT_HOUR_IP_FORM_AGENT = 10;
	const SUBMISSION_LIMIT_DAY_IP_FORM = 100;

	/**
	 * The form
	 *
	 * @var FormModel
	 */
	private $form = null;

	/**
	 * The id of the engagement funnel action
	 *
	 * @var int
	 */
	private $action_id = - 1;

	/**
	 * The id of the engagement funnel config
	 *
	 * @var int
	 */
	private $config_id = - 1;

	/**
	 * The id of the post meta id of the last submission
	 *
	 * @var int
	 */
	private $predecessor_id = - 1;

	/**
	 * The id of this submission (-1 before it is saved)
	 *
	 * @var int
	 */
	private $post_meta_id = - 1;

	/**
	 * The submitters IP address
	 *
	 * @var string
	 */
	private $ip;

	/**
	 * The submitters user agent information
	 *
	 * @var string
	 */
	private $user_agent;

	/**
	 * The forms nonce
	 *
	 * @var string
	 */
	private $nonce;

	/**
	 * The response status code
	 *
	 * @var int
	 */
	private $status;

	/**
	 * The form data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * The email found in the submitted data
	 *
	 * @var string
	 */
	private $email;

	/**
	 * The validation errors
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Cache for the form fields (as defined in the backend)
	 *
	 * @var FormField[]
	 */
	private $fields;

	function __construct() {
		$this->register_actions();
	}

	private function register_actions() {
		add_action( 'wp_ajax_supt_form_submit', array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_supt_form_submit', array( $this, 'handle_submit' ) );
		add_action( 'supt_form_save_to_crm', array( __CLASS__, 'save_to_crm' ) );
		add_action( 'supt_form_mail_send', array( __CLASS__, 'send_mails' ) );

		if ( ! WP_DEBUG && get_field( 'form_smtp_enabled', 'options' ) ) {
			add_action( 'phpmailer_init', array( $this, 'setup_SMTP' ) );
		}
	}

	/**
	 * Send out the pending mails
	 *
	 * Called by the WordPress cron job
	 */
	public static function send_mails() {
		require_once __DIR__ . '/include/Mailer.php';
		Mailer::send_mails();
	}

	/**
	 * Save the form submissions to the crm
	 *
	 * Called by the WordPress cron job
	 */
	public static function save_to_crm() {
		require_once __DIR__ . '/include/CrmSaver.php';
		CrmSaver::save_to_crm();
	}

	/**
	 * Process form submission
	 */
	public function handle_submit() {
		$this->add_submission_metadata();
		$this->abort_if_invalid_header();
		$this->abort_if_limit_exceeded();
		$this->add_data();
		$this->abort_if_invalid_data();
		$this->add_metadata();
		$this->save();
		$this->add_mails_to_sending_queue();
		$this->add_to_saving_queue_of_crm();
		spawn_cron();

		$this->status = 200;
		$this->send_response();
	}

	/**
	 * Set ip, user_agent, form_id. And if present: action_id, config_id, nonce
	 */
	private function add_submission_metadata() {
		$this->ip         = $this->get_user_ip();
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];

		try {
			$this->form = new FormModel( absint( $_POST['form_id'] ) );
		} catch ( Exception $e ) {
			$this->respond_with_error( 400, array( 'Submission not valid' ) );

			return;
		}

		if ( isset( $_POST['action_id'] ) ) {
			$this->action_id = absint( $_POST['action_id'] );
		}

		if ( isset( $_POST['config_id'] ) ) {
			$this->config_id = absint( $_POST['config_id'] );
		}

		if ( isset( $_POST['predecessor_id'] ) ) {
			$this->predecessor_id = intval( $_POST['predecessor_id'] );
		}

		// @todo: rethink the nonces if using caching
		if ( isset( $_POST['nonce'] ) ) {
			$this->nonce = $_POST['nonce'];
		}
	}

	/**
	 * Get visitor ip. If behind proxy, try to find real ip.
	 *
	 * @see https://stackoverflow.com/a/13646848
	 *
	 * @return string
	 */
	private function get_user_ip() {
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			if ( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) > 0 ) {
				$addr = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );

				return trim( $addr[0] );
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Send error message to client and die
	 *
	 * @param int $code
	 * @param string|array $messages
	 */
	private function respond_with_error( $code, $messages ) {
		$this->errors = array_merge( $this->errors, (array) $messages );
		$this->status = $code;
		$this->send_response();
	}

	/**
	 * Send response and die
	 */
	private function send_response() {
		status_header( $this->status );

		if ( $this->status === 200 ) {
			/**
			 * Filters the id of the next form. The default triggers the thank you page of the form.
			 *
			 * @param int id of the next form.
			 */
			$next_action_id = apply_filters( FormType::MODEL_NAME . '-next-form-id', self::NEXT_ACTION_ID_DEFAULT );

			$html = '';
			if ( self::NEXT_ACTION_ID_DEFAULT !== $next_action_id ) {
				$context['block']['configuration'] = $this->config_id;
				$context['block']['action']        = $next_action_id;

				$templates = [
					get_stylesheet_directory() . '/templates/engagement-funnel.twig',

					// fallback if child theme doesn't implement it
					get_template_directory() . '/templates/engagement-funnel.twig',
				];

				/** @noinspection PhpUndefinedClassInspection */
				$html = Timber::compile( $templates, $context );
			}

			wp_send_json_success( [
				'next_action_id' => $next_action_id,
				'html'           => $html,
				'redirect'       => $this->form->get_redirect_url(),
				'predecessor_id' => $this->post_meta_id,
			] );
		} else {
			wp_send_json_error( $this->errors );
		}

		wp_die();
	}

	/**
	 * Check if form is submitted using ajax, has a valid nonce and ip.
	 */
	private function abort_if_invalid_header() {
		$valid = true;

		// check nonce
		if ( ! wp_verify_nonce( $this->nonce, self::ACTION_BASE_NAME ) ) {
			$valid = false;
		}

		// only accept ajax submissions
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$valid = false;
		}

		// check for valid ip
		if ( ! filter_var( $this->ip, FILTER_VALIDATE_IP ) ) {
			$valid = false;
		}

		// exit here if any of the above checks failed
		if ( ! $valid ) {
			$this->respond_with_error( 400, array( 'Submission not valid' ) );

			return;
		}
	}

	/**
	 * Limit form submissions according to
	 * - SUBMISSION_LIMIT_MINUTE_IP_FORM_AGENT
	 * - SUBMISSION_LIMIT_MINUTE_IP_FORM
	 * - SUBMISSION_LIMIT_HOUR_IP_FORM_AGENT
	 * - SUBMISSION_LIMIT_DAY_IP_FORM
	 */
	private function abort_if_limit_exceeded() {
		// @todo: implement submission limiting
	}

	/**
	 * Populate data field with validated and sanitized form data.
	 */
	private function add_data() {
		$fields = $this->get_fields();

		foreach ( $fields as $key => $field ) {
			if ( $field->has_fixed_crm_value() ) {
				$this->data[ $key ] = $field->get_fixed_crm_value();
				continue;
			}

			// sanitize and validate checkboxes
			if ( $field->is_checkbox() ) {
				$this->data[ $key ] = array();

				foreach ( $field->get_choices() as $value_key => $value ) {
					$raw     = $this->get_field_data( $value_key, true );
					$checked = $field->sanitize( $raw );
					$valid   = $field->validate( $checked );

					if ( ! $valid ) {
						$this->errors[ $key ] = __( 'Invalid value.', THEME_DOMAIN );
					}

					if ( $checked ) {
						$this->data[ $key ][ $value_key ] = $value;
					}
				}

			} else {
				$raw = $this->get_field_data( $key );

				$sanitized = $field->sanitize( $raw );
				$valid     = $field->validate( $sanitized );

				if ( ! $valid ) {
					$this->errors[ $key ] = __( 'Invalid or missing value.', THEME_DOMAIN );
				}

				$this->data[ $key ] = $sanitized;
			}
		}
	}

	/**
	 * Return the form fields as defined in the backend, using cache
	 *
	 * @return FormField[]
	 */
	private function get_fields() {
		if ( ! $this->fields ) {
			$this->fields = $this->form->get_fields();
		}

		return $this->fields;
	}

	/**
	 * Get value from post variable or set error
	 *
	 * @param $key
	 * @param $allow_empty
	 *
	 * @return string|null
	 */
	private function get_field_data( $key, $allow_empty = false ) {
		// if field was not transmitted
		if ( ! array_key_exists( $key, $_POST ) ) {

			if ( $allow_empty ) {
				return '';
			}

			$this->errors[ $key ] = __( 'Missing data.', THEME_DOMAIN );

			return null;
		}

		// get data
		return trim( $_POST[ $key ] );
	}

	/**
	 * Stop execution with a 400 if the data threw a validation error
	 */
	private function abort_if_invalid_data() {
		if ( ! empty( $this->errors ) ) {
			$this->respond_with_error( 400, $this->errors );

			return;
		}
	}

	/**
	 * Add some meta data to the form data
	 */
	private function add_metadata() {
		$meta['_meta_'] = array(
			'form_id'        => $this->form->get_id(),
			'action_id'      => $this->action_id,
			'config_id'      => $this->config_id,
			'timestamp'      => date( 'Y-m-d H:i:s' ),
			'email'          => $this->get_email_address(),
			'predecessor_id' => $this->predecessor_id,
			'descendant_id'  => - 1,
		);

		// make sure to add the meta data before the actual data
		// so we have it first in the submission table
		$this->data = $meta + $this->data;
	}

	/**
	 * Find email by looking for the crm email field. If none found,
	 * return the value of the first non empty field with type email.
	 *
	 * @return string|false email or false if no email was found
	 */
	private function get_email_address() {
		if ( null !== $this->email ) {
			return $this->email;
		}

		foreach ( $this->get_fields() as $key => $field ) {
			if ( $field->is_crm_email() ) {

				$email = $this->data[ $key ];
				if ( ! empty( $email && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) ) {
					return $email;
				}
			}
		}

		foreach ( $this->get_fields() as $key => $field ) {
			if ( $field->is_email() ) {
				$email = $this->data[ $key ];
				if ( ! empty( $email ) ) {
					return $email; // already validated
				}
			}
		}

		return false;
	}

	/**
	 * Save form data locally and, if crm data is set, to crm
	 */
	private function save() {
		/**
		 * Filters the submitted data, before it's persisted locally.
		 *
		 * @param array $this ->data
		 */
		$data = (array) apply_filters( FormType::MODEL_NAME . '-before-local-save', $this->data );

		// save locally
		try {
			$submission = new SubmissionModel( null, $data );
			$submission->save();
			$this->post_meta_id = $submission->meta_get_id();
		} catch ( Exception $e ) {
			$this->report_error( 'save form data locally', $data, $e );
			$this->respond_with_error( 500, array( 'Internal server error.' ) );

			return;
		}

		$this->update_predecessor( $this->post_meta_id );

		if ( $this->post_meta_id ) {
			/**
			 * Fires after the data is persisted.
			 *
			 * @param array $data with
			 *  'form_data'      => array with the validated and sanitized form data
			 *  'action_id'      => int the engagement funnel action id
			 *  'config_id'      => int the engagement funnel config id
			 *  'post_meta_id'   => int the id of the post meta table where the form data is stored
			 *  'predecessor_id' => int the id of the predecessor submission
			 */
			do_action( FormType::MODEL_NAME . '-after-save',
				array(
					'form_data'      => $data,
					'action_id'      => $this->action_id,
					'config_id'      => $this->config_id,
					'post_meta_id'   => $this->post_meta_id,
					'predecessor_id' => $this->predecessor_id,
				)
			);
		}
	}

	/**
	 * Notify the site admin about the error
	 *
	 * @param string $action
	 * @param mixed $data
	 * @param Exception $exception
	 */
	private function report_error( $action, $data, $exception ) {
		$form = $this->form->get_title();

		Util::report_form_error( $action, $data, $exception, $form );
	}

	/**
	 * Add the id of this submission to the previous submission
	 *
	 * @param int $submission_id the meta_id of the current submission
	 */
	private function update_predecessor( $submission_id ) {
		if ( $this->predecessor_id >= 0 ) {
			try {
				$predecessor = new SubmissionModel( $this->predecessor_id );
				$predecessor->meta_set_descendant( $submission_id );
				$predecessor->save();
			} catch ( Exception $e ) {
				$this->report_error( 'update predecessor', $this->data, $e );
			}
		}
	}

	/**
	 * Add the notification and confirmation mails to the sending queue, processed by a cron job
	 */
	private function add_mails_to_sending_queue() {
		if ( ! $this->form->has_confirmation() && ! $this->form->has_notification() ) {
			// nothing to send
			return;
		}

		require_once __DIR__ . '/include/Mailer.php';

		try {
			$mailer = new Mailer( $this->post_meta_id );
			$mailer->queue_mails();
		} catch ( Exception $e ) {
			Util::report_form_error( 'add mails to sending queue', $this->data, $e, $this->form->get_title() );
		}
	}

	/**
	 * Add the data to save to the saving queue, processed by a cron job
	 */
	private function add_to_saving_queue_of_crm() {
		require_once __DIR__ . '/include/CrmDao.php';

		// bail early, if the crm api isn't configured
		if ( ! CrmDao::has_api_url() ) {
			return;
		}

		require_once __DIR__ . '/include/CrmSaver.php';

		try {
			$saver = new CrmSaver( $this->post_meta_id );
			$saver->queue();
		} catch ( Exception $e ) {
			Util::report_form_error( 'add data to saving queue of crm', $this->data, $e, $this->form->get_title() );
		}
	}

	/**
	 * Configure mailing service to use an SMTP account
	 *
	 * @param PHPMailer $phpmailer
	 */
	public function setup_SMTP( $phpmailer ) {
		// todo: test and move into mailer
		$config = get_field( 'form_smtp', 'options' );
		$phpmailer->isSMTP();
		$phpmailer->Host     = $config['host'];
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port     = $config['port'];
		$phpmailer->Username = $config['username'];
		$phpmailer->Password = $config['password'];
	}
}
