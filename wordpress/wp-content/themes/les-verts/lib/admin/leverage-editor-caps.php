<?php

/**
 * Add caps to edit the menu and widgets for editors
 */
add_filter( 'user_has_cap', function ( $allcaps, $cap, $args, $user ) {
	if ( ! 'edit_theme_options' === $cap && in_array( 'editor', $user->roles ) ) {
		return $allcaps;
	}

	$allcaps['edit_theme_options'] = true;

	return $allcaps;
}, 10, 4 );

