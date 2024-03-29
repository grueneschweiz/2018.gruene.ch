<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use SUPT\SUPTPostQuery;

$context = Timber::get_context();

if ( isset( $post_type ) && 'tribe_events' === $post_type ) {
	$posts = tribe_get_events( array(
		'eventDisplay'   => tribe_get_request_var(
			'tribe_event_display',
			'list'
		),
		'posts_per_page' => - 1,
	) );
} else {
	$posts = new SUPTPostQuery( array(
		'post_type'           => 'post',
		'orderby'             => 'date',
		'ignore_sticky_posts' => true,
	) );
}

$context['posts']      = $posts;
$context['title']      = __( 'Latest posts', THEME_DOMAIN );
$context['page_class'] = 'page-404';

Timber::render( '404.twig', $context );
