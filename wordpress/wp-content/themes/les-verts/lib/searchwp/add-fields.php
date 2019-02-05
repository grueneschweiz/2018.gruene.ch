<?php

/**
 * add the necessary fields to the index
 */
add_filter( 'searchwp_custom_field_keys', function ( $keys ) {
	$keys[] = 'teaser';

	/**
	 * content blocks
	 */
	// in short block
	$keys[] = 'main_content_content_%_title'; // title
	$keys[] = 'main_content_content_%_content_%_text'; // text

	// text block
	$keys[] = 'main_content_content_%_text'; // text

	// quote block
	$keys[] = 'main_content_content_%_quote'; // quote
	$keys[] = 'main_content_content_%_role'; // role

	// quote block & person block
	$keys[] = 'main_content_content_%_person'; // person name

	/**
	 * events
	 */
	$keys[] = 'description'; // text

	return $keys;
} );

/**
 * make sure the relationship fields title is processed (instead of the id)
 */
add_filter( 'searchwp_custom_field_my_acf_relationship_field', function ( $existing_value, $the_post ) {
	if ( ! is_array( $existing_value ) ) {
		return $existing_value;
	}
	// We want to index Titles not IDs
	$content_to_index = '';

	// Iterate over stored ACF Relationship field and retrieve Title for each item
	foreach ( $existing_value as $related_post_data ) {
		if ( is_numeric( $related_post_data ) ) {
			$post_title       = get_the_title( absint( $related_post_data ) );
			$content_to_index .= $post_title . ' ';
		} else {
			$related_post_data = maybe_unserialize( $related_post_data );
			if ( is_array( $related_post_data ) && ! empty( $related_post_data ) ) {
				foreach ( $related_post_data as $related_post_id ) {
					$post_title       = get_the_title( absint( $related_post_id ) );
					$content_to_index .= $post_title . ' ';
				}
			}
		}
	}

	// Instead of indexing the original ACF field value (likely array of IDs)
	// we will instead be indexing a string of Titles for each chosen post
	return $content_to_index;
}, 10, 2 );
