<?php
/*
 * Template Name: Overview Page
 * Template Post Type: post
 */

$context         = Timber::get_context();
$post            = new \SUPT\ACFPost();
$context['post'] = $post;
$templates       = array( 'overview.twig' );
Timber::render( $templates, $context );
