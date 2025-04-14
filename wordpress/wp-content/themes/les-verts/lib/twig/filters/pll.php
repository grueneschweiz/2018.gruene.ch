<?php

namespace SUPT;

use Twig\TwigFilter;

/**
 * Makes a text translatable
 *
 * USAGE:
 * - in twig `{{ pll__('My text') }}`
 */
add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter( new TwigFilter( 'pll', function ( $string ) {
		if ( function_exists( 'pll__' ) ) {
			return pll__( $string );
		} else {
			return $string;
		}
	} ) );

	return $twig;
} );
