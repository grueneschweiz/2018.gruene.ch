<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$templates = array( 'search.twig', 'archive.twig', 'index.twig' );
$context = Timber::get_context();

$context['posts'] = new \SUPT\SUPTPostQuery();
$context['title']               = _x('Search', 'Noun', THEME_DOMAIN);
$context['archive_description'] = __("You've been searching for:", THEME_DOMAIN) .' '. get_search_query();

Timber::render( $templates, $context );
