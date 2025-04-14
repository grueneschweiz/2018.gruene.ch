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
use Timber\Timber;

$templates = array('search.twig', 'archive.twig', 'index.twig');
$context = Timber::context();

$context['title'] = sprintf(__('Search results for "%s"', THEME_DOMAIN), get_search_query());
$context['posts'] = new SUPTPostQuery();

Timber::render($templates, $context);
