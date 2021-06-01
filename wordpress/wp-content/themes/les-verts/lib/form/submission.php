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

	const NEXT_ACTION_ID_DEFAULT = - 1;
	const ERROR_GENERAL = 1;
	const ERROR_VALIDATION = 2;

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
	 * The url to the page that contains the form
	 *
	 * @var string
	 */
	private $referer_url = '';

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
	private $validation_errors = array();

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
		add_action( 'supt_form_remove_expired_nonces', array( __CLASS__, 'remove_expired_nonces' ) );

		if ( ! WP_DEBUG && get_field( 'form_smtp_enabled', 'options' ) ) {
			add_action( 'phpmailer_init', array( $this, 'setup_SMTP' ) );
		}
	}

	/**
	 * Remove the expired nonces
	 *
	 * Called by the WordPress cron job
	 */
	public static function remove_expired_nonces() {
		require_once __DIR__ . '/include/Nonce.php';
		Nonce::remove_expired();
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
		$this->abort_if_limit_exceeded();
		$this->add_submission_metadata();
		$this->abort_if_invalid_header();
		$this->add_data();
		$this->abort_if_invalid_data();
		$this->add_metadata();
		$this->save();
		$this->add_mails_to_sending_queue();
		$this->add_to_saving_queue_of_crm();
		spawn_cron();

		$this->send_response();
	}

	/**
	 * Limit form submissions per ip and ip user agent combination.
	 *
	 * Prevent spamming and dos attacks.
	 */
	private function abort_if_limit_exceeded() {
		require_once __DIR__ . '/include/Limiter.php';

		$limiter = new Limiter( 'submission' );

		if ( ! $limiter->below_limit() ) {
			$this->respond_with_general_error(
				429,
				__( 'Too many requests. Please try again later.', THEME_DOMAIN )
			);

			return;
		}

		$limiter->log_attempt();
	}

	/**
	 * Send error message to client and die
	 *
	 * @param int $status_code http status code
	 * @param mixed $messages the error message(s)
	 * @param bool $new_nonce should a new nonce be sent?
	 */
	private function respond_with_general_error( $status_code, $messages, $new_nonce = false ) {
		$this->send_error_response( self::ERROR_GENERAL, $status_code, $messages, $new_nonce );
	}

	/**
	 * Send error message to client and die
	 *
	 * @param string $type 'validation' or 'general'
	 * @param int $status_code http status code
	 * @param mixed $errors the error message(s)
	 * @param bool $nonce should a new nonce be sent?
	 */
	private function send_error_response( $type, $status_code, $errors, $nonce ) {
		if ( self::ERROR_VALIDATION === $type ) {
			$data['validation'] = $errors;
		} else {
			$data['general'] = $errors;
		}

		if ( $nonce ) {
			require_once __DIR__ . '/include/Nonce.php';
			$data['nonce'] = Nonce::create();
		}

		wp_send_json_error( $data, $status_code );
		wp_die();
	}

	/**
	 * Set form. And if present: action_id, config_id, predecessor_id, nonce
	 */
	private function add_submission_metadata() {
		try {
			$this->form = new FormModel( absint( $_POST['form_id'] ) );
		} catch ( Exception $e ) {
			$this->respond_with_general_error( 400, 'Invalid form.' );

			return;
		}

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$this->referer_url = $this->get_safe_referer();
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

		if ( isset( $_POST['nonce'] ) ) {
			$this->nonce = $_POST['nonce'];
		}
	}

	private function get_safe_referer() {
		return htmlspecialchars(
			strip_tags(
				filter_var( $_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL )
			)
		);
	}

	/**
	 * Check if form is submitted using ajax and has a valid nonce.
	 */
	private function abort_if_invalid_header() {
		require_once __DIR__ . '/include/Nonce.php';

		if ( ! Nonce::consume( $this->nonce ) ) {
			$this->respond_with_general_error( 400, 'Invalid nonce.' );

			return;
		}

		// only accept ajax submissions
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$this->respond_with_general_error( 400, 'Submission not valid.' );
		}
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
			if ( $field->is_checkbox_type() ) {
				$this->data[ $key ] = array();

				foreach ( $field->get_choices() as $value_key => $value ) {
					$raw     = $this->get_field_data( $value_key, true );
					$checked = $field->sanitize( $raw );
					$valid   = $field->validate( $checked );

					if ( ! $valid ) {
						$this->validation_errors[ $key ] = sprintf(
							__( '%s: Invalid value.', THEME_DOMAIN ),
							$field->get_label()
						);
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
					$this->validation_errors[ $key ] = sprintf(
						__( '%s: Invalid or missing value.', THEME_DOMAIN ),
						$field->get_label()
					);
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

			$this->validation_errors[ $key ] = sprintf( __( '%s: Missing data.', THEME_DOMAIN ), $key );

			return null;
		}

		// get data
		return trim( $_POST[ $key ] );
	}

	/**
	 * Stop execution with a 400 if the data threw a validation error
	 */
	private function abort_if_invalid_data() {
		if ( ! empty( $this->validation_errors ) ) {
			$this->send_error_response( self::ERROR_VALIDATION, 400, $this->validation_errors, true );

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
			'referer_url'    => $this->referer_url,
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
			if ( $field->is_email_type() ) {
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
			$this->respond_with_general_error( 500, 'Internal server error.', true );

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
	 * Send response and die
	 */
	private function send_response() {
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

		wp_send_json_success(
			array(
				'next_action_id' => $next_action_id,
				'html'           => $html,
				'redirect'       => $this->form->get_redirect_url(),
				'predecessor_id' => $this->post_meta_id,
			),
			200
		);

		wp_die();
	}

	/**
	 * Configure mailing service to use an SMTP account
	 *
	 * @param PHPMailer $phpmailer
	 */
	public function setup_SMTP( $phpmailer ) {
		// todo: test and move into mailer
		$config = get_field( 'form_smtp', 'options' );

		$security = $config['form_smtp_security'] === 'none' ? '' : $config['form_smtp_security'];

		$phpmailer->isSMTP();
		$phpmailer->Host       = $config['form_smtp_host'];
		$phpmailer->SMTPAuth   = true;
		$phpmailer->SMTPSecure = $security;
		$phpmailer->Port       = $config['form_smtp_port'];
		$phpmailer->Username   = $config['form_smtp_username'];
		$phpmailer->Password   = $config['form_smtp_password'];
	}
}
