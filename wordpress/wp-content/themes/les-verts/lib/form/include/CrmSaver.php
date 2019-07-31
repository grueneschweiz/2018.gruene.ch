<?php


namespace SUPT;

use Exception;

require_once __DIR__ . '/CrmFieldData.php';

class CrmSaver {
	const OPTION_CRM_SAVE_QUEUE = 'supt_form_crm_save_queue';
	const CRON_HOOK_CRM_SAVE = 'supt_form_save_to_crm';
	const CRON_CRM_SAVE_RETRY_INTERVAL = 'hourly';

	const CRM_NEWSLETTER_VALUE = 'yes';
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
	 * CrmSaver constructor.
	 *
	 * @param int $submission_id
	 *
	 * @throws \Exception
	 */
	public function __construct( $submission_id ) {
		require_once __DIR__ . '/SubmissionModel.php';

		$this->submission = new SubmissionModel( $submission_id );
		$this->form       = $this->submission->meta_get_form();
	}

	/**
	 * Add the submission to saving queue
	 */
	public function queue() {
		$data = $this->get_data();

		if ( ! empty( $data ) ) {
			$this->queue_data( $data );
			$this->schedule_cron();
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
	 * Add data to saving queue
	 *
	 * @param CrmFieldData[] $data
	 */
	private function queue_data( $data ) {
		$to_save = get_option( self::OPTION_CRM_SAVE_QUEUE, array() );
		if ( ! in_array( $data, $to_save ) ) {
			$to_save[] = $data;
			update_option( self::OPTION_CRM_SAVE_QUEUE, $to_save, false );
		}
	}

	/**
	 * Ensure the saving cron is scheduled
	 */
	private function schedule_cron() {
		Util::add_cron( self::CRON_HOOK_CRM_SAVE, time() - 1, self::CRON_CRM_SAVE_RETRY_INTERVAL );
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

		// the add the current data
		// (so in case we have overlapping fields, the current data will be used)
		foreach ( $this->form->get_fields() as $field ) {
			if ( ! empty( $field['crm_field'] ) ) {
				$data = $this->get_field_data( $field );

				$this->add_crm_data_field_with_special_fields( $field, $data );
			}
		}

		$this->auto_add_group();
		$this->auto_add_entry_channel();
		$this->auto_add_language();
	}

	/**
	 * Get the corresponding data of the given field, respecting the hidden field value
	 *
	 * @param array $field
	 *
	 * @return null|mixed
	 */
	private function get_field_data( $field ) {
		if ( $field['hidden_field'] ) {
			return $field['hidden_field_value'];
		}

		$key = $field['slug'];

		return $this->submission->{"get_$key"};
	}

	/**
	 * Add the crm group as defined in the settings
	 */
	private function auto_add_group() {
		$fake_field = array(
			'crm_field'      => 'groups',
			'insertion_mode' => CrmFieldData::MODE_ADD_IF_NEW
		);
		$data       = \get_field( 'group_id', 'option' );

		$this->add_crm_data_field( $fake_field, $data );
	}

	/**
	 * Add the entry channel, if not set before.
	 *
	 * The value will follow the pattern 'example.tld - form title'
	 */
	private function auto_add_entry_channel() {
		if ( ! isset( $this->crm_data['entryChannel'] ) ) {
			$fake_field = array(
				'crm_field'      => 'entryChannel',
				'insertion_mode' => CrmFieldData::MODE_ADD_IF_NEW
			);
			$data       = Util::get_domain() . ' - ' . $this->form->get_title();

			$this->add_crm_data_field( $fake_field, $data );
		}
	}

	/**
	 * Add the language, derived from the current website language.
	 */
	private function auto_add_language() {
		$locale = substr( get_locale(), 0, 1 );
		if ( in_array( $locale, array( 'd', 'f', 'i' ) ) ) {
			$fake_field = array(
				'crm_field'      => 'language',
				'insertion_mode' => CrmFieldData::MODE_REPLACE_EMPTY
			);

			$this->add_crm_data_field( $fake_field, $locale );
		}
	}

	/**
	 * Add the given field and data to $this->crm_data as CrmFieldData object.
	 *
	 * Special fields:
	 * - Newsletter: Only add it, if it is a subscription (prevent accidental overwrite).
	 * - Greeting: Also add gender and the formal greeting. Skip field if value is unknown.
	 *
	 * @param $field
	 * @param $data
	 */
	private function add_crm_data_field_with_special_fields( $field, $data ) {
		if ( FormSubmission::TYPE_CRM_NEWSLETTER === $field['form_input_type'] ) {
			$this->add_newsletter( $field, $data );

			return;
		}

		if ( FormSubmission::TYPE_CRM_GREETING === $field['form_input_type'] ) {
			$this->add_greeting( $field, $data );

			return;
		}

		$this->add_crm_data_field( $field, $data );
	}

	/**
	 * Add the different greetings and the gender field
	 *
	 * @param $field
	 * @param $data
	 */
	private function add_greeting( $field, $data ) {
		$key = strtolower( $data );
		if ( ! array_key_exists( $key, self::CRM_GREETINGS_INFORMAL ) ) {
			// don't process any other values than defined in self::CRM_GREETINGS_ACCEPTED
			return;
		}

		$this->add_crm_data_field( $field, self::CRM_GREETINGS_INFORMAL[ $key ] );

		$fake_field = array(
			'crm_field'      => 'gender',
			'insertion_mode' => $field['insertion_mode']
		);
		$this->add_crm_data_field( $fake_field, self::CRM_GREETINGS_INFORMAL_TO_GENDER[ $key ] );

		// skip the formal greeting for neutral gender
		if ( array_key_exists( $key, self::CRM_GREETINGS_INFORMAL_TO_FORMAL ) ) {
			$fake_field = array(
				'crm_field'      => 'salutationFormal',
				'insertion_mode' => $field['insertion_mode']
			);
			$this->add_crm_data_field( $fake_field, self::CRM_GREETINGS_INFORMAL_TO_FORMAL[ $key ] );
		}
	}

	/**
	 * If a newsletter subscription was checked, add the subscription.
	 *
	 * @param array $field as returned by the form model
	 * @param null|array|string $data use array for multiselect fields only
	 */
	private function add_newsletter( $field, $data ) {
		if ( ! $data ) {
			// don't store the newsletter field, if it was left empty
			// this prevents unwanted unsubscriptions
			return;
		}

		$field['insertion_mode'] = CrmFieldData::MODE_REPLACE;
		$data                    = self::CRM_NEWSLETTER_VALUE;

		$this->add_crm_data_field( $field, $data );
	}

	/**
	 * Add the given field and data as CrmFieldData to $this->crm_data
	 *
	 * @param array $field as returned by the form model
	 * @param null|array|string $data use array for multiselect fields only
	 */
	private function add_crm_data_field( $field, $data ) {
		$this->crm_data[ $field['crm_field'] ] = new CrmFieldData( $field, $data );
	}

	/**
	 * Recursively add all data of the linked previous forms.
	 *
	 * If two forms contain the same field, the newer one is authoritative
	 *
	 * @param int $predecessor_id
	 */
	private function add_predecessor_form_data( $predecessor_id ) {
		// add data from linked submissions first
		if ( $predecessor_id >= 0 ) {
			try {
				$predecessor = new SubmissionModel( $predecessor_id );

				$pre_predecessor = $predecessor->meta_get_predecessor();
				if ( $pre_predecessor ) {
					$this->add_predecessor_form_data( $pre_predecessor->meta_get_id() );
				}

				$fields = $predecessor->meta_get_form()->get_fields();
			} catch ( Exception $e ) {
				Util::report_form_error(
					'add crm mapped data of linked submissions',
					array( 'predecessor_id' => $predecessor_id ),
					$e,
					$this->form->get_title()
				);

				return;
			}

			foreach ( $fields as $key => $field ) {
				if ( ! empty( $field['crm_field'] ) ) {
					$data = $predecessor->{"get_$key"}();

					$this->add_crm_data_field_with_special_fields( $field, $data );
				}
			}
		}
	}

	/**
	 * Save the form submissions to the crm
	 *
	 * Called by the WordPress cron job
	 */
	public static function save_to_crm() {
		$to_save = get_option( self::OPTION_CRM_SAVE_QUEUE, array() );
		if ( empty( $to_save ) ) {
			Util::remove_cron( self::CRON_HOOK_CRM_SAVE );

			return;
		}

		require_once __DIR__ . '/CrmDao.php';
		$dao = new CrmDao();
		foreach ( $to_save as $key => $submission ) {
			$crm_id = false;

			try {
				$crm_id = $dao->save( $submission );
			} catch ( Exception $e ) {
				Util::report_form_error( 'save to crm', $submission, $e, 'FORM UNKNOWN - ASYNC CALL' );
			}

			if ( $crm_id ) {
				unset( $to_save[ $key ] );
			}
		}

		update_option( self::OPTION_CRM_SAVE_QUEUE, $to_save );

		if ( empty( $to_save ) ) {
			// since everything was saved, we can now disable the cron job
			// it will automatically be reenabled, if needed
			Util::remove_cron( self::CRON_HOOK_CRM_SAVE );
		}
	}
}
