<?php

namespace SUPT;

require_once __DIR__ . '/CrmDao.php';
require_once __DIR__ . '/CrmSaver.php';
require_once __DIR__ . '/MailchimpSaver.php';
require_once __DIR__ . '/Util.php';
require_once __DIR__ . '/QueueDao.php';
require_once __DIR__ . '/CrmQueueItem.php';
require_once __DIR__ . '/CrmMaxSyncsException.php';

class SyncProcessor {
    const CRON_HOOK_CRM_MC_SAVE = 'supt_form_save_to_crm';
    const CRON_SYNC_SAVE_INTERVAL = 'every_ten_minutes';
    const CRON_SYNC_SAVE_INTERVAL_SECONDS = 10 * MINUTE_IN_SECONDS;
    const MAX_BATCH_SIZE = 30;
    const CRM_MAX_SYNCS_PER_RUN = 15;
    const MAX_SAVE_ATTEMPTS = 5;
    const MIN_ATTEMPT_TIMEOUT = 19 * MINUTE_IN_SECONDS;
    const CRON_LOCK_KEY = 'sync_processor_lock';
    const CRON_LOCK_TIMEOUT = 9 * MINUTE_IN_SECONDS;

    const MSG_ITEM_NO_DATA = "Item has no data";
    const SUBJECT_ITEM_MAX_ATTEMPTS = "SyncProcessor: Max attempts reached";
    const MSG_ITEM_MAX_ATTEMPTS = "Item was processed more than " . self::MAX_SAVE_ATTEMPTS . " times. " .
        "Has been removed from the queue.";

    private CrmDao $crm_dao;
    private QueueDao $queue;
    private int $crm_sync_count = 0;
    private bool $can_sync_to_crm = false;
    private bool $can_sync_to_mailchimp = false;

    public function __construct() {
        $this->can_sync_to_crm = CrmDao::has_api_url();
        $this->can_sync_to_mailchimp = MailchimpSaver::has_mailchimp_api_key();
    }

    public static function process_queue() {
        $processor = new self();

        if (!$processor->can_sync_to_crm && !$processor->can_sync_to_mailchimp) {
            Util::remove_cron(self::CRON_HOOK_CRM_MC_SAVE);
            Util::debug_log("msg=No CRM or Mailchimp API key found, removing cron job");
            return;
        }

        if (!$processor->acquire_lock()) {
            Util::debug_log("msg=Sync processor already running, skipping this execution");
            return;
        }

        try {
            $processor->queue = self::get_queue();
            $items = array_slice($processor->queue->get_all(), 0, self::MAX_BATCH_SIZE);

            if (empty($items)) {
                return;
            }

            if ($processor->can_sync_to_crm) {
                try {
                    $processor->crm_dao = new CrmDao();
                } catch (\Exception $e) {
                    $first_item = $items[0] ?? null;
                    $submission = ($first_item instanceof CrmQueueItem) ? $first_item->get_submission_id() : "(submission id not found)";
                    CrmSaver::send_permanent_error_notification($submission, $e->getMessage());
                    Util::debug_log("submissionId={$submission} msg=Failed to create CRM DAO. Skipping.");
                    return;
                }
            }

            /** @var CrmQueueItem $item */
            foreach ($items as $item) {
                if ($processor->too_many_attempts($item) || $processor->too_recent_attempt($item)) {
                    continue;
                }

                if (!$item->has_data()) {
                    Util::report_form_error('sync queue process', $item, new \Exception(self::MSG_ITEM_NO_DATA), null);
                    $processor->remove_from_queue($item);
                    continue;
                }

                try {
                    if ($processor->process_item($item)) {
                        $processor->remove_from_queue($item);
                    } else {
                        $processor->update_queue($item);
                    }
                } catch (CrmMaxSyncsException $e) {
                    Util::debug_log("submissionId=" . $item->get_submission_id() . " msg=Too many CRM syncs per run. Ending this run.");
                    return;
                } catch (\Exception $e) {
                    Util::report_form_error('sync queue process', $item, $e, null);
                    $processor->update_queue($item);
                }
            }

            $processor->schedule_next_batch();
            Util::debug_log("msg=Processed batch of {$processor->crm_sync_count} CRM syncs");
        } finally {
            $processor->release_lock();
        }
    }

    /**
     * (Re)schedule the CRM-MC save cron job if not already scheduled
     * or if the next run is too far in the future.
     *
     * The checks ensure that the cron job respects the current (shortened)
     * interval after a configuration update.
     */
    public static function schedule_cron() {
        Util::add_cron(self::CRON_HOOK_CRM_MC_SAVE, time() - 1, self::CRON_SYNC_SAVE_INTERVAL);
    }

    /**
     * Get the queue without any filtering
     */
    public static function get_queue(): QueueDao {
        return new QueueDao(SyncEnqueuer::QUEUE_KEY);
    }

    /**
     * Try to acquire the processing lock
     *
     * @return bool True if lock was acquired, false if already locked
     */
    private function acquire_lock(): bool {
        if (get_transient(self::CRON_LOCK_KEY)) {
            Util::debug_log("msg=Sync processor already running, skipping this execution");
            return false;
        }

        return set_transient(self::CRON_LOCK_KEY, true, self::CRON_LOCK_TIMEOUT);
    }

