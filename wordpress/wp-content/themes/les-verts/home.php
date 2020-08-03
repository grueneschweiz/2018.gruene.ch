<?php

/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

use SUPT\ACFPost;
use SUPT\SUPTPostQuery;

$templates = array( 'home.twig', 'archive.twig', 'index.twig' );

$context          = Timber::get_context();
$posts            = new SUPTPostQuery();
$context['posts'] = $posts;

$post                           = new ACFPost();
$context['title']               = $post->post_title;
$context['archive_description'] = $post->full_excerpt;

Timber::render( $templates, $context );
