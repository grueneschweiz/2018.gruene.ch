<?php

namespace SUPT;

require_once __DIR__ . '/CrmFieldData.php';
require_once __DIR__ . '/SubmissionModel.php';
require_once __DIR__ . '/FormModel.php';
require_once __DIR__ . '/QueueDao.php';
require_once __DIR__ . '/Util.php';

class SyncEnqueuer {
    const QUEUE_KEY = 'sync_save';

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
     * @var array
     */
    private $crm_data = [];

    /**
     * Form data to be processed
     *
     * @var array
     */
    public $data;

    /**
     * Form ID
     *
     * @var int|null
     */
    public $form_id;

    /**
     * SyncEnqueuer constructor.
     *
     * @param int $submission_id
     *
     * @throws \Exception
     */
    public function __construct($submission_id, $form_id = null) {
        $this->submission = new SubmissionModel($submission_id);
        $this->form = $this->submission->meta_get_form();
        $this->form_id = $form_id;
    }

   /**
     * Process data and add to queue
     *
     * @return bool Success status
     * @throws \Exception On queue error
     */
    public function add_to_queue($data) {
        $this->data = $data;
        $queue = new QueueDao(self::QUEUE_KEY);

        // Process the data
        $this->apply_filtered_data();

        if ( ! empty( $this->data ) ) {
            unset($this->data['_meta_']);
			$item = new CrmQueueItem( $this->data, $this->submission );
			$queue->push_if_not_in_queue( $item );
        }

        // If SUPT_FORM_ASYNC is false, process the sync queue immediately (for debugging)
        if (defined('SUPT_FORM_ASYNC') && !SUPT_FORM_ASYNC) {
            SyncProcessor::process_queue();
        }

        return true;
    }

    /**
     * Get and apply the filtered data for the crm
     */
    private function apply_filtered_data() {
        $this->data = $this->add_crm_mapped_data();

        /**
         * Filters the submitted data, right before it's saved in the crm.
         *
         * @param CrmFieldData[] $data
         */
        $this->data = (array) apply_filters(FormType::MODEL_NAME . '-before-crm-save', $this->data);
    }

    /**
     * Add data from fields that were mapped to crm fields
     *
     * Additionally add:
     * - The fields of the predecessor form
     * - The group (only if a new record is created)
     * - The entry channel (only if a new record is created)
     * - The language (only if none is set yet)
     *
     * @return array
     */
    private function add_crm_mapped_data() {
        $this->add_predecessor_form_data($this->submission->meta_get_predecessor());
        $this->add_form_fields_data($this->form->get_fields());

        // only add the automatic field data, if no data was added from the form
        // else every record would be added, even if the form wasn't configured to
        // store data to the crm
        if (!empty($this->crm_data)) {
            $this->auto_add_entry_channel();
            $this->auto_add_language();
        }

        return $this->crm_data;
    }

    /**
     * Add the entry channel, if not set before.
     *
     * The value will follow the pattern 'example.tld - form title'
     */
    private function auto_add_entry_channel() {
        if (!isset($this->crm_data['entryChannel'])) {
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
        $locale = substr(get_locale(), 0, 1);
        if (in_array($locale, array('d', 'f', 'i'))) {
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
     * Recursively add all data of the linked previous forms.
     *
     * If two forms contain the same field, the newer one is authoritative
     *
     * @param SubmissionModel $predecessor
     */
    private function add_predecessor_form_data($predecessor) {
        // add data from linked submissions first
        if ($predecessor) {
            $pre_predecessor = $predecessor->meta_get_predecessor();
            if ($pre_predecessor) {
                $this->add_predecessor_form_data($pre_predecessor);
            }

            try {
                $form_fields = $predecessor->meta_get_form()->get_fields();
            } catch (\Exception $e) {
                Util::report_form_error(
                    'add crm mapped data of predecessor submissions',
                    $predecessor,
                    $e,
                    $this->form
                );

                return;
            }

            // set the predecessor data, so it can be added
            $submission = $this->submission;
            $this->submission = $predecessor;

            $this->add_form_fields_data($form_fields);

            // restore the current submission
            $this->submission = $submission;
        }
    }

    /**
     * Load all data belonging to the given form fields into $this->crm_data
     *
     * @param FormField[] $form_fields
     */
    private function add_form_fields_data($form_fields) {
        foreach ($form_fields as $key => $form_field) {
            if ($form_field->has_crm_config()) {
                $data = $this->get_field_data($form_field);
                $this->add_crm_data_field_with_special_fields($form_field, $data);
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
    private function get_field_data($form_field) {
        if ($form_field->has_fixed_crm_value()) {
            return $form_field->get_fixed_crm_value();
        }

        $key = $form_field->get_slug();
        $value = $this->submission->{"get_$key"}();

        if (is_array($value) && !$form_field->is_crm_multiselect_type()) {
            $value = implode(', ', $value);
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
    private function add_crm_data_field_with_special_fields($form_field, $data) {
        if ($form_field->is_crm_subscription_type()
            || ($form_field->has_fixed_crm_value() && $form_field->is_crm_subscription())
        ) {
            $this->add_subscription($form_field, $data);
            return;
        }

        if ($form_field->is_crm_greeting_type()) {
            $this->add_greeting($form_field, $data);
            return;
        }

        $this->add_crm_data_field_from_form_field($form_field, $data);
    }

    /**
     * If a newsletter or magazine subscription was checked, add the subscription.
     *
     * @param FormField $form_field
     * @param null|array|string $data use array for multiselect fields only
     */
    private function add_subscription($form_field, $data) {
        if (!$data) {
            // don't store the newsletter field, if it was left empty
            // this prevents unwanted unsubscriptions
            return;
        }

        $form_field->set_insertion_mode(CrmFieldData::MODE_REPLACE);
        $data = self::CRM_SUBSCRIPTION_VALUE;

        $this->add_crm_data_field_from_form_field($form_field, $data);
    }

    /**
     * Add the given field and data as CrmFieldData to $this->crm_data
     *
     * @param FormField $form_field
     * @param null|array|string $data use array for multiselect fields only
     */
    private function add_crm_data_field_from_form_field($form_field, $data) {
        $this->add_crm_data(
            $form_field->get_crm_field(),
            $form_field->get_insertion_mode(),
            $form_field->get_choices(),
            $form_field->get_crm_choice_map(),
            $data,
            !$form_field->has_fixed_crm_value()
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
    private function add_crm_data($crm_key, $insertion_mode, $choices, $crm_choices, $data, $replace) {
        $this->crm_data[$crm_key] = new CrmFieldData(
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
    private function add_greeting($form_field, $data) {
        $key = strtolower($data);
        if (!array_key_exists($key, self::CRM_GREETINGS_INFORMAL)) {
            // don't process any other values than defined in self::CRM_GREETINGS_INFORMAL
            return;
        }

        $this->add_crm_data_field_from_form_field($form_field, self::CRM_GREETINGS_INFORMAL[$key]);

        $this->add_crm_data(
            'gender',
            $form_field->get_insertion_mode(),
            array(),
            array(),
            self::CRM_GREETINGS_INFORMAL_TO_GENDER[$key],
            false
        );

        // skip the formal greeting for neutral gender
        if (array_key_exists($key, self::CRM_GREETINGS_INFORMAL_TO_FORMAL)) {
            $this->add_crm_data(
                'salutationFormal',
                $form_field->get_insertion_mode(),
                array(),
                array(),
                self::CRM_GREETINGS_INFORMAL_TO_FORMAL[$key],
                false
            );
        }
    }
}
