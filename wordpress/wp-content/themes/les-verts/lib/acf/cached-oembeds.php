<?php

namespace SUPT;

/**
 * Disables acf_field_oembed::format_value()
 */
add_action( 'acf/init', function () {
	$field_type = acf_get_field_type( 'oembed' );
	remove_filter( 'acf/format_value/type=oembed', [ $field_type, 'format_value' ] );
}, 1 );

/**
 * Fetch the cached oEmbed HTML; Replaces the original method
 *
 * This aims not only to get more speed, but also ensures the
 * 'embed_oembed_html' filter is called (used by the embed
 * privacy plugin).
 */
add_filter( 'acf/format_value/type=oembed', function ( $value, $post_id, $field ) {

	if ( ! empty( $value ) ) {
		$value = acf_oembed_get( $value, $post_id, $field );
	}

	return $value;
}, 10, 3 );

/**
 * Cache the oEmbed HTML
 */
add_filter( 'acf/update_value/type=oembed', function ( $value, $post_id, $field ) {

	if ( ! empty( $value ) ) {
		// Warm the cache
		acf_oembed_get( $value, $post_id, $field );
	}

	return $value;
}, 10, 3 );

/**
 * Attempts to fetch the embed HTML for a provided URL using oEmbed.
 *
 * Checks for a cached result (stored as custom post or in the post meta).
 *
 * @param mixed $value The URL to cache.
 * @param integer $post_id The post ID to save against.
 * @param array $field The field structure.
 *
 * @return string|null The embed HTML on success, otherwise the original URL.
 * @see  \WP_Embed::shortcode()
 *
 */
function acf_oembed_get( $value, $post_id, $field ) {
	if ( empty( $value ) ) {
		return $value;
	}

	global $wp_embed;

	$attr = [
		'width'  => $field['width'],
		'height' => $field['height'],
	];

	$html = $wp_embed->shortcode( $attr, $value );

	if ( $html ) {
		return $html;
	}

	return $value;
}
