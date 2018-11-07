<?php

namespace SUPT;

require_once __DIR__ . '/Model.php';

class AlertType extends Model {

	const MODEL_NAME = 'alert';
	const COLUMN_ACTIVE_NAME = 'active';
	const COLUMN_END_DATE_NAME = 'end_date';

	public static function register_post_types($domain) {
		register_post_type(self::MODEL_NAME,
			array(
				'labels' => array(
					'name'               => _x( 'Alerts', 'post type general name', $domain ),
					'singular_name'      => _x( 'Alert', 'post type singular name', $domain ),
					'menu_name'          => _x( 'Alerts', 'admin menu', $domain ),
					'name_admin_bar'     => _x( 'Alerts', 'add new on admin bar', $domain ),
					'add_new_item'       => __( 'Add New Alert', $domain ),
					'new_item'           => __( 'New Alert', $domain ),
					'edit_item'          => __( "Edit Alert", $domain ),
					'view_item'          => __( "View Alert", $domain ),
					'all_items'          => __( 'All Alerts', $domain ),
					'search_items'       => __( 'Search Alerts', $domain ),
					'parent_item'        => null,
					'parent_item_colon'  => null,
					'not_found'          => __( 'No alert found.', $domain ),
				),
				'menu_icon' => 'dashicons-warning', // See https://developer.wordpress.org/resource/dashicons
				'public' => false,
				'show_ui' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'hierarchical' => false,
				'supports' => array(
					'title',
					'revisions'
				),
				'capability_type' => self::MODEL_NAME,
				'map_meta_cap' => true,
			)
		);
		self::register_actions_filters();
	}

	public static function register_actions_filters() {
		add_filter( 'manage_edit-'. self::MODEL_NAME .'_columns', array(__CLASS__, 'register_columns_active' ) );
		add_action( 'manage_'. self::MODEL_NAME .'_posts_custom_column', array(__CLASS__, 'populate_columns_active'), 10, 2 );
	}

	/**
	 * Add extra columns in edit table
	 *
	 * @param array $post_columns An array of column names.
	 */
	public static function register_columns_active( $columns ) {
		return array_merge(
			array_slice( $columns, 0, 2 ),
			array( self::COLUMN_ACTIVE_NAME => __( 'Active', THEME_DOMAIN ) ),
			array( self::COLUMN_END_DATE_NAME => __( 'End date', THEME_DOMAIN ) ),
			array_slice( $columns, 2 )
		);
	}

	/**
	 * Populate extra columns with specific content, if available
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 */
	public static function populate_columns_active( $column_name, $post_id ) {
		if ( $column_name == self::COLUMN_ACTIVE_NAME ) {
			$is_active = get_field( 'alert_is_active', $post_id );
			if ($is_active) {
				$end_date = get_field( 'alert_end_date', $post_id );
				if (!empty($end_date)) {
					$is_active = strtotime($end_date) > time();
				}
			}
			$icon_name = $is_active ? 'yes' : 'no-alt';

			echo "<span class=\"dashicons dashicons-$icon_name\"></span>";
		}
		else if ( $column_name == self::COLUMN_END_DATE_NAME ) {
			$end_date = get_field( 'alert_end_date', $post_id);
			if ( !empty($end_date) ) {
				echo date(get_option( 'date_format' ) .' '. get_option( 'time_format'), strtotime($end_date));
			}
			else {
				echo '-';
			}
		}
	}
}
