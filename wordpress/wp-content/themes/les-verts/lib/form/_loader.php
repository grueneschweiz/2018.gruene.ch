<?php

use SUPT\FormType;

require_once __DIR__ . '/include/Util.php';
require_once __DIR__ . '/include/FormType.php';
require_once __DIR__ . '/settings-page.php';
require_once __DIR__ . '/submission.php';
require_once __DIR__ . '/include/ProgressHelper.php';
require_once __DIR__ . '/rest.php';
require_once __DIR__ . '/public-api.php';

// Load the Custom post Type
add_action( 'init', array( '\SUPT\FormType', 'register_type' ) );
FormType::register_actions_filters();

new SUPT\FormSubmission();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/include/cli/FormCommand.php';
}

// Remove cron jobs on deactivation of the theme
add_action( 'switch_theme', array( SUPT\Util::class, 'remove_all_crons' ) );
