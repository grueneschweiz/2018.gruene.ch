<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'email', function ( $string ) {
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
