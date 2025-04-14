<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'nice_link', function ( $string ) {
			$string = trim( $string );
			$string = rtrim( $string, '/' );
			
			$pos = strpos( $string, '://' );
			if ( false === $pos ) {
				return $string;
			}
			
			$string = substr($string, $pos + 3);
			
			return $string;
		} )
	);
	
	return $twig;
} );
