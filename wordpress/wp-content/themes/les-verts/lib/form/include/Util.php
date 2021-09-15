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
	 * @param FormModel|null $form
	 */
	public static function report_form_error( string $action, $data, Exception $exception, $form ) {
		$domain = Util::get_domain();

		if ( is_string( $data ) ) {
			$data_str = $data;
		} else {
			$data_str = print_r( $data, true );
		}

		$error_msg = $exception->__toString();

		$form_name = $form ? $form->get_title() : 'DELETED FORM';
		$form_link = $form ? $form->get_link() : 'DELETED FORM';

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
				"The link to the form: %s\n\n" .
				"The data:\n%s\n\n" .
				"The error:\n%s",
				THEME_DOMAIN
			),
			$domain,
			$form_name,
			$action,
			$form_link,
			$data_str,
			$error_msg
		);

		self::send_mail_to_admin( $subject, $message );
	}

	/**
	 * Send mail to blog admin
	 *
	 * @param string $subject
	 * @param string $body
	 */
	public static function send_mail_to_admin( $subject, $body ) {
		$to = get_bloginfo( 'admin_email' );
		wp_mail( $to, $subject, $body );
	}

	/**
	 * Add new cron job, if not yet scheduled. Else reschedule.
	 * Notify site admin by email, if scheduling failed.
	 *
	 * @param string $hook Action hook to execute when event is run.
	 * @param int $start Unix timestamp (UTC) for when to run the event first.
	 * @param string $recurrence How often the event should recur.
	 */
	public static function add_cron( $hook, $start, $recurrence ) {
		if ( false !== wp_next_scheduled( $hook ) ) {
			self::remove_cron( $hook );
		}

		$scheduled = wp_schedule_event(
			$start,
			$recurrence,
			$hook
		);

		if ( ! $scheduled ) {
			$domain  = self::get_domain();
			$subject = sprintf(
				__( 'ERROR scheduling wp_cron: %s', THEME_DOMAIN ),
				$domain
			);
			$message = sprintf(
				__(
					"Hi Admin\n\n" .
					"The wp_cron job %s could not be scheduled on %s.\n" .
					"Fix this soon, else the form submission won't work as expected.\n" .
					"There is no further info about the error, as WordPress only returns 'false' :(",
					THEME_DOMAIN
				),
				$hook,
				$domain
			);
			self::send_mail_to_admin(
				$subject,
				$message
			);
		}
	}

	/**
	 * Remove cron job
	 *
	 * @param string $hook
	 */
	public static function remove_cron( $hook ) {
		wp_unschedule_hook( $hook );
	}

	/**
	 * Unschedule any cron jobs from the mailer and the crm saver
	 */
	public static function remove_all_crons() {
		require_once 'Mailer.php';
		require_once 'CrmSaver.php';

		self::remove_cron( Mailer::CRON_HOOK_MAIL_SEND );
		self::remove_cron( CrmSaver::CRON_HOOK_CRM_SAVE );
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
