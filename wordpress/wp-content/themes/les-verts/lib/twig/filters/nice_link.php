<?php

namespace SUPT;

use \Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'nice_link', function ( $string ) {
			$string = trim($string);
			
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
