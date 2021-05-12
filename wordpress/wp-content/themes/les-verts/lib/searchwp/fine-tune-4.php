<?php

use SearchWP\Mod;

/**
 * Hide admin bar button
 */
add_filter( 'searchwp\admin_bar', '__return_false' );

/**
 * Order search results by date
 */
add_filter( 'searchwp\query\mods', function ( $mods ) {
	// Build Mod to sort results by date
	$mod = new Mod();
	$mod->order_by( "s1.post_date", 'DESC', 9 );

	$mods[] = $mod;

	return $mods;
} );

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
 * Tell SearchWP to index the persons name from a Relationship ACF field instead of the post ID
 *
 * @link https://searchwp.com/documentation/knowledge-base/process-acf-fields-to-index-expected-data/
 */
add_filter( 'searchwp\source\post\attributes\meta', function ( $meta_value, $args ) {
	$acf_field_name = 'person'; // The ACF Relationship field name.

	// If we're not indexing the Read Next field, return the existing meta value.
	// This logic also works for sub-fields of an ACF field as well.
	if ( $acf_field_name !== substr( $args['meta_key'], strlen( $args['meta_key'] ) - strlen( $acf_field_name ) ) ) {
		return $meta_value;
	}

	// We're going to store all of our Titles together as one string for SearchWP to index.
	$content_to_index = '';
	if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
		foreach ( $meta_value as $acf_relationship_item ) {
			if ( is_numeric( $acf_relationship_item ) ) {
				// ACF stores only the post ID but we want the name.
				$acf_relationship_item = absint( $acf_relationship_item );
				$content_to_index      .= ' ' . get_field( 'full_name', $acf_relationship_item );
			}
		}
	}

	// Return the string of content we want to index instead of the data stored by ACF.
	return $content_to_index;
}, 20, 2 );
