<?php

namespace SUPT;

use \Twig_SimpleFilter;

/**
 * Makes a text translatable
 *
 * USAGE:
 * - in twig `{{ pll__('My text') }}`
 */
add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter( new Twig_SimpleFilter( 'pll', function ( $string ) {
		if ( function_exists( 'pll__' ) ) {
			return pll__( $string );
		} else {
			return $string;
		}
	} ) );

	return $twig;
} );
