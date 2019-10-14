<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'uniqueid', function ( $string ) {
			global $supt_unique_id_counter;
			return $string . '-' . $supt_unique_id_counter++;
		} )
	);
	
	return $twig;
} );
