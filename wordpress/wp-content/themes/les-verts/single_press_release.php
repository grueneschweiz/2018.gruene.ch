<?php
/*
 * Template Name: Press Release
 * Template Post Type: post
 */

$context                     = Timber::get_context();
$post                        = new \SUPT\ACFPost();
$context['post']             = $post;
$context['is_press_release'] = true;

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ),
		$context );
}
