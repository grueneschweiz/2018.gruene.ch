<?php
/*
 * Template Name: Press Release
 * Template Post Type: post
 */

$context         = Timber::get_context();
$post            = Timber::query_post();
$context['post'] = $post;

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ),
		$context );
}
