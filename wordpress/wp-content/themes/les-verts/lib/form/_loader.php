<?php

use SUPT\Nonce;

require_once __DIR__ . '/include/Util.php';
require_once __DIR__ . '/include/FormType.php';
require_once __DIR__ . '/settings-page.php';
require_once __DIR__ . '/submission.php';

// Load the Custom post Type
add_action( 'init', array( '\SUPT\FormType', 'register_type' ) );
\SUPT\FormType::register_actions_filters();

new SUPT\FormSubmission();

function supt_theme_form_create_nonce() {
	require_once __DIR__ . '/include/Nonce.php';
	return Nonce::create();
}
