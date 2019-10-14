<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'social_link', function ( $string, $type ) {
			
			$string = trim( $string );
			
			if ( 0 === strpos( $string, 'http' ) ) {
				return $string;
			}
			
			if ( 'facebook' == $type ) {
				$base = 'https://www.facebook.com/';
			} elseif ( 'twitter' == $type ) {
				$base   = 'https://twitter.com/';
				$string = str_replace( '@', '', $string );
			} elseif ( 'instagram' == $type ) {
				$base   = 'https://www.instagram.com/';
				$string = str_replace( '@', '', $string );
			}
			
			return $base . $string;
		} )
	);
	
	return $twig;
} );
