<?php
// Helper
require_once __DIR__ . '/helpers/send-email.php';
require_once __DIR__ . '/helpers/slugify.php';

require_once __DIR__ . '/FormType.php';
require_once __DIR__ . '/settings-page.php';
require_once __DIR__ . '/submission.php';

// Load the Custom post Tyle
add_action( 'init', array( '\SUPT\FormType', 'register_type' ) );

new FormSubmission();
