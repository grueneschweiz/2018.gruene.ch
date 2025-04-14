<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'hexencode', function ( $string ) {
			$hex = '';
			for ( $i = 0; $i < strlen( $string ); $i ++ ) {
				$ord     = ord( $string[ $i ] );
				$hexCode = dechex( $ord );
				$hex     .= '&#x' . substr( '0' . $hexCode, - 2 ) . ';';
			}
			
			return $hex;
		} )
	);
	
	return $twig;
} );
