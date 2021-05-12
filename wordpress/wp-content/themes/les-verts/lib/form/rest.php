<?php

// see also ./public-api.php


namespace SUPT;

define( 'SUPT_FORM_API_V1_BASE', 'supt-form/v1' );
define( 'SUPT_FORM_API_ENDPOINT_NONCE', '/nonce' );

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
} );

