<?php


namespace SUPT;

use Exception;

require_once __DIR__ . '/CrmFieldData.php';
require_once __DIR__ . '/QueueDao.php';
require_once __DIR__ . '/CrmQueueItem.php';

class CrmSaver {
	const QUEUE_KEY = 'crm_save';
	const CRON_HOOK_CRM_SAVE = 'supt_form_save_to_crm';
	const CRON_CRM_SAVE_RETRY_INTERVAL = 'hourly';
	const MAX_SAVE_ATTEMPTS = 3;
	const MIN_ATTEMPT_TIMEOUT = 3600; // seconds

	const CRM_SUBSCRIPTION_VALUE = 'yes';
	const CRM_GREETINGS_INFORMAL = array(
		'hallo'  => 'nD',
		'liebe'  => 'fD',
		'lieber' => 'mD',
		'salut'  => 'nF',
		'chère'  => 'fF',
		'cher'   => 'mF',
	);
	const CRM_GREETINGS_INFORMAL_TO_FORMAL = array(
		'liebe'  => 'fD',
		'lieber' => 'mD',
		'chère'  => 'fF',
		'cher'   => 'mF'
	);
	const CRM_GREETINGS_INFORMAL_TO_GENDER = array(
		'hallo'  => 'n',
		'liebe'  => 'f',
		'lieber' => 'm',
		'salut'  => 'n',
		'chère'  => 'f',
		'cher'   => 'm'
	);

	/**
	 * @var SubmissionModel
	 */
	private $submission;

	/**
	 * @var FormModel
	 */
	private $form;

	/**
	 * @var CrmFieldData[]
	 */
	private $crm_data = array();

	/**
	 * @var QueueDao
	 */
	private $queue;

	/**
	 * CrmSaver constructor.
	 *
	 * @param int $submission_id
	 *
	 * @throws Exception
	 */
	public function __construct( $submission_id ) {
		require_once __DIR__ . '/SubmissionModel.php';

		$this->submission = new SubmissionModel( $submission_id );
		$this->form       = $this->submission->meta_get_form();
		$this->queue      = self::get_queue();
	}

	/**
	 * Get the save queue
	 *
	 * @return QueueDao
	 */
	public static function get_queue(): QueueDao {
		return new QueueDao( self::QUEUE_KEY );
	}

	/**
	 * Save the form submissions to the crm
	 *
	 * Called by the WordPress cron job
	 */
	public static function save_to_crm( bool $force = false ) {
		require_once __DIR__ . '/CrmDao.php';

		$queue = self::get_queue();

		// bail early, if the crm api isn't configured
		if ( ! CrmDao::has_api_url() ) {
			Util::remove_cron( self::CRON_HOOK_CRM_SAVE );

			return;
		}

		try {
			$crm_dao = new CrmDao();
		} catch ( Exception $e ) {
			$subject = "Error: permanent CRM authentication failure.";
			$message = $e->getMessage();
			Util::send_mail_to_admin( $subject, $message );

			return;
		}

		/** @var CrmQueueItem $item */
		foreach ( $queue->get_all() as $item ) {
			Util::debug_log( "submissionId={$item->get_submission_id()} msg=Start saving to CRM." );

			if ( ! $item->has_data() ) {
				Util::debug_log( "submissionId={$item->get_submission_id()} msg=No data. Discarding entry." );

				// remove item from queue
				try {
					$queue->filter( static function ( $q_item ) use ( $item ) {
						return $item->get_submission_id() !== $q_item->get_submission_id();
					} );
				} catch ( Exception $e ) {
					// do nothing, well remove it the next time
				}
			}

			// if we have exceeded the max attempts,
			// and we don't want to force save (=ignore max attempts)
			if ( ! $force && $item->get_attempts() >= self::MAX_SAVE_ATTEMPTS ) {
				Util::debug_log( "submissionId={$item->get_submission_id()} msg=Too many attempts. Skipping." );
				// skip
				continue;
			}

			// if not enough time has passed since the last attempt,
			// and we don't want to force save (=ignore max attempts)
			if ( ! $force
			     && $item->last_attempt_seconds_ago()
			     && $item->last_attempt_seconds_ago() < self::MIN_ATTEMPT_TIMEOUT
			) {
				Util::debug_log( "submissionId={$item->get_submission_id()} msg=Last attempt only {$item->last_attempt_seconds_ago()} seconds ago. Skipping." );
				// skip
				continue;
			}

			$item->add_attempt();
			$crm_id = self::save( $item, $crm_dao );

			// on error
			if ( ! $crm_id ) {
				Util::debug_log( "submissionId={$item->get_submission_id()} msg=Failed to save to CRM." );
				// keep in queue
				continue;
			}

			// on success: remove item from queue
			Util::debug_log( "submissionId={$item->get_submission_id()} msg=Saved successfully. CRM id: $crm_id" );

			try {
				$queue->filter( static function ( $q_item ) use ( $item ) {
					if ( $item->get_submission_id() === $q_item->get_submission_id() ) {
						Util::debug_log( "submissionId={$item->get_submission_id()} msg=Remove from queue." );

						return false; // remove from queue
					}

					return true;
				} );
			} catch ( Exception $e ) {
				// do nothing, we'll remove it the next time
			}
		}
	}

