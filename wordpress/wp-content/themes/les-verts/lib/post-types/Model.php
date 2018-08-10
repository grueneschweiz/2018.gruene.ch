<?php

namespace SUPT;

use \Timber;

class Model {

	/**
	 * Get all the posts.
	 */
	static function getPosts($custom_query = array()) {
		return Timber::get_posts(array_merge(
			array(
				'post_type' => static::MODEL_NAME,
				'posts_per_page' => -1,
				// do not move sticky posts to the start of the set by default
				'ignore_sticky_posts' => 1,
			),
			$custom_query
		));
	}

	/**
	 * Get all posts, filtered by a taxonomy value
	 **/
	static function getPostsByTaxonomy($taxonomy_value, $taxonomy_slug = null) {
		if (empty($taxonomy_slug)) {
			$taxonomy_slug = static::CATEGORY_NAME;
		}
		return static::getPosts(
			array(
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy_slug,
						'field' => 'slug',
						'terms' => $taxonomy_value,
					)
				)
			)
		);
	}

	static function getTaxonomies($output = 'names') {
		return get_object_taxonomies(static::MODEL_NAME, $output);
	}

	/**
	 * Get all terms of a taxonomy.
	 * If no custom taxonomy specified, gets all categories.
	 */
	static function getTerms($taxonomy_slug = null, $custom_query = array()) {
		if (empty($taxonomy_slug)) {
			$taxonomy_slug = static::CATEGORY_NAME;
		}
		$terms = Timber::get_terms(array_merge(
			array(
				'taxonomy' => $taxonomy_slug,
				'hide_empty' => true,
			),
			$custom_query
		));
		return $terms;
	}

	/**
	 * Get all posts, grouped by a taxonomy.
	 * If no custom taxonomy specified, groups by category.
	 */
	static function getPostsGroupedByTaxonomy($taxonomy_slug = null) {
		if (empty($taxonomy_slug)) {
			$taxonomy_slug = static::CATEGORY_NAME;
		}
		$groups = array();
		$terms = static::getTerms($taxonomy_slug);
		foreach ($terms as $term) {
			$posts = static::getPostsByTaxonomy($term->slug, $taxonomy_slug);
			$groups[$term->slug] = array(
				'taxonomy' => $term,
				'posts' => $posts,
			);
		}
		return $groups;
	}
}
