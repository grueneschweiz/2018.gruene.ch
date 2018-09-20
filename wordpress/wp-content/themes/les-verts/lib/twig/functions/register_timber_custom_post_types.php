<?php

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'ACFPost', function ( $post ) {
			return new \SUPT\ACFPost( $post );
		} )
	);
	
	return $twig;
} );

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'PostQuery', function ( $query ) {
			return new \SUPT\SUPTPostQuery( $query, '\SUPT\ACFPost' );
		} )
	);
	
	return $twig;
} );

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'TribeEvent', function ( $query ) {
			return new \SUPT\SUPTTribeEvent( $query, '\SUPT\SUPTTribeEvent' );
		} )
	);
	
	return $twig;
} );
