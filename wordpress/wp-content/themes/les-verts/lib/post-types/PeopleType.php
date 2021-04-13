<?php

namespace SUPT;

require_once __DIR__ . '/Model.php';

class PeopleType extends Model {
	
	const MODEL_NAME = 'people';
	
	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'People', THEME_DOMAIN ),
			'singular_name'         => __( 'Person', THEME_DOMAIN ),
			'add_new_item'          => __( 'Add New Person', THEME_DOMAIN ),
			'edit_item'             => __( 'Edit Person', THEME_DOMAIN ),
			'new_item'              => __( 'New Person', THEME_DOMAIN ),
			'view_item'             => __( 'View Person', THEME_DOMAIN ),
			'view_items'            => __( 'View People', THEME_DOMAIN ),
			'search_items'          => __( 'Search Person', THEME_DOMAIN ),
			'not_found'             => __( 'No people found', THEME_DOMAIN ),
			'not_found_in_trash'    => __( 'No people found in trash', THEME_DOMAIN ),
			'all_items'             => __( 'All People', THEME_DOMAIN ),
			'archives'              => __( 'People Archives', THEME_DOMAIN ),
			'attributes'            => __( 'People Attributes', THEME_DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this person', THEME_DOMAIN ),
		);
		$args   = array(
			'labels'              => $labels,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'menu_icon'           => 'dashicons-groups',
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'revisions', 'custom-fields' ),
			'has_archive'         => false,
			'can_export'          => true,
			'show_in_rest'        => true,
		);
		
		register_post_type( self::MODEL_NAME, $args );
	}
	
	public static function register_taxonomy() {
		$args = array(
			'hierarchical'      => false,
			'label'             => __( 'Testimonial Category', THEME_DOMAIN ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => false,
			'description'       => __( 'This taxonomy is used to organise the testimonials only.', THEME_DOMAIN ),
			'query_var'         => false,
			'rewrite'           => false,
			'public'            => false,
		);
		
		register_taxonomy( 'testimonials', self::MODEL_NAME, $args );
	}
}
