<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

// Return 404
status_header( 404 );
nocache_headers();
include( get_query_template( '404' ) );
die();
