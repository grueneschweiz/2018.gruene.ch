<?php

namespace SUPT\Migrations\GetActiveButton;

use function add_action;
use function array_merge_recursive;
use function function_exists;
use function get_option;
use function get_theme_mod;
use function has_nav_menu;
use function is_array;
use function is_nav_menu;
use function pll_languages_list;
use function pll_translate_string;
use function set_theme_mod;
use function sprintf;
use function strlen;
use function substr;
use function trim;
use function update_option;
use function wp_create_nav_menu;
use function wp_get_nav_menu_object;
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

	// use $languages because as pll_languages_list() may
	// also return an empty array.
	if ( empty( $languages ) ) {
		migrate();
	}
} );

function migrate( string $lang = '' ) {
	$nav_name   = get_nav_name( $lang );
	$item_title = trim( substr( $nav_name, 0, - strlen( $lang ) ) );
	$link       = get_nav_link( $lang );
	$location   = 'get-active-nav';

	// exit if no get active button was defined in the customizer
	if ( ! $link ) {
		return;
	}

	if ( ! is_nav_menu( $nav_name ) ) {
		$menu_id = wp_create_nav_menu( $nav_name );
	} else {
		$menu_id = wp_get_nav_menu_object( $nav_name )->term_id;
	}

	if ( ! wp_get_nav_menu_items( $menu_id ) ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => $item_title,
			'menu-item-url'    => $link,
			'menu-item-status' => 'publish'
		) );
	}

	// Set menu location if needed
	if ( ! has_nav_menu( $location ) ) {
		set_nav_location( $location, $menu_id, $lang );
	}
}

function set_nav_location( string $location, int $menu_id, string $lang ) {
	$polylang = get_option( 'polylang' );
	if ( empty( $lang ) || ! $polylang || ! is_array( $polylang ) ) {
		// menu not localized
		$locations              = get_theme_mod( 'nav_menu_locations' );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
	if ( $polylang && is_array( $polylang ) ) {
		// localized menu
		$add      = [ 'nav_menus' => [ 'les-verts' => [ $location => [ $lang => $menu_id ] ] ] ];
		$polylang = array_merge_recursive( $polylang, $add );
		update_option( 'polylang', $polylang );
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

