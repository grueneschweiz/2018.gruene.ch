<?php


namespace SUPT;

/**
 * Data queue that writes changes immediately but caches reads. Based on WordPress' options api.
 *
 * @package SUPT
 */
class QueueDao {
	/**
	 * The key under witch the queue is stored
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The queue items
	 *
	 * @var array
	 */
	private $items;

	/**
	 * CronQueue constructor.
	 *
	 * @param string $key the key of the queue
	 */
	public function __construct( $key ) {
		$this->key = FormType::MODEL_NAME . '_' . $key . '_queue';
	}

	/**
	 * Add item to the end of the queue
	 *
	 * @param mixed $item
	 */
	public function push( $item ) {
		$queue = $this->get_all();
		if ( ! in_array( $item, $queue ) ) {
			$queue[] = $item;
			$this->save( $queue );
		}
	}

	/**
	 * Return the first item from the queue and remove it
	 *
	 * @return mixed|null null if queue is empty
	 */
	public function pop() {
		$queue = $this->get_all();
		$item  = array_shift( $queue );
		$this->save( $queue );

		return $item;
	}

	/**
	 * The number of items in the queue
	 *
	 * @return int
	 */
	public function length() {
		return count( $this->get_all() );
	}

	/**
	 * True if there are some items in the queue
	 *
	 * @return bool
	 */
	public function has_items(): bool {
		return 0 < $this->length();
	}

	/**
	 * Get the cached queue items
	 *
	 * @return array
	 */
	private function get_all() {
		if ( null === $this->items ) {
			$this->items = get_option( $this->key, array() );
		}

		return $this->items;
	}

	/**
	 * Store the given queue in the database and update the cache
	 *
	 * @param array $queue
	 */
	private function save( $queue ) {
		update_option( $this->key, $queue, false );
		$this->items = $queue;
	}
}
