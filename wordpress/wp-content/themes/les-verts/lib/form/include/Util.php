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

		$to      = get_bloginfo( 'admin_email' );
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

		wp_mail( $to, $subject, $message );
	}

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
}
