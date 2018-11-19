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
	
	public function setup_SMTP( $phpmailer ) {
		$config = get_field( 'form_smtp', 'options' );
		$phpmailer->isSMTP();
		$phpmailer->Host     = $config['host'];
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port     = $config['port'];
		$phpmailer->Username = $config['username'];
		$phpmailer->Password = $config['password'];
	}
	
	public function handle_submit() {
		$this->form_id = (int) $_POST['form_id'];
		
		if ( ! $this->is_submission_valid() ) {
			$this->errors[] = 'Submission not valid.';
			$this->status   = 400;
			$this->response();
			
			return;
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
		
		// TODO check names of ACF
		if ( in_array( $sendMethod, array( 'email', 'news' ) ) ) {
			$email = $this->getEmailParams();
			
			// Sends emails
			try {
				// Notification
				$sent1 = supt_form_send_email( $email['to'], $email['from'], $email['notif']['subject'],
					$email['notif']['template'], $this->data );
				
				// Auto Reply
				$sent2 = true;
				if ( isset( $this->autoReplyEmail ) ) {
					$sent2 = supt_form_send_email( $this->autoReplyEmail, $email['from'], $email['autoreply']['subject'],
						$email['autoreply']['template'], $this->data );
				}
			} catch ( Exception $e ) {
				error_log( 'FORM SUBMISSION - Exception thrown while sending a form: ' . $e );
				$this->error['global'] = true;
				$this->status          = 500;
			}
			
			
			if ( ! ( isset( $sent1 ) && $sent1 && isset( $sent2 ) && $sent2 ) ) {
				error_log( 'FORM SUBMISSION - Error while sending a form.' );
				$this->error['global'] = true;
				$this->status          = 500;
			}
		}
		
		$this->response();
	}
	
	/**
	 * Populate data field with validated and sanitized form data.
	 */
	private function add_data() {
		$whitelist = array_keys( $this->get_fields() );
		
		foreach ( $whitelist as $field ) {
			if ( ! array_key_exists( $field, $_POST ) ) {
				// if field was not transmitted
				$this->errors[ $field ] = __( 'Missing data.', THEME_DOMAIN );
				continue;
			}
			
			// get data
			$raw = trim( $_POST[ $field ] );
			
			// sanitize and validate
			$type      = $this->get_fields()[ $field ]['form_input_type'];
			$choices   = $this->get_fields()[ $field ]['form_input_choices'];
			$options   = array_map( 'trim', explode( "\n", $choices ) );
			$required  = $this->get_fields()[ $field ]['form_input_required'];
			$sanitized = $this->sanitize( $raw, $type );
			$valid     = $this->validate( $sanitized, $type, $options, $required );
			
			if ( true !== $valid ) {
				$this->errors[ $field ] = $valid;
			}
			
			$this->data[ $field ] = $sanitized;
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
				if ( 'checkbox' === $field['form_input_type'] ) {
					$labels = explode( "\n", $field['form_input_choices'] );
					foreach ( $labels as $label ) {
						$key                  = $this->slugify( $label );
						$this->fields[ $key ] = $field;
					}
				} else {
					$key                  = $this->slugify( $field['form_input_label'] );
					$this->fields[ $key ] = $field;
				}
			}
		}
		
		return $this->fields;
	}
	
	/**
	 * Transform given string in slug (as used in the slugify twig function)
	 *
	 * @param $string
	 *
	 * @return string
	 */
	private function slugify( $string ) {
		return sanitize_title( trim( $string ) );
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
	 * Retrieve the params for the emails
	 * from the form & global options
	 *
	 * @return  array    The email params
	 */
	private function getEmailParams() {
		
		// Retrieve options from form
		$to        = get_field( 'form_email_to', $this->id );
		$fromName  = get_field( 'form_name_from', $this->id );
		$fromEmail = get_field( 'form_email_from', $this->id );
		$notif     = get_field( 'form_notif', $this->id );
		$autoreply = get_field( 'form_autoreply', $this->id );
		
		// If options empty retrieve global options
		if ( empty( $to ) ) {
			$to = get_field( 'form_email_to', 'options' );
		}
		if ( empty( $fromName ) ) {
			$fromName = get_field( 'form_name_from', 'options' );
		}
		if ( empty( $fromEmail ) ) {
			$fromEmail = get_field( 'form_email_from', 'options' );
		}
		
		$optNotif          = get_field( 'form_notif', 'options' );
		$notif['subject']  = empty( $notif['form_notif_subject'] ) ? $optNotif['form_notif_subject'] : $notif['form_notif_subject'];
		$notif['template'] = empty( $notif['form_notif_template'] ) ? $optNotif['form_notif_template'] : $notif['form_notif_template'];
		
		$optAutoreply          = get_field( 'form_autoreply', 'options' );
		$autoreply['subject']  = empty( $autoreply['form_autoreply_subject'] ) ? $optAutoreply['form_autoreply_subject'] : $autoreply['form_autoreply_subject'];
		$autoreply['template'] = empty( $autoreply['form_autoreply_template'] ) ? $optAutoreply['form_autoreply_template'] : $autoreply['form_autoreply_template'];
		
		if ( ! empty( $fromName ) ) {
			$from = "$fromName <$fromEmail>";
		} else {
			$from = $fromEmail;
		}
		
		return array(
			'to'        => $to,
			'from'      => $from,
			'notif'     => $notif,
			'autoreply' => $autoreply,
		);
	}
	
	/**
	 * Persist form submission data
	 */
	function save_submission() {
		add_post_meta( $this->form_id, \SUPT\FormType::POST_META_NAME_FORM_SENT, $this->data );
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
