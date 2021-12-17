<?php


/**
 * Registers strings for translation
 */
if ( defined( 'POLYLANG_DIR' ) && function_exists( 'pll__' ) ) {
	add_action( 'admin_init', function () {
		/**
		 * Register WP SEO options to be translatable
		 */
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

		/**
		 * Register default open graph image to be translatable (URL)
		 */
		pll_register_string(
			'og_default_image',
			WPSEO_Options::get( 'og_default_image', '' ),
			'wordpress-seo'
		);
	} );

	/**
	 * Translate default open graph image (URL)
	 */
	add_filter( 'wpseo_add_opengraph_additional_images', function ( $og_image ) {
		/** @var WPSEO_OpenGraph_Image $og_image */
		if ( ! $og_image->has_images() ) {
			$default_image_url = WPSEO_Options::get( 'og_default_image', '' );
			$og_image->add_image_by_url( pll__( $default_image_url ) );
		}

		return $og_image;
	}, 9999 );

	add_filter( 'wpseo_twitter_image', function ( $image_url ) {
		$default_image_url = WPSEO_Options::get( 'og_default_image', '' );

		if ( $default_image_url === $image_url ) {
			$image_url = pll__( $default_image_url );
		}

		return $image_url;
	}, 9999 );


	/**
	 * Translate twitter site
	 */
	add_filter( 'wpseo_twitter_site', function ( $name ) {
		return pll__( $name );
	} );

	/**
	 * Translate twitter creator account
	 */
	add_filter( 'wpseo_twitter_creator_account', function ( $name ) {
		return pll__( $name );
	} );

	/**
	 * Translate open graph article publisher
	 */
	add_filter( 'wpseo_og_article:publisher', function ( $url ) {
		return pll__( $url );
	} );

	/**
	 * Translate open graph facebook author
	 */
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

		$image_id = $image['id'];
	} else {
		$image_id = get_post_thumbnail_id( get_the_ID() );
	}

	$image = wp_get_attachment_image_src( $image_id, apply_filters( 'wpseo_opengraph_image_size', 'full' ) );

	if ( $image === null || empty( $image ) ) {
		return;
	}

	$image_url = $image[0];

	$og->add_image( array( 'url' => $image_url ) );
} );

/**
 * Nice open graph descriptions
 *
 * If the open graph description data is taken from the content instead of the lead (p.ex. for events)
 * it may contain html tags, and it is of infinite length. This function cleans out the tags and trims
 * the description to a max of 140 chars.
 */
add_filter( 'wpseo_opengraph_desc', static function ( $description ) {
	$limit = 140; // cut off descriptions at 140 chars
	$clean = preg_replace( '/\s+/', ' ', trim( wp_strip_all_tags( $description, true ) ) );

	if ( strlen( $clean ) > $limit ) {
		// chop off at $limit
		$hard_trimmed = substr( $clean, 0, $limit );

		// chop of last word part, so we stop with space
		$soft_trim_pos = strrpos( rtrim( $hard_trimmed, '.,?!;:-‒–"«»›‹' ), ' ' );
		$trimmed       = substr( $clean, 0, $soft_trim_pos );

		return $trimmed . ' …';
	}

	return $clean;
} );
