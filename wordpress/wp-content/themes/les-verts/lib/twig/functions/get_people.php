<?php

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFunction( new Twig_SimpleFunction( 'getPeopleTestimonialTaxonomies', function ( $category ) {
			$meta_query = array();
			foreach ( $category as $cat_id ) {
				$meta_query[] = array(
					'key'     => 'testimonials_$_taxonomy',
					'compare' => 'LIKE',
					'value'   => '"' . $cat_id . '"',
				);
			}
			
			$meta_query['relation'] = 'OR';
			
			
			$query = array(
				'post_type'  => 'people',
				'meta_query' => $meta_query
			);
			
			return new \Timber\PostQuery( $query, '\SUPT\SUPTPerson' );
		} )
	);
	
	return $twig;
} );


add_filter('posts_where', function($where){
	/**
	 * @link https://www.advancedcustomfields.com/resources/query-posts-custom-fields/
	 */
	$where = str_replace('meta_key = \'testimonials_$_taxonomy', "meta_key LIKE 'testimonials_%_taxonomy", $where);
	
	return $where;
});
