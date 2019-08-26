<?php


namespace SUPT;


class FormField {
	const TYPE_TEXT = 'text';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_CONFIRMATION = 'confirmation';
	const TYPE_SELECT = 'select';
	const TYPE_RADIO = 'radio';
	const TYPE_EMAIL = 'email';
	const TYPE_NUMBER = 'number';
	const TYPE_DATE = 'date';
	const TYPE_PHONE = 'tel';
	const TYPE_CRM_SUBSCRIPTION = 'crm_newsletter';
	const TYPE_CRM_GREETING = 'crm_greeting';

	const CHOICE_TYPES = array(
		self::TYPE_SELECT,
		self::TYPE_RADIO,
		self::TYPE_CRM_GREETING,
		self::TYPE_CHECKBOX,
	);

	const CRM_EMAIL_FIELD = 'email1';
	const CRM_INTERESTS_FIELD = 'interests';
	const CRM_REQUEST_FIELD = 'request';

	const CRM_MULTISELECT_FIELDS = array(
		self::CRM_INTERESTS_FIELD,
		self::CRM_REQUEST_FIELD,
	);

	private $label;
	private $type;
	private $choices;
	private $required;
	private $slug;
	private $crm_field;
	private $crm_choice_map;
	private $crm_insertion_mode;
	private $crm_hidden;
	private $crm_value;

	public function __construct( $config ) {
		$this->label     = $config['form_input_label'];
		$this->type      = $config['form_input_type'];
		$this->required  = $config['form_input_required'];
		$this->slug      = $config['slug'];
		$this->crm_field = $config['crm_field'];

		if ( $this->is_choice_type() ) {
			$this->choices = $this->split_choices( $config['form_input_choices'] );
		}

		if ( $this->has_crm_config() ) {
			$this->crm_insertion_mode = $config['insertion_mode'];
			$this->crm_hidden         = $config['hidden_field'];

			if ( $this->is_choice_type() && $this->type !== self::TYPE_CRM_GREETING ) {
				$this->crm_choice_map = $this->split_choices( $config['choice_map'] );
			}

			if ( $this->has_fixed_crm_value() ) {
				if ( in_array( $this->crm_field, self::CRM_MULTISELECT_FIELDS ) ) {
					$this->crm_value = $this->split_choices( $config['hidden_field_choices'] );
				} else {
					$this->crm_value = $config['hidden_field_value'];
				}
			}
		}
	}

	private function is_choice_type() {
		return in_array( $this->type, self::CHOICE_TYPES );
	}

	/**
	 * Get array of choices from choices string
	 *
	 * @param $string
	 *
	 * @return array
	 */
	private function split_choices( $string ) {
		$choices = explode( "\n", trim( $string ) );

		$return = array();
		$i      = 0;
		foreach ( $choices as $choice ) {
			$choice = trim( $choice );

			if ( '' !== $choice ) {
				$return[ $this->slug . '-' . $i ] = $choice;
				$i ++;
			}
		}

		return $return;
	}

	public function has_crm_config() {
		return ! empty( $this->crm_field );
	}

