<?php

namespace SUPT;

use \Twig_SimpleFunction;

/**
 * Prints a simple languages switcher
 * 
 * USAGE:
 * - in php `SUPT\get_languages_switcher( $page_id )`
 * - in twig `{{ get_languages_switcher( page.ID ) }}`
 */
function get_languages_switcher($post_id) {
	if ( ! function_exists('pll_the_languages') ) return '';
	$switcher = pll_the_languages(array(
		'dropdown' => 1,
		'display_names_as' => 'slug',
		'hide_if_empty' => 0,
		'echo' => 0,
		'post_id' => $post_id
	));
	return $switcher;
}
// Add the function to Twig
add_filter( 'get_twig', function( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'get_languages_switcher', 'SUPT\get_languages_switcher' ) );
	return $twig;
});
