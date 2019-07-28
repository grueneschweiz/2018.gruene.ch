<?php

namespace SUPT;

use Exception;
use PHPMailer;
use WP_Error;
use function apply_filters;

require_once __DIR__ . '/include/FormModel.php';
require_once __DIR__ . '/include/SubmissionModel.php';

/**
 * handle the form submission according to FormType fields
 */
class FormSubmission {

	const ACTION_BASE_NAME = 'supt-form';
	const NEXT_ACTION_ID_DEFAULT = - 1;

	const OPTION_CRM_SAVE_QUEUE = 'supt_form_crm_save_queue';
	const CRON_HOOK_CRM_SAVE = 'supt_form_save_to_crm';
	const CRON_CRM_SAVE_RETRY_INTERVAL = 'hourly';

	const SUBMISSION_LIMIT_MINUTE_IP_FORM_AGENT = 2;
	const SUBMISSION_LIMIT_MINUTE_IP_FORM = 5;
	const SUBMISSION_LIMIT_HOUR_IP_FORM_AGENT = 10;
	const SUBMISSION_LIMIT_DAY_IP_FORM = 100;

	const CHECKBOX_TYPE = 'checkbox';
	const CONFIRMATION_TYPE = 'confirmation';
	const SELECT_TYPE = 'select';
	const RADIO_TYPE = 'radio';
	const EMAIL_TYPE = 'email';
	const NUMBER_TYPE = 'number';
	const PHONE_TYPE = 'phone';

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
	 * The form data mapped to the crm keys
	 *
	 * @var array
	 */
	private $crm_data;

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
	 * @var array
	 */
	private $fields;

	function __construct() {
		$this->register_actions();
	}

