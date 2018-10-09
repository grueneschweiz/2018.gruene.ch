<?php

namespace SUPT;

class Navigation_controller {
	
	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
		add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'add_events_to_nav' ) );
	}
	
	/**
	 * Detect the Automatic Agenda menu item and add all upcoming events as
	 * sub items. Change the url of the parent item to lead to the list of
	 * all events.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public static function add_events_to_nav( $items ) {
		if ( is_admin() || ! defined('TRIBE_EVENTS_FILE') ) {
			return $items;
		}
		
		foreach ( $items as &$item ) {
			if ( '#supt_agenda' == $item->url ) {
				$item->url = tribe_get_events_link();
				$events    = tribe_get_events();
				
				foreach ( $events as $key => &$event ) {
					$event->menu_item_parent = $item->ID;
					$event->object_id        = (string) $event->ID;
					$event->db_id            = $event->ID;
					$event->classes          = array();
					
					$date         = date(
						_x( 'y-m-d', 'short date format', THEME_DOMAIN ),
						strtotime( $event->EventStartDate )
					);
					$event->title = sprintf(
						_x( '%s: %s', 'Example: 21.03.2018: Delegiertenversammlung', THEME_DOMAIN ),
						$date,
						$event->post_title
					);
					
					array_push( $items, $event );
				}
			}
		}
		
		return $items;
	}
	
	public static function add_to_context( $context ) {
		
		$menu = new \Timber\Menu( 'main-nav', [ 'depth' => 3 ] );
		self::prepare_special_menu_items( $menu->items );
		
		$context['menu']['main']        = $menu;
		$context['menu']['language']    = new \Timber\Menu( 'language-nav', [ 'depth' => 1 ] );
		$context['menu']['cta']         = self::get_menu_cta();
		$context['menu']['footer']      = new \Timber\Menu( 'footer-nav', [ 'depth' => 2 ] );
		$context['menu']['footer_meta'] = new \Timber\Menu( 'footer-meta-nav', [ 'depth' => 1 ] );
		
		$img_id                  = get_theme_mod( Customizer\Logo::SETTING_LOGO_DARK, false );
		$context['menu']['logo'] = [
			'image'  => new \Timber\Image( $img_id ),
			'srcset' => [ 2, 3 ],
			'resize' => [ 116 ],
		];
		
		return $context;
	}
	
	/**
	 * Walk the menu recursively and perform all needed custom actions
	 *
	 * @param $timberMenu
	 */
	public static function prepare_special_menu_items( &$timberMenu ) {
		if ( ! is_array( $timberMenu ) && ! is_object( $timberMenu ) ) {
			return;
		}
		
		foreach ( $timberMenu as &$item ) {
			if ( is_array( $item ) ) {
				foreach ( $item as &$i ) {
					self::prepare_special_menu_items( $i );
				}
			}
			
			if ( $item instanceof \Timber\MenuItem ) {
				
				// custom actions
				self::add_featured_item( $item );
				
				if ( $item->children ) {
					self::prepare_special_menu_items( $item->children );
				}
			}
		}
	}
	
	/**
	 * Transform all category ids that belong to a featured image into a
	 * TimberTerm and all images in a TimberImage.
	 *
	 * @param \Timber\MenuItem $item
	 */
	public static function add_featured_item( &$item ) {
		if ( $item->featured_menu_item ) {
			$item->category = new \Timber\Term( $item->category );
			$item->image    = new \Timber\Image( $item->image );
		}
	}
	
	/**
	 * Return array with label and link for the get active button.
	 *
	 * The values come from the customizer. If no page is defined, an empty
	 * array will be returned.
	 *
	 * @return array
	 */
	public static function get_menu_cta() {
		$post_id = get_theme_mod( Customizer\GetActive::SETTING_GET_ACTIVE_POST_ID, false );
		
		if ( ! $post_id ) {
			return [];
		}
		
		$label = get_theme_mod( Customizer\GetActive::SETTING_GET_ACTIVE_LABEL, false );
		
		if ( ! $label ) {
			$label = __( 'Get Active', THEME_DOMAIN );
		}
		
		return [
			'label' => $label,
			'link'  => get_permalink( $post_id ),
		];
	}
}
