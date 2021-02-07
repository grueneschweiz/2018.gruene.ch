<?php

namespace SUPT;

use WP_Post;
use WP_Term;
use WPSEO_Primary_Term;
use function absint;
use function get_nav_menu_locations;
use function get_queried_object_id;
use function get_term;
use function get_the_ID;
use function tribe_get_events_link;
use function wp_get_nav_menu_items;
use function wp_get_nav_menu_object;

/**
 * Configure breadcrumbs
 */
add_filter( 'wpseo_breadcrumb_output_wrapper', function () {
	return 'div';
} );

/**
 * Add a class to the second last breadcrumb so we can apply good mobile styles
 */
add_filter( 'wpseo_breadcrumb_output', function ( $out ) {
	$last_class = 'breadcrumb_last';

	// get position of 'breadcrumb_last'
	$last_pos = strpos( $out, $last_class );

	// bail early if there is only one element
	if ( false === $last_pos ) {
		return $out;
	}

	// search for the last '<a ' before 'breadcrumb_last' to get second last element
	$before       = substr( $out, 0, $last_pos );
	$last_element = strrpos( $before, '<a ' );

	// bail early if there is no link -> only one element
	if ( false === $last_element ) {
		return $out;
	}

	// replace the '<a ' with '<a class="breadcrumb_second_last" '
	$out = substr_replace( $out, '<a class="breadcrumb_second_last" ', $last_element, 3 );

	return $out;
} );

/**
 * Remove the default separator between breadcrumbs
 */
add_filter( 'wpseo_breadcrumb_separator', function () {
	return '';
} );


/**
 * Generate breadcrumbs from menu structure.
 *
 * Use the first matching most specific trail to the given page.
 *
 * This is inspired by Antonimos code.
 * @see https://gist.github.com/Antonimo/d930a2a60e468b4c8f4bcafc7465e135
 */
class CustomMenuBreadcrumbs {

	private $menu_location = '';
	private $menu = false;
	private $menu_items = array();
	private $parent_menu_item = false;

	public function __construct( $menu_location = '' ) {

		$this->menu_location = $menu_location;

		// for convenience everything is built on Menu location (e.g. user changes out an entire Menu)
		$menu_locations = get_nav_menu_locations();

		// make sure the location exists
		if ( isset( $menu_locations[ $this->menu_location ] ) ) {
			$this->menu       = wp_get_nav_menu_object( $menu_locations[ $this->menu_location ] );
			$this->menu_items = wp_get_nav_menu_items( $this->menu->term_id );
		}
	}

	/**
	 * Get the primary category of the given post (by id)
	 *
	 * @param int $post_id
	 *
	 * @return false|WP_Term
	 */
	private function get_primary_category( $post_id ) {
		if ( ! class_exists( '\WPSEO_Primary_Term' ) ) {
			return false;
		}

		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $post_id );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();

		if ( $wpseo_primary_term ) {
			$primary = get_term( $wpseo_primary_term );
		} else {
			/**
			 * Fallback for imported posts
			 *
			 * Just take the first category.
			 *
			 * @since 0.16.1
			 */
			$categories = wp_get_post_categories( $post_id );
			if ( empty( $categories ) ) {
				return false;
			}

			$primary = get_term( $categories[0] );
		}

		if ( is_wp_error( $primary ) || null === $primary ) {
			return false;
		}

