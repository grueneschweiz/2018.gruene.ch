<?php

namespace SUPT;

use \Twig_SimpleFunction;

const DEFAULT_MODIFIERS = array(
	'default',
	'color-primary',
	'left'
);

/**
 * Returns the component classes
 * 
 * USAGE:
 * - in php `SUPT\component_classes( $page_id )`
 * - in twig `{{ component_classes( page.ID ) }}`
 */
function component_classes($class, $modifiers) {
	$classes = $class;
	foreach ($modifiers as $modifier) {
		// Add the modifier only if not a default one
		// This way, the html is not polluted with useless modifiers.
		if (!in_array($modifier, \SUPT\DEFAULT_MODIFIERS)) {
			$classes .= " " . $class . "--" . $modifier;
		}
	}
	return $classes;
}
// Add the function to Twig
add_filter( 'get_twig', function( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'component_classes', 'SUPT\component_classes' ) );
	return $twig;
} );
