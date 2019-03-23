<?php
/**
 * Created by PhpStorm.
 * User: cyrill.bolliger
 * Date: 2019-03-22
 * Time: 19:11
 */

namespace SUPT;


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
	 * @param \WP_Post $post
	 *
	 * @throws \Exception
	 */
	public function __construct( $id, $post = null ) {
		$this->id = $id;

		if ( get_post_type( $id ) !== FormType::MODEL_NAME ) {
			throw new \Exception( "Invalid form id ($id)." );
		}

		if ( $post ) {
			$this->post = $post;
		}
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * The forms columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = [];

		foreach ( $this->get_fields() as $key => $field ) {
			$columns[ $key ] = $field['form_input_label'];
		}

		return $columns;
	}

	/**
	 * The form fields
	 *
	 * @return array
	 */
	public function get_fields() {
		if ( empty( $this->fields ) ) {
			foreach ( get_field( 'form_fields', $this->id ) as $field ) {
				$this->fields[ $field['slug'] ] = $field;
			}
		}

		return $this->fields;
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
				} catch ( \Exception $e ) {
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
		return $this->get_mail_settings( self::CONFIRMATION )['form_mail_template'];
	}

	/**
	 * Cached confirmation or notification mail settings
	 *
	 * @param string $which self::CONFIRMATION or self::NOTIFICATION
	 *
	 * @return null|array
	 */
	private function get_mail_settings( $which ) {
		if ( ! $this->{$which . '_settings'} ) {
			$this->{$which . '_settings'} = get_field( "form_{$which}_mail", $this->id );
		}

		return $this->{$which . '_settings'};
	}

	/**
	 * The notification mail template
	 *
	 * @return string
	 */
	public function get_notification_template() {
		return $this->get_mail_settings( self::NOTIFICATION )['form_mail_template'];
	}

	/**
	 * The confirmation mail subject
	 *
	 * @return string
	 */
	public function get_confirmation_subject() {
		return $this->get_mail_settings( self::CONFIRMATION )['form_mail_subject'];
	}

	/**
	 * The notification mail subject
	 *
	 * @return string
	 */
	public function get_notification_subject() {
		return $this->get_mail_settings( self::NOTIFICATION )['form_mail_subject'];
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
