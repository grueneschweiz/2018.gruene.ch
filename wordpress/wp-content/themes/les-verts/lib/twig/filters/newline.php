<?php

namespace SUPT;

use \Twig_SimpleFilter;

/**
 * Sanitize line breaks to have proper newlines characters (ASCII 10 and 13)
 * 
 * Used mainly for titles, to be able to handle properly the line breaks with css
 * with the `white-space` property (for example, we want line breaks on desktop
 * but not on mobile: we apply the property `white-space: pre-line` on desktop)
 * 
 * USAGE: `{{ data.my_title | newline }}`
 */
add_filter( 'get_twig', function( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'newline', function ( $string ) {
			// We support "|", "br" and "\n" for newlines and are very tolerant to
			// avoid having multiple spaces at a time
			// (see the regex in action here: https://regex101.com/r/ZJPdjG/1)
			$newline_regex = '/(?:\s)*(?:\||\<br\>|\<br\/\>|\n)+(?:\s)*/i';
			return preg_replace( $newline_regex, '&#13;&#10;', $string );
		} )
	);
	return $twig;
} );
