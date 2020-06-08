<?php

/**
 * Hide admin bar button
 */

use SearchWP\Mod;

add_filter( 'searchwp\admin_bar', '__return_false' );

/**
 * Order search results by date
 */
add_filter( 'searchwp\query\mods', function ( $mods ) {
	global $wpdb;

	// Build Mod to sort results by date
	$mod = new Mod();
	$mod->order_by( "{$wpdb->posts}.post_date", 'DESC' );

	$mods[] = $mod;

	return $mods;
}, 20 );

/**
 * Load all posts
 *
 * Else we can't grab the categories and tags
 */
add_filter( 'searchwp\query\args', function ( $args ) {
	$args['per_page'] = - 1;

	return $args;
} );

/**
 * Filter search results by category
 */
add_filter( 'searchwp\query\results', function ( $results ) {
	// Bail early, if no category filters are provided
	if ( empty( $_GET['cat'] ) ) {
		return $results;
	}

	$category_ids = array_map( 'absint', explode( ',', $_GET['cat'] ) );

	foreach ( $results as $key => &$result ) {
		foreach ( $category_ids as $category ) {
			if ( ! has_category( $category, $result ) ) {
				unset( $results[ $key ] );
				continue 2;
			}
		}
	}

	return $results;
}, 20, 1 );

/**
 * Add person name from people blocks to search results
 */
// todo
