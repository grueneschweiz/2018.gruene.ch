<?php

/**
 * handle the form submission according to FormType fields
 */
class FormSubmission {
	
	const ACTION_BASE_NAME = 'supt-form';
	const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
	
	function __construct() {
		$this->status  = 400;
		$this->data    = false;
		$this->errors  = false;
		$this->is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		
		$this->register_actions();
	}
	
	private function register_actions() {
		add_action( 'wp_ajax_supt_form_submit',array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_supt_form_submit', array( $this, 'handle_submit' ));
		
		if ( ! WP_DEBUG && get_field( 'form_smtp_enabled', 'options' ) ) {
			add_action( 'phpmailer_init', array( $this, 'setupSMTP' ) );
		}
	}
	
	public function setupSMTP( $phpmailer ) {
		$config = get_field( 'form_smtp', 'options' );
		$phpmailer->isSMTP();
		$phpmailer->Host     = $config['host'];
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port     = $config['port'];
		$phpmailer->Username = $config['username'];
		$phpmailer->Password = $config['password'];
	}
	
	public function handle_submit() {
		var_dump($_REQUEST); die();
		
		if ( ! ( isset( $_REQUEST['_supt_action'] ) && strpos( $_REQUEST['_supt_action'],
				self::ACTION_BASE_NAME ) !== false ) ) {
			return;
		}
		
		$this->action_name = $_REQUEST['_supt_action'];
		
		// Retrieve the form id from the action and check if the post type is correct
		$this->id = (int) str_replace( self::ACTION_BASE_NAME . '_', '', $this->action_name );
		if ( get_post_type( $this->id ) !== \SUPT\FormType::MODEL_NAME ) {
			return;
		}
		
		// define if it's an ajax request
		$this->is_ajax = ( isset( $_REQUEST['ajx'] ) && $_REQUEST['ajx'] );
		
		if ( ! $this->isSecure() ) {
			$this->response();
			
			return;
		}
		
		$this->validateRecaptcha();
		if ( ! empty( $this->errors ) ) {
			$this->response();
			
			return;
		}
		
		// Validate inputs and stop process if any error
		$this->filterSanitizeValidate();
		if ( ! empty( $this->errors ) ) {
			$this->response();
			
			return;
		}
		
		$sendMethod = get_field( 'which_type_of_form', $this->id );
		
		$this->status = 200;
		$this->save_submission();
		
		$subscribed = $this->subscribe_campaign_monitor();
		
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
	
	private function isSecure() {
		if ( $this->is_ajax ) {
			$isSecure = check_ajax_referer( $this->action_name, false, false );
		} else {
			$isSecure = wp_verify_nonce( $_REQUEST['_wpnonce'], $this->action_name );
		}
		
		if ( ! $isSecure ) {
			$this->status           = 403;
			$this->errors['global'] = true;
		}
		
		return $isSecure;
	}
	
	/**
	 * Filter, sanitize & validate the data accordingly to the
	 * registered fields linked to this form
	 */
	private function filterSanitizeValidate() {
		
		$fields      = get_field( 'fields', $this->id );
		$conf_fields = get_field( 'confirmation_fields', $this->id );
		
		if ( ! is_array( $conf_fields ) ) {
			$conf_fields = array();
		}
		for ( $i = 0; $i < count( $conf_fields ); $i ++ ) {
			$conf_fields[ $i ]['type'] = 'checkbox';
			// $conf_fields[$i]['required'] = false; // commented: this is now set in the admin
		}
		
		foreach ( array_merge( $fields, $conf_fields ) as $field ) {
			$name = ( empty( $field['name'] ) ? supt_form_sanitize_with_underscore( $field['label'] ) : $field['name'] );
			
			// if not existing & required, add an error
			if ( ! isset( $_REQUEST[ $name ] ) && $field['required'] ) {
				$this->errors[ $name ][] = pll__( 'Mandatory field' );
				continue;
			}
			
			// if not existing & not required, maybe do stuff
			if ( ! isset( $_REQUEST[ $name ] ) ) {
				switch ( $field['type'] ) {
					case 'checkbox':
						$_REQUEST[ $name ] = "no";
						break;
					
					default:
						continue;
						break;
				}
			}
			
			switch ( $field['type'] ) {
				case 'email':
					if ( ! empty( $_REQUEST[ $name ] ) && ! is_email( $_REQUEST[ $name ] ) ) {
						$this->errors[ $name ][] = pll__( 'Invalid email address' );
					}
					$this->data[ $name ]  = sanitize_email( $_REQUEST[ $name ] );
					$this->autoReplyEmail = $this->data[ $name ];
					break;
				
				case 'number':
					$this->data[ $name ] = (int) $_REQUEST[ $name ];
					break;
				
				case 'checkbox':
					if ( is_array( $_REQUEST[ $name ] ) && isset( $field['choices'] ) ) {
						$choices = supt_form_parse_field_choices( $field['choices'] );
						
						foreach ( $_REQUEST[ $name ] as $value ) {
							$this->data[ $name ][ $value ] = $choices[ $value ];
						}
						
					} else {
						$this->data[ $name ] = empty( $_REQUEST[ $name ] ) ? "yes" : $_REQUEST[ $name ];
					}
					break;
				case 'radio':
					$choices             = supt_form_parse_field_choices( $field['choices'] );
					$this->data[ $name ] = $choices[ $_REQUEST[ $name ][0] ];
					break;
				
				case 'textarea':
					$this->data[ $name ] = sanitize_textarea_field( $_REQUEST[ $name ] );
					break;
				
				case 'text':
				default:
					$this->data[ $name ] = sanitize_text_field( $_REQUEST[ $name ] );
					break;
			}
			
			if ( $field['required'] && empty( $this->data[ $name ] ) ) {
				$this->errors[ $name ][] = pll__( 'Mandatory field' );
			}
		}
		
		// Add timestamp for later use
		$this->data['timestamp'] = time();
	}
	
	private function validateRecaptcha() {
		if ( ! get_field( 'recaptcha_active', 'options' ) ) {
			return true;
		}
		
		if ( ! isset( $_REQUEST['g-recaptcha-response'] ) || empty( $_REQUEST['g-recaptcha-response'] ) ) {
			error_log( 'FORM SUBMISSION - Missing reCAPTCHA token' );
			$this->errors['global'] = true;
		}
		
		// Init the request object
		$ch = curl_init();
		
		// Set the request parameters
		curl_setopt_array( $ch, array(
			CURLOPT_URL            => self::RECAPTCHA_VERIFY_URL,
			CURLOPT_HEADER         => false,
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => array(
				"secret"   => get_field( 'recaptcha_secret_key', 'options' ),
				"response" => $_REQUEST['g-recaptcha-response'],
				"remoteip" => $_SERVER['REMOTE_ADDR'],
			)
		) );
		
		// Execute & close the request
		$response = json_decode( curl_exec( $ch ) );
		curl_close( $ch );
		
		if ( ! $response->success ) {
			error_log( 'FORM SUBMISSION - Unenable to verify reCAPTCHA token' );
			$this->errors['global'] = true;
		}
		
		return empty( $this->errors );
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
	 * Saves form submission data into the database
	 * Only saves if setting `form_save_db` is enables
	 */
	function save_submission() {
		if ( get_field( 'form_save_db', 'options' ) ) {
			add_post_meta( $this->id, \SUPT\FormType::POST_META_NAME_FORM_SENT, $this->data );
		}
	}
	
	function subscribe_campaign_monitor() {
		
		$sendMethod = get_field( 'which_type_of_form', $this->id ); // email, cm, news
		
		if ( in_array( $sendMethod, array( 'cm', 'news' ) ) ) {
			
			$api = \CampaignMonitorAPI::getAPI();
			
			// if checkbox & is checked or no checkbox but do not notify
			if ( $sendMethod == 'cm' || isset( $_REQUEST['subscribe_newsletter'] ) ) {
				
				$cmSettings   = get_field( 'campaign_monitor_settings', $this->id );
				$sourceFields = $cmSettings['source_fields'];
				// Retrieve email info
				$emailFieldName = $sourceFields['email_field_source'];
				$email          = $this->data[ $emailFieldName ];
				
				// Retrieve name info
				
				
				$firstnameFieldName = $sourceFields['first_name_field_source'];
				$lastnameFieldName  = $sourceFields['last_name_field_source'];
				$name               = $this->data[ $firstnameFieldName ] . ( ! empty( $lastnameFieldName ) ? ' ' . $this->data[ $lastnameFieldName ] : '' );
				
				// Subscribe user
				if ( ! ( empty( $email ) && empty( $name ) ) ) {
					$cmListId = $cmSettings['subscribers_list'];
					
					return $api->addSubscriber( $cmListId, $email, $name );
				}
			}
			
		}
		
		return false;
	}
	
	/**
	 * Send the repsonse if ajax
	 * or set the status in global variable
	 */
	private function response() {
		if ( $this->is_ajax ) {
			status_header( $this->status );
			
			if ( $this->status == 200 ) {
				wp_send_json_success( $this->data );
			} else {
				wp_send_json_error( $this->errors );
			}
			
			die();
		} else {
			
			if ( $this->status == 200 ) {
				$GLOBALS[ self::ACTION_BASE_NAME ]['success'] = true;
			} else {
				$GLOBALS[ self::ACTION_BASE_NAME ]['status'] = $this->status;
				$GLOBALS[ self::ACTION_BASE_NAME ]['errors'] = $this->errors;
			}
		}
	}
}
