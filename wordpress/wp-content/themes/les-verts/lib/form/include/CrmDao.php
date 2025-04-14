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
	 * @param array $data
	 * @param ?int $id the id of the record to update
	 *
	 * @return array|mixed|object
	 * @throws Exception
	 */
	public function save( array $data, ?int $id = null ) {
		$crm_data = array();
		foreach ( $data as $key => $crm_field_data ) {
			$crm_data[ $key ] = array(
				'value' => $crm_field_data->get_value(),
				'mode'  => $crm_field_data->get_mode(),
			);
		}

		$url = $this->api_url . 'api/v1/member';
		if ( $id ) {
			$url .= "/$id";
		}
		$method = $id ? 'put' : 'post';
		$args   = array( 'body' => json_encode( $crm_data ) );
		$resp   = $this->request( $method, $url, $args );

		return json_decode( $resp->get_data(), true );
	}

	/**
	 * Send http request and validate it
	 *
	 * Retries with a new access token, if the current token is invalid.
	 * Throws exception, if the api doesn't return with a 2xx status code.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $args
	 *
	 * @return WP_HTTP_Requests_Response
	 * @throws Exception
	 */
	private function request( string $method, string $url, array $args = [] ) {
		$args['method'] = $method = strtoupper( $method );

		if ( ! isset( $args['timeout'] ) ) {
			$args['timeout'] = self::WP_REMOTE_TIMEOUT;
		}

		$response = wp_remote_request( $url, $this->add_headers( $args ) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$code          = $this->is_timeout_error( $error_message ) ? 408 : null;
			throw new Exception( "$method request to $url failed: $error_message.", $code );
		}

		/** @var WP_HTTP_Requests_Response $resp */
		$resp = $response['http_response'];
		if ( in_array( $resp->get_status(), array( 401, 403 ) ) ) {
			// if the token isn't valid, get a new one. if the new one is valid, retry
			if ( ! $this->is_token_valid() ) {
				$this->obtain_token( true );
				if ( $this->is_token_valid() ) {
					return $this->request( $method, $url, $args );
				}
			}
		}

		$status_code = $resp->get_status();
		if ( $status_code < 200 || $status_code >= 300 ) {
			$status_code = $this->is_timeout_error( $resp->get_data() ) ? 408 : $resp->get_status();

			$data_sent = print_r( $this->redactToken( $args ), true );
			throw new Exception( "$method request to $url failed. Crm returned status code: {$resp->get_status()}. Reason: {$resp->get_data()}.\n\nData sent: {$data_sent}", $status_code );
		}

		return $resp;
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
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
		);

		$args['headers'] = array_merge( $args['headers'], $headers );

		return $args;
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

	/**
	 * Censor Authorization header from given wp_remote_* args.
	 * Use for logging.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function redactToken( array $args ): array {
		if ( isset( $args['headers']['Authorization'] ) ) {
			$args['headers']['Authorization'] = 'REDACTED';
		}

		return $args;
	}

	/**
	 * Check if there is an entry in the crm that matches the given data.
	 *
	 * @link https://github.com/grueneschweiz/weblingservice/blob/master/docs/API.md#-find-matching-records
	 *
	 * @param $data
	 *
	 * @return array @see https://github.com/grueneschweiz/weblingservice/blob/master/docs/API.md#-find-matching-records
	 * @throws Exception
	 */
	public function match( $data ) {
		$crm_data = array();
		foreach ( $data as $key => $crm_field_data ) {
			$crm_data[ $key ] = array(
				'value' => $crm_field_data->get_value(),
			);
		}

		$args = array( 'body' => json_encode( $crm_data ) );
		$url  = $this->api_url . 'api/v1/member/match';
		$resp = $this->request( 'post', $url, $args );

		$body = json_decode( $resp->get_data(), true );

		if ( ! array_key_exists( 'status', $body ) ) {
			$body_string = print_r( $body, true );
			$data_sent   = print_r( $this->redactToken( $args ), true );
			throw new Exception( "Invalid response from $url endpoint of crm wrapper. Response: {$body_string}. \n\nData sent: {$data_sent}" );
		}

		return $body;
	}

	/**
	 * Find the main member in CRM from the given member id
	 *
	 * @link https://github.com/grueneschweiz/weblingservice/blob/master/docs/API.md#find-main-record
	 *
	 * @param int $member_id
	 *
	 * @return array @see https://github.com/grueneschweiz/weblingservice/blob/master/docs/API.md#find-main-record
	 * @throws Exception
	 */
	public function main( int $member_id ) {
		$url  = $this->api_url . "api/v1/member/$member_id/main";
		$resp = $this->request( 'get', $url );

		$body = json_decode( $resp->get_data(), true );

		if ( ! array_key_exists( 'id', $body ) ) {
			$body_string = print_r( $body, true );
			throw new Exception( "Invalid response from $url endpoint of crm wrapper. Response: {$body_string}." );
		}

		return $body;
	}
}
