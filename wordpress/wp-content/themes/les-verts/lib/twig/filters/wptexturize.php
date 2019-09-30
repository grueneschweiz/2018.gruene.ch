<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'wptexturize', function ( $string ) {
			$string = wptexturize( $string );

			$simple_chars = '?!:';
			$raquo = '&raquo;|&#187;|\x{00bb}';
			$rsaquo ='&rsaquo;|&#8250;|\x{203a}';
			$string = preg_replace("/ ([$simple_chars]|$raquo|$rsaquo)/u", "&nbsp;$1", $string);

			$laquo = '&laquo;|&#171;|\x{00ab}';
			$lsaquo ='&lsaquo;|&#8249;|\x{2039}';
			$string = preg_replace("/($laquo|$lsaquo) /u", "$1&nbsp;", $string);
			
			return $string;
		} )
	);
	
	return $twig;
} );