	/**
	 * @param CrmQueueItem $item
	 * @param CrmDao $dao
	 *
	 * @return false|mixed false on error else the crm id
	 */
	private static function save( CrmQueueItem $item, CrmDao $dao ) {
		$crm_id = false;
		$data   = $item->get_data();

		try {
			$match = $dao->match( $data );
			$data  = self::add_group( $data, self::determine_group( $match['status'] ) );

			if ( $match['status'] === CrmDao::MATCH_MULTIPLE ) {
				$main_id = $dao->main( $match['matches'][0]['id'] )['id'];
			}

			$crm_id = $dao->save( $data, $main_id ?? null );
		} catch ( Exception $e ) {
			Util::debug_log( "submissionId={$item->get_submission_id()} msg=Failed to save to CRM: {$e->getCode()} {$e->getMessage()}" );
			if ( self::is_non_permanent_error( $e->getCode() ) ) {
				try {
					$form = new FormModel( $item->get_form_id() );
				} catch ( Exception $e ) {
					$form = null;
				}
				Util::report_form_error( 'save to crm', $item, $e, $form );
			} else if ( $item->get_attempts() >= self::MAX_SAVE_ATTEMPTS ) {
				self::send_permanent_error_notification( $item, $e->getMessage() );
			}
		}

		return $crm_id;
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
	private static function add_group( array $data, int $group_id ): array {
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
	 * Check if the given status code is likely to change if we retry the same request later
	 *
	 * @param int $code
	 *
	 * @return bool
	 */
	private static function is_non_permanent_error( $code ) {
		$non_permanent = array( 401, 408, 429, 499, 503, 504, 599 );

		return in_array( $code, $non_permanent );
	}

	private static function send_permanent_error_notification( $submission, $err_message ) {
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
			print_r( $submission, true ),
			$domain,
			$err_message
		);

		Util::send_mail_to_admin( $subject, $message );
	}

	/**
	 * Add the submission to saving queue
	 *
	 * @throws Exception
	 */
	public function queue() {
		$data = $this->get_data();

		if ( ! empty( $data ) ) {
			$item = new CrmQueueItem( $data, $this->submission );
			$this->queue->push_if_not_in_queue( $item );

			if ( defined( 'SUPT_FORM_ASYNC' ) && ! SUPT_FORM_ASYNC ) {
				do_action( self::CRON_HOOK_CRM_SAVE );
			} else {
				$this->schedule_cron();
			}
		}
	}

	/**
	 * Get the filtered data for the crm
	 *
	 * @return array
	 */
	private function get_data() {
		if ( empty( $this->crm_data ) ) {
			$this->add_crm_mapped_data();
		}

		$data = $this->crm_data;

		/**
		 * Filters the submitted data, right before it's saved in the crm.
		 *
		 * @param CrmFieldData[] $data
		 */
		return (array) apply_filters( FormType::MODEL_NAME . '-before-crm-save', $data );
	}

	/**
	 * Add data from fields that were mapped to crm fields
	 *
	 * Additionally add:
	 * - The fields of the predecessor form
	 * - The group (only if a new record is created)
	 * - The entry channel (only if a new record is created)
	 * - The language (only if none is set yet)
	 */
	private function add_crm_mapped_data() {
		$this->add_predecessor_form_data( $this->submission->meta_get_predecessor() );
		$this->add_form_fields_data( $this->form->get_fields() );

		// only add the automatic field data, if no data was added from the form
		// else every record would be added, even if the form wasn't configured to
		// store data to the crm
		if ( ! empty( $this->crm_data ) ) {
			$this->auto_add_entry_channel();
			$this->auto_add_language();
		}
	}

	/**
	 * Recursively add all data of the linked previous forms.
	 *
	 * If two forms contain the same field, the newer one is authoritative
	 *
	 * @param SubmissionModel $predecessor
	 */
	private function add_predecessor_form_data( $predecessor ) {
		// add data from linked submissions first
		if ( $predecessor ) {
			$pre_predecessor = $predecessor->meta_get_predecessor();
			if ( $pre_predecessor ) {
				$this->add_predecessor_form_data( $pre_predecessor );
			}

			try {
				$form_fields = $predecessor->meta_get_form()->get_fields();
			} catch ( Exception $e ) {
				Util::report_form_error(
					'add crm mapped data of predecessor submissions',
					$predecessor,
					$e,
					$this->form
				);

				return;
			}

			// set the predecessor data, so it can be added
			$submission       = $this->submission;
			$this->submission = $predecessor;

			$this->add_form_fields_data( $form_fields );

			// restore the current submission
			$this->submission = $submission;
		}
	}

	/**
	 * Load all data belonging to the given form fields into $this->crm_data
	 *
	 * @param FormField[] $form_fields
	 */
	private function add_form_fields_data( $form_fields ) {
		foreach ( $form_fields as $key => $form_field ) {
			if ( $form_field->has_crm_config() ) {
				$data = $this->get_field_data( $form_field );

				$this->add_crm_data_field_with_special_fields( $form_field, $data );
			}
		}
	}

	/**
	 * Get the corresponding data of the given field, respecting the hidden field value
	 *
	 * @param FormField $form_field
	 *
	 * @return null|mixed
	 */
	private function get_field_data( $form_field ) {
		if ( $form_field->has_fixed_crm_value() ) {
			return $form_field->get_fixed_crm_value();
		}

		$key   = $form_field->get_slug();
		$value = $this->submission->{"get_$key"}();

		if ( is_array( $value ) && ! $form_field->is_crm_multiselect_type() ) {
			$value = implode( ', ', $value );
		}

		return $value;
	}

	/**
	 * Add the given field and data to $this->crm_data as CrmFieldData object.
	 *
	 * Special fields:
	 * - Newsletter: Only add it, if it is a subscription (prevent accidental overwrite).
	 * - Greeting: Also add gender and the formal greeting. Skip field if value is unknown.
	 *
	 * @param FormField $form_field
	 * @param string|array $data
	 */
	private function add_crm_data_field_with_special_fields( $form_field, $data ) {
		if ( $form_field->is_crm_subscription_type()
		     || ( $form_field->has_fixed_crm_value() && $form_field->is_crm_subscription() )
		) {
			$this->add_subscription( $form_field, $data );

			return;
		}

		if ( $form_field->is_crm_greeting_type() ) {
			$this->add_greeting( $form_field, $data );

			return;
		}

		$this->add_crm_data_field_from_form_field( $form_field, $data );
	}

	/**
	 * If a newsletter or magazine subscription was checked, add the subscription.
	 *
	 * @param FormField $form_field
	 * @param null|array|string $data use array for multiselect fields only
	 */
	private function add_subscription( $form_field, $data ) {
		if ( ! $data ) {
			// don't store the newsletter field, if it was left empty
			// this prevents unwanted unsubscriptions
			return;
		}

		$form_field->set_insertion_mode( CrmFieldData::MODE_REPLACE );
		$data = self::CRM_SUBSCRIPTION_VALUE;

		$this->add_crm_data_field_from_form_field( $form_field, $data );
	}

	/**
	 * Add the given field and data as CrmFieldData to $this->crm_data
	 *
	 * @param FormField $form_field
	 * @param null|array|string $data use array for multiselect fields only
	 */
	private function add_crm_data_field_from_form_field( $form_field, $data ) {
		$this->add_crm_data(
			$form_field->get_crm_field(),
			$form_field->get_insertion_mode(),
			$form_field->get_choices(),
			$form_field->get_crm_choice_map(),
			$data,
			! $form_field->has_fixed_crm_value()
		);
	}

	/**
	 * Add the given field and data as CrmFieldData to $this->crm_data
	 *
	 * @param string $crm_key the crm field key
	 * @param string $insertion_mode the insertion mode
	 * @param array $choices the possible choices (if mapped field)
	 * @param array $crm_choices the replacements for the choices
	 * @param null|array|string $data use array for multiselect fields only
	 * @param bool $replace should the choices be replaced with the crm_choices?
	 */
	private function add_crm_data( $crm_key, $insertion_mode, $choices, $crm_choices, $data, $replace ) {
		$this->crm_data[ $crm_key ] = new CrmFieldData(
			$crm_key,
			$insertion_mode,
			$choices,
			$crm_choices,
			$data,
			$replace
		);
	}

	/**
	 * Add the different greetings and the gender field
	 *
	 * @param FormField $form_field
	 * @param string $data
	 */
	private function add_greeting( $form_field, $data ) {
		$key = strtolower( $data );
		if ( ! array_key_exists( $key, self::CRM_GREETINGS_INFORMAL ) ) {
			// don't process any other values than defined in self::CRM_GREETINGS_INFORMAL
			return;
		}

		$this->add_crm_data_field_from_form_field( $form_field, self::CRM_GREETINGS_INFORMAL[ $key ] );

		$this->add_crm_data(
			'gender',
			$form_field->get_insertion_mode(),
			array(),
			array(),
			self::CRM_GREETINGS_INFORMAL_TO_GENDER[ $key ],
			false
		);

		// skip the formal greeting for neutral gender
		if ( array_key_exists( $key, self::CRM_GREETINGS_INFORMAL_TO_FORMAL ) ) {
			$this->add_crm_data(
				'salutationFormal',
				$form_field->get_insertion_mode(),
				array(),
				array(),
				self::CRM_GREETINGS_INFORMAL_TO_FORMAL[ $key ],
				false
			);
		}
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

	/**
	 * Add the entry channel, if not set before.
	 *
	 * The value will follow the pattern 'example.tld - form title'
	 */
	private function auto_add_entry_channel() {
		if ( ! isset( $this->crm_data['entryChannel'] ) ) {
			$this->add_crm_data(
				'entryChannel',
				CrmFieldData::MODE_ADD_IF_NEW,
				array(),
				array(),
				Util::get_domain() . ' - ' . $this->form->get_title(),
				false
			);
		}
	}

	/**
	 * Add the language, derived from the current website language.
	 */
	private function auto_add_language() {
		$locale = substr( get_locale(), 0, 1 );
		if ( in_array( $locale, array( 'd', 'f', 'i' ) ) ) {
			$this->add_crm_data(
				'language',
				CrmFieldData::MODE_REPLACE_EMPTY,
				array(),
				array(),
				$locale,
				false
			);
		}
	}

	/**
	 * Ensure the saving cron is scheduled
	 */
	private function schedule_cron() {
		Util::add_cron( self::CRON_HOOK_CRM_SAVE, time() - 1, self::CRON_CRM_SAVE_RETRY_INTERVAL );
	}
}
