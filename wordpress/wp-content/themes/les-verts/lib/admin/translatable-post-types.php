<?php

// add post types to translatable list of polylang
add_filter( 'pll_get_post_types', function( $post_types, $is_settings ) {
	foreach ( [ 'alert', 'position' /* >> ADD HERE YOUR MODEL NAMES << */ ] as $t ) {
		if ( $is_settings ) {
			// hides $t from the list of custom post types in Polylang settings
			unset( $post_types[$t] );
		} else {
			// enables language and translation management for $t
			$post_types[$t] = $t;
		}
	}
	return $post_types;
}, 10, 2 );

// add taxonomies to translatable list of polylang
add_filter( 'pll_get_taxonomies', function( $taxonomies, $is_settings ) {
	foreach ( [ 'post_tax_country', 'post_tax_cause' /* >> ADD HERE YOUR TAXONOMIES << */ ] as $t ) {
		if ( $is_settings ) {
			// hides $t from the list of custom post types in Polylang settings
			unset( $taxonomies[$t] );
		} else {
			// enables language and translation management for $t
			$taxonomies[$t] = $t;
		}
	}
	return $taxonomies;
}, 10, 2 );
