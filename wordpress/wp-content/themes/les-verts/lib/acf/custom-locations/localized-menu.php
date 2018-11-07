<?php

namespace SUPT;

/**
 * Dynamic location for localized menus.
 *
 * In multilang websites, acf dynamically creates a menu location per language.
 * Since we are in a multisite setup, we don't want to manually change the ACF
 * configuration to add all locations (main___en, main___fr, etc…) in each website.
 * So we define here a filter that takes care of it.
 *
 */

// Add type of rule
add_filter( 'acf/location/rule_types', function( $choices ) {

	$choices['Custom']['localized_menu'] = 'Localized Menu';

	return $choices;

} );

// Add options
add_filter('acf/location/rule_values/localized_menu', function( $choices ) {

	// default choice
	$choices[ 'all' ] = 'All';

	// vars
	$locations = get_registered_nav_menus();

	// add menu locations
	foreach ($locations as $slug => $label) {

		// we don't want translated menus
		if ( strpos($slug, '___') !== false ) continue;

		// add choice
		$choices[$slug] = $slug;

	}

	return $choices;

} );

// Match the rule (= show the fields group)
// inspired by `class-acf-location-nav-menu-item.php` from ACF plugin
add_filter('acf/location/rule_match/localized_menu', function( $result, $rule, $screen ) {

	// vars
	$nav_menu_item = acf_maybe_get( $screen, 'nav_menu_item' );

	// bail early if not nav_menu_item
	if( !$nav_menu_item ) return false;

	// we selected 'all', no need to test further we know we are in a menu item here
	if( $rule['value'] == 'all' ) return true;

	// append nav_menu data
	if( !isset($screen['nav_menu']) ) {
		$screen['nav_menu'] = acf_get_data('nav_menu_id');
	}

	// vars
	$menu_locations = get_nav_menu_locations();
	$match = ( $rule['operator'] == '!=' ); // if '==', $match == false // if '!=', $match == true
	$val = $rule['value'];

	// match the rule
	foreach ( $menu_locations as $slug => $nav_id ) {
		if (
			// location contains the current menu
			$nav_id == $screen['nav_menu']
			// AND
			&& (
				// location slug exactly matches the rule value (ex: 'menu')
				$slug == $val
				// location slug is a translation of the rule value (ex: 'menu___fr')
				|| substr( $slug, 0, strlen( $val . '___' ) ) === $val . '___'
			)
		) {

			// if we found a match & operator was '!=', it will return false
			// if we found a match & operator was '==', it will return true
			// thanks to what we did above ( `$match = ( $rule['operator'] == '!=' );` )
			return !$match;

		}
	}

	// by security, if we don't have any menu location
	return false;

}, 10, 3);
