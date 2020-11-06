<?php
/*
 * Template Name: Front Page
 * Template Post Type: page
 *
 * Uses lib/controllers/frontpage.php
 */

use SUPT\ACFPost;

$context         = Timber::get_context();
$post            = new ACFPost();
$context['post'] = $post;
$templates       = array( 'front-page.twig' );

Timber::render( $templates, $context );
