<?php


namespace SUPT;


use Exception;
use WP_Error;

class Util {
	/**
	 * Return domain of current blog
	 */
	public static function get_domain() {
		$url = get_site_url();
		preg_match( "/^https?:\/\/(www.)?([^\/?:]*)/", $url, $matches );
		if ( $matches && is_array( $matches ) ) {
			return $matches[ count( $matches ) - 1 ];
		}

		return new WP_Error( 'cant-get-domain', 'The domain could not be parsed from the site url', $url );
	}

	/**
	 * Notify the site admin about the error from a static context
	 *
	 * @param string $action
	 * @param mixed $data
	 * @param Exception $exception
	 * @param string $form_name
	 */
	public static function report_form_error( $action, $data, $exception, $form_name ) {
		$domain = Util::get_domain();

		if ( is_string( $data ) ) {
			$data_str = $data;
		} else {
			$data_str = print_r( $data, true );
		}

		$error_msg = $exception->__toString();

		$subject = sprintf(
			__( 'ERROR on form submission: %s - %s', THEME_DOMAIN ),
			$form_name,
			$domain
		);
		$message = sprintf(
			__(
				"Hi Admin of %s\n\n" .
				"There was a problem submitting the form: %s\n" .
				"The following action failed: %s\n\n" .
				"The data:\n%s\n\n" .
				"The error:\n%s",
				THEME_DOMAIN
			),
			$domain,
			$form_name,
			$action,
			$data_str,
			$error_msg
		);

		self::send_mail_to_admin($subject, $message);
	}

	/**
	 * Send mail to blog admin
	 *
	 * @param string $subject
	 * @param string $body
	 */
	public static function send_mail_to_admin($subject, $body) {
		$to      = get_bloginfo( 'admin_email' );
		wp_mail( $to, $subject, $body );
	}

	/**
	 * Add new cron job, if not yet scheduled
	 *
	 * @param string $hook Action hook to execute when event is run.
	 * @param int $start Unix timestamp (UTC) for when to run the event first.
	 * @param string $recurrence How often the event should recur.
	 */
	public static function add_cron( $hook, $start, $recurrence ) {
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event(
				$start,
				$recurrence,
				$hook );
		}
	}

	/**
	 * Remove cron job
	 *
	 * @param string $hook
	 */
	public static function remove_cron( $hook ) {
		$timestamp = wp_next_scheduled( $hook );
		wp_unschedule_event( $timestamp, $hook );
	}

	/**
	 * Return the group id for possible duplicates, defined in the form settings
	 *
	 * @return int|false false if not defined
	 */
	public static function get_setting_duplicate_group_id() {
		$group_id = get_field( 'group_id_duplicates', 'option' );

		return empty( $group_id ) ? false : (int) $group_id;
	}

	/**
	 * Return the group id for new records, defined in the form settings
	 *
	 * @return int
	 */
	public static function get_setting_default_group_id() {
		return (int) get_field( 'group_id', 'option' );
	}
}
