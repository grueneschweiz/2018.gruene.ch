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

$templates              = array( 'search.twig', 'archive.twig', 'index.twig' );
$context                = Timber::get_context();
$context['list_header'] = true;

$context['posts']               = new \SUPT\SUPTPostQuery();
$context['block_title']         = _x( 'Search', 'Noun', THEME_DOMAIN );
$context['title']               = sprintf( __( 'Search results for: %s', THEME_DOMAIN ), get_search_query() );
$context['archive_description'] = sprintf( __( "You've been searching for: %s", THEME_DOMAIN ), get_search_query() );

Timber::render( $templates, $context );
