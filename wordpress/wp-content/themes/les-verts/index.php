<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

use SUPT\SUPTPostQuery;
use Timber\Timber;

$context = Timber::context();
$context['posts'] = new SUPTPostQuery();
$templates = array('index.twig');

if (is_home()) {
    $context['title'] = get_the_title(get_option('page_for_posts', true));
}

Timber::render($templates, $context);
