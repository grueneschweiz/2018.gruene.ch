<?php
/*
 * Template Name: Overview
 * Template Post Type: page
 */

$context         = Timber::get_context();
$post            = new \SUPT\ACFPost();
$context['post'] = $post;
$templates       = array( 'overview.twig' );
Timber::render( $templates, $context );
