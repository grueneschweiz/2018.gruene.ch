<?php


namespace SUPT;


class Mailer {
	const OPTION_MAIL_SEND_QUEUE = 'supt_form_mail_send_queue';
	const CRON_HOOK_MAIL_SEND = 'supt_form_mail_send';
	const CRON_MAIL_SEND_RETRY_INTERVAL = 'hourly';

	/**
	 * @var SubmissionModel
	 */
	private $submission;

	/**
	 * @var FormModel
	 */
	private $form;

	/**
	 * Mailer constructor.
	 *
	 * @param $submission_id
	 *
	 * @throws \Exception
	 */
	public function __construct( $submission_id ) {
		require_once __DIR__ . '/Mail.php';
		require_once __DIR__ . '/SubmissionModel.php';

		$this->submission = new SubmissionModel( $submission_id );
		$this->form       = $this->submission->meta_get_form();
	}

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
				$this->submission->meta_get_id()
			);

			$this->queue_mail( $confirmation );
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
				$this->submission->meta_get_id()
			);

			$this->queue_mail( $notification );
		}
	}

	/**
	 * Add mail to sending queue
	 *
	 * @param Mail $mail
	 */
	private function queue_mail( $mail ) {
		$to_send   = get_option( self::OPTION_MAIL_SEND_QUEUE, array() );
		$to_send[] = $mail;
		update_option( self::OPTION_MAIL_SEND_QUEUE, $to_send, false );
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
		require_once __DIR__ . '/Mail.php';

		/** @var Mail[] $to_send */
		$to_send = get_option( self::OPTION_MAIL_SEND_QUEUE, array() );
		if ( empty( $to_send ) ) {
			Util::remove_cron( self::CRON_HOOK_MAIL_SEND );

			return;
		}

		foreach ( $to_send as $key => $mail ) {
			$sent = $mail->send();

			if ( $sent ) {
				unset( $to_send[ $key ] );
			}
		}

		update_option( self::OPTION_MAIL_SEND_QUEUE, $to_send );

		if ( empty( $to_send ) ) {
			// since everything was sent, we can now disable the cron job
			// it will automatically be reenabled, if needed
			Util::remove_cron( self::CRON_HOOK_MAIL_SEND );
		}
	}
}
