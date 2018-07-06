<?php

namespace SUPT;

use \Twig_SimpleFunction;

/**
 * Makes a text translatable
 * 
 * USAGE:
 * - in twig `{{ pll__('My text') }}`
 */
if ( function_exists( 'pll__') ) {
	add_filter( 'get_twig', function( $twig ) {
		$twig->addFunction( new Twig_SimpleFunction( 'pll__', 'pll__' ) );
		return $twig;
	} );
}
