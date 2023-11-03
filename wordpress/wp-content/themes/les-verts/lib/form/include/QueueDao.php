<?php


namespace SUPT;

use Exception;

/**
 * Data queue. Based on WordPress' options api.
 *
 * @package SUPT
 */
class QueueDao {
	/**
	 * Time to wait to acquire a lock.
	 *
	 * Should be long enough to survive the sending time of a mail, but
	 * be short enough to not run into PHPs max_execution_time timeout.
	 */
	const LOCK_ACQUIRE_TIMEOUT_SECONDS = 15;

	/**
	 * The key under witch the queue is stored
	 *
	 * @var string
	 */
	private $key;

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
	 *
	 * @throws Exception
	 */
	public function push( $item ) {
		$this->lock();
		$queue = $this->get_all();
		if ( ! in_array( $item, $queue, true ) ) {
			$queue[] = $item;
			$this->save( $queue );
		}
		$this->unlock();
	}

	/**
	 * Return the first item from the queue and remove it
	 *
	 * @return mixed|null null if queue is empty
	 *
	 * @throws Exception
	 */
	public function pop() {
		$this->lock();
		$queue = $this->get_all();
		$item  = array_shift( $queue );
		$this->save( $queue );
		$this->unlock();

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
	 * Get the queue items
	 *
	 * @return array
	 */
	public function get_all() {
		return get_option( $this->key, array() );
	}

	/**
	 * Store the given queue in the database and update the cache
	 *
	 * @param array $queue
	 */
	private function save( $queue ) {
		update_option( $this->key, $queue, false );
	}

	/**
	 * Remove all items from queue
	 */
	public function clear() {
		$this->save( array() );
	}

	/**
	 * Remove item with given index from queue
	 *
	 * @param int $index
	 *
	 * @throws Exception
	 */
	public function remove( int $index ) {
		$this->lock();
		$queue = $this->get_all();
		unset( $queue[ $index ] );
		$reindexed = array_values( $queue );
		$this->save( $reindexed );
		$this->unlock();
	}

	/**
	 * Remove elements from queue, that don't satisfy the filter callback.
	 *
	 * @param callable $callback receives a queue item as only argument,
	 *                           must return a boolean. False will remove
	 *                           the item, true will keep it.
	 *
	 * @return int The number of elements removed
	 *
	 * @throws Exception
	 */
	public function filter( callable $callback ): int {
		$this->lock();
		$queue     = $this->get_all();
		$lenBefore = count( $queue );
		$queue     = array_filter( $queue, $callback );
		$lenAfter  = count( $queue );
		$reindexed = array_values( $queue );
		$this->save( $reindexed );
		$this->unlock();

		return $lenBefore - $lenAfter;
	}

	/**
	 * @throws Exception
	 */
	private function lock() {
		global $wpdb;
		$lock    = $this->get_lock_name();
		$timeout = self::LOCK_ACQUIRE_TIMEOUT_SECONDS;
		$result  = $wpdb->get_var( "SELECT GET_LOCK('$lock', $timeout);" );
		if ( $result !== '1' ) {
			throw new Exception( "Failed to acquire DB lock \"$lock\": $result" );
		}
	}

	/**
	 * @throws Exception
	 */
	private function unlock() {
		global $wpdb;
		$lock   = $this->get_lock_name();
		$result = $wpdb->get_var( "SELECT RELEASE_LOCK('$lock');" );
		if ( $result !== '1' ) {
			throw new Exception( "Failed to release DB lock \"$lock\": $result" );
		}
	}

	private function get_lock_name(): string {
		global $wpdb;

		return DB_NAME . '.' . $wpdb->postmeta . '.' . $this->key;
	}
}
