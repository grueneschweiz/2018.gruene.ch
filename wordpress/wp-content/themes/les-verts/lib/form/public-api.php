<?php

/**
 *  Public API
 *  ==========
 *
 * This file exposes form function to use anywhere in your code
 */

use SUPT\FormModel;
use SUPT\Nonce;

/**
 * Get a form submission nonce
 *
 * @return string
 */
function supt_theme_form_create_nonce() {
	// disable litespeed caching
	do_action( 'litespeed_control_set_nocache' );

	// disable wp super cache
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	require_once __DIR__ . '/include/Nonce.php';

	return Nonce::create();
}

/**
 * Get the number of form submissions
 *
 * @param int $form_id
 *
 * @return int
 */
function supt_theme_form_submission_count($form_id) {
	require_once __DIR__ . '/include/FormModel.php';

	try {
		$form = new FormModel( $form_id );
	} catch ( Exception $e ) {
		return - 1;
	}

	return $form->get_submission_count();
}


function supt_get_nonce_api_url(): string {
	return get_rest_url( null, SUPT_FORM_API_V1_BASE . SUPT_FORM_API_ENDPOINT_NONCE );
}
