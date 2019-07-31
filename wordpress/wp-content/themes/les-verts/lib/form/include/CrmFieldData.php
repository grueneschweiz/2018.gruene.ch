<?php


namespace SUPT;


use InvalidArgumentException;

class CrmFieldData {
	const MODE_REPLACE = 'replace';
	const MODE_REPLACE_EMPTY = 'replaceEmpty';
	const MODE_APPEND = 'append';
	const MODE_ADD_IF_NEW = 'addIfNew';

	private static $mappedFields = [
		'recordCategory',
		'salutationInformal',
		'interests',
		'request'
	];

	/**
	 * The field key
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The field data
	 *
	 * @var string|array array in case of a multi select field
	 */
	private $value;

	/**
	 * The insertion mode
	 *
	 * @var string
	 */
	private $mode;

	/**
	 * The key holds the form choice, the value the crm choice
	 *
	 * @var array
	 */
	private $substitution_map;

	/**
	 * CRMFieldData constructor.
	 *
	 * @param array $field as returned by the form model
	 * @param null|array|string $value use array for multiselect fields only
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $field, $value ) {
		$this->key = $field['crm_field'];
		$this->set_mode( $field['insertion_mode'] );

		if ( $this->is_mapped_field() ) {
			$this->set_substitution_map( $field['form_input_choices'], $field['choice_map'] );
		}

		$this->set_value( $value );
	}

	/**
	 * Set the insertion mode
	 *
	 * @param string $mode
	 *
	 * @throws InvalidArgumentException
	 */
	private function set_mode( $mode ) {
		if ( ! in_array( $mode, [
			self::MODE_APPEND,
			self::MODE_REPLACE,
			self::MODE_REPLACE_EMPTY,
			self::MODE_ADD_IF_NEW
		] ) ) {
			throw new InvalidArgumentException( 'Invalid crm insertion mode' );
		}

		$this->mode = $mode;
	}

	/**
	 * Set the value, mapped if necessary
	 *
	 * @param array|string $value
	 */
	private function set_value( $value ) {
		if ( $this->is_mapped_field() ) {
			$this->value = is_array( $value ) ? array_map( [ $this, 'map_value' ], $value ) : $this->map_value( $value );
		} else {
			$this->value = $value;
		}
	}

	/**
	 * Tells if the values of this field have to be mapped to the crm values
	 *
	 * @return bool
	 */
	private function is_mapped_field() {
		return in_array( $this->key, self::$mappedFields );
	}

	/**
	 * Populate the the choice substitution map
	 *
	 * @param string $search
	 * @param string $replace
	 *
	 * @throws InvalidArgumentException
	 */
	private function set_substitution_map( $search, $replace ) {
		$search  = FormModel::split_choices( $search );
		$replace = FormModel::split_choices( $replace );

		if ( count( $search ) !== count( $replace ) ) {
			throw new InvalidArgumentException( 'The the webling choices must match the choices in the form.' );
		}

		foreach ( $search as $k => $s ) {
			$this->substitution_map[ $s ] = $replace[ $k ];
		}
	}

	/**
	 * Get crm value from from value
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function map_value( $value ) {
		return $this->substitution_map[ $value ];
	}

	public function get_value() {
		return $this->value;
	}

	public function get_mode() {
		return $this->mode;
	}
}