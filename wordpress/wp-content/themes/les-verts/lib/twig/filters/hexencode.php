<?php

namespace SUPT;

use \Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'hexencode', function ( $string ) {
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
