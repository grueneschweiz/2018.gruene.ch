<?php

namespace SUPT;

class Branding_controller {

	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}

	public static function add_to_context( $context ) {
		
		$context['branding'] = [
			'unbreakeables' => [ 'Ökologisch konsequent.', 'Sozial engagiert.', 'Global solidarisch.' ],
			// todo: get them from the customizer
		];

		return $context;
	}
}
