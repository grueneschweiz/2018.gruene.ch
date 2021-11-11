<?php

namespace SUPT;

use DateTime;
use DateTimeZone;
use Exception;
use Timber\Image;
use Timber\Menu;
use Timber\MenuItem;
use Timber\Term;
use WP_Post;

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
		if ( is_admin() || ! defined( 'TRIBE_EVENTS_FILE' ) ) {
			return $items;
		}

		foreach ( $items as &$item ) {

			if ( '#supt_agenda' == $item->url ) {
				// trailing slash leads to bug in tribe events 5.0.* (white page)
				$item->url = rtrim( tribe_get_listview_link(), '/' );

				$events = tribe_get_events( array(
					'start_date'       => date( 'Y-m-d' ),
					'schedule_details' => true,
					'orderby'          => 'event_date_utc',
					'order'            => 'ASC'
				) );

				if ( self::get_menu_item_level( $item, $items ) != 1 ) {
					// auto events do only work on the first menu level.
					// it even leads to a bug, if it was added on first and second level
					// so lets escape early to prevent this.
					continue;
				}

				if ( empty( $events ) ) {
					continue;
				}

				$type = get_post_type_object( reset( $events )->post_type );

				foreach ( $events as $key => &$event ) {
					$event->url              = get_permalink( $event );
					$event->menu_item_parent = $item->ID;
					$event->object_id        = (string) $event->ID;
					$event->db_id            = 0;
					$event->classes          = array();
					$event->type             = $event->post_type;
					$event->xfn              = '';
					$event->object           = $event->post_type;
					$event->attr_title       = '';
					$event->description      = '';
					$event->target           = '';
					$event->type_label       = $type->label;
					$event->menu_order       = 10000 + $key; // hack to enforce the items order (in some rare edge cases, there weren't)

					$event->title = sprintf(
						_x( '%s: %s', 'Example: 21.03.2018: Delegiertenversammlung', THEME_DOMAIN ),
						self::get_start_date( $event ),
						$event->post_title
					);

					array_push( $items, $event );
				}
			}
		}

		return $items;
	}

	/**
	 * Returns the level of the menu entry (zero indexed)
	 *
	 * @param $item
	 * @param $menu
	 *
	 * @return int
	 */
	private static function get_menu_item_level( $item, $menu ) {
		if ( empty( $item->menu_item_parent ) ) {
			return 0;
		}

		$parent_id = (int) $item->menu_item_parent;

		foreach ( $menu as $loop_item ) {
			if ( $loop_item->ID !== $parent_id ) {
				continue;
			}

			return self::get_menu_item_level( $loop_item, $menu ) + 1;
		}

		// there is a paren't item, but it isn't in the menu
		return 0;
	}

	/**
	 * Get the local start date of the given event and return it localized short date format
	 *
	 * @param WP_Post $event
	 *
	 * @return false|string
	 */
	private static function get_start_date( $event ) {
		/**
		 * legacy for compatibility with the events calendar < 4.9.0
		 */
		$raw_date = isset( $event->event_date ) ? $event->event_date : $event->EventStartDate;

		if ( empty( $raw_date ) ) {
			$utc_date = $event->event_date_utc;

			try {
				// get UTC time and convert it to local time
				$dt = new DateTime( $utc_date, new DateTimeZone( 'UTC' ) );
				$dt->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
				$raw_date = $dt->format( 'Y-m-d H:i:s' );
			} catch ( Exception $e ) {
				$raw_date = $utc_date;
			}
		}

		return date(
			_x( 'y-m-d', 'short date format', THEME_DOMAIN ),
			strtotime( $raw_date )
		);
	}

	public static function add_to_context( $context ) {

		$main_menu = new Menu( 'main-nav', [ 'depth' => 3 ] );
		self::prepare_special_menu_items( $main_menu->items );

		$cta_menu = new Menu( 'get-active-nav', [ 'depth' => 3 ] );
		self::prepare_special_menu_items( $cta_menu->items );

		$context['menu']['main']        = $main_menu;
		$context['menu']['language']    = new Menu( 'language-nav', [ 'depth' => 1 ] );
		$context['menu']['cta']         = $cta_menu;
		$context['menu']['footer']      = new Menu( 'footer-nav', [ 'depth' => 2 ] );
		$context['menu']['footer_meta'] = new Menu( 'footer-meta-nav', [ 'depth' => 1 ] );
		$context['menu']['search']      = is_plugin_active( 'searchwp/index.php' );

		$img_id                  = get_theme_mod( Customizer\Logo::SETTING_LOGO_DARK, false );
		$context['menu']['logo'] = [
			'image'  => new Image( $img_id ),
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

			if ( $item instanceof MenuItem ) {

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
	 * @param MenuItem $item
	 */
	public static function add_featured_item( &$item ) {
		if ( $item->featured_menu_item ) {
			$item->category = new Term( $item->category );
			$item->image    = new Image( $item->image );
		}
	}
}
