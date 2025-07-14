<?php

namespace SUPT;

require_once __DIR__ . '/CrmDao.php';
require_once __DIR__ . '/CrmSaver.php';
require_once __DIR__ . '/MailchimpSaver.php';
require_once __DIR__ . '/Util.php';
require_once __DIR__ . '/QueueDao.php';
require_once __DIR__ . '/CrmQueueItem.php';

class SyncProcessor {
    const CRON_HOOK_SYNC_SAVE = 'supt_form_save_to_sync';
    const CRON_SYNC_SAVE_INTERVAL = 'every_minute';
    const MAX_BATCH_SIZE = 200;
    const MAX_SAVE_ATTEMPTS = 5;

    private CrmDao $crm_dao;
    private bool $can_sync_to_crm = false;
    private bool $can_sync_to_mailchimp = false;

    public function __construct() {
        $this->can_sync_to_crm = CrmDao::has_api_url();
        $this->can_sync_to_mailchimp = MailchimpSaver::has_mailchimp_api_key();
    }

    public static function process_queue() {
        $processor = new self();

        $queue = new QueueDao(SyncEnqueuer::QUEUE_KEY);
        $items = array_slice($queue->get_all(), 0, self::MAX_BATCH_SIZE);

        if (empty($items) ||
            (!$processor->can_sync_to_crm && !$processor->can_sync_to_mailchimp)) {
            return;
        }

        if ($processor->can_sync_to_crm) {
            try {
                $processor->crm_dao = new CrmDao();
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

    private function processItem(CrmQueueItem $item): bool {
        $crm_field_data_objects = $item->get_data();
        $simple_data = $this->restructureFieldData($crm_field_data_objects);

        $new_contacts_to_crm = !$this->can_sync_to_mailchimp || $this->shouldForceCrmSync($item);

        // Try to match in CRM
        if ($this->can_sync_to_crm) {
            $match = $this->crm_dao->match($crm_field_data_objects);

            if ($this->isValidCrmMatch($match) || $new_contacts_to_crm) {
                if (CrmSaver::save_to_crm($crm_field_data_objects, $this->crm_dao, $match, $new_contacts_to_crm)) {
                    $this->logProcessed($item, 'CRM');
                    return true;
                }
            }
        }

        // If not matched in CRM and Mailchimp is configured, send to Mailchimp
        if (!$new_contacts_to_crm) {
            MailchimpSaver::send_to_mailchimp($simple_data);
            $this->logProcessed($item, 'Mailchimp');
            return true;
        }

        return false;
    }

    /**
     * Check if the form should be forced to sync to CRM
     *
     * @param CrmQueueItem $item The item to get the form id from
     * @return bool True if the form has the 'force_crm_sync' option set
     */
    private function shouldForceCrmSync(CrmQueueItem $item): bool {
        return $this->can_sync_to_crm && (bool) get_field('force_crm_sync', $item->get_form_id());
    }

    /**
     * Check if the CRM match result indicates a valid match
     *
     * @param array $match The match result from CRM
     * @return bool True if there's a valid match, false otherwise
     */
    private function isValidCrmMatch(array $match): bool {
        return isset($match['status']) && $match['status'] !== CrmDao::MATCH_NONE;
    }

    /**
     * Log that the item was processed
     *
     * @param CrmQueueItem $item The item that was processed
     * @param string $system The system that processed the item
     */
    private function logProcessed(CrmQueueItem $item, string $system): void {
        Util::debug_log("submissionId={$item->get_submission_id()} msg=Processed by $system");
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

    public static function get_queue(): QueueDao {
        return new QueueDao(SyncEnqueuer::QUEUE_KEY);
    }

    public static function add_cron_schedule($schedules) {
        $schedules['every_minute'] = [
            'interval' => 60,
            'display' => 'Every Minute',
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
