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
 * Lower the Yoast Metabox to be shown after the normal content
 * when editing posts/pages
 */
add_filter( 'wpseo_metabox_prio', function () {
	return 'low';
} );

/**
 * Reduce twitter image size to make sure we dont exceed the limits
 */
add_filter( 'wpseo_twitter_image_size', function () {
	return 'large';
} );


/**
 * Configure breadcrumbs
 */
add_filter( 'wpseo_breadcrumb_output_wrapper', function () {
	return 'div';
} );

/**
 * Add a class to the second last breadcrumb so we can apply good mobile styles
 */
add_filter( 'wpseo_breadcrumb_output', function ( $out ) {
	
	// add breadcrumbs for single events
	if ( is_singular( 'tribe_events' ) ) {
		$queried_object  = get_queried_object();
		$event_list_obj  = get_post_type_object( 'tribe_events' );
		$event_list_link = get_post_type_archive_link( 'tribe_events' );
		
		$replace = '  <span><a href="' . $event_list_link . '">' . $event_list_obj->labels->name . '</a>  <span class="breadcrumb_last">' . $queried_object->post_title . '</span></span></span></div>';
		
		$out = str_replace( '</div>', $replace, $out );
	}
	
	$last_class = 'breadcrumb_last';
	
	// get position of 'breadcrumb_last'
	$last_pos = strpos( $out, $last_class );
	
	// bail early if there is only one element
	if ( false === $last_pos ) {
		return $out;
	}
	
	// search for the last '<a ' before 'breadcrumb_last' to get second last element
	$before       = substr( $out, 0, $last_pos );
	$last_element = strrpos( $before, '<a ' );
	
	// replace the '<a ' with '<a class="breadcrumb_second_last" '
	$out = substr_replace( $out, '<a class="breadcrumb_second_last" ', $last_element, 3 );
	
	return $out;
} );

/**
 * Remove the default separator between breadcrumbs
 */
add_filter( 'wpseo_breadcrumb_separator', function () {
	return '';
} );


/**
 * Remove the customizer settings we don't want
 *
 * - The user shall not disable the breadcrumbs for blog pages
 * - The separator will be filtered and has therefor no effect
 */
add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->remove_control( 'wpseo-breadcrumbs-display-blog-page' );
	$wp_customize->remove_control( 'wpseo-breadcrumbs-separator' );
}, 20 );
