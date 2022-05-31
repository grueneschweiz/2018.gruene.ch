<?php

/**
 * Remove the option meta box of tribe events, as these options are not supported
 */
add_action( 'do_meta_boxes', function () {
	remove_meta_box( 'tribe_events_event_options', 'tribe_events', 'side' );
} );

/**
 * Disable the tribe events customizer
 */
add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->remove_panel( 'tribe_customizer' );
}, 99 );

/**
 * Disable tribe events in admin bar
 */
if ( ! defined( 'TRIBE_DISABLE_TOOLBAR_ITEMS' ) ) {
	define( 'TRIBE_DISABLE_TOOLBAR_ITEMS', true );
}
