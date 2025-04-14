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
use Timber\Timber;

$context = Timber::context();
$context['posts'] = new SUPTPostQuery();
$context['title'] = get_the_title(get_option('page_for_posts', true));
$templates = array('home.twig', 'archive.twig', 'index.twig');

Timber::render($templates, $context);
