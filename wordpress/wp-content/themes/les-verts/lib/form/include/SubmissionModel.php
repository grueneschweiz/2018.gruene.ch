<?php

namespace SUPT;


use BadMethodCallException;
use Exception;
use InvalidArgumentException;

class SubmissionModel {
	const META_KEY = '_meta_';
	const EMAIL_KEY = 'email';
	const TIMESTAMP_KEY = 'timestamp';
	const FORM_KEY = 'form_id';
	const DESCENDANT_KEY = 'descendant_id';
	const PREDECESSOR_KEY = 'predecessor_id';
	const REFERER_KEY = 'referer_url';

	const MAX_LABEL_LEN = 50;

	const DIRECTION_BOTH = 0;
	const DIRECTION_PREDECESSOR = 1;
	const DIRECTION_DESCENDANT = 2;

	private $id;
	private $meta = [];
	private $data = [];

	/**
	 * SubmissionModel constructor.
	 *
	 * @param int|null $id
	 * @param array $data
	 *
	 * @throws InvalidArgumentException
	 * @throws Exception
	 */
	public function __construct( $id = null, $data = null ) {
		$this->id = $id;

		if ( is_null( $data ) ) {
			$data = $this->fetch_data_by_id( $id );

			if ( ! $data ) {
				throw new Exception( 'No submission with this id.' );
			}
		}

		$this->unpack( $data );
	}