    /**
     * Release the processing lock
     */
    private function release_lock(): void {
        delete_transient(self::CRON_LOCK_KEY);
    }

    /**
     * Extract simple data from CrmFieldData objects
     *
     * @param array $crm_field_data_objects Array of CrmFieldData objects
     * @return array Simple key-value array
     */
    private function restructure_field_data(array $crm_field_data_objects): array {
        $data = array();
        foreach ($crm_field_data_objects as $crm_field_data) {
            if ($crm_field_data instanceof CrmFieldData) {
                $data[$crm_field_data->get_key()] = $crm_field_data->get_value();
            }
        }
        return $data;
    }

    /**
     * Check if the item should be processed
     *
     * @param CrmQueueItem $item The item to check
     * @return bool True if the item should be processed, false otherwise
     */
    private function too_many_attempts(CrmQueueItem $item) {
        if ( $item->get_attempts() >= self::MAX_SAVE_ATTEMPTS ) {
            $this->remove_from_queue($item);
            Util::send_mail_to_admin(
                self::SUBJECT_ITEM_MAX_ATTEMPTS . "id=" . $item->get_submission_id(),
                self::MSG_ITEM_MAX_ATTEMPTS . "\n\n" . json_encode($item->get_data())
            );
            Util::debug_log("submissionId=" . $item->get_submission_id() . " msg=Too many attempts. Removed from queue.");
            return true;
        }

        return false;
    }

    /**
     * Check if the item should be skipped because it was processed too recently
     *
     * @param CrmQueueItem $item The item to check
     * @return bool True if the item should be skipped, false otherwise
     */
    private function too_recent_attempt(CrmQueueItem $item) {
        if (
            $item->last_attempt_seconds_ago() &&
            $item->last_attempt_seconds_ago() < self::MIN_ATTEMPT_TIMEOUT
        ) {
            Util::debug_log("submissionId={$item->get_submission_id()} msg=Last attempt only {$item->last_attempt_seconds_ago()} seconds ago. Skipping.");
            return true;
        }

        return false;
    }

    /**
     * Process a single queue item
     *
     * @param CrmQueueItem $item The queue item to process
     * @return bool True if the item was processed successfully, false otherwise
     * @throws Exception If an error occurs during matching or saving
     * @throws CrmMaxSyncsException If the maximum number of CRM syncs per run is exceeded
     */
    private function process_item(CrmQueueItem $item): bool {
        $crm_field_data_objects = $item->get_data();
        $simple_data = $this->restructure_field_data($crm_field_data_objects);

        $new_contacts_to_crm = !$this->can_sync_to_mailchimp || $this->should_force_crm_sync($item);

        // Try to match in CRM
        if ($this->can_sync_to_crm) {
            $match = $this->crm_dao->match($crm_field_data_objects);
            if ($this->is_valid_crm_match($match) || $new_contacts_to_crm) {
                if ($this->crm_sync_count >= self::CRM_MAX_SYNCS_PER_RUN) {
                    throw new CrmMaxSyncsException();
                }
                $this->crm_sync_count++;
                if (CrmSaver::save_to_crm($crm_field_data_objects, $this->crm_dao, $match, $new_contacts_to_crm)) {
                    Util::logProcessed($item, 'CRM');
                    return true;
                }
            }
        }

        // If not matched in CRM and Mailchimp is configured, send to Mailchimp
        if (!$new_contacts_to_crm) {
            MailchimpSaver::send_to_mailchimp($simple_data);
            Util::logProcessed($item, 'Mailchimp');
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
    private function should_force_crm_sync(CrmQueueItem $item): bool {
        return $this->can_sync_to_crm && (bool) get_field('force_crm_sync', $item->get_form_id());
    }

    /**
     * Check if the CRM match result indicates a valid match
     *
     * @param array $match The match result from CRM
     * @return bool True if there's a valid match, false otherwise
     */
    private function is_valid_crm_match(array $match): bool {
        return isset($match['status']) && $match['status'] !== CrmDao::MATCH_NONE;
    }

    /**
     * Update the queue based on processing result
     *
     * @param CrmQueueItem $item Queue item that was processed
     */
    private function update_queue(CrmQueueItem $item): void {
        $item->add_attempt();
        $this->queue->update_and_move_to_end($item);
        Util::debug_log("submissionId={$item->get_submission_id()} msg=Processing failed, will retry. attempts={$item->get_attempts()}");
    }

    /**
     * Remove the item from the queue
     *
     * @param CrmQueueItem $item The item to remove
     */
    private function remove_from_queue(CrmQueueItem $item): void {
        $this->queue->filter(function ($q_item) use ($item) {
            return $q_item->get_submission_id() !== $item->get_submission_id();
        });
    }

    /**
     * Schedule the next batch processing if there are more items in the queue
     * This creates a chain of processing until the queue is empty
     */
    private function schedule_next_batch(): void {
        if ($this->queue->has_items()) {
            wp_schedule_single_event(
                time() + 60,
                self::CRON_HOOK_CRM_MC_SAVE,
                [wp_generate_uuid4()]
            );
        }
    }
}
