<?php

namespace SUPT;

use \Timber;

class Alert_controller {

	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}

	public static function add_to_context( $context ) {

		$alerts = Timber::get_posts(array(
			'posts_per_page' => -1,
			'post_type'      => 'alert',
			'orderby'        => 'modified',
			'order'          => 'DESC',
			// 'post_status'    => 'publish', // NOTE: don't filter by post_status,
			                                  // it's handled automatically behind-the-scenes
			                                  // so an authenticated user sees if it's private etc.
			'meta_query'     => array(
				array(
					'key'   => 'alert_is_active',
					'value' => 1,
				),
			),
		));


		foreach ( $alerts as $alert ) {
			if ( empty($alert->alert_end_date) || strtotime($alert->alert_end_date) > time() ) {
				$context['alert'] = array(
					"title"        => $alert->post_title,
					"description"  => $alert->alert_description,
					"timestamp"    => get_post_modified_time('G', true, $alert->ID),
					"button"       => $alert->alert_button,
					"cookie_name"  => "supt_alert_" . get_current_blog_id(),
				);
				break;
			}
		}

		return $context;
	}
}
