<?php

/**
 *  Public API
 *  ==========
 *
 * This file exposes form function to use anywhere in your code
 */

/**
 * Get a form submission nonce
 *
 * @return string
 */
function supt_theme_form_create_nonce() {
	require_once __DIR__ . '/include/Nonce.php';
	return \SUPT\Nonce::create();
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
		$form = new \SUPT\FormModel( $form_id );
	} catch ( Exception $e ) {
		return -1;
	}

	return $form->get_submission_count();
}
