<?php

namespace SUPT;

require_once __DIR__ . '/Model.php';

class BlockType extends Model {
	
	const MODEL_NAME = 'block';
	
	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Blocks', THEME_DOMAIN ),
			'singular_name'         => __( 'Block', THEME_DOMAIN ),
			'add_new_item'          => __( 'Add New Block', THEME_DOMAIN ),
			'edit_item'             => __( 'Edit Block', THEME_DOMAIN ),
			'new_item'              => __( 'New Block', THEME_DOMAIN ),
			'view_item'             => __( 'View Block', THEME_DOMAIN ),
			'view_items'            => __( 'View Blocks', THEME_DOMAIN ),
			'search_items'          => __( 'Search Blocks', THEME_DOMAIN ),
			'not_found'             => __( 'No blocks found', THEME_DOMAIN ),
			'not_found_in_trash'    => __( 'No blocks found in trash', THEME_DOMAIN ),
			'all_items'             => __( 'All Blocks', THEME_DOMAIN ),
			'archives'              => __( 'Block Archives', THEME_DOMAIN ),
			'attributes'            => __( 'Block Attributes', THEME_DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this block', THEME_DOMAIN ),
		);
		$args   = array(
			'labels'              => $labels,
			'exclude_from_search' => false,
			'publicly_queryable'  => true, // todo: implement single block page view
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'menu_icon'           => 'dashicons-screenoptions',
			'capability_type'     => 'post',
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
