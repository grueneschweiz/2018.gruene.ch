<?php


namespace SUPT;

/**
 * Data queue. Based on WordPress' options api.
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
	 */
	public function remove( int $index ) {
		$this->lock();
		$queue = $this->get_all();
		unset( $queue[ $index ] );
		$reindexed = array_values( $queue );
		$this->save( $reindexed );
		$this->unlock();
	}

	private function lock() {
		global $wpdb;
		$wpdb->query( "LOCK TABLES $wpdb->options WRITE" );
	}

	private function unlock() {
		global $wpdb;
		$wpdb->query( "UNLOCK TABLES" );
	}
}
