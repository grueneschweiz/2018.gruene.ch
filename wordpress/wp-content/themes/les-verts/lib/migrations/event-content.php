<?php

namespace SUPT\Migrations\EventContent;

use WP_Query;

/**
 * Migrate event body text from the ACF description field to the much more versatile event_content
 */
add_action( 'wp', function () {
	$query = new WP_Query( array(
		'post_type'   => array( 'tribe_events' ),
		'post_status' => 'any',
		'nopaging'    => true
	) );

	while ( $query->have_posts() ) {
		$query->the_post();

		/**
		 * move body text
		 */
		$content = get_field( 'event_content', false );

		if ( empty( $content['content'] ) ) {
			$content['content'] = array();
		}

		/**
		 * move legacy acf event description
		 */
		if ( ! empty( get_field( 'description' ) ) ) {
			$idx                        = empty( $content['content'] ) ? 0 : count( $content );
			$content['content'][ $idx ] = array(
				'acf_fc_layout' => 'text',
				'text'          => get_field( 'description' )
			);

			update_field( 'event_content', $content );
			delete_field( 'description' );
		}
	}

	wp_reset_postdata();
} );
