<?php

namespace SUPT;

require_once __DIR__ . '/Model.php';

class PeopleType extends Model {
	
	const MODEL_NAME = 'people';
	
	public static function register_post_types( $domain ) {
		$labels = array(
			'name'                  => __( 'People', $domain ),
			'singular_name'         => __( 'Person', $domain ),
			'add_new_item'          => __( 'Add New Person', $domain ),
			'edit_item'             => __( 'Edit Person', $domain ),
			'new_item'              => __( 'New Person', $domain ),
			'view_item'             => __( 'View Person', $domain ),
			'view_items'            => __( 'View People', $domain ),
			'search_items'          => __( 'Search Person', $domain ),
			'not_found'             => __( 'No people found', $domain ),
			'not_found_in_trash'    => __( 'No people found in trash', $domain ),
			'all_items'             => __( 'All People', $domain ),
			'archives'              => __( 'People Archives', $domain ),
			'attributes'            => __( 'People Attributes', $domain ),
			'uploaded_to_this_item' => __( 'Uploaded to this person', $domain ),
		);
		$args   = array(
			'labels'              => $labels,
			'exclude_from_search' => false,
			'publicly_queryable'  => true, // todo: implement single person page view
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
}
