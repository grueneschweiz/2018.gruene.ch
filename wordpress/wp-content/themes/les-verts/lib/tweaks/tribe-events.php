<?php

/**
 * Force ugly links for tribe events as the pretty permalinks
 * were constantly buggy, especially in combination with polylang.
 */
add_filter( 'tribe_events_force_ugly_link', '__return_true' );

/**
 * Force ugly links: To really get the ugly links, we do also have to disable url rewriting.
 */
add_filter('tribe_events_register_event_cat_type_args', function($args) {
	$args['rewrite'] = false;
	return $args;
});

/**
 * Set the correct base url, as it does not in every case work properly with polylang.
 */
add_filter( 'tribe_events_ugly_link_baseurl', function ( $base_url ) {
	if ( function_exists( 'pll_home_url' ) ) {
		return pll_home_url();
	}

	return $base_url;
} );

/**
 * Fix wrong link from and for the events calendar 5.5.0.1
 * (maybe older version are affected too).
 */
add_filter( 'tribe_events_ugly_link', function( $url ) {
	return str_replace('tribe_event_category', 'tribe_events_cat', $url);
} );
