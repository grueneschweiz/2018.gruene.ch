<?php


namespace SUPT;

use Exception;

require_once __DIR__ . '/CrmFieldData.php';
require_once __DIR__ . '/CrmDao.php';
require_once __DIR__ . '/Util.php';

class CrmSaver {

	/**
	 * @param array $data
	 * @param CrmDao $dao
	 * @throws Exception on crm save error
	 *
	 * @return bool true on success else false
	 */
	public static function save_to_crm( array $data, CrmDao $dao, $match, $new_contacts_to_crm ) {
		$crm_saver = new self();
		$data = $crm_saver->add_group( $data, $crm_saver->determine_group( $match['status'] ) );

		if ( $match['status'] === CrmDao::MATCH_MULTIPLE ) {
			$main_id = $dao->main( $match['matches'][0]['id'] )['id'];
		}

		$crm_id = $dao->save( $data, $main_id ?? null );
		return $crm_id !== false;
	}

	/**
	 * Adds the given group to the given data in 'add if new' mode
	 *
	 * @param array $data
	 * @param int $group_id
	 *
	 * @return array
	 * @throws Exception
	 */
	private function add_group( array $data, int $group_id ): array {
		$data['groups'] = new CrmFieldData(
			'groups',
			CrmFieldData::MODE_ADD_IF_NEW,
			array(),
			array(),
			$group_id,
			false
		);

		return $data;
	}

	/**
	 * Return the group the record should be added to, considering
	 * if a group id for possible duplicates was defined.
	 *
	 * @param string $match any of the CrmDao::MATCH_* constants
	 *
	 * @return int the group id
	 */
	private static function determine_group( string $match ): int {
		$duplicate_group_id = Util::get_setting_duplicate_group_id();
		$default_group_id   = Util::get_setting_default_group_id();

		if ( ! $duplicate_group_id ) {
			return $default_group_id;
		}

		if ( in_array( $match, array( CrmDao::MATCH_NONE, CrmDao::MATCH_EXACT, CrmDao::MATCH_MULTIPLE ), true ) ) {
			return $default_group_id;
		}

		return $duplicate_group_id;
	}

	public static function send_permanent_error_notification( $submission, $err_message ) {
		$domain = Util::get_domain();

		$subject = sprintf(
			__( '%s: PERMANENT ERROR saving form data to crm', THEME_DOMAIN ),
			$domain
		);

		$message = sprintf(
			__(
				"Hi Admin of %s\n\n" .
				"There was a PERMANENT ERROR saving the following data to the crm:\n%s\n\n" .
				"The data was removed from the saving queue, so YOU MUST ADD IT MANUALLY. " .
				"Please correct the form configuration, to prevent this error in the future. " .
				"More details in the error message below.\n\n" .
				"Have a nice day.\n" .
				"Your Website - %s\n\n" .
				"Error message:\n%s",
				THEME_DOMAIN
			),
			$domain,
			$submission,
			$domain,
			$err_message
		);

		Util::send_mail_to_admin( $subject, $message );
	}
}
