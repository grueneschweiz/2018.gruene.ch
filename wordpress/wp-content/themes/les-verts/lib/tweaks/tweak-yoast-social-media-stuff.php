<?php


/**
 * Make social links translatable
 */
if ( defined( 'POLYLANG_DIR' ) && function_exists( 'pll__' ) ) {
	add_action( 'admin_init', function () {
		$options      = get_option( 'wpseo_social' );
		$translatable = array(
			'facebook_site',
			'twitter_site',
		);

		foreach ( $options as $key => $value ) {
			if ( in_array( $key, $translatable ) ) {
				pll_register_string( $key, $value, 'wordpress-seo' );
			}
		}
	} );

	add_filter( 'wpseo_twitter_site', function ( $name ) {
		return pll__( $name );
	} );

	add_filter( 'wpseo_twitter_creator_account', function ( $name ) {
		return pll__( $name );
	} );

	add_filter( 'wpseo_og_article:publisher', function ( $url ) {
		return pll__( $url );
	} );

	add_filter( 'wpseo_opengraph_author_facebook', function ( $url ) {
		return pll__( $url );
	} );
}

/**
 * Reduce twitter image size to make sure we dont exceed the limits
 */
add_filter( 'wpseo_twitter_image_size', function () {
	return 'large';
} );
