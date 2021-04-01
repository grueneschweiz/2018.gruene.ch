<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'wptexturize', function ( $string ) {
			$string = wptexturize( $string );

			$locale = get_locale();
			$french = 0 === strpos( $locale, 'fr' );

			/**
			 * Handle spaces around quotes, question and exclamation marks and colons.
			 */
			if ( $french ) {
				$simple_chars = '?!:';
				$raquo        = '&raquo;|&#187;|\x{00bb}';
				$rsaquo       = '&rsaquo;|&#8250;|\x{203a}';
				$string       = preg_replace( "/ ([$simple_chars]|$raquo|$rsaquo)/u", "&nbsp;$1", $string );

				$laquo  = '&laquo;|&#171;|\x{00ab}';
				$lsaquo = '&lsaquo;|&#8249;|\x{2039}';
				$string = preg_replace( "/($laquo|$lsaquo) /u", "$1&nbsp;", $string );
			}

			/**
			 * Wrap gender variations with <span class="nowrap"> tag to prevent wordwrap
			 *
			 * Tags words like: VERT-E-S, VERT.E.S, VERT·E·S, vert-e-s, enseignant-e, vaudois-es,
			 *                  der*die, Frauen*streik, Arbeiter*innen, etc.
			 * Does not affect words like: forgotten.space, long-word
			 * Does not affect gendered words in attributes like: <a href="vert-e-s.png">
			 */
			if ( $french ) {
				$separators = '-|\.|&middot;|&#183;|\x{00B7}';
				$endings    = "(($separators)[es]{1,2}){1,2}";
			} else if ( 0 === strpos( $locale, 'de' ) ) {
				$endings = "\*\w*";
			} else {
				return $string;
			}

			$pattern = "(?!<[^>]+)\b(\w+($endings))\b(?![^<]+?>)";
			$string  = preg_replace( "/$pattern/iu", "<span class='nowrap'>$1</span>", $string );

			return $string;
		} )
	);

	return $twig;
} );
