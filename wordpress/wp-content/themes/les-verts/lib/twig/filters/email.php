<?php

namespace SUPT;

use \Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'email', function ( $string ) {
			$string = trim($string);
			
			// make sure the mailto is in the link
			if ( false === strpos( $string, 'mailto:' ) ) {
				$string = 'mailto:' . $string;
			}
			
			return $string;
		} )
	);
	
	return $twig;
} );
