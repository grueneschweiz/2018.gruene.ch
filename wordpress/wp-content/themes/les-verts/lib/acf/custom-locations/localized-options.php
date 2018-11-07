<?php

namespace SUPT;

/**
 * Dynamic location for localized options.
 *
 * In multilang websites, we dynamically create an option page per language (see `lib/admin/acf-option-pages`).
 * Since we are in a multisite setup, we need to dynamically assign field groups
 * to the right option pages as well.
 *
 */

// Add type of rule
add_filter( 'acf/location/rule_types', function( $choices ) {

	$choices['Custom']['localized_options'] = 'Localized Options';

	return $choices;

} );

// Add options
add_filter('acf/location/rule_values/localized_options', function( $choices ) {

	$choices[ 'all' ] = 'All';

	return $choices;

} );

// Match the rule (= show the fields group)
add_filter('acf/location/rule_match/localized_options', function( $match, $rule, $options ) {

	$page_name_begins_with = 'acf-options-lang-';

	return (
		isset( $options['options_page'] )
		&& $options['options_page']
		&& substr($options['options_page'], 0, strlen($page_name_begins_with)) == $page_name_begins_with
	);

}, 10, 3);
