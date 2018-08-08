<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::get_context();
$post            = new TimberPost();
$context['post'] = $post;
$templates       = array( 'page-' . $post->post_name . '.twig', 'page.twig' );
if ( is_front_page() ) {
	supt_get_latest_press_release( $context );
	array_unshift( $templates, 'front-page.twig' );
}
Timber::render( $templates, $context );

/**
 * Adds a post 'latest_press_release' to the context. Uses the category id from
 * the media block (front page only) as well as the exclude categories.
 *
 * @param $context
 */
function supt_get_latest_press_release( &$context ) {
	$context['latest_press_release'] = null;
	
	foreach ( $context['post']->custom['content_blocks'] as $id => $type ) {
		if ( 'media' == $type ) {
			// get post category
			$id     = (int) $id;
			$cat_id = $context['post']->custom["content_blocks_{$id}_category_category"];
			
			// get latest post query
			$args = array(
				'posts_per_page' => 1,
				'cat'            => $cat_id,
				'post_status'    => 'publish', // prevent 'private' if logged in
			);
			
			// exclude the posts form the excluded categories
			$exclude_ids = $context['post']->custom["content_blocks_{$id}_exclude_category"];
			if ( $exclude_ids ) {
				$args['category__not_in'] = $exclude_ids;
			}
			
			// get the latest post
			$press_post = Timber::get_posts( $args );
			
			if ( $press_post ) {
				$context['latest_press_release'] = $press_post[0];
			}
			
			// the latest post is limited to one, so we're done now
			return;
		}
	}
}
