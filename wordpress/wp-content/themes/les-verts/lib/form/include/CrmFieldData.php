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
	 * Set to false to completely disable choice replacement
	 *
	 * @var bool
	 */
	private $replace;

	/**
	 * Constructor
	 *
	 * @param string $key the crm field key
	 * @param string $mode the insertion mode
	 * @param array $choices the possible choices (if mapped field)
	 * @param array $replacements the replacements for the choices
	 * @param null|array|string $value use array for multiselect fields only
	 * @param bool $replace skip choice replacement if false
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $key, $mode, $choices, $replacements, $value, $replace ) {
		$this->key = $key;
		$this->set_mode( $mode );
		$this->replace = $replace;

		if ( $this->should_map_values() ) {
			$this->set_substitution_map( $choices, $replacements );
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
			throw new InvalidArgumentException( "Invalid crm insertion mode '$mode' in field '{$this->key}'" );
		}

		$this->mode = $mode;
	}

	/**
	 * Check if the values of this field have to be mapped to the crm values
	 *
	 * @return bool
	 */
	private function should_map_values() {
		return $this->replace && in_array( $this->key, self::$mappedFields );
	}

	/**
	 * Populate the the choice substitution map
	 *
	 * @param array $search
	 * @param array $replace
	 *
	 * @throws InvalidArgumentException
	 */
	private function set_substitution_map( $search, $replace ) {
		if ( count( $search ) !== count( $replace ) ) {
			throw new InvalidArgumentException( 'The the webling choices must match the choices in the form.' );
		}

		foreach ( $search as $k => $s ) {
			$this->substitution_map[ $s ] = $replace[ $k ];
		}
	}

	/**
	 * Set the value, mapped if necessary
	 *
	 * @param array|string $value
	 */
	private function set_value( $value ) {
		if ( $this->should_map_values() ) {
			$this->value = is_array( $value ) ? array_map( [ $this, 'map_value' ], $value ) : $this->map_value( $value );
		} else {
			$this->value = $value;
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

	public function get_key(): string {
		return $this->key;
	}
}
