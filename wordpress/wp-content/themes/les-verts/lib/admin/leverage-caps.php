<?php

/**
 * Add caps to edit the menu and widgets for editors
 */
add_filter( 'user_has_cap', function ( $allcaps, $cap, $args, $user ) {
	// allow editors to manage menu and widgets
	if ( 'edit_theme_options' === $cap && in_array( 'editor', $user->roles ) ) {
		$allcaps['edit_theme_options'] = true;
	}

	return $allcaps;
}, 10, 4 );

/**
 * Add caps to add code for editors and admins
 *
 * Don't use the 'user_has_cap' hook from above, as this doesn't work on multisite.
 * @see https://kellenmace.com/add-unfiltered_html-capability-to-admins-or-editors-in-wordpress-multisite/
 */
add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id ) {
	// allow editors and admins to add code
	if ( 'unfiltered_html' === $cap && user_can( $user_id, 'editor' ) ) {
		$caps = array( 'unfiltered_html' );
	}

	return $caps;
}, 1, 3 );