	/**
	 * Fetch the form data form the database
	 *
	 * @param int $id
	 *
	 * @return array|false
	 */
	private function fetch_data_by_id( $id ) {
		global $wpdb;

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->postmeta WHERE meta_id = %d", $id
			) );

		if ( empty( $data ) ) {
			return false;
		}

		return (array) maybe_unserialize( $data->meta_value );
	}

	/**
	 * Split data and meta data and place the in the corresponding fields.
	 *
	 * @param $data
	 */
	private function unpack( $data ) {
		$data = (array) $data;

		if ( ! array_key_exists( self::META_KEY, $data ) ) {
			throw new InvalidArgumentException( 'Invalid submission data.' );
		}

		$this->meta = $data[ self::META_KEY ];
		unset( $data[ self::META_KEY ] );

		$this->data = $data;
	}

	/**
	 * Access the data using getters and setters
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return bool|mixed|null
	 */
	public function __call( $name, $arguments ) {
		if ( 0 === strpos( $name, 'get_pretty_' ) ) {
			return $this->get_pretty( substr( $name, 11 ) );
		}

		if ( 0 === strpos( $name, 'get_' ) ) {
			return $this->get( substr( $name, 4 ) );
		}

		if ( 0 === strpos( $name, 'set_' ) ) {
			$this->set( substr( $name, 4 ), $arguments[0] );

			return true;
		}

		throw new BadMethodCallException( 'Method does not exist.' );
	}

	/**
	 * Getter method for data
	 *
	 * @param string $property
	 *
	 * @return mixed|null
	 */
	private function get( $property ) {
		if ( array_key_exists( $property, $this->data ) ) {
			return $this->data[ $property ];
		}

		return null;
	}

	/**
	 * Getter method for data with label
	 *
	 * @param string $property
	 *
	 * @return array|null
	 */
	private function get_pretty( $property ) {
		// prepare value
		if ( array_key_exists( $property, $this->data ) ) {
			$value = $this->data[ $property ];
		} else {
			$value = '';
		}

		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		// prepare label
		try {
			$label = $this->meta_get_form()->get_column( $property );
		} catch ( Exception $e ) {
			$label = null;
		}

		if ( empty( $label ) ) {
			$label = sprintf( _x( 'Old: %s', 'Form element. Ex: Name', THEME_DOMAIN ), $property );
		}

		$label = wp_trim_words( $label, 4, '...' );

		if ( strlen( $label ) > self::MAX_LABEL_LEN ) {
			$label = substr( rtrim( $label, '.' ), 0, self::MAX_LABEL_LEN - 3 ) . '...';
		}

		return [
			'label' => $label,
			'value' => $value
		];
	}

	/**
	 * Setter method for data
	 *
	 * @param $property
	 * @param $value
	 */
	private function set( $property, $value ) {
		$this->data[ $property ] = $value;
	}

	/**
	 * The fields
	 *
	 * @return array
	 */
	public function column_keys() {
		return array_keys( $this->data );
	}

	/**
	 * Timestamp of form submission
	 *
	 * @return string
	 */
	public function meta_get_timestamp() {
		return $this->meta[ self::TIMESTAMP_KEY ];
	}

	/**
	 * The email address of the submitter
	 *
	 * @return string|false
	 */
	public function meta_get_email() {
		return $this->meta[ self::EMAIL_KEY ];
	}

	/**
	 * Return the first submitter email found in any linked submission
	 *
	 * If multiple submission have an email address, the priority is as follows:
	 * 1. this form
	 * 2. the descendants (closest descendant first)
	 * 3. the predecessors (closest predecessor first)
	 *
	 * @return false|string
	 */
	public function meta_get_linked_email() {
		$email = $this->meta_get_email();
		if ( $email ) {
			return $email;
		}

		$descendant = $this->meta_get_descendant();
		if ( $descendant ) {
			$email = $descendant->meta_get_linked_email();
			if ( $email ) {
				return $email;
			}
		}

		$predecessor = $this->meta_get_predecessor();
		if ( $predecessor ) {
			$email = $predecessor->meta_get_linked_email();
			if ( $email ) {
				return $email;
			}
		}

		return false;
	}

	/**
	 * The submission of the predecessor form or false if none
	 *
	 * @return bool|SubmissionModel
	 */
	public function meta_get_predecessor() {
		if ( 0 >= $this->meta[ self::PREDECESSOR_KEY ] ) {
			return false;
		}

		try {
			return new SubmissionModel( $this->meta[ self::PREDECESSOR_KEY ] );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Set the predecessor submission
	 *
	 * @param int|SubmissionModel $predecessor the predecessor id or submission
	 */
	public function meta_set_predecessor( $predecessor ) {
		if ( $predecessor instanceof SubmissionModel ) {
			$this->meta[ self::PREDECESSOR_KEY ] = $predecessor->meta_get_id();
		} else {
			$this->meta[ self::PREDECESSOR_KEY ] = (int) $predecessor;
		}
	}

	/**
	 * The post meta id
	 *
	 * @return int
	 */
	public function meta_get_id() {
		return $this->id;
	}

	/**
	 * The submission of the descendant or false if none
	 *
	 * @return bool|SubmissionModel
	 */
	public function meta_get_descendant() {
		if ( 0 >= $this->meta[ self::DESCENDANT_KEY ] ) {
			return false;
		}

		try {
			return new SubmissionModel( $this->meta[ self::DESCENDANT_KEY ] );
		} catch ( Exception $e ) {
			return false;
		}

	}

	/**
	 * Set the descendant submission
	 *
	 * @param int|SubmissionModel $descendant the descendant id or submission
	 */
	public function meta_set_descendant( $descendant ) {
		if ( $descendant instanceof SubmissionModel ) {
			$this->meta[ self::DESCENDANT_KEY ] = $descendant->meta_get_id();
		} else {
			$this->meta[ self::DESCENDANT_KEY ] = (int) $descendant;
		}
	}

	/**
	 * The url of the page, where the form was submitted
	 *
	 * @return string
	 */
	public function meta_get_referer() {
		return $this->meta[ self::REFERER_KEY ] ?? '';
	}

	/**
	 * Delete the current submission
	 *
	 * @return bool
	 */
	public function delete() {
		global $wpdb;

		return (bool) $wpdb->delete(
			$wpdb->postmeta,
			[ 'meta_id' => $this->id ],
			[ '%d' ]
		);
	}

	/**
	 * Persist submission
	 *
	 * @return bool
	 */
	public function save() {
		if ( $this->meta_get_id() ) {
			$success = $this->update();
		} else {
			$success = $this->insert();
		}

		return $success;
	}

	/**
	 * Update the current submission in the database
	 *
	 * @return bool
	 */
	private function update() {
		global $wpdb;

		$postmeta = $wpdb->get_row( "SELECT * FROM {$wpdb->postmeta} WHERE meta_id = {$this->id}" );

		if ( $postmeta ) {
			$orig = maybe_unserialize( $postmeta->meta_value );
			$new  = $this->get_packed();

			$success = update_post_meta( $postmeta->post_id, FormType::MODEL_NAME, $new, $orig );
		} else {
			$success = false;
		}

		return $success;
	}

	/**
	 * Merge all data into one array to be ready to save the data
	 *
	 * @return array
	 */
	private function get_packed() {
		$data[ self::META_KEY ] = $this->meta;

		return array_merge( $data, $this->data );
	}

	/**
	 * Add current submission to database
	 *
	 * @return bool
	 */
	private function insert() {
		$data = $this->get_packed();

		try {
			$form_id = $this->meta_get_form()->get_id();
		} catch ( Exception $e ) {
			return false;
		}

		$return = add_post_meta( $form_id, FormType::MODEL_NAME, $data );

		if ( $return ) {
			$this->id = $return;
			$return   = true;
		}

		return $return;
	}

	/**
	 * The form that was used to submit this data
	 *
	 * @return FormModel
	 *
	 * @throws Exception
	 */
	public function meta_get_form() {
		return new FormModel( (int) $this->meta[ self::FORM_KEY ] );
	}

	/**
	 * Return the submission data as array
	 *
	 * @return array
	 */
	public function as_array() {
		return $this->data;
	}

	/**
	 * Return the submission with all linked data as array.
	 *
	 * If multiple submissions use the same key, the descendants value is returned.
	 *
	 * @param int $direction internal use
	 *
	 * @return array
	 */
	public function as_array_with_linked_data( $direction = self::DIRECTION_BOTH ) {
		$data = $this->as_array();

		if ( $direction !== self::DIRECTION_DESCENDANT ) {
			$predecessor = $this->meta_get_predecessor();

			if ( $predecessor ) {
				$data = array_merge( $predecessor->as_array_with_linked_data( self::DIRECTION_PREDECESSOR ), $data );
			}
		}

		if ( $direction !== self::DIRECTION_PREDECESSOR ) {
			$descendant = $this->meta_get_descendant();

			if ( $descendant ) {
				$data = array_merge( $data, $descendant->as_array_with_linked_data( self::DIRECTION_DESCENDANT ) );
			}
		}

		return $data;
	}

	/**
	 * Delete this submission with all its linked submissions
	 *
	 * @param int $direction internal use
	 *
	 * @return bool indicates ONLY if the root element was deleted.
	 *              we explicitly don't want to signal a fail, if a
	 *              linked element wasn't existent.
	 */
	public function delete_including_linked_submissions( $direction = self::DIRECTION_BOTH ) {
		if ( $direction !== self::DIRECTION_DESCENDANT ) {
			$predecessor = $this->meta_get_predecessor();

			if ( $predecessor ) {
				$predecessor->delete_including_linked_submissions( self::DIRECTION_PREDECESSOR );
			}
		}

		if ( $direction !== self::DIRECTION_PREDECESSOR ) {
			$descendant = $this->meta_get_descendant();

			if ( $descendant ) {
				$descendant->delete_including_linked_submissions( self::DIRECTION_DESCENDANT );
			}
		}

		return $this->delete();
	}
}
