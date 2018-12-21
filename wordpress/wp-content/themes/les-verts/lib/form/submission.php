<?php

namespace SUPT;

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
	 * The form post
	 *
	 * @var int
	 */
	private $form_id = - 1;
	
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
	private $errors;
	
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
		$this->add_data();
		$this->abort_if_invalid_data();
		$this->add_crm_mapped_data();
		$this->add_metadata();
		$this->save();
		$this->send_notifications();
		
		$this->status = 200;
		$this->send_response();
	}
	
	/**
	 * Set ip, user_agent, form_id. And if present: action_id, config_id, nonce
	 */
	private function add_submission_metadata() {
		$this->ip         = $this->get_user_ip();
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->form_id    = absint( $_POST['form_id'] );
		
		if ( $_POST['action_id'] ) {
			$this->action_id = absint( $_POST['action_id'] );
		}
		
		if ( $_POST['config_id'] ) {
			$this->config_id = absint( $_POST['config_id'] );
		}
		
		// todo: rethink the nonces if using caching
		if ( $_POST['nonce'] ) {
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
	 * Check if form is submitted using ajax, has a valid nonce, form id and ip.
	 * Limit form submissions according to
	 * - SUBMISSION_LIMIT_MINUTE_IP_FORM_AGENT
	 * - SUBMISSION_LIMIT_MINUTE_IP_FORM
	 * - SUBMISSION_LIMIT_HOUR_IP_FORM_AGENT
	 * - SUBMISSION_LIMIT_DAY_IP_FORM
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
		
		// check if form id corresponds to a form post type
		if ( get_post_type( $this->form_id ) !== FormType::MODEL_NAME ) {
			$valid = false;
		}
		
		// check for valid ip
		if ( ! filter_var( $this->ip, FILTER_VALIDATE_IP ) ) {
			$valid = false;
		}
		
		// exit here if any of the above checks failed
		if ( ! $valid ) {
			$this->errors[] = 'Submission not valid.';
			$this->status   = 400;
			$this->send_response();
			
			return;
		}
		
		// todo: implement submission limiting
	}
	
	/**
	 * Stop execution with a 400 if the data threw a validation error
	 */
	private function abort_if_invalid_data() {
		if ( ! empty($this->errors) ) {
			$this->status   = 400;
			$this->send_response();
			
			return;
		}
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
			$next_action_id = \apply_filters( FormType::MODEL_NAME . '-next-form-id', self::NEXT_ACTION_ID_DEFAULT );
			
			$html = '';
			if ( self::NEXT_ACTION_ID_DEFAULT !== $next_action_id ) {
				$context['block']['configuration'] = $this->config_id;
				$context['block']['action']        = $next_action_id;
				
				$templates = [
					get_stylesheet_directory() . '/templates/engagement-funnel.twig',
					
					// fallback if child theme doesn't implement it
					get_template_directory() . '/templates/engagement-funnel.twig',
				];
				
				$html = \Timber::compile( $templates, $context );
			}
			
			wp_send_json_success( [ 'next_action_id' => $next_action_id, 'html' => $html ] );
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
			if ( $field['hidden_field'] ) {
				continue;
			}
			
			// sanitize and validate checkboxes
			if ( 'checkbox' === $field['form_input_type'] ) {
				$choices  = $field['form_input_choices'];
				$options  = array_map( 'trim', explode( "\n", $choices ) );
				$required = $field['form_input_required'];
				
				foreach ( $field['values'] as $value_key => $value ) {
					$raw     = $this->get_field_data( $value_key );
					$checked = $this->sanitize( $raw, 'checkbox' );
					$valid   = $this->validate( $checked, 'checkbox', $options, $required );
					
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
				$choices   = $field['form_input_choices'];
				$options   = array_map( 'trim', explode( "\n", $choices ) );
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
			$fields = get_field_objects( $this->form_id )['form_fields']['value'];
			
			foreach ( $fields as $field ) {
				$key                  = supt_slugify( $field['form_input_label'] );
				$this->fields[ $key ] = $field;
				
				if ( 'checkbox' === $field['form_input_type'] ) {
					$labels = explode( "\n", $field['form_input_choices'] );
					foreach ( $labels as $label ) {
						$valueKey                                    = supt_slugify( $label );
						$this->fields[ $key ]['values'][ $valueKey ] = trim( $label );
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
	 *
	 * @return string|null
	 */
	private function get_field_data( $key ) {
		if ( ! array_key_exists( $key, $_POST ) ) {
			// if field was not transmitted
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
			case 'checkbox':
			case 'confirmation':
				return filter_var( $data, FILTER_VALIDATE_BOOLEAN );
			case 'radio':
			case 'select':
				return $data;
			case 'phone':
				$allowed = '\d\+ -\\\(\)';
				
				return preg_replace( "/[^${allowed}]/", '', $data );
			case 'email':
				return filter_var( $data, FILTER_SANITIZE_EMAIL );
			case 'number':
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
			case 'checkbox':
			case 'confirmation':
				$valid = true;
				break;
			
			case 'radio':
			case 'select':
				if ( empty( $data ) ) {
					$valid = true;
					break;
				}
				
				$valid = in_array( $data, $options );
				break;
			
			case 'email':
				$valid   = filter_var( $data, FILTER_VALIDATE_EMAIL );
				$message = __( 'Invalid email.', THEME_DOMAIN );
				break;
			
			case 'number':
				$valid   = is_numeric( $data );
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
	 * Add data from fields that were mapped to crm fields
	 */
	private function add_crm_mapped_data() {
		foreach ( $this->get_fields() as $key => $field ) {
			if ( ! empty( $field['webling_field'] ) ) {
				if ( $field['hidden_field'] ) {
					$this->crm_data[ $field['webling_field'] ] = $field['hidden_field_value'];
				} else {
					$this->crm_data[ $field['webling_field'] ] = $this->data[ $key ];
				}
			}
		}
	}
	
	/**
	 * Add some meta data to the form data
	 */
	private function add_metadata() {
		$meta['_meta_'] = array(
			'form_id'   => $this->form_id,
			'action_id' => $this->action_id,
			'config_id' => $this->config_id,
			'timestamp' => date( 'Y-m-d H:i:s' ),
			'email'     => $this->get_email_address(),
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
			if ( 'email' === $field['form_input_type'] ) {
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
		$post_meta_id = add_post_meta( $this->form_id, FormType::MODEL_NAME, $data );
		
		// safe to crm
		if ( ! empty( $this->crm_data ) && $this->get_email_address()) {
			/**
			 * Filters the submitted data, before it's sent to the crm.
			 *
			 * @param array $this ->crm_data
			 */
			$crm_data = (array) apply_filters( FormType::MODEL_NAME . '-before-crm-save', $this->crm_data );
			try {
				include_once __DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Crm_Dao.php';
				$crm_dao  = new Crm_Dao();
				/** @noinspection PhpParamsInspection */
				$crm_id = $crm_dao->save( $this->get_email_address(), $crm_data );
			} catch (\Exception $e) {
				// todo: send mail
			}
		}
		
		if ( $post_meta_id ) {
			/**
			 * Fires after the data is persisted.
			 *
			 * @param array $data with
			 *  'form_data'    => array with the validated and sanitized form data
			 *  'action_id'    => int the engagement funnel action id
			 *  'config_id'    => int the engagement funnel config id
			 *  'post_meta_id' => int the id of the post meta table where the form data is stored
			 *  'crm_id'       => int the id of the person in the crm
			 */
			do_action( FormType::MODEL_NAME . '-after-save',
				array(
					'form_data'    => $data,
					'action_id'    => $this->action_id,
					'config_id'    => $this->config_id,
					'post_meta_id' => $post_meta_id,
					'crm_id'       => empty( $crm_id ) ? -1 : $crm_id
				)
			);
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
		
		$fields = get_field_objects( $this->form_id );
		
		$confirmation = $fields['form_send_confirmation']['value'];
		$notification = $fields['form_send_notification']['value'];
		
		if ( ! $confirmation && ! $notification ) {
			// nothing to send
			return;
		}
		
		$sender_name = $fields['form_sender_name']['value'];
		
		/**
		 * Filters the sender address of email notifications
		 *
		 * @param array $data the form data
		 */
		$from = apply_filters( FormType::MODEL_NAME . '-email-from',
			"$sender_name <noreply@{$this->get_domain()}>" );
		
		$reply_to        = $fields['form_reply_to']['value'];
		$confirmation_to = $this->get_email_address();
		
		if ( $confirmation && $confirmation_to ) {
			$confirmation_mail = $fields['form_confirmation_mail']['value'];
			
			supt_form_send_email(
				$confirmation_to,
				$from,
				$reply_to,
				$confirmation_mail['form_mail_subject'],
				$confirmation_mail['form_mail_template'],
				$this->data
			);
		}
		
		if ( $notification ) {
			$notification_mail = $fields['form_notification_mail']['value'];
			
			supt_form_send_email(
				$notification_mail['form_confirmation_destination'],
				$from,
				$reply_to,
				$notification_mail['form_mail_subject'],
				$notification_mail['form_mail_template'],
				$this->data
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
		
		return new \WP_Error( 'cant-get-domain', 'The domain could not be parsed from the site url', $url );
	}
	
	/**
	 * Configure mailing service to use an SMTP account
	 *
	 * @param \PHPMailer $phpmailer
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
}
