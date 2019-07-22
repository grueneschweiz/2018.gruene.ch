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

use SUPT\SUPTPostQuery;

$templates = array( 'search.twig', 'archive.twig', 'index.twig' );
$query     = new SUPTPostQuery();

$context                = Timber::get_context();
$context['list_header'] = true;
$context['posts']       = $query;
$context['block_title'] = _x( 'Search', 'Noun', THEME_DOMAIN );
$context['title']       = sprintf( _n( 'Search result for: %s', 'Search results for: %s', $query->found_posts(), THEME_DOMAIN ), get_search_query() );

global $wp;
$context['unfiltered_results_link'] = remove_query_arg( 'cat', add_query_arg( $wp->query_vars, home_url( $wp->request ) ) );

Timber::render( $templates, $context );
