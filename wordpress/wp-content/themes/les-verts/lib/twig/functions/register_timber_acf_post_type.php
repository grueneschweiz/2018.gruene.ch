<?php

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'ACFPost', function ( $post ) {
			return new \SUPT\ACFPost( $post );
		} )
	);
	
	return $twig;
} );
