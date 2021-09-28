<?php

/**
 * Use the 'archive-tribe_events.php' template file for event archives.
 */
add_filter( 'tribe_events_views_v2_use_wp_template_hierarchy', function ( $load, $template, $context, $query ) {
	return $query->is_archive() && strpos( $template, 'archive-tribe_events.php' );
}, 10, 4 );

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
		$default_home_url   = rtrim( pll_home_url( pll_default_language() ), '/' );
		$localized_home_url = rtrim( pll_home_url(), '/' );

		return str_replace( $default_home_url, $localized_home_url, $base_url );
	}

	return $base_url;
} );

/**
 * Fix wrong link from and for the events calendar 5.5.0.1
 * (maybe older version are affected too).
 */
add_filter( 'tribe_events_ugly_link', function ( $url ) {
	return str_replace( 'tribe_event_category', 'tribe_events_cat', $url );
} );


/**
 * Fix tribe events using the regular archive.php template instead of archive-tribe_events.php
 * if an event category was queried
 */
add_filter( 'template_include', function ( $template ) {
	if ( strpos( $template, 'archive.php' ) && get_query_var( 'tribe_events_cat', false ) ) {
		return str_replace( 'archive.php', 'archive-tribe_events.php', $template );
	}

	return $template;
} );
