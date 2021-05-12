<?php


namespace SUPT;


class Limiter {
	const OPTION_BASE_KEY = FormType::MODEL_NAME . '-limiter-';

	/**
	 * This must be greater or equal to the greatest submission limit period!
	 *
	 * Period in seconds
	 */
	const LOG_KEEPING_PERIOD = 86400;

	/**
	 * Add always in descending order by period!
	 *
	 * Period in seconds
	 */
	const SUBMISSION_LIMITS_IP = array(
		array( 'attempts' => 300, 'period' => 86400 ),
		array( 'attempts' => 100, 'period' => 3600 ),
		array( 'attempts' => 20, 'period' => 60 ),
		array( 'attempts' => 3, 'period' => 1 ),
	);

	/**
	 * Add always in descending order by period!
	 *
	 * Period in seconds
	 */
	const SUBMISSION_LIMITS_IP_AGENT = array(
		array( 'attempts' => 20, 'period' => 3600 ),
		array( 'attempts' => 5, 'period' => 60 ),
		array( 'attempts' => 1, 'period' => 1 ),
	);

	/**
	 * The submitters ip address
	 *
	 * @var string
	 */
	private $ip;

	/**
	 * The submitters user agent
	 *
	 * @var string
	 */
	private $user_agent;

	/**
	 * Cache the attempts
	 *
	 * @var array (hash => timestamp)
	 */
	private $attempts;

	/**
	 * The wp option_name that holds the submissions
	 *
	 * @var string
	 */
	private $option_key;

	/**
	 * Limiter constructor.
	 */
	public function __construct( string $for ) {
		$this->ip         = $this->get_user_ip();
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->option_key = self::OPTION_BASE_KEY . $for;
	}

	/**
	 * Get visitor ip. If behind proxy, try to find real ip.
	 *
	 * @see https://stackoverflow.com/a/13646848
	 *
	 * @return string
	 */
	private function get_user_ip() {
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			if ( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) > 0 ) {
				$addr = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );

				return trim( $addr[0] );
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Log the attempt and do some housekeeping.
	 *
	 * Log the hashed ip as well as the hashed ip concatenated with the user agent.
	 * Remove old log entries.
	 */
	public function log_attempt() {
		$ip    = $this->hash( $this->ip );
		$ip_ua = $this->hash( $this->ip . $this->user_agent );

		$this->add( $ip );
		$this->add( $ip_ua );
		$this->remove_old();
		$this->save();
	}

	private function hash( $data ) {
		return wp_hash( $data );
	}

	private function add( $hash ) {
		$attempts = $this->get_attempts();

		if ( array_key_exists( $hash, $attempts ) ) {
			$this->attempts[ $hash ][] = time();
		} else {
			$this->attempts[ $hash ] = array( time() );
		}
	}

	private function get_attempts() {
		if ( empty( $this->attempts ) ) {
			$this->attempts = get_option( $this->option_key, array() );
		}

		return $this->attempts;
	}

	/**
	 * Remove old attempts from log.
	 *
	 * Deletes hash entries, where no new entry during the keeping period was made.
	 * This method ensures the log doesn't explode.
	 */
	private function remove_old() {
		if ( empty( $this->get_attempts() ) ) {
			return;
		}

		$keep_limit = time() - self::LOG_KEEPING_PERIOD;

		foreach ( $this->get_attempts() as $hash => $timestamps ) {
			// the newest is to old -> trash all
			if ( end( $timestamps ) < $keep_limit ) {
				unset( $this->attempts[ $hash ] );
			}
		}
	}

	private function save() {
		update_option( $this->option_key, $this->get_attempts() );
	}

	/**
	 * Check if the current request hasn't exceeded the limit.
	 *
	 * First check, if the ip is below the limit, then test for the ip & user
	 * agent combination. If either one exceeds the limit, the method returns
	 * false.
	 *
	 * @return bool true if below limit
	 */
	public function below_limit() {
		$ip = $this->hash( $this->ip );

		if ( $this->hash_below_limit( $ip, self::SUBMISSION_LIMITS_IP ) ) {
			$ip_ua = $this->hash( $this->ip . $this->user_agent );

			return $this->hash_below_limit( $ip_ua, self::SUBMISSION_LIMITS_IP_AGENT );
		}

		return false;
	}

	private function hash_below_limit( $hash, $limits ) {
		$all_attempts = $this->get_attempts();

		if ( ! array_key_exists( $hash, $all_attempts ) ) {
			return true;
		}

		$attempts = $all_attempts[ $hash ];

		foreach ( $limits as $limit ) {
			$allowed = $limit['attempts'];
			$period  = $limit['period'];

			$attempts = array_filter( $attempts, function ( $timestamp ) use ( $period ) {
				return $timestamp > ( time() - $period );
			} );

			if ( count( $attempts ) > $allowed ) {
				return false;
			}
		}

		return true;
	}
}
