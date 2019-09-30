<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'phone', function ( $string ) {
			// strip spaces
			$string = str_replace(' ', '', trim($string));
			
			// make sure to get +41
			if (0 !== strpos($string, '+')) {
				if (0 === strpos($string, '00')) {
					$string = '+'.substr($string, 2);
				} else {
					$string = '+41'.substr($string, 1);
				}
			}
			
			// make sure the mailto is in the link
			if ( false === strpos( $string, 'tel:' ) ) {
				$string = 'tel:' . $string;
			}
			
			return $string;
		} )
	);
	
	return $twig;
} );
