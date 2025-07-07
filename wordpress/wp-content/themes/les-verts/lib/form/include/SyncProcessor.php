<?php

namespace SUPT;

require_once __DIR__ . '/CrmDao.php';
require_once __DIR__ . '/CrmSaver.php';
require_once __DIR__ . '/MailchimpSaver.php';
require_once __DIR__ . '/Util.php';
require_once __DIR__ . '/QueueDao.php';
require_once __DIR__ . '/CrmQueueItem.php';
require_once __DIR__ . '/SubmissionModel.php';

class SyncProcessor {
    const QUEUE_KEY = 'sync_save';
    const CRON_HOOK_SYNC_SAVE = 'supt_form_save_to_sync';
    const CRON_SYNC_SAVE_INTERVAL = 'every_minute';
    const MAX_BATCH_SIZE = 200;
    const MAX_SAVE_ATTEMPTS = 5;

    private CrmDao $crmDao;
    private bool $canSyncToCrm = false;
    private bool $canSyncToMailchimp = false;

    public function __construct() {
        $this->canSyncToCrm = CrmDao::has_api_url();
        $this->canSyncToMailchimp = MailchimpSaver::has_mailchimp_api_key();
    }

    public static function process_queue() {
        $processor = new self();

        $queue = new QueueDao(self::QUEUE_KEY);
        $items = array_slice($queue->get_all(), 0, self::MAX_BATCH_SIZE);

        // If neither sync option is available, nothing to do
        if (!$processor->canSyncToCrm && !$processor->canSyncToMailchimp) {
            return;
        }

        if ($processor->canSyncToCrm) {
            try {
                $processor->crmDao = new CrmDao();
            } catch (\Exception $e) {
                $first_item = $items[0] ?? null;
                $submission = ($first_item instanceof CrmQueueItem) ? $first_item->get_submission_id() : "(submission id not found)";
                CrmSaver::send_permanent_error_notification($submission, $e->getMessage());
                return;
            }
        }

        /** @var CrmQueueItem $item */
        foreach ($items as $item) {
            if (!$item->has_data()) {
                Util::debug_log("submissionId={$item->get_submission_id()} msg=No data to save to CRM");
                continue;
            }

            $processed = false;
            try {
                $processed = $processor->processItem($item);
            } catch (\Exception $e) {
                Util::report_form_error('sync queue process', $item, $e, null);
            }
            finally {
                $processor->updateQueue($queue, $item, $processed);
            }
        }
    }

    /**
     * Extract simple data from CrmFieldData objects
     *
     * @param array $crm_field_data_objects Array of CrmFieldData objects
     * @return array Simple key-value array
     */
    private function restructureFieldData(array $crm_field_data_objects): array {
        $data = array();
        foreach ($crm_field_data_objects as $crm_field_data) {
            if ($crm_field_data instanceof CrmFieldData) {
                $data[$crm_field_data->get_key()] = $crm_field_data->get_value();
            }
        }
        return $data;
    }

    /**
     * Process a single queue item
     *
     * @param CrmQueueItem $item Queue item to process
     * @return bool Whether the item was successfully processed
     * @throws \Exception On API error
     */
    private function processItem(CrmQueueItem $item): bool {
        $crm_field_data_objects = $item->get_data();
        $simple_data = $this->restructureFieldData($crm_field_data_objects);

        // Try CRM first if available
        if ($this->canSyncToCrm && $this->crmDao) {
            $processed = $this->maybeSendToCrmSaver($item, $crm_field_data_objects);
            if ($processed) {
                return true;
            }
        }

        // If not processed by CRM and Mailchimp is configured, send to Mailchimp
        if ($this->canSyncToMailchimp) {
            MailchimpSaver::send_to_mailchimp($simple_data);
            Util::debug_log("submissionId={$item->get_submission_id()} msg=Processed by Mailchimp");
            return true;
        }

        return false;
    }

    /**
     * Process an item with the CRM
     *
     * @param CrmQueueItem $item Queue item to process
     * @param array $data Extracted simple data array
     * @return bool Whether the item was successfully processed
     * @throws \Exception On API error
     */
    private function maybeSendToCrmSaver(CrmQueueItem $item, array $crm_field_data_objects): bool {
        $match = $this->crmDao->match($crm_field_data_objects);

        if (!isset($match['status']) || $match['status'] === CrmDao::MATCH_NONE) {
            return false;
        }

        $crm_saver = new CrmSaver();
        $processed = $crm_saver->save_to_crm($crm_field_data_objects, $this->crmDao, $match);

        if ($processed) {
            Util::debug_log("submissionId={$item->get_submission_id()} msg=Processed by CRM");
        }

        return $processed;
    }

    /**
     * Update the queue based on processing result
     *
     * @param QueueDao $queue Queue data access object
     * @param CrmQueueItem $item Queue item that was processed
     * @param bool $processed Whether the item was successfully processed
     */
    private function updateQueue(QueueDao $queue, CrmQueueItem $item, bool $processed): void {
        // Remove from queue if processed or max attempts reached
        if ($processed || ($item->get_attempts() ?? 0) >= self::MAX_SAVE_ATTEMPTS) {
            $queue->filter(function($q_item) use ($item) {
                return $q_item->get_submission_id() !== $item->get_submission_id();
            });
        } else {
            $item->add_attempt();
            $queue->push_if_not_in_queue($item);

            Util::debug_log("submissionId={$item->get_submission_id()} msg=Processing failed, will retry. attempts={$item->get_attempts()}");
        }
    }

    public static function add_cron_schedule($schedules) {
        $schedules['every_minute'] = [
            'interval' => 60,
            'display' => __('Every Minute'),
        ];
        return $schedules;
    }

    public static function register_cron() {
        if (!wp_next_scheduled(self::CRON_HOOK_SYNC_SAVE)) {
            wp_schedule_event(time(), self::CRON_SYNC_SAVE_INTERVAL, self::CRON_HOOK_SYNC_SAVE);
        }
        add_action(self::CRON_HOOK_SYNC_SAVE, [__CLASS__, 'process_queue']);
    }
}

// Register WordPress hooks for cron jobs
add_filter('cron_schedules', ['\SUPT\SyncProcessor', 'add_cron_schedule']);
add_action('init', ['\SUPT\SyncProcessor', 'register_cron']);
