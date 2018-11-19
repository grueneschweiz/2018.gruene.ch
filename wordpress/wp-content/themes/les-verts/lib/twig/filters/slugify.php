<?php

namespace SUPT;

use Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'slugify', function ( $string ) {
			/**
			 * @see https://codex.wordpress.org/Function_Reference/sanitize_title
			 */
			return sanitize_title( trim( $string ) );
		} )
	);
	
	return $twig;
} );
