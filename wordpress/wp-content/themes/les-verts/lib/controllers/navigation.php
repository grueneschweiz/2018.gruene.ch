<?php

namespace SUPT;

use \TimberMenu;

class Navigation_controller {
	
	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}
	
	public static function add_to_context( $context ) {
		
		// Inject some default WP vars
		$context['mainNav']     = new TimberMenu( 'main-nav', ['depth' => 3] );
		$context['languageNav'] = new TimberMenu( 'language-nav', ['depth' => 1] );
		$context['menuCta']     = [ 'label' => 'mitmachen', 'link' => '#' ]; // todo: get them from the customizer
		
		return $context;
	}
}
