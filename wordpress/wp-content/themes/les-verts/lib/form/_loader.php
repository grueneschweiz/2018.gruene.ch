<?php
// Helper
require_once __DIR__ . '/helpers/send-email.php';
require_once __DIR__ . '/helpers/parse-field-choices.php';
require_once __DIR__ . '/helpers/sanitize-with-underscore.php';

require_once __DIR__ . '/FormType.php';
require_once __DIR__ . '/settings-page.php';
require_once __DIR__ . '/submission.php';

// Load the Custom post Tyle
add_action( 'init', array( '\SUPT\FormType', 'register_type' ) );

new FormSubmission();
