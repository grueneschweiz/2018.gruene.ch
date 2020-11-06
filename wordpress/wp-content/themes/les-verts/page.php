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
	//  Uses lib/controllers/frontpage.php
	array_unshift( $templates, 'front-page.twig' );
}

Timber::render( $templates, $context );
