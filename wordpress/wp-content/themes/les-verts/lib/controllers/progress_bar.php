<?php

namespace SUPT;

use Twig\TwigFunction;

class Progress_controller {
	public static function register() {
		add_filter( 'get_twig', function ( $twig ) {
			$twig->addFunction( new TwigFunction( 'Progress_Bar', function ( $array ) {
					include_once dirname( __DIR__ ) . '/form/include/ProgressHelper.php';

					return ProgressHelper::from_array( $array );
				} )
			);

			return $twig;
		} );
	}
}
