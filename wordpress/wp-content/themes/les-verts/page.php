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

use SUPT\ACFPost;
use SUPT\SUPTPostQuery;

$context   = Timber::get_context();
$post      = new ACFPost();
$templates = array( 'page.twig', 'single.twig' );

// if any post was found (check for name, because we always get a post object back
if ( $post->post_name ) {
	array_unshift( $templates, 'page-' . $post->post_name . '.twig' );

	$context['post'] = $post;
	$post_type       = $post->post_type;
} else {
	// else this must be an archive (event archives use this page)
	global $wp_query;
	$post_type        = $wp_query->query['post_type'];
	$context['posts'] = new SUPTPostQuery();
}

// handle events of the events calendar plugin
if ( 'tribe_events' === $post_type ) {
	if ( in_array( get_query_var( 'eventDisplay' ), [ 'list', 'month' ] ) ) {

		// the list view
		$context['title'] = __( 'Events', THEME_DOMAIN );
		array_unshift( $templates, 'archive.twig' );

	} else {

		// the single view
		array_unshift( $templates, 'event.twig' );
	}
}

// handle front page
if ( is_front_page() ) {
	$context['latest_press_release'] = supt_get_latest_press_release( $context );
	$context['events']               = supt_get_events( $context );
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
	if ( empty( $context['post']->custom['content_blocks'] ) ) {
		return;
	}

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
						'key'   => 'settings_show_on_front_page',
						'value' => '1',
					)
				),
			);

			// get the latest post
			$press_post = Timber::get_posts( $args );

			if ( $press_post ) {
				return $press_post[0];
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
 *
 * @return WP_Query
 */
function supt_get_events( &$context ) {
	$context['venues'] = null;

	if ( empty( $context['post']->custom['content_blocks'] ) ) {
		return;
	}

	foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
		if ( 'events' == $type ) {

			$post_per_page = (int) $context['post']->{"content_blocks_{$id}_max_num"};

			// get upcoming and running events
			$args = array(
				'post_type'      => 'tribe_events',
				'posts_per_page' => $post_per_page,
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

			// the latest post is limited to one, so we're done now
			return $events;
		}
	}
}
