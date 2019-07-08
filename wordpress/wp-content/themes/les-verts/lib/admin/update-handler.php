<?php

add_action( 'after_setup_theme', function () {
	$current_version = get_theme_mod( 'version_number', null );

	// if everything is up to date stop here
	if ( THEME_VERSION == $current_version ) {
		return;
	}

	// run the upgrade routine for versions smaller 0.6.0
	if ( - 1 == version_compare( $current_version, '0.6.0' ) ) {
		remove_all_blogs_synchronized_acf();
	}

	// set the current version number
	set_theme_mod( 'version_number', THEME_VERSION );
}, 0 );

/**
 * Multisite proof wrapper to remove all acf definitions in the database.
 *
 * @see remove_all_blogs_synchronized_acf()
 */
function remove_all_blogs_synchronized_acf() {
	if ( is_multisite() ) {
		$current_blog_id = get_current_blog_id();
		foreach ( get_sites() as $blog ) {
			switch_to_blog( $blog->blog_id );
			remove_single_blog_synchronized_acf();
		}
		switch_to_blog( $current_blog_id );
	} else {
		remove_single_blog_synchronized_acf();
	}
}

/**
 * Remove all ACF definitions that were synchronized into the database.
 *
 * This brings consistency and performance. And it assures changes from
 * acf JSON take effect.
 */
function remove_single_blog_synchronized_acf() {
	global $wpdb;

	$posts = $wpdb->get_results( 'SELECT id FROM ' . $wpdb->posts . ' WHERE post_type IN ("acf-field", "acf-field-group")' );

	if ( empty( $posts ) ) {
		return;
	}

	$ids = implode( ',', array_column( $posts, 'id' ) );

	$meta = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ({$ids})" );
	if ( false !== $meta ) {
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE ID IN ({$ids})" );
	}
}
