<?php

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'PostQuery', function ( $query ) {
			return new \SUPT\SUPTPostQuery( $query, '\SUPT\ACFPost' );
		} )
	);
	
	return $twig;
} );
