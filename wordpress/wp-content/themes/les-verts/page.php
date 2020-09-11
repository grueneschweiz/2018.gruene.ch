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

		if ( tribe_is_past() ) {
			$context['events_link'] = [
				// trailing slash leads to bug in tribe events 5.0.* (white page)
				'link'  => rtrim( tribe_get_next_events_link(), '/' ),
				'label' => __( 'Upcoming events', THEME_DOMAIN )
			];
		} else {
			$context['events_link'] = [
				'link'  => tribe_get_previous_events_link(),
				'label' => __( 'Previous events', THEME_DOMAIN )
			];
		}

		array_unshift( $templates, 'archive.twig' );

	} else {

		// the single view
		array_unshift( $templates, 'event.twig' );
	}
}

// handle front page
if ( is_front_page() ) {
	$context['latest_press_release'] = supt_get_latest_press_release( $context );
	$context['latest_posts']         = supt_get_latest_posts( $context );
	$context['events']               = supt_get_events( $context );
	$context['no_sanuk']             = ! file_exists( WP_CONTENT_DIR . '/sanuk/font.ttf' );
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

/**
 * Get the content of the post / page block if set to automatic post selection.
 *
 * Asserts, that no post is twice on the front page (unless added twice manually).
 *
 * @param $context
 *
 * @return array the latest posts
 */
function supt_get_latest_posts( $context ) {
	if ( empty( $context['post']->custom['content_blocks'] ) ) {
		return array();
	}

	$configs = [];
	foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
		if ( $type !== 'single' && $type !== 'double' ) {
			continue;
		}

		$configs = array_merge( $configs, supt_get_post_config_selectors( $id, $type ) );
	}

	$loaded_posts = [];
	if ( $context['latest_press_release'] ) {
		$loaded_posts[] = $context['latest_press_release']->id;
	}

	$latest_posts = [];
	foreach ( $configs as $config ) {
		$data = $context['post'];

		if ( 'manual' === $data->{$config['selection']} ) {
			$loaded_posts[] = (int) $data->{$config['post']};
		}
	}

	foreach ( $configs as $config ) {
		if ( 'latest' === $data->{$config['selection']} ) {
			$cat_id   = $data->{$config['category']};
			$post_ids = get_posts(
				array(
					'numberposts'  => 1,
					'category'     => $cat_id,
					'exclude'      => $loaded_posts,
					'fields'       => 'ids',
					'has_password' => false,
					'post_status'  => 'publish',
					'orderby'      => 'date',
					'order'        => 'DESC',
				)
			);

			if ( $post_ids ) {
				$loaded_posts = array_merge(
					$loaded_posts,
					$post_ids
				);

				$latest_posts[ $config['id'][0] ][ $config['id'][1] ] = $post_ids[0];
			}
		}
	}

	return $latest_posts;
}

function supt_get_post_config_selectors( $id, $type ) {
	if ( 'single' === $type ) {
		return array(
			array(
				'selection' => "content_blocks_{$id}_post_selection",
				'post'      => "content_blocks_{$id}_post",
				'category'  => "content_blocks_{$id}_category",
				'id'        => array( $id, 0 ),
			)
		);
	} else {
		return array(
			array(
				'selection' => "content_blocks_{$id}_post_1_post_selection",
				'post'      => "content_blocks_{$id}_post_1_post",
				'category'  => "content_blocks_{$id}_post_1_category",
				'id'        => array( $id, 0 ),
			),
			array(
				'selection' => "content_blocks_{$id}_post_2_post_selection",
				'post'      => "content_blocks_{$id}_post_2_post",
				'category'  => "content_blocks_{$id}_post_2_category",
				'id'        => array( $id, 1 ),
			),
		);
	}
}
