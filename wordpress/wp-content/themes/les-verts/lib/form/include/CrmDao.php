<?php

namespace SUPT;

use Exception;
use WP_HTTP_Requests_Response;
use function get_field;

/**
 * lock out script kiddies: die on direct call
 */
defined( 'ABSPATH' ) or die( 'Better secure than sorry!' );


/**
 * Class Crm_Dao
 *
 * @package SUPT
 */
class CrmDao {
	const OPTION_KEY_TOKEN = 'supt_form_crm_token';
	const WP_REMOTE_TIMEOUT = 30; //seconds

	/**
	 * The api URL with a trailing slash
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * The token data
	 *
	 * @var array [token => bearer token, renew => timestamp of renewal time]
	 */
	private $token;

	/**
	 * Crm_Dao constructor.
	 * @throws Exception
	 */
	public function __construct() {
		$this->api_url = self::get_api_url();

		$this->obtain_token();
	}

	/**
	 * Check if there is an api url configured
	 *
	 * @return bool
	 */
	public static function has_api_url() {
		return ! empty( self::get_api_url() );
	}

	/**
	 * The api url
	 *
	 * @return string
	 */
	private static function get_api_url() {
		$url = get_field( 'api_url', 'option' );

		return $url ? trailingslashit( $url ) : '';
	}

	/**
	 * Send the data to the crm's api
	 *
	 * @param $data
	 *
	 * @return array|mixed|object
	 * @throws Exception
	 */
	public function save( $data ) {
		$crm_data = array();
		foreach ( $data as $key => $crm_field_data ) {
			$crm_data[ $key ] = array(
				'value' => $crm_field_data->get_value(),
				'mode'  => $crm_field_data->get_mode(),
			);
		}

		$args     = array( 'body' => json_encode( $crm_data ), 'timeout' => self::WP_REMOTE_TIMEOUT );
		$response = wp_remote_post( $this->api_url . 'api/v1/member', $this->add_auth_header( $args ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			throw new Exception( "Could save member to crm: $error_message" );
		}

		/** @var WP_HTTP_Requests_Response $resp */
		$resp = $response['http_response'];
		if ( in_array( $resp->get_status(), array( 401, 403 ) ) ) {
			// if the token isn't valid, get a new one. if the new one is valid, retry to save
			if ( ! $this->is_token_valid() ) {
				$this->obtain_token( true );
				if ( $this->is_token_valid() ) {
					return $this->save( $data );
				}
			}
		}

		if ( $resp->get_status() !== 201 ) {
			$data_sent = print_r( $crm_data, true );
			throw new Exception( "Could save member to crm. Crm returned status code: {$resp->get_status()}. Reason: {$resp->get_data()}.\n\nData sent: {$data_sent}", $resp->get_status() );
		}

		return json_decode( $resp->get_data() );
	}

	/**
	 * Get valid token, either from local storage or a new one from the oAuth server
	 *
	 * @param bool $force refresh token
	 *
	 * @throws Exception
	 */
	private function obtain_token( $force = false ) {
		$this->token = get_option( self::OPTION_KEY_TOKEN, null );

		if ( ! $this->token || $force ) {
			$data = array(
				'body'    => array(
					'grant_type'    => 'client_credentials',
					'client_id'     => get_field( 'client_id', 'option' ),
					'client_secret' => get_field( 'client_secret', 'option' ),
					'scope'         => ''
				),
				'timeout' => self::WP_REMOTE_TIMEOUT,
			);

			$response = wp_remote_post( $this->api_url . 'oauth/token', $data );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				throw new Exception( "Could not obtain oAuth token from crm: $error_message" );
			}

			/** @var WP_HTTP_Requests_Response $resp */
			$resp = $response['http_response'];
			if ( $resp->get_status() !== 200 ) {
				throw new Exception( "Could not obtain oAuth token from crm. Crm returned status code: {$resp->get_status()}" );
			}

			$token_data = json_decode( $resp->get_data() );

			$token = array(
				// expire token after half of his validity period
				'renew' => time() + ( $token_data->expires_in / 2 ),
				'token' => $token_data->access_token,
			);

			$this->token = $token;
			update_option( self::OPTION_KEY_TOKEN, $token, false );
		}
	}

	/**
	 * Test, if the token is valid
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function is_token_valid() {
		// missing or incomplete token data
		if ( empty( $this->token ) || ! isset( $this->token['renew'] ) || ! isset( $this->token['token'] ) ) {
			return false;
		}

		// token expired (= more than half of it's validity period has passed)
		if ( time() > $this->token['renew'] ) {
			return false;
		}

		$response = wp_remote_get( $this->api_url . 'api/v1/auth', $this->add_auth_header( array( 'timeout' => self::WP_REMOTE_TIMEOUT ) ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			throw new Exception( "Could not validate oAuth token from crm: $error_message" );
		}

		/** @var WP_HTTP_Requests_Response $resp */
		$resp = $response['http_response'];

		return 200 === $resp->get_status();
	}

	/**
	 * Add the authorization header to the given wp_remote_METHOD args
	 *
	 * @param array $args of wp_remote_METHOD
	 *
	 * @return array
	 */
	private function add_auth_header( $args = array() ) {
		$bearer_token = "Bearer {$this->token['token']}";

		if ( ! array_key_exists( 'headers', $args ) ) {
			$args['headers'] = array();
		}

		$args['headers'] = array_merge( $args['headers'], array( 'Authorization' => $bearer_token ) );

		return $args;
	}
}
