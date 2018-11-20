<?php

namespace SUPT;

use Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'slugify', function ( $string ) {
			return supt_slugify($string);
		} )
	);
	
	return $twig;
} );
