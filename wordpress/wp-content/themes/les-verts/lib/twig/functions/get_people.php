<?php

namespace SUPT;

use Twig\TwigFunction;

add_filter( 'timber/twig', function ( $twig ) {
	$twig->addFunction( new TwigFunction( 'getPeopleTestimonialByTaxonomies', function ( $taxonomies ) {
		$args = array(
			'post_type'  => 'people',
			'posts_per_page' => -1,
			'tax_query' => array(
				'relation' => 'AND'
			),
			'meta_query' => array(
				array(
					'key'     => 'testimonial',
					'value'   => '',
					'compare' => '!='
				)
			)
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			if ( ! empty( $terms ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $terms
				);
			}
		}

		$query = new \WP_Query( $args );
		return array_map( function ( $post ) {
			return new ACFPost( $post );
		}, $query->posts );
	} ) );

	return $twig;
} );
