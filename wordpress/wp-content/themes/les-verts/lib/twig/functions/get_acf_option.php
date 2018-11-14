<?php

namespace SUPT;

use \Twig_SimpleFunction;

// get acf option
add_filter( 'get_twig', function( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'get_acf_option', function($option_name) {
		return get_field($option_name, 'options');
	} ) );
	return $twig;
});
