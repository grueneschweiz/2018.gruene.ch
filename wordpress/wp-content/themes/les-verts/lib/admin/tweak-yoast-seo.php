<?php

/**
 * Lower the Yoast Metabox to be shown after the normal content
 * when editing posts/pages
 */
add_filter( 'wpseo_metabox_prio', function () {
	return 'low';
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
