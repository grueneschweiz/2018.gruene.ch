<?php

namespace SUPT;

use Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'wptexturize', function ( $string ) {
			
			return wptexturize( $string );
		} )
	);
	
	return $twig;
} );
