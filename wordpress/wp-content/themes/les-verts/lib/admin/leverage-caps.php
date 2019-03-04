<?php

/**
 * Add caps to edit the menu and widgets for editors and to add code for editors and admins
 */
add_filter( 'user_has_cap', function ( $allcaps, $cap, $args, $user ) {
	// allow editors to manage menu and widgets
	if ( 'edit_theme_options' === $cap && in_array( 'editor', $user->roles ) ) {
		$allcaps['edit_theme_options'] = true;
	}

	// allow editors and admins to add code
	if ( 'unfiltered_html' === $cap
	     && ( in_array( 'editor', $user->roles ) || in_array( 'administrator', $user->roles ) ) ) {
		$allcaps['unfiltered_html'] = true;
	}

	return $allcaps;
}, 10, 4 );
