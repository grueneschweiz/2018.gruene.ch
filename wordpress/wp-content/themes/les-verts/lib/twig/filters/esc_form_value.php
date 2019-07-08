<?php

namespace SUPT;

use Twig_SimpleFilter;

/**
 * Remove bad chars (like esc_attr but it strips the characters instead of transforming them)
 *
 * This filter is used in conjunction with forms. Changes must be reflected in the form submission validation.
 */
add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter( new Twig_SimpleFilter( 'esc_form_value', function ( $str ) {
		return preg_replace( '/[&<>"\']/', '', $str );
	} ) );

	return $twig;
} );
