<?php

namespace SUPT;

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

	// add breadcrumbs for single events
	if ( \is_singular( 'tribe_events' ) ) {
		$queried_object  = \get_queried_object();

		$replace = '  <span class="breadcrumb_last">' . $queried_object->post_title . '</span></span></div>';

		$out = str_replace( '</div>', $replace, $out );
	}

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

	public $menu_location = '';
	public $menu = false;
	public $menu_items = array();

	public function __construct( $menu_location = '' ) {

		$this->menu_location = $menu_location;

		// for convenience everything is built on Menu location (e.g. user changes out an entire Menu)
		$menu_locations = \get_nav_menu_locations();

		// make sure the location exists
		if ( isset( $menu_locations[ $this->menu_location ] ) ) {
			$this->menu       = \wp_get_nav_menu_object( $menu_locations[ $this->menu_location ] );
			$this->menu_items = \wp_get_nav_menu_items( $this->menu->term_id );
		}
	}

	/**
	 * Get the primary category of the given post (by id)
	 *
	 * @param int $post_id
	 *
	 * @return false|\WP_Term
	 */
	private function get_primary_category( $post_id ) {
		if ( ! class_exists( '\WPSEO_Primary_Term' ) ) {
			return false;
		}

		$wpseo_primary_term = new \WPSEO_Primary_Term( 'category', $post_id );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$primary            = \get_term( $wpseo_primary_term );

		if ( is_wp_error( $primary ) || null === $primary ) {
			return false;
		}

		return $primary;
	}

	/**
	 * Retrieve the most specific Menu item object for the current Menu by the given object id
	 *
	 * @param       string|int       The id of the object we want to find the menu entry of
	 *
	 * @return      false|\WP_Post    The current Menu item
	 */
	private function get_menu_item_object_by_object_id( $object_id ) {
		if ( empty( $this->menu_items ) ) {
			return false;
		}

		$object_id = (string) $object_id;

		// loop through the entire nav menu and determine whether any object id matches
		$match = false;
		foreach ( $this->menu_items as $menu_item ) {
			// if the current object id match
			if ( isset( $menu_item->object_id ) && $object_id === $menu_item->object_id ) {
				// if we already had a matching object, check if this is a child of the last match
				// if so, use this child element.
				if ( $match ) {
					if ( \absint( $menu_item->menu_item_parent ) === \absint( $match->ID ) ) {
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
	 * @param       string           The exact url to match against
	 *
	 * @return      false|\WP_Post    The current Menu item
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
					if ( \absint( $menu_item->menu_item_parent ) === \absint( $match->ID ) ) {
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
	 * @since       1.0.0
	 *
	 * @param       \WP_Post $current_menu_item The current Menu item object
	 *
	 * @return      bool|\WP_Post                    The parent Menu object
	 */
	private function get_parent_menu_item_object( $current_menu_item ) {

		if ( empty( $this->menu_items ) ) {
			return false;
		}

		foreach ( $this->menu_items as $menu_item ) {
			if ( \absint( $current_menu_item->menu_item_parent ) == \absint( $menu_item->ID ) ) {
				return $menu_item;
			}
		}

		return false;
	}

	/**
	 * Generate an array of WP_Post objects that constitutes a breadcrumb trail based on a Menu
	 *
	 * @since       1.0.0
	 * @return      array|string    Breadcrumb of WP_Post objects
	 */
	public function generate_trail() {
		$object_id      = \get_queried_object_id();
		$indirect_match = false;

		// for all regular menu elements
		if ( $object_id ) {
			$current_menu_item = $this->get_menu_item_object_by_object_id( $object_id );
		}

		// for posts where only the primary is linked in the menu
		if ( empty( $current_menu_item ) ) {
			$primary_category = $this->get_primary_category( \get_the_ID() );
			if ( $primary_category ) {
				$current_menu_item = $this->get_menu_item_object_by_object_id( $primary_category->term_id );
				$indirect_match    = true;
			}
		}

		// hits the events archive of the events calendar from tribe
		if ( empty( $current_menu_item ) && empty( $object_id ) && function_exists( '\tribe_get_events_link' ) ) {
			$current_menu_item = $this->get_menu_object_by_url( \tribe_get_events_link() );
		}

		if ( empty( $current_menu_item ) ) {
			return '';
		}

		// there's at least one level
		$breadcrumb = array( $current_menu_item );

		// work backwards from the current menu item all the way to the top
		while ( $current_menu_item = $this->get_parent_menu_item_object( $current_menu_item ) ) {
			$breadcrumb[] = $current_menu_item;
		}

		// since we worked backwards, we need to reverse everything
		$breadcrumb = array_reverse( $breadcrumb );

		// add a level for each breadcrumb object
		$i = 1;
		foreach ( $breadcrumb as $key => $val ) {
			if ( ! isset( $val->menu_breadcrumb_level ) ) {
				$val->menu_breadcrumb_level = $i;
				$breadcrumb[ $key ]         = $val;
			}
			$i ++;
		}

		if ( ! $indirect_match ) {
			array_pop( $breadcrumb );
		}

		return $breadcrumb;
	}

}

/**
 * Try to replace the breadcrumbs by the menu hierarchy. Use the default ones if this fails.
 */
add_filter( 'wpseo_breadcrumb_links', function ( $crumbs ) {
	$menu_breadcrumbs = new CustomMenuBreadcrumbs( 'main-nav' );
	$breadcrumbs      = $menu_breadcrumbs->generate_trail();

	if ( empty( $breadcrumbs ) ) {
		return $crumbs;
	}

	$last_crumb = array_pop( $crumbs );
	$crumbs     = [ reset( $crumbs ) ];

	foreach ( $breadcrumbs as $key => $breadcrumb ) {

		$_url = $breadcrumb->url;
		if ( $_url === '#' ) {
			$_url = '';
		}

		$crumbs[] = array(
			'text'       => $breadcrumb->title,
			'url'        => $_url,
			'allow_html' => false,
		);
	}

	$crumbs[] = $last_crumb;

	return $crumbs;
} );

// todo: handle fist level entries to open nav, if there are any submenu entries
// check if we can change the url directly in the generate_trail method to some hash we can catch using js
