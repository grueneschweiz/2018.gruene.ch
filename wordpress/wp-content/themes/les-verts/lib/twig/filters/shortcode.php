<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'shortcode', function ( $string ) {
			if ( ! empty( $string ) && is_string( $string ) ) {
				return do_shortcode( $string );
			}
			return $string;
		} )
	);

	return $twig;
} );
