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
 * Reduce open graph and twitter image size to make sure we don't exceed the limits
 */
add_filter( 'wpseo_opengraph_image_size', function () {
	return 'large';
} );
add_filter( 'wpseo_twitter_image_size', function () {
	return 'large';
} );

/**
 * Manually add image, if wordpress seo fails to find in himself
 *
 * For some reason wpseo fails to get the og image if the uploaded image is to big. This fixes this issue.
 */
add_action( 'wpseo_add_opengraph_additional_images', function ( $og ) {
	if ( $og->has_images() ) {
		return;
	}

	if ( is_front_page() ) {
		if ( get_field( 'campaign_show_main_campaign' ) ) {
			$image = get_field( 'campaign_image' );
		}

		if ( empty( $image ) ) {
			return;
		}

		$image_url = $image['url'];
	} else {
		$image_id = get_post_thumbnail_id( get_the_ID() );
		$image    = wp_get_attachment_image_src( $image_id, apply_filters( 'wpseo_opengraph_image_size', 'full' ) );

		if ( $image === null || empty( $image ) ) {
			return;
		}

		$image_url = $image[0];
	}

	$og->add_image( array( 'url' => $image_url ) );
} );
