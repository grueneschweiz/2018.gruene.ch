<?php

namespace SUPT;

use \Twig_SimpleFunction;

/**
 * Returns the current lang, in ISO 2-letters code
 * 
 * USAGE:
 * - in php `SUPT\get_lang( $page_id )`
 * - in twig `{{ get_lang( post.ID ) }}`
 */
function get_lang() {
	if (function_exists('pll_current_language')) {
		return pll_current_language();
	} else {
		return get_locale();
	}
}
// Add the function to Twig
add_filter( 'get_twig', function( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'get_lang', 'SUPT\get_lang' ) );
	return $twig;
});
