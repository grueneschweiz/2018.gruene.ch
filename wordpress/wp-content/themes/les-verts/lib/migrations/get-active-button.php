<?php

namespace SUPT\Migrations\GetActiveButton;

use function add_action;
use function function_exists;
use function get_theme_mod;
use function has_nav_menu;
use function is_nav_menu;
use function pll_languages_list;
use function pll_translate_string;
use function set_theme_mod;
use function sprintf;
use function wp_create_nav_menu;
use function wp_update_nav_menu_item;
use const THEME_DOMAIN;

add_action( 'wp_loaded', function () {
	// don't fire earlier, otherwise have_nav_menu() will always
	// return false as the menus are not yet loaded.

	$languages = [];
	if ( function_exists( '\pll_languages_list' ) ) {
		$languages = pll_languages_list();
		foreach ( $languages as $lang ) {
			migrate( $lang );
		}
	}

	// use $locales because as pll_languages_list() may
	// also return an empty array.
	if ( empty( $languages ) ) {
		migrate();
	}
} );

function migrate( string $lang = '' ) {
	$name     = get_nav_name( $lang );
	$link     = get_nav_link( $lang );
	$location = 'get-active-nav';

	// bail out if there is already a menu assigned
	if ( ! $link || has_nav_menu( $location ) || is_nav_menu( $name ) ) {
		return;
	}

	// first create nav menu
	$menu_id = wp_create_nav_menu( $name );

	// then add item into newly-created menu
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => $name,
		'menu-item-url'    => $link,
		'menu-item-status' => 'publish'
	) );


	// Set menu location if needed
	if ( ! has_nav_menu( $location ) ) {
		$locations              = get_theme_mod( 'nav_menu_locations' );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

function get_nav_name( string $lang ) {
	$label = get_theme_mod( 'get_active_label', '' );

	if ( function_exists( '\pll_translate_string' ) && ! empty( $lang ) ) {
		if ( empty( $label ) ) {
			$label = sprintf( __( 'Get Active', THEME_DOMAIN ) . ' %s', $lang );
		} else {
			$label = sprintf( pll_translate_string( $label, $lang ) . ' %s', $lang );
		}
	}

	if ( empty( $label ) ) {
		$label = __( 'Get Active', THEME_DOMAIN );
	}

	return $label;
}

function get_nav_link( string $lang ) {
	$link = get_theme_mod( 'get_active_link', '' );

	if ( function_exists( '\pll_translate_string' ) && ! empty( $lang ) && ! empty( $link ) ) {
		$link = pll_translate_string( $link, $lang );
	}

	return $link;
}

