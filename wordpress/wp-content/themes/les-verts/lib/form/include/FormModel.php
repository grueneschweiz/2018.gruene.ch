<?php
/**
 * Created by PhpStorm.
 * User: cyrill.bolliger
 * Date: 2019-03-22
 * Time: 19:11
 */

namespace SUPT;


use Exception;
use WP_Post;

class FormModel {
	const CONFIRMATION = 'confirmation';
	const NOTIFICATION = 'notification';

	private $id;
	private $fields = [];
	private $submissions = [];
	private $post;
	private $redirect;
	private $sender_name;
	private $has_confirmation;
	private $has_notification;
	private $reply_to;
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$confirmation_settings;
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$notification_settings;

	/**
	 * FormModel constructor.
	 *
	 * @param int $id the form id
	 * @param WP_Post $post
	 *
	 * @throws Exception
	 */
	public function __construct( $id, $post = null ) {
		$this->id = $id;

		if ( get_post_type( $id ) !== FormType::MODEL_NAME ) {
			throw new Exception( "Invalid form id ($id)." );
		}

		if ( $post ) {
			$this->post = $post;
		}
	}

	/**
	 * Get an instance of every form
	 *
	 * @return FormModel[]
	 */
	public static function get_forms() {
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => FormType::MODEL_NAME,
		);

		$forms = array();
		foreach ( get_posts( $args ) as $form ) {
			try {
				$forms[ $form->ID ] = new FormModel( $form->ID, $form );
			} catch ( Exception $e ) {
				// exception impossible in this special case:
				// we do only query for form type so it can't be not of type form
			}
		}

		return $forms;
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get single field
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function get_column( $key ) {
		$columns = $this->get_columns();

		if ( isset( $columns[ $key ] ) ) {
			return $columns[ $key ];
		}

		return null;
	}

	/**
	 * The forms columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = [];

		foreach ( $this->get_fields() as $key => $field ) {
			$columns[ $key ] = $field->get_label();
		}

		return $columns;
	}

	/**
	 * The form fields
	 *
	 * @return FormField[]
	 */
	public function get_fields() {
		if ( empty( $this->fields ) ) {
			require_once __DIR__ . '/FormField.php';

			foreach ( get_field( 'form_fields', $this->id ) as $field ) {
				$this->fields[ $field['slug'] ] = new FormField( $field );
			}
		}

		return $this->fields;
	}

	/**
	 * Get single field
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function get_field( $key ) {
		$fields = $this->get_fields();

		if ( isset( $fields[ $key ] ) ) {
			return $fields[ $key ];
		}

		return null;
	}

	/**
	 * The submissions
	 *
	 * @return SubmissionModel[]
	 */
	public function get_submissions() {
		require_once __DIR__ . '/SubmissionModel.php';

		if ( empty( $this->submissions ) ) {
			$submissions = $this->get_post_meta( $this->id, FormType::MODEL_NAME );

			foreach ( $submissions as $id => $submission ) {
				try {
					$this->submissions[ $id ] = new SubmissionModel( $id, $submission );
				} catch ( Exception $e ) {
					// yes, in this case we want to hide this exception
					// (malformed submission)
				}
			}
		}

		return $this->submissions;
	}

