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
	$twig->addFunction( new Twig_SimpleFunction( 'TribeEvent', function ( $post ) {
			return new \SUPT\SUPTTribeEvent( $post, '\SUPT\SUPTTribeEvent' );
		} )
	);
	
	return $twig;
} );

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'EFPConfiguration', function ( $post ) {
			return new \SUPT\EFPConfiguration( $post, '\SUPT\EFPConfiguration' );
		} )
	);
	
	return $twig;
} );
