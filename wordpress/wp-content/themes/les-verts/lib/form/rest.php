<?php

// see also ./public-api.php


namespace SUPT;

define( 'SUPT_FORM_API_V1_BASE', 'supt-form/v1' );
define( 'SUPT_FORM_API_ENDPOINT_NONCE', '/nonce' );
define( 'SUPT_FORM_API_ENDPOINT_PROGRESS', '/progress' );

/**
 * Register REST routes here
 */
add_action( 'rest_api_init', function () {
	/**
	 * Route to obtain form submission nonce
	 */
	register_rest_route(
		SUPT_FORM_API_V1_BASE,
		SUPT_FORM_API_ENDPOINT_NONCE,
		array(
			'methods'             => 'GET',
			'callback'            => 'supt_theme_form_create_nonce',
			'permission_callback' => '__return_true'
		) );

	/**
	 * Route to obtain submission count data
	 */
	register_rest_route(
		SUPT_FORM_API_V1_BASE,
		SUPT_FORM_API_ENDPOINT_PROGRESS . '/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => array( ProgressHelper::class, 'handle_api_request' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'validate_callback' => function ( $param ) {
						return is_numeric( $param ) && (int) $param >= 0;
					},
					'sanitize_callback' => 'absint'
				),
				'gm' => array(
					'validate_callback' => function ( $param ) {
						return $param === 'manual' || $param === 'adaptive';
					}
				),
				'g'  => array(
					'validate_callback' => function ( $param ) {
						return empty( $param ) || is_numeric( $param );
					},
					'sanitize_callback' => 'absint',
				),
				'o'  => array(
					'validate_callback' => function ( $param ) {
						return empty( $param ) || is_numeric( $param );
					},
					'sanitize_callback' => 'absint',
				),
				'l'  => array(
					'validate_callback' => function ( $param ) {
						return empty( $param ) || ( is_string( $param ) && strlen( $param ) <= 280 );
					},
				)
			),
		) );
} );

