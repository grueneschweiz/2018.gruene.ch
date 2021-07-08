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
	const WP_REMOTE_TIMEOUT = 45; //seconds

	const MATCH_NONE = 'no_match';
	const MATCH_EXACT = 'match';
	const MATCH_AMBIGUOUS = 'ambiguous';
	const MATCH_MULTIPLE = 'multiple';

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
	 * The api url
	 *
	 * @return string
	 */
	private static function get_api_url() {
		$url = get_field( 'api_url', 'option' );

		return $url ? trailingslashit( $url ) : '';
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
				'headers' => array(
					'Accept' => 'application/json',
				)
			);

			$response = wp_remote_post( $this->api_url . 'oauth/token', $data );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				throw new Exception( get_home_url() . ": Could not obtain oAuth token from crm: $error_message" );
			}

			/** @var WP_HTTP_Requests_Response $resp */
			$resp = $response['http_response'];
			if ( $resp->get_status() !== 200 ) {
				throw new Exception( get_home_url() . ": Could not obtain oAuth token from crm. Crm returned status code: {$resp->get_status()}" );
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
	 * Check if there is an api url configured
	 *
	 * @return bool
	 */
	public static function has_api_url() {
		return ! empty( self::get_api_url() );
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
		$response = wp_remote_post( $this->api_url . 'api/v1/member', $this->add_headers( $args ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			if ( $this->is_timeout_error( $error_message ) ) {
				throw new Exception( "Could save member to crm: $error_message.", 408 );
			} else {
				throw new Exception( "Could save member to crm: $error_message." );
			}
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
			$status_code = $this->is_timeout_error( $resp->get_data() ) ? 408 : $resp->get_status();

			$data_sent = print_r( $crm_data, true );
			throw new Exception( "Could save member to crm. Crm returned status code: {$resp->get_status()}. Reason: {$resp->get_data()}.\n\nData sent: {$data_sent}", $status_code );
		}

		return json_decode( $resp->get_data(), true );
	}

	/**
	 * Check if there is an entry in the crm that matches the given data.
	 *
	 * @link https://github.com/grueneschweiz/weblingservice/blob/master/docs/API.md#-find-matching-records
	 *
	 * @param $data
	 *
	 * @return string no_match|match|ambiguous|multiple -> use MATCH_* class constants
	 * @throws Exception
	 */
	public function match( $data ) {
		$crm_data = array();
		foreach ( $data as $key => $crm_field_data ) {
			$crm_data[ $key ] = array(
				'value' => $crm_field_data->get_value(),
			);
		}

		$args     = array( 'body' => json_encode( $crm_data ), 'timeout' => self::WP_REMOTE_TIMEOUT );
		$response = wp_remote_post( $this->api_url . 'api/v1/member/match', $this->add_headers( $args ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			if ( $this->is_timeout_error( $error_message ) ) {
				throw new Exception( "Could match member in crm: $error_message.", 408 );
			} else {
				throw new Exception( "Could match member in crm: $error_message." );
			}
		}

		/** @var WP_HTTP_Requests_Response $resp */
		$resp = $response['http_response'];
		if ( in_array( $resp->get_status(), array( 401, 403 ) ) ) {
			// if the token isn't valid, get a new one. if the new one is valid, retry to save
			if ( ! $this->is_token_valid() ) {
				$this->obtain_token( true );
				if ( $this->is_token_valid() ) {
					return $this->match( $data );
				}
			}
		}

		if ( $resp->get_status() !== 200 ) {
			$status_code = $this->is_timeout_error( $resp->get_data() ) ? 408 : $resp->get_status();

			$data_sent = print_r( $crm_data, true );
			throw new Exception( "Could match member in crm. Crm returned status code: {$resp->get_status()}. Reason: {$resp->get_data()}.\n\nData sent: {$data_sent}", $status_code );
		}

		$body = json_decode( $resp->get_data(), true );

		if ( ! array_key_exists( 'status', $body ) ) {
			$body_string = print_r( $body, true );
			$data_sent   = print_r( $crm_data, true );
			throw new Exception( "Invalid response from member match endpoint of crm wrapper. Response: {$body_string}. \n\nData sent: {$data_sent}" );
		}

		return $body['status'];
	}

	/**
	 * Checks if the given error message contains 'Operation timed out after'
	 *
	 * @param string $error_message
	 *
	 * @return bool
	 */
	private function is_timeout_error( $error_message ) {
		return false !== strpos( $error_message, 'Operation timed out after' );
	}

	/**
	 * Add the authorization header to the given wp_remote_METHOD args
	 *
	 * @param array $args of wp_remote_METHOD
	 *
	 * @return array
	 */
	private function add_headers( $args = array() ) {
		$bearer_token = "Bearer {$this->token['token']}";

		if ( ! array_key_exists( 'headers', $args ) ) {
			$args['headers'] = array();
		}

		$headers = array(
			'Authorization' => $bearer_token,
			'Accept'        => 'application/json'
		);

		$args['headers'] = array_merge( $args['headers'], $headers );

		return $args;
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

		$response = wp_remote_get( $this->api_url . 'api/v1/auth', $this->add_headers( array( 'timeout' => self::WP_REMOTE_TIMEOUT ) ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			throw new Exception( "Could not validate oAuth token from crm: $error_message" );
		}

		/** @var WP_HTTP_Requests_Response $resp */
		$resp = $response['http_response'];

		return 200 === $resp->get_status();
	}
}
