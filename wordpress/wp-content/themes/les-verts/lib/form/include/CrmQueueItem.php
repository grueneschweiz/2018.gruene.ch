<?php


namespace SUPT;


use DateTime;
use Exception;

class CrmQueueItem {
	const ID_INVALID_FORM = - 1;

	/**
	 * The data to store in the crm
	 *
	 * @var CrmFieldData[]
	 */
	private $crm_data;

	/**
	 * The id of the submitted form data
	 *
	 * @var integer
	 */
	private $submission_id;

	/**
	 * The id of the form
	 *
	 * @var integer
	 */
	private $form_id;

	/**
	 * The number of attempts to save the item to the crm
	 *
	 * @var integer
	 */
	private $attempts = 0;

	/**
	 * @var DateTime
	 */
	private $last_attempt;

	/**
	 * CrmQueueItem constructor.
	 *
	 * @param CrmFieldData[] $crm_data
	 * @param SubmissionModel $submission
	 */
	public function __construct( array $crm_data, SubmissionModel $submission ) {
		$this->crm_data      = $crm_data;
		$this->submission_id = $submission->meta_get_id();

		try {
			$this->form_id = $submission->meta_get_form()->get_id();
		} catch ( Exception $e ) {
			$this->form_id = self::ID_INVALID_FORM;
		}
	}

	public function get_form_id(): int {
		return $this->form_id;
	}

	public function get_attempts(): int {
		return $this->attempts;
	}

	/**
	 * @throws Exception if the submission was deleted
	 */
	public function get_submission(): SubmissionModel {
		return new SubmissionModel( $this->submission_id );
	}

	public function get_submission_id(): int {
		return $this->submission_id;
	}

	public function add_attempt(): int {
		$this->last_attempt = new DateTime();

		return $this->attempts ++;
	}

	public function has_data(): bool {
		return ! empty( $this->get_data() );
	}

	/**
	 * @return CrmFieldData[]
	 */
	public function get_data(): array {
		return $this->crm_data;
	}

	/**
	 * The seconds elapsed since the last saving attempt
	 *
	 * @return int|null null if there is no previous attempt
	 */
	public function last_attempt_seconds_ago() {
		if ( ! $this->last_attempt ) {
			return null;
		}

		return (new DateTime())->getTimestamp() - $this->last_attempt->getTimestamp();
	}
}
