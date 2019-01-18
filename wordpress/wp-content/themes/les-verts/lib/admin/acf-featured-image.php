<?php

/**
 * Add the header image as a featured image.
 *
 * Yoast SEO then recognizes it as default share image.
 */
add_filter( 'acf/update_value/key=field_5b6c3dd87bf2a', function ( $value, $post_id ) {
	// this ensures it will also be deleted, if the header image is removed
	delete_post_thumbnail( $post_id );
	
	// add header image as post thumbnail
	if ( $value !== '' ) {
		set_post_thumbnail( $post_id, (int) $value );
	}
	
	return $value;
}, 10, 2 );