	/**
	 * Retrieve metadata with id for the specified object.
	 *
	 * @param int $post_id ID of the object metadata is for
	 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
	 *                        the specified object.
	 *
	 * @return array
	 */
	private function get_post_meta( $post_id, $meta_key = '' ) {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$post_id, $meta_key ) );

		if ( empty( $results ) ) {
			return [];
		}

		$data = [];
		foreach ( $results as $result ) {
			$id          = $result->meta_id;
			$data[ $id ] = (array) maybe_unserialize( $result->meta_value );
		}

		return $data;
	}

	/**
	 * Retrieve total number of form submissions
	 *
	 * @return int
	 */
	public function get_submission_count() {
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
				$this->id,
				FormType::MODEL_NAME
			)
		);

		return $count ? $count : 0;
	}

	/**
	 * The form name
	 *
	 * @return string
	 */
	public function get_title() {
		if ( ! $this->post ) {
			$this->post = get_post( $this->id );
		}

		return $this->post->post_title;
	}

	/**
	 * The form slug
	 *
	 * @return string
	 */
	public function get_slug() {
		if ( ! $this->post ) {
			$this->post = get_post( $this->id );
		}

		return $this->post->post_name;
	}

	/**
	 * Get url to redirect to after success.
	 *
	 * Return empty string if no redirect is configured.
	 *
	 * @return string
	 */
	public function get_redirect_url() {
		if ( $this->redirect ) {
			return $this->redirect;
		}

		$form_success = get_field( 'form_success', $this->id );

		if ( 'inline' === $form_success['after_success_action'] ) {
			$this->redirect = '';

			return $this->redirect;
		}

		if ( empty( $form_success['redirect'] ) ) {
			$this->redirect = '';

			return $this->redirect;
		}

		$this->redirect = get_permalink( $form_success['redirect'] );

		return $this->redirect;
	}

	/**
	 * The name of the sender (for notification and confirmation email)
	 *
	 * @return string
	 */
	public function get_sender_name() {
		if ( $this->sender_name ) {
			return $this->sender_name;
		}

		$this->sender_name = get_field( 'form_sender_name', $this->id );

		return $this->sender_name;
	}

	/**
	 * The reply to address
	 */
	public function get_reply_to_address() {
		if ( ! $this->reply_to ) {
			$this->reply_to = get_field( 'form_reply_to', $this->id );
		}

		return $this->reply_to;
	}

	/**
	 * Should a confirmation mail be sent?
	 *
	 * @return bool
	 */
	public function has_confirmation() {
		if ( is_null( $this->has_confirmation ) ) {
			$this->has_confirmation = get_field( 'form_send_confirmation', $this->id );
		}

		return $this->has_confirmation;
	}

	/**
	 * The confirmation mail template
	 *
	 * @return string
	 */
	public function get_confirmation_template() {
		return $this->get_mail_template( self::CONFIRMATION );
	}

	/**
	 * The mailtemplate with only wpautop() applied
	 *
	 * @param string $which self::CONFIRMATION or self::NOTIFICATION
	 *
	 * @return string
	 */
	private function get_mail_template( $which ) {
		// we need this unformatted, else complex twig templates won't work
		$parent_key = self::NOTIFICATION === $which ? 'field_5bf2bdfc61f42' : 'field_5bf2bc9461f40';

		$raw_template = $this->get_mail_settings( $which, false )["{$parent_key}_field_5be2e5d83fdf7"];

		return wpautop( $raw_template );
	}

	/**
	 * Cached confirmation or notification mail settings
	 *
	 * @param string $which self::CONFIRMATION or self::NOTIFICATION
	 * @param bool $formatted see get_field()
	 *
	 * @return null|array
	 */
	private function get_mail_settings( $which, $formatted = true ) {
		if ( ! $this->{$which . '_settings'}[ $formatted ] ) {
			$this->{$which . '_settings'}[ $formatted ] = get_field( "form_{$which}_mail", $this->id, $formatted );
		}

		return $this->{$which . '_settings'}[ $formatted ];
	}

	/**
	 * The notification mail template
	 *
	 * @return string
	 */
	public function get_notification_template() {
		return $this->get_mail_template( self::NOTIFICATION );
	}

	/**
	 * The confirmation mail subject
	 *
	 * @return string
	 */
	public function get_confirmation_subject() {
		return $this->get_mail_settings( self::CONFIRMATION, false )['field_5bf2bc9461f40_field_5be2e5b13fdf6'];
	}

	/**
	 * The notification mail subject
	 *
	 * @return string
	 */
	public function get_notification_subject() {
		return $this->get_mail_settings( self::NOTIFICATION, false )['field_5bf2bdfc61f42_field_5be2e5b13fdf6'];
	}

	/**
	 * The confirmation mail destination
	 *
	 * @return string
	 */
	public function get_notification_destination() {
		return $this->get_mail_settings( self::NOTIFICATION )['form_confirmation_destination'];
	}

	/**
	 * Should a notification mail be sent?
	 *
	 * @return bool
	 */
	public function has_notification() {
		if ( is_null( $this->has_notification ) ) {
			$this->has_notification = get_field( 'form_send_notification', $this->id );
		}

		return $this->has_notification;
	}
}
