<?php

/**
 * Enable unfiltered_html capability for site admins and editors.
 *
 * @param  array $caps The user's capabilities.
 * @param  string $cap Capability name.
 * @param  int $user_id The user ID.
 *
 * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
 */
add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id ) {
	if ( 'unfiltered_html' === $cap
	     && ( user_can( $user_id, 'administrator' ) || user_can( $user_id, 'editor' ) )
	) {
		$caps = array( 'unfiltered_html' );
	}

	return $caps;
}, 1, 3 );