		return $primary;
	}

	/**
	 * Retrieve the most specific menu item object for the current menu by the given object id
	 *
	 * @param string|int $object_id The id of the object we want to find the menu entry of
	 * @param bool $is_term_id Controls if the object id represents a taxonomy
	 *
	 * @return      false|WP_Post    The current Menu item
	 */
	private function get_menu_item_object_by_object_id( $object_id, $is_term_id = false ) {
		if ( empty( $this->menu_items ) ) {
			return false;
		}

		$object_id = (string) $object_id;

		// loop through the entire nav menu and determine whether any object id matches
		$match = false;
		foreach ( $this->menu_items as $menu_item ) {
			// if the current object id match
			if ( isset( $menu_item->object_id ) && $object_id === $menu_item->object_id ) {
				// if the given id was a term_id, the menu item must represent a taxonomy
				if ( $is_term_id && isset( $menu_item->type ) && $menu_item->type !== 'taxonomy' ) {
					continue;
				}
				// if it was not a term_id, the menu item must not represent a taxonomy
				if ( ! $is_term_id && isset( $menu_item->type ) && $menu_item->type === 'taxonomy' ) {
					continue;
				}
				// if we already had a matching object, check if this is a child of the last match
				// if so, use this child element.
				if ( $match ) {
					if ( absint( $menu_item->menu_item_parent ) === absint( $match->ID ) ) {
						$match = $menu_item;
					}
					// else skip it and stick to the first match
				} else {
					$match = $menu_item;
				}
			}
		}

		return $match;
	}

	/**
	 * Retrieve the most specific Menu item object for the current Menu by the given url
	 *
	 * @param string           The exact url to match against
	 *
	 * @return      false|WP_Post    The current Menu item
	 */
	private function get_menu_object_by_url( $url ) {
		if ( empty( $this->menu_items ) || empty( $url ) ) {
			return false;
		}

		// loop through the entire nav menu and determine whether any object id matches
		$match = false;
		foreach ( $this->menu_items as $menu_item ) {
			// if the current object id match
			if ( isset( $menu_item->url ) && $url === $menu_item->url ) {
				// if we already had a matching object, check if this is a child of the last match
				// if so, use this child element.
				if ( $match ) {
					if ( absint( $menu_item->menu_item_parent ) === absint( $match->ID ) ) {
						$match = $menu_item;
					}
					// else skip it and stick to the first match
				} else {
					$match = $menu_item;
				}
			}
		}

		return $match;
	}

	/**
	 * Retrieve the current Menu item object's parent Menu item object
	 *
	 * @param WP_Post $current_menu_item The current Menu item object
	 *
	 * @return      bool|WP_Post                    The parent Menu object
	 * @since       1.0.0
	 *
	 */
	private function get_parent_menu_item_object( $current_menu_item ) {

		if ( empty( $this->menu_items ) ) {
			return false;
		}

		foreach ( $this->menu_items as $menu_item ) {
			if ( absint( $current_menu_item->menu_item_parent ) == absint( $menu_item->ID ) ) {
				return $menu_item;
			}
		}

		return false;
	}

	/**
	 * Get the menu item of the current page
	 *
	 * If the current page is not linked in the menu, but an archive of it's primary
	 * category is, then return the menu item of the archive and set the class
	 * variable 'parent_menu_item' to true.
	 *
	 * @return false|WP_Post false if no page and no archive is found in the menu.
	 */
	private function get_current_menu_item() {
		$object_id = get_queried_object_id();

		// for all regular menu elements
		if ( $object_id ) {
			$current_menu_item = $this->get_menu_item_object_by_object_id( $object_id );
		}

		// for posts not the post itself but the primary category is linked in the menu
		if ( empty( $current_menu_item ) ) {
			$primary_category = $this->get_primary_category( get_the_ID() );
			if ( $primary_category ) {
				$current_menu_item      = $this->get_menu_item_object_by_object_id( $primary_category->term_id, true );
				$this->parent_menu_item = true;
			}
		}

		// hits the events archive of the events calendar from tribe
		if ( empty( $current_menu_item ) && empty( $object_id ) && function_exists( '\tribe_get_events_link' ) ) {
			$current_menu_item = $this->get_menu_object_by_url( tribe_get_events_link() );
		}

		return $current_menu_item;
	}

	/**
	 * Generate an array of WP_Post objects that constitutes a breadcrumb trail based on a Menu
	 *
	 * @param array $wpseo_crumbs The original breadcrumbs from yoast wpseo
	 *
	 * @return      array|string    Breadcrumb of WP_Post objects
	 * @since       1.0.0
	 */
	public function generate_trail( $wpseo_crumbs ) {
		$current_menu_item = $this->get_current_menu_item();

		// use the original breadcrumbs if we can't locate the menu item
		if ( empty( $current_menu_item ) ) {
			return $wpseo_crumbs;
		}

		// there's at least one level
		$breadcrumbs = array( $current_menu_item );

		// work backwards from the current menu item all the way to the top
		while ( $current_menu_item = $this->get_parent_menu_item_object( $current_menu_item ) ) {
			$breadcrumbs[] = $current_menu_item;
		}

		// since we worked backwards, we need to reverse everything
		$breadcrumbs = array_reverse( $breadcrumbs );

		// add a level for each breadcrumb object
		$i = 1;
		foreach ( $breadcrumbs as $key => $val ) {
			if ( ! isset( $val->menu_breadcrumb_level ) ) {
				$val->menu_breadcrumb_level = $i;
				$breadcrumbs[ $key ]        = $val;
			}
			$i ++;
		}

		/**
		 * build final array of breadcrumbs that conforms wpseo's expected format
		 */
		// add the home breadcrumb
		$final_crumbs = [ reset( $wpseo_crumbs ) ];

		// add the custom breadcrumbs in the expected format
		foreach ( $breadcrumbs as $key => $breadcrumb ) {

			$_url = $breadcrumb->url;

			// add #menu-item-ID to the first level crumbs so we can
			// intercept the links with js to open the nav instead of
			// following the link.
			if ( 1 === $breadcrumb->menu_breadcrumb_level ) {
				$_url = rtrim( $_url, '#' ); // cut trailing hashes, because were appending a hash
				$_url .= '#menu-item-' . $breadcrumb->ID;
			}

			$final_crumbs[] = array(
				'text'       => $breadcrumb->title,
				'url'        => $_url,
				'allow_html' => false,
			);
		}

		// add the breadcrumb of the current page if we could only locate the
		// parent item of the current page in the menu.
		if ( $this->parent_menu_item ) {
			$final_crumbs[] = array_pop( $wpseo_crumbs );
		}

		return $final_crumbs;
	}

}

/**
 * Try to replace the breadcrumbs by the menu hierarchy. Use the default ones if this fails.
 */
add_filter( 'wpseo_breadcrumb_links', function ( $wpseo_crumbs ) {
	$menu_breadcrumbs = new CustomMenuBreadcrumbs( 'main-nav' );

	return $menu_breadcrumbs->generate_trail( $wpseo_crumbs );
} );
