<?php


namespace SUPT;

/**
 * Session based nonces.
 *
 * Opposed to the WordPress built-in nonces, those nonces are limited to single
 * use and are deleted once verified. The time to live is limited by either the
 * session life time or the TTL constant of this class (whatever expires first).
 *
 * @package SUPT
 */
class Nonce {
	const TTL = 3600; // 1h
	const HASH_ALGO = 'sha256'; // the fallback is sha1
	const SESSION_KEY = FormType::MODEL_NAME . '_nonces';

	/**
	 * The nonce
	 *
	 * @var string
	 */
	private $nonce;

	/**
	 * Timestamp of expiration
	 *
	 * @var int unix timestamp
	 */
	private $expire;

	private function __construct() {
		$this->nonce  = $this->hash( random_bytes( 16 ) ); // 128 bit entropy
		$this->expire = time() + self::TTL;
		$this->store();
	}

	private function hash( $data ) {
		$alogs = hash_algos();

		if ( array_key_exists( self::HASH_ALGO, $alogs ) ) {
			$hash = hash( self::HASH_ALGO, $data );
		} else {
			$hash = sha1( $data );
		}

		return $hash;
	}

	private function store() {
		if ( ! array_key_exists( self::SESSION_KEY, $_SESSION ) || empty( $_SESSION[ self::SESSION_KEY ] ) ) {
			$_SESSION[ self::SESSION_KEY ] = array();
		}

		$nonces = $_SESSION[ self::SESSION_KEY ];

		$_SESSION[ self::SESSION_KEY ] = array_merge( $nonces, array( $this->nonce => $this ) );
	}

	/**
	 * Return a nonce ready to consume
	 *
	 * @return string the nonce
	 */
	public static function create() {
		$nonce = new self();

		return $nonce->get_nonce();
	}

	private function get_nonce() {
		return $this->nonce;
	}

	/**
	 * Verify and invalidate nonce.
	 *
	 * A valid nonce returns true on the first call only. Once the nonce is
	 * consumed it is immediately invalidated.
	 *
	 * @param string $nonce
	 *
	 * @return bool
	 */
	public static function consume( $nonce ) {
		session_start();

		if ( ! array_key_exists( self::SESSION_KEY, $_SESSION )
		     || empty( $_SESSION[ self::SESSION_KEY ] ) ) {
			return false;
		}

		if ( ! array_key_exists( $nonce, $_SESSION[ self::SESSION_KEY ] ) ) {
			return false;
		}

		/** @var Nonce $s_nonce */
		$s_nonce = $_SESSION[ self::SESSION_KEY ][ $nonce ];

		if ( time() > $s_nonce->get_expire() ) {
			return false;
		}

		unset( $_SESSION[ self::SESSION_KEY ][ $nonce ] );

		return true;
	}

	private function get_expire() {
		return $this->expire;
	}
}
