<?php

/**
 * Lower the Yoast Metabox to be shown after the normal content
 * when editing posts/pages
 */
add_filter( 'wpseo_metabox_prio', function() {
	return 'low';
});


/**
 * Add a class to the second last breadcrumb so we can apply good mobile styles
 */
add_filter( 'wpseo_breadcrumb_output', function ( $out ) {
	$last_class  = 'breadcrumb_last';
	$element     = '<span>';
	$replacement = '<span class="breadcrumb_second_last">';
	
	// get position of 'breadcrumb_last'
	$last_pos = strpos( $out, $last_class );
	
	// search for the last <span> before 'breadcrumb_last' to get second last element
	$before       = substr( $out, 0, $last_pos );
	$last_element = strrpos( $before, $element );
	
	// replace the <span> with <span class="breadcrumb_second_last">
	$out = substr_replace( $out, $replacement, $last_element, strlen( $element ) );
	
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
add_action( 'customize_register', function($wp_customize) {
	$wp_customize->remove_control('wpseo-breadcrumbs-display-blog-page');
	$wp_customize->remove_control('wpseo-breadcrumbs-separator');
}, 20 );
