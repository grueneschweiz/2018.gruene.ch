<?php

namespace SUPT;

use \TimberMenu;

class Navigation_controller {
	
	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}
	
	public static function add_to_context( $context ) {
		
		$context['menu']['main']     = new TimberMenu( 'main-nav', [ 'depth' => 3 ] );
		$context['menu']['language'] = new TimberMenu( 'language-nav', [ 'depth' => 1 ] );
		$context['menu']['cta']      = [ 'label' => 'mitmachen', 'link' => '#' ]; // todo: get them from the customizer
		
		$img_id                  = get_theme_mod( Customizer\Logo::SETTING_LOGO_DARK, false );
		$context['menu']['logo'] = [
			'image' => new \TimberImage( $img_id ),
			'srcset' => [2, 3],
			'resize' => [116],
		];
		
		return $context;
	}
}
