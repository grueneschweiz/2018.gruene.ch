<?php

namespace SUPT;

use \Twig_SimpleFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'uniqueid', function ( $string ) {
			global $supt_unique_id_counter;
			return $string . '-' . $supt_unique_id_counter++;
		} )
	);
	
	return $twig;
} );