	public function has_fixed_crm_value() {
		return true === $this->crm_hidden;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_insertion_mode() {
		return $this->crm_insertion_mode;
	}

	public function set_insertion_mode( $mode ) {
		$this->crm_insertion_mode = $mode;
	}

	public function is_checkbox() {
		return self::TYPE_CHECKBOX === $this->type;
	}

	public function is_crm_email() {
		return $this->has_crm_config() && self::CRM_EMAIL_FIELD === $this->crm_field;
	}

	public function is_crm_subscription() {
		return self::TYPE_CRM_SUBSCRIPTION === $this->type;
	}

	public function is_crm_greeting() {
		return self::TYPE_CRM_GREETING === $this->type;
	}

	public function is_email() {
		return self::TYPE_EMAIL === $this->type;
	}

	public function is_confirmation() {
		return self::TYPE_CONFIRMATION === $this->type;
	}

	public function get_fixed_crm_value() {
		return $this->crm_value;
	}

	public function get_choices() {
		return $this->choices;
	}

	public function get_crm_field() {
		return $this->crm_field;
	}

	public function get_crm_choice_map() {
		return $this->crm_choice_map;
	}

	/**
	 * Validate data according to type
	 *
	 * @param string|bool $data to validate
	 *
	 * @return bool
	 */
	public function validate( $data ) {
		if ( ! $this->required && '' === $data ) {
			return true;
		}

		$valid = false;

		switch ( $this->type ) {
			case self::TYPE_CHECKBOX:
			case self::TYPE_CONFIRMATION:
			case self::TYPE_CRM_SUBSCRIPTION:
				$valid = true;
				break;

			case self::TYPE_RADIO:
			case self::TYPE_SELECT:
			case self::TYPE_CRM_GREETING:
				if ( empty( $data ) && '0' !== $data ) {
					$valid = true;
					break;
				}

				$options = array();
				foreach ( $this->choices as $key => $val ) {
					$options[ $key ] = trim( preg_replace( '/[&<>"\']/', '', $val ) );
				}

				$valid = in_array( $data, $options );
				break;

			case self::TYPE_EMAIL:
				$valid = filter_var( $data, FILTER_VALIDATE_EMAIL );
				break;

			case self::TYPE_NUMBER:
				$valid = is_numeric( $data ) || '' === $data;
				break;

			case self::TYPE_TEXT:
			case self::TYPE_TEXTAREA:
				$valid = is_string( $data );
				break;

			case self::TYPE_PHONE:
				$valid = is_string( $data ) || is_numeric($data);
				break;

			case self::TYPE_DATE:
				$valid = '' === $data || (bool) $data;
		}

		if ( ! is_bool( $data ) && $this->required && empty( trim( $data ) ) ) {
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Basic sanitation.
	 *
	 * Note: Radio and select data stays unchanged.
	 *
	 * @param $data
	 *
	 * @return bool|string
	 */
	public function sanitize( $data ) {
		switch ( $this->type ) {
			case self::TYPE_CHECKBOX:
			case self::TYPE_CONFIRMATION:
			case self::TYPE_CRM_SUBSCRIPTION:
				return filter_var( $data, FILTER_VALIDATE_BOOLEAN );

			case self::TYPE_RADIO:
			case self::TYPE_SELECT:
			case self::TYPE_CRM_GREETING:
				return $data;

			case self::TYPE_PHONE:
				$allowed = '\d\+ -\\\(\)';

				return preg_replace( "/[^${allowed}]/", '', $data );

			case self::TYPE_EMAIL:
				return filter_var( $data, FILTER_SANITIZE_EMAIL );

			case self::TYPE_NUMBER:
				return filter_var( $data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

			case self::TYPE_DATE:
				return $this->parse_date( $data );

			default:
				return strip_tags( $data );
		}
	}

	/**
	 * Return date in YYYY-MM-DD format from given string,
	 * '' if string is empty, false if date couldn't be parsed.
	 *
	 * @param string $string
	 *
	 * @return false|string
	 */
	private function parse_date( $string ) {
		$string = trim($string);

		if (empty($string)){
			return '';
		}

		$tmp1 = date_parse( $string );
		$tmp2 = date_parse_from_format( 'd.m.y', $string );
		$tmp3 = date_parse_from_format( 'Y-m-d', $string );

		if ( $tmp1['error_count'] && $tmp2['error_count'] && $tmp3['error_count'] ) {
			return false;
		}

		if ( ! $tmp1['error_count'] && $tmp1['year'] ) {
			$date = $tmp1;
		} elseif ( ! $tmp2['error_count'] ) {
			$date = $tmp2;
		} else {
			$date = $tmp3;
		}

		$datestring = $date['year'] . "-" . $date['month'] . "-" . $date['day'];

		return date( 'Y-m-d', strtotime( $datestring ) );
	}
}
