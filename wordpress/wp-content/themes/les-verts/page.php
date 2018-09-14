<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::get_context();
$post            = new TimberPost();
$context['post'] = $post;
$templates       = array( 'page-' . $post->post_name . '.twig', 'page.twig', 'single.twig' );
if ( is_front_page() ) {
	supt_get_latest_press_release( $context );
	supt_get_events( $context );
	array_unshift( $templates, 'front-page.twig' );
}
Timber::render( $templates, $context );

/**
 * Adds a post 'latest_press_release' to the context. Uses the category id from
 * the media block (front page only) as well as the exclude categories.
 *
 * @param $context
 */
function supt_get_latest_press_release( &$context ) {
	$context['latest_press_release'] = null;
	
	foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
		if ( 'media' == $type ) {
			// get post category
			$id     = (int) $id;
			$cat_id = $context['post']->custom["content_blocks_{$id}_category"];
			
			// get latest post query
			$args = array(
				'posts_per_page' => 1,
				'cat'            => $cat_id,
				'post_status'    => 'publish', // prevent 'private' if logged in
				'meta_query'     => array(
					array(
						'key'     => 'settings_show_on_front_page',
						'value'   => '1',
					)
				),
			);
			
			// get the latest post
			$press_post = Timber::get_posts( $args );
			
			if ( $press_post ) {
				$context['latest_press_release'] = $press_post[0];
			}
			
			// the latest post is limited to one, so we're done now
			return;
		}
	}
}

/**
 * Adds the event posts to the context (only if the events block is used)
 *
 * @param $context
 */
function supt_get_events( &$context ) {
	$context['events'] = null;
	$context['venues'] = null;
	
	foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
		if ( 'events' == $type ) {
			
			// get upcoming and running events
			$args = array(
				'post_type'      => 'tribe_events',
				'posts_per_page' => - 1,
				'post_status'    => 'publish', // prevent 'private' if logged in
				'orderby'        => 'meta_value_datetime',
				'meta_query'     => array(
					array(
						'key'     => '_EventEndDate',
						'compare' => '>',
						'value'   => date( 'Y-m-d H:i:s' ),
						'type'    => 'DATETIME'
					)
				),
			);
			
			$events = Timber::get_posts( $args );
			
			if ( $events ) {
				$context['events'] = $events;
			}
			
			// add the venues
			$venue_ids = array();
			foreach ( $events as $event ) {
				if ( $event->_EventVenueID ) {
					array_push( $venue_ids, $event->_EventVenueID );
				}
			}
			
			$venue_args = array(
				'post_type'      => 'tribe_venue',
				'posts_per_page' => - 1,
				'post_status'    => 'publish', // prevent 'private' if logged in
				'post__in'       => $venue_ids
			);
			
			$venues = Timber::get_posts( $venue_args );
			if ( $venues ) {
				foreach($venues as $venue){
					$context['venues'][$venue->id] = $venue;
				}
			}
			
			// the latest post is limited to one, so we're done now
			return;
		}
	}
}
