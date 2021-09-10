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

$context         = Timber::get_context();
$post            = new ACFPost();
$context['post'] = $post;
$templates       = array( 'page.twig', 'single.twig' );

if ( is_front_page() ) {
	//  Uses lib/controllers/frontpage.php
	array_unshift( $templates, 'front-page.twig' );
} elseif ( 'tribe_events' === $post->post_type ) {
	array_unshift( $templates, 'event.twig' );
} else {
	array_unshift( $templates, 'page-' . $post->post_name . '.twig' );
}

Timber::render( $templates, $context );
