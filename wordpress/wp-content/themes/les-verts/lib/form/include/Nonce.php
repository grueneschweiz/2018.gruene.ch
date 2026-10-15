<?php


namespace SUPT;

/**
 * Real nonces.
 *
 * Opposed to the WordPress built-in nonces, those nonces are limited to single
 * use and are deleted once verified. The time to live is limited by the TTL
 * constant of this class.
 *
 * @package SUPT
 */
class Nonce {
	const TTL = 3600; // 1h
	const HASH_ALGO = 'sha256'; // the fallback is sha1
	const OPTION_PREFIX = FormType::MODEL_NAME . '_nonce_';
	const CRON_HOOK_REMOVE_EXPIRED = 'supt_form_remove_expired_nonces';
	const CRON_REMOVE_EXPIRED_INTERVAL = 'hourly';

	/**
	 * Return a nonce ready to consume
	 *
	 * @return string the nonce
	 */
	public static function create() {
		self::schedule_cron();

		return self::get_nonce();
	}

	/**
	 * Ensure the house keeping cron is scheduled
	 */
	private static function schedule_cron() {
		Util::add_cron( self::CRON_HOOK_REMOVE_EXPIRED, time(), self::CRON_REMOVE_EXPIRED_INTERVAL );
	}

	private static function get_nonce() {
		$nonce = self::hash( random_bytes( 16 ) ); // 128 bit entropy

		self::add( $nonce );

		return $nonce;
	}

	private static function hash( $data ) {
		$alogs = hash_algos();

		if ( array_key_exists( self::HASH_ALGO, $alogs ) ) {
			$hash = hash( self::HASH_ALGO, $data );
		} else {
			$hash = sha1( $data );
		}

		return $hash;
	}

	private static function add( $nonce ) {
		$name        = self::get_option_name( $nonce );
		$valid_until = time() + self::TTL;
		add_option( $name, $valid_until, '', 'no' );
	}

	private static function get_option_name( $nonce ) {
		return self::OPTION_PREFIX . $nonce;
	}

	/**
	 * Verify and invalidate nonce.
	 *
	 * A valid nonce returns true on the first call only. Once the nonce is
	 * consumed it is immediately invalidated.
	 *
	 * @param string $nonce
	 * @param int $min_age_seconds Minimum elapsed seconds required since nonce creation
	 *
	 * @return bool
	 */
	public static function consume( $nonce, int $min_age_seconds = 0 ) {
		if ( empty( $nonce ) || ! is_string( $nonce ) ) {
			return false;
		}

		$valid = self::verify( $nonce, $min_age_seconds );
		self::remove( $nonce );

		return $valid;
	}

	private static function verify( $nonce, int $min_age_seconds = 0 ) {
		$name        = self::get_option_name( $nonce );
		$valid_until = (int) get_option( $name, 0 );

		if ( $valid_until <= 0 ) {
			return false;
		}

		$now = time();
		if ( $valid_until <= $now ) {
			return false;
		}

		if ( $min_age_seconds > 0 ) {
			$created_at = $valid_until - self::TTL;
			if ( ( $now - $created_at ) < $min_age_seconds ) {
				return false;
			}
		}

		return true;
	}

	private static function remove( $nonce ) {
		$name = self::get_option_name( $nonce );
		delete_option( $name );
	}

	/**
	 * Remove expired nonces.
	 */
	public static function remove_expired() {
		global $wpdb;

		$nonces = $wpdb->get_results(
			"SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '" . self::OPTION_PREFIX . "%'"
		);

		if ( empty( $nonces ) ) {
			return;
		}

		$now = time();
		foreach ( $nonces as $nonce ) {
			if ( absint( $nonce->option_value ) < $now ) {
				delete_option( $nonce->option_name );
			}
		}
	}
}