	private function register_actions() {
		add_action( 'wp_ajax_supt_form_submit', array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_supt_form_submit', array( $this, 'handle_submit' ) );
		add_action( 'supt_form_save_to_crm', array( __CLASS__, 'save_to_crm' ) );

		if ( ! WP_DEBUG && get_field( 'form_smtp_enabled', 'options' ) ) {
			add_action( 'phpmailer_init', array( $this, 'setup_SMTP' ) );
		}
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
		$this->add_to_saving_queue_of_crm();
		$this->send_notifications();
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

		// todo: rethink the nonces if using caching
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
		// todo: implement submission limiting
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
	 * Populate data field with validated and sanitized form data.
	 */
	private function add_data() {
		$fields = $this->get_fields();

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['hidden_field'] ) && $field['hidden_field'] ) {
				continue;
			}

			// sanitize and validate checkboxes
			if ( self::CHECKBOX_TYPE === $field['form_input_type'] ) {
				$choices = trim( $field['form_input_choices'] );
				$options = FormModel::split_choices( $choices );

				foreach ( $field['values'] as $value_key => $value ) {
					$raw     = $this->get_field_data( $value_key, true );
					$checked = $this->sanitize( $raw, self::CHECKBOX_TYPE );
					$valid   = $this->validate( $checked, self::CHECKBOX_TYPE, $options, false );

					if ( true !== $valid ) {
						$this->errors[ $key ] = $valid;
					}

					if ( $checked ) {
						$this->data[ $key ][ $value_key ] = $value;
					}
				}

			} else {
				$raw = $this->get_field_data( $key );

				$type      = $field['form_input_type'];
				$choices   = trim( $field['form_input_choices'] );
				$options   = FormModel::split_choices( $choices );
				$required  = $field['form_input_required'];
				$sanitized = $this->sanitize( $raw, $type );
				$valid     = $this->validate( $sanitized, $type, $options, $required );

				if ( true !== $valid ) {
					$this->errors[ $key ] = $valid;
				}

				$this->data[ $key ] = $sanitized;
			}
		}
	}

	/**
	 * Return the form fields as defined in the backend, using cache
	 *
	 * @return array
	 */
	private function get_fields() {
		if ( ! $this->fields ) {
			$this->fields = $this->form->get_fields();

			foreach ( $this->fields as $key => $field ) {
				if ( self::CHECKBOX_TYPE === $field['form_input_type'] ) {
					$labels = FormModel::split_choices( $field['form_input_choices'] );
					for ( $i = 0; $i < count( $labels ); $i ++ ) {
						$valueKey                                    = $key . '-' . $i;
						$this->fields[ $key ]['values'][ $valueKey ] = trim( $labels[ $i ] );
					}
				}
			}
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
	 * Basic sanitation.
	 *
	 * Note: Radio and select data stays unchanged.
	 *
	 * @param $data
	 * @param $type
	 *
	 * @return bool|string
	 */
	private function sanitize( $data, $type ) {
		switch ( $type ) {
			case self::CHECKBOX_TYPE:
			case self::CONFIRMATION_TYPE:
				return filter_var( $data, FILTER_VALIDATE_BOOLEAN );
			case self::RADIO_TYPE:
			case self::SELECT_TYPE:
				return $data;
			case self::PHONE_TYPE:
				$allowed = '\d\+ -\\\(\)';

				return preg_replace( "/[^${allowed}]/", '', $data );
			case self::EMAIL_TYPE:
				return filter_var( $data, FILTER_SANITIZE_EMAIL );
			case self::NUMBER_TYPE:
				return filter_var( $data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			default:
				return strip_tags( $data );
		}
	}

	/**
	 * Validate data according to type
	 *
	 * @param string|bool $data to validate
	 * @param string $type possible values: checkbox, confirmation, radio, select, email, phone, text, textarea
	 * @param array $options possible values for select and radio fields
	 * @param bool $required
	 *
	 * @return bool
	 */
	private function validate( $data, $type, $options, $required ) {
		$valid   = true;
		$message = __( 'Invalid input.', THEME_DOMAIN );

		switch ( $type ) {
			case self::CHECKBOX_TYPE:
			case self::CONFIRMATION_TYPE:
				$valid = true;
				break;

			case self::RADIO_TYPE:
			case self::SELECT_TYPE:
				if ( empty( $data ) ) {
					$valid = true;
					break;
				}

				foreach ( $options as $key => $val ) {
					$options[ $key ] = trim( preg_replace( '/[&<>"\']/', '', $val ) );
				}

				$valid = in_array( $data, $options );
				break;

			case self::EMAIL_TYPE:
				$valid   = filter_var( $data, FILTER_VALIDATE_EMAIL );
				$message = __( 'Invalid email.', THEME_DOMAIN );
				break;

			case self::NUMBER_TYPE:
				$valid   = is_numeric( $data ) || '' === $data;
				$message = __( 'Invalid number', THEME_DOMAIN );
				break;
		}

		if ( $required && empty( trim( $data ) ) ) {
			$valid   = false;
			$message = __( 'This field is required.', THEME_DOMAIN );
		}

		return (bool) $valid ? (bool) $valid : $message;
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
	 * Find email in CRM data if not existent, return the value of the first non
	 * empty field with type email.
	 *
	 * @return string|false email or false if no email was found
	 */
	private function get_email_address() {
		if ( null !== $this->email ) {
			return $this->email;
		}

		if ( $this->crm_data ) {
			foreach ( $this->crm_data as $key => $value ) {
				if ( $key === 'email1' && filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
					return $value;
				}
			}
		}

		foreach ( $this->get_fields() as $key => $field ) {
			if ( self::EMAIL_TYPE === $field['form_input_type'] ) {
				$email = $this->data[ $key ];
				if ( ! empty( $email && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) ) {
					return $email;
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
			// todo: send email
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

	private function add_to_saving_queue_of_crm() {
		try {
			$this->add_crm_mapped_data();
		} catch ( \InvalidArgumentException $e ) {
			// todo: !important! send email
		}

		if ( ! empty( $this->crm_data ) ) {
			/**
			 * Filters the submitted data, before it's sent to the crm.
			 *
			 * @param array $this ->crm_data
			 */
			$crm_data = (array) apply_filters( FormType::MODEL_NAME . '-before-crm-save', $this->crm_data );

			$to_save = get_option( self::OPTION_CRM_SAVE_QUEUE, array() );

			// don't save double submissions twice
			if ( ! in_array( $crm_data, $to_save ) ) {
				$to_save[] = $crm_data;
				update_option( self::OPTION_CRM_SAVE_QUEUE, $to_save, false );
			}

			// schedule cron
			if ( ! wp_next_scheduled( self::CRON_HOOK_CRM_SAVE ) ) {
				wp_schedule_event( time(), self::CRON_CRM_SAVE_RETRY_INTERVAL, self::CRON_HOOK_CRM_SAVE );
			}
		}
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
				// todo: send email
			}
		}
	}

	/**
	 * Send notification and confirmation mails
	 */
	private function send_notifications() {
		/**
		 * Filters the submitted data, before notifications are sent.
		 *
		 * @param array $data the form data
		 */
		$data = (array) apply_filters( FormType::MODEL_NAME . '-before-email-notification', $this->data );

		/**
		 * Fires before the email notifications are sent.
		 *
		 * @param array $data with
		 *  'form_data' => array with the validated and sanitized form data
		 *  'action_id' => int|null the engagement funnel form data
		 */
		do_action( FormType::MODEL_NAME . '-before-email-notification',
			array(
				'form_data' => $data,
				'action_id' => $this->action_id,
			)
		);

		$form = $this->form;

		if ( ! $form->has_confirmation() && ! $form->has_notification() ) {
			// nothing to send
			return;
		}

		$sender_name = $form->get_sender_name();

		/**
		 * Filters the sender address of email notifications
		 *
		 * @param array $data the form data
		 */
		$from = apply_filters( FormType::MODEL_NAME . '-email-from',
			"$sender_name <noreply@{$this->get_domain()}>" );

		$reply_to        = $form->get_reply_to_address();
		$confirmation_to = $this->get_email_address();

		if ( $form->has_confirmation() && $confirmation_to ) {
			supt_form_send_email(
				$confirmation_to,
				$from,
				$reply_to,
				$form->get_confirmation_subject(),
				$form->get_confirmation_template(),
				$this->data,
				$this->post_meta_id
			);
		}

		if ( $form->has_notification() ) {
			supt_form_send_email(
				$form->get_notification_destination(),
				$from,
				$this->get_email_address(),
				$form->get_notification_subject(),
				$form->get_notification_template(),
				$this->data,
				$this->post_meta_id
			);
		}
	}

	/**
	 * Return domain of current blog
	 */
	private function get_domain() {
		$url = get_site_url();
		preg_match( "/^https?:\/\/(www.)?([^\/?:]*)/", $url, $matches );
		if ( $matches && is_array( $matches ) ) {
			return $matches[ count( $matches ) - 1 ];
		}

		return new WP_Error( 'cant-get-domain', 'The domain could not be parsed from the site url', $url );
	}

	/**
	 * Configure mailing service to use an SMTP account
	 *
	 * @param PHPMailer $phpmailer
	 */
	public function setup_SMTP( $phpmailer ) {
		$config = get_field( 'form_smtp', 'options' );
		$phpmailer->isSMTP();
		$phpmailer->Host     = $config['host'];
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port     = $config['port'];
		$phpmailer->Username = $config['username'];
		$phpmailer->Password = $config['password'];
	}

	/**
	 * Add data from fields that were mapped to crm fields
	 *
	 * @throws \InvalidArgumentException
	 */
	private function add_crm_mapped_data() {
		require_once __DIR__ . '/include/CrmFieldData.php';

		// add data from linked submissions first
		if ( $this->predecessor_id >= 0 ) {
			try {
				$predecessor = new SubmissionModel( $this->predecessor_id );
				$fields      = $predecessor->meta_get_form()->get_fields();

				foreach ( $fields as $key => $field ) {
					if ( ! empty( $field['crm_field'] ) ) {
						$data                                  = $predecessor->{"get_$key"}();
						$this->crm_data[ $field['crm_field'] ] = new CrmFieldData( $field, $data );
					}
				}
			} catch ( Exception $e ) {
				// todo: send email
			}
		}

		// the add the current data
		// (so in case we have overlapping fields, the current data will be used)
		foreach ( $this->get_fields() as $key => $field ) {
			if ( ! empty( $field['crm_field'] ) ) {
				$data = $field['hidden_field'] ? $field['hidden_field_value'] : $this->data[ $key ];

				$this->crm_data[ $field['crm_field'] ] = new CrmFieldData( $field, $data );
			}
		}

		// add the group
		$fake_field = array(
			'crm_field'       => 'groups',
			'insertion_modus' => 'append'
		);
		$data       = \get_field( 'group_id', 'option' );

		$this->crm_data['groups'] = new CrmFieldData( $fake_field, $data );

		// add the entry channel if not already set
		if ( ! isset( $this->crm_data['entryChannel'] ) ) {
			$fake_field = array(
				'crm_field'       => 'entryChannel',
				'insertion_modus' => 'addIfNew'
			);
			$data       = get_home_url() . ' - ' . $this->form->get_title();

			$this->crm_data['entryChannel'] = new CrmFieldData( $fake_field, $data );
		}
	}

	/**
	 * Save the new form submissions to the crm
	 *
	 * Called by the WordPress cron job
	 */
	public static function save_to_crm() {
		require_once __DIR__ . '/include/CrmFieldData.php';

		$to_save = get_option( self::OPTION_CRM_SAVE_QUEUE, array() );
		if ( empty( $to_save ) ) {
			return;
		}

		require_once __DIR__ . '/include/CrmDao.php';
		$dao = new CrmDao();
		foreach ( $to_save as $key => $submission ) {
			$crm_id = false;

			try {
				$crm_id = $dao->save( $submission );
			} catch ( Exception $e ) {
				echo $e->getMessage();
				// todo: if permanent error -> send mail, else do nothing
			}

			if ( $crm_id ) {
				unset( $to_save[ $key ] );
			}
		}

		update_option( self::OPTION_CRM_SAVE_QUEUE, $to_save );

		if ( empty( $to_save ) ) {
			// since everything was saved, we can now disable the cron job
			// it will automatically be reenabled, if needed
			$timestamp = wp_next_scheduled( self::CRON_HOOK_CRM_SAVE );
			wp_unschedule_event( $timestamp, self::CRON_HOOK_CRM_SAVE );
		}
	}
}
