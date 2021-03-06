<?php


namespace SUPT;

use Exception;

require_once __DIR__ . '/Mail.php';
require_once __DIR__ . '/QueueDao.php';

class Mailer {
	const QUEUE_KEY = 'mail';
	const CRON_HOOK_MAIL_SEND = 'supt_form_mail_send';
	const CRON_MAIL_SEND_RETRY_INTERVAL = 'hourly';
	const SENDING_RETRIES = 3;

	/**
	 * @var SubmissionModel
	 */
	private $submission;

	/**
	 * @var FormModel
	 */
	private $form;

	/**
	 * @var QueueDao
	 */
	private $queue;

	/**
	 * Mailer constructor.
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
	 * Get the mail queue
	 *
	 * @return QueueDao
	 */
	public static function get_queue(): QueueDao {
		return new QueueDao( self::QUEUE_KEY );
	}

	/**
	 * Add the mails for the submission to the sending queue
	 */
	public function queue_mails() {
		$this->queue_confirmation();
		$this->queue_notification();
		$this->schedule_cron();
	}

	/**
	 * Queue the confirmation mail, if confirmation is enabled and we have a destination address
	 */
	private function queue_confirmation() {
		$to = $this->submission->meta_get_linked_email();

		if ( $this->form->has_confirmation() && $to ) {
			$confirmation = new Mail(
				$to,
				$this->from(),
				$this->reply_to(),
				$this->form->get_confirmation_subject(),
				$this->form->get_confirmation_template(),
				$this->get_data(),
				$this->submission->meta_get_id(),
				$this->submission->meta_get_referer()
			);

			$this->queue->push( $confirmation );
		}
	}

	/**
	 * Queue the notification mail, if notification is enabled
	 */
	private function queue_notification() {
		if ( $this->form->has_notification() ) {
			$notification = new Mail(
				$this->form->get_notification_destination(),
				$this->from(),
				$this->submission->meta_get_linked_email(),
				$this->form->get_notification_subject(),
				$this->form->get_notification_template(),
				$this->get_data(),
				$this->submission->meta_get_id(),
				$this->submission->meta_get_referer()
			);

			$this->queue->push( $notification );
		}
	}

	/**
	 * Ensure the sending cron is scheduled
	 */
	private function schedule_cron() {
		Util::add_cron( self::CRON_HOOK_MAIL_SEND, time() - 1, self::CRON_MAIL_SEND_RETRY_INTERVAL );
	}

	/**
	 * Return the filtered data of the submission including all linked submissions
	 *
	 * @return array
	 */
	private function get_data() {
		$data = $this->submission->as_array_with_linked_data();

		/**
		 * Filters the submitted data, before emails are queued.
		 *
		 * @param array $data the form data
		 */
		return (array) apply_filters( FormType::MODEL_NAME . '-email-data', $data );
	}

	/**
	 * Return the filtered sender address
	 *
	 * @return string
	 */
	private function from() {
		$sender_name = $this->form->get_sender_name();

		/**
		 * Filters the sender address of email
		 *
		 * @param string the sender email
		 */
		return apply_filters(
			FormType::MODEL_NAME . '-email-from',
			"$sender_name <website@" . Util::get_domain() . '>'
		);
	}

	/**
	 * Return the filtered reply to address
	 *
	 * @return string
	 */
	private function reply_to() {
		$reply_to = $this->form->get_reply_to_address();

		/**
		 * Filters the reply to address
		 *
		 * @param string the reply to address
		 */
		return apply_filters( FormType::MODEL_NAME . '-email-reply-to', $reply_to );
	}

	/**
	 * Send out the queued mails
	 *
	 * Called by the WordPress cron job
	 */
	public static function send_mails() {
		$queue = self::get_queue();
		$error = 0;

		/** @var Mail $mail */
		$mail = $queue->pop();
		while ( ! empty( $mail ) ) {
			$sent = $mail->send();

			// on error
			if ( ! $sent ) {

				// requeue mail on error if number of retries is not exceeded
				if ( $mail->get_sending_attempts() < self::SENDING_RETRIES ) {
					$queue->push( $mail );
					$error ++;
				}

				// if there are only the errored mails left in the queue
				if ( $error >= $queue->length() ) {
					break;
				}
			}

			$mail = $queue->pop();
		}
	}
}
