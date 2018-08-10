<?php

namespace SUPT;


class Navigation_controller {
	
	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}
	
	public static function add_to_context( $context ) {
		
		$menu = new \TimberMenu( 'main-nav', [ 'depth' => 3 ] );
		self::prepare_featured_items( $menu->items );
		
		$context['menu']['main']        = $menu;
		$context['menu']['language']    = new \TimberMenu( 'language-nav', [ 'depth' => 1 ] );
		$context['menu']['cta']         = [
			'label' => 'mitmachen',
			'link'  => '#'
		]; // todo: get them from the customizer
		$context['menu']['footer']      = new \TimberMenu( 'footer-nav', [ 'depth' => 2 ] );
		$context['menu']['footer_meta'] = new \TimberMenu( 'footer-meta-nav', [ 'depth' => 1 ] );
		
		$img_id                  = get_theme_mod( Customizer\Logo::SETTING_LOGO_DARK, false );
		$context['menu']['logo'] = [
			'image'  => new \TimberImage( $img_id ),
			'srcset' => [ 2, 3 ],
			'resize' => [ 116 ],
		];
		
		return $context;
	}
	
	/**
	 * Walk the menu recursively and transform all category ids that belong to
	 * a featured image into a TimberTerm and all images in a TimberImage.
	 *
	 * @param $timberMenu
	 */
	public static function prepare_featured_items( &$timberMenu ) {
		if ( ! is_array( $timberMenu ) && ! is_object( $timberMenu ) ) {
			return;
		}
		
		foreach ( $timberMenu as &$item ) {
			if ( is_array( $item ) ) {
				foreach ( $item as &$i ) {
					self::prepare_featured_items( $i );
				}
			}
			
			if ( $item instanceof \Timber\MenuItem ) {
				if ( $item->featured_menu_item ) {
					$item->category = new \TimberTerm( $item->category );
					$item->image    = new \TimberImage( $item->image );
				}
				
				if ( $item->children ) {
					self::prepare_featured_items( $item->children );
				}
			}
		}
	}
}
