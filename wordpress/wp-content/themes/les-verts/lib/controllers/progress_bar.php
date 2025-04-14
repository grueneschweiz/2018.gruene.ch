<?php

namespace SUPT;

use Twig\TwigFunction;

class Progress_controller {
	public static function register() {
		add_filter('timber/twig', function ( $twig ) {
			$twig->addFunction( new TwigFunction( 'Progress_Bar', function ( $array ) {
					include_once dirname( __DIR__ ) . '/form/include/ProgressHelper.php';

					return ProgressHelper::from_array( $array );
				} )
			);

			return $twig;
		} );

		add_action( 'theme_form-after-save', function ( array $data ) {
			include_once dirname( __DIR__ ) . '/form/include/ProgressHelper.php';
			$form_id = (int) $data['form_data']['_meta_']['form_id'];
			ProgressHelper::cache_submission_count( $form_id );
		} );
	}
}
