<?php

namespace SUPT;

use Twig\TwigFilter;

/**
 * The disableable_wpautop applies wpautop to all output not enclosed in
 * '<!-- lesverts-noautop -->here wpautop won't be applied<!-- lesverts-end-noautop -->'
 *
 * The tags <!-- lesverts-noautop --> and <!-- lesverts-end-noautop --> are stripped.
 *
 * Example:
 * here wpautop is applied.
 * <!-- lesverts-noautop -->no wpautop in here<!-- lesverts-end-noautop -->
 * wpautop is applied again here.
 */
add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter( new TwigFilter( 'disableable_wpautop', function ( $str ) {
		if ( false === strpos( $str, '<!-- lesverts-noautop -->' ) ) {
			return wpautop( $str );
		}

		$parts = preg_split(
			'/(<!-- lesverts-noautop -->.*<!-- lesverts-end-noautop -->)/sUi',
			$str,
			- 1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
		);

		$str = '';
		foreach ( $parts as $part ) {
			if ( 0 === strpos( $part, '<!-- lesverts-noautop -->' ) ) {
				$str .= str_replace( [ '<!-- lesverts-noautop -->', '<!-- lesverts-end-noautop -->' ], '', $part );
			} else {
				$str .= wpautop( $part );
			}
		}

		return $str;
	} ) );

	return $twig;
} );
