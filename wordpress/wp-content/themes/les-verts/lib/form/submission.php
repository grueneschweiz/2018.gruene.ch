<?php

/**
 * handle the form submission according to FormType fields
 */
class FormSubmission {
	
	const ACTION_BASE_NAME = 'supt-form';
	
	/**
	 * The form post
	 *
	 * @var int
	 */
	private $form_id;
	
	/**
	 * The id of the engagement funnel action
	 *
	 * @var int|null
	 */
	private $action_id;
	
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
	 * Configure mailing service to use an SMTP account
	 *
	 * @param $phpmailer
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
	 * Process form submission
	 */
	public function handle_submit() {
		$this->form_id = (int) $_POST['form_id'];
		
		if ( ! $this->is_submission_valid() ) {
			$this->errors[] = 'Submission not valid.';
			$this->status   = 400;
			$this->response();
			
			return;
		}
		
		if ( $_POST['action_id'] ) {
			$this->action_id = (int) $_POST['action_id'];
		}
		
		// get and validate data from request
		$this->add_data();
		
		// if there were some validation errors
		if ( $this->errors ) {
			$this->status = 400;
			$this->response();
			
			return;
		}
		
		$this->status = 200;
		$this->save_submission();
		$this->send_notifications();
		
		$this->response();
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
		$data = (array) apply_filters( \SUPT\FormType::MODEL_NAME . '-before-email-notification', $this->data );
		
		/**
		 * Fires before the email notifications are sent.
		 *
		 * @param array $data with
		 *  'form_data' => array with the validated and sanitized form data
		 *  'action_id' => int|null the engagement funnel form data
		 */
		do_action( \SUPT\FormType::MODEL_NAME . '-before-email-notification',
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
		$from = apply_filters( \SUPT\FormType::MODEL_NAME . '-email-from',
			"$sender_name <noreply@{$this->get_domain()}>" );
		
		$reply_to        = $fields['form_reply_to']['value'];
		$confirmation_to = $this->determine_confirmation_email_address();
		
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
		
		new WP_Error( 'cant-get-domain', 'The domain could not be parsed from the site url', $url );
	}
	
	/**
	 * Return the value of the first non empty field with type email
	 *
	 * @return bool|string
	 */
	private function determine_confirmation_email_address() {
		foreach ( $this->get_fields() as $field ) {
			if ( 'email' === $field['form_input_type'] ) {
				$field_slug = supt_slugify( $field['form_input_label'] );
				if ( ! empty( $this->data[ $field_slug ] ) ) {
					return $this->data[ $field_slug ];
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Populate data field with validated and sanitized form data.
	 */
	private function add_data() {
		$fields = $this->get_fields();
		
		$this->data['_meta_'] = array(
			'form_id' => $this->form_id,
			'action_id' => $this->action_id,
			'timestamp' => date('Y-m-d H:i:s')
		);
		
		foreach ( $fields as $key => $field ) {
			
			// sanitize and validate checkboxes
			if ( 'checkbox' === $field['form_input_type'] ) {
				$choices  = $field['form_input_choices'];
				$options  = array_map( 'trim', explode( "\n", $choices ) );
				$required = $field['form_input_required'];
				
				foreach ( $field['values'] as $value_key => $value ) {
					$raw     = $this->get_field( $value_key );
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
				$raw = $this->get_field( $key );
				
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
	 * Get value from post variable or set error
	 *
	 * @param $key
	 *
	 * @return string|null
	 */
	private function get_field( $key ) {
		if ( ! array_key_exists( $key, $_POST ) ) {
			// if field was not transmitted
			$this->errors[ $key ] = __( 'Missing data.', THEME_DOMAIN );
			
			return null;
		}
		
		// get data
		return trim( $_POST[ $key ] );
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
		}
		
		if ( $required && empty( trim( $data ) ) ) {
			$valid   = false;
			$message = __( 'This field is required.', THEME_DOMAIN );
		}
		
		return (bool) $valid ? (bool) $valid : $message;
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
				$allowed = '\d\+ -';
				
				return preg_replace( "/[^${allowed}]/", '', $data );
			case 'email':
				return filter_var( $data, FILTER_SANITIZE_EMAIL );
			default:
				return strip_tags( $data );
		}
	}
	
	/**
	 * Return the form fields as defined in the backend from cache
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
	 * Check if form is submitted using ajax, has a valid nonce and form id
	 *
	 * @return bool
	 */
	private function is_submission_valid() {
		// check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], self::ACTION_BASE_NAME ) ) {
			return false;
		}
		
		// only accept ajax submissions
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return false;
		}
		
		// check if form id corresponds to a form post type
		if ( get_post_type( $this->form_id ) !== \SUPT\FormType::MODEL_NAME ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Persist form submission data
	 */
	function save_submission() {
		/**
		 * Filters the submitted data, before it's persisted.
		 *
		 * @param array $this ->data
		 */
		$data = (array) apply_filters( \SUPT\FormType::MODEL_NAME . '-before-save', $this->data );
		
		$post_meta_id = add_post_meta( $this->form_id, \SUPT\FormType::MODEL_NAME, $data );
		
		if ( $post_meta_id ) {
			/**
			 * Fires after the data is persisted.
			 *
			 * @param array $data with
			 *  'form_data' => array with the validated and sanitized form data
			 *  'action_id' => int|null the engagement funnel form data
			 *  'post_meta_id' => the id of the post meta table where the form data is stored
			 */
			do_action( \SUPT\FormType::MODEL_NAME . '-after-save',
				array(
					'form_data'    => $data,
					'action_id'    => $this->action_id,
					'post_meta_id' => $post_meta_id
				)
			);
		}
	}
	
	/**
	 * Send response and die
	 */
	private function response() {
		status_header( $this->status );
		
		if ( $this->status == 200 ) {
			wp_send_json_success( $this->data );
		} else {
			wp_send_json_error( $this->errors );
		}
		
		wp_die();
	}
}
