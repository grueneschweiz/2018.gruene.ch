<?php
/*
 * Template Name: Landing Page
 * Template Post Type: post, page
 */

use SUPT\ACFPost;

$context                 = Timber::get_context();
$post                    = new ACFPost();
$context['post']         = $post;
$context['landing_page'] = true;
$templates               = array( 'single.twig' );
Timber::render( $templates, $context );
