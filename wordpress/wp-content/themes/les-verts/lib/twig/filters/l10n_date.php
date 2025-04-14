<?php

namespace SUPT;

use Twig\TwigFilter;

/**
 * Adds the "er" to frenchs first day of month
 */
add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter( new TwigFilter( 'l10n_date', function ( $datestr ) {
		if ( 0 === strpos( get_locale(), 'fr' ) && 0 === strpos( $datestr, '1 ' ) ) {
			$datestr = '1<sup>er</sup> ' . substr( $datestr, 2 );
		}

		return $datestr;
	} ) );

	return $twig;
} );
