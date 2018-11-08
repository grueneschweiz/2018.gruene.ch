<?php

namespace SUPT;

use \Timber\Timber;

require_once realpath( __DIR__ . '/../post-types/Model.php' );

class FormType extends Model {
	
	const MODEL_NAME = 'theme_form';
	const COLUMN_FIELD_NAME = 'fields';
	const POST_META_NAME_FORM_SENT = 'form_sent';
	
	static function register_type() {
		self::register_post_type();
		self::register_acf_fields();
		self::register_actions_filters();
	}
	
	public static function register_post_type() {
		register_post_type( self::MODEL_NAME,
			array(
				'labels'              => array(
					'name'               => _x( 'Forms', 'post type general name', THEME_DOMAIN ),
					'singular_name'      => _x( 'Form', 'post type singular name', THEME_DOMAIN ),
					'menu_name'          => _x( 'Forms', 'admin menu', THEME_DOMAIN ),
					'name_admin_bar'     => _x( 'Form', 'add new on admin bar', THEME_DOMAIN ),
					'add_new'            => _x( 'Add New', 'form', THEME_DOMAIN ),
					'add_new_item'       => __( 'Add New Form', THEME_DOMAIN ),
					'new_item'           => __( 'New Form', THEME_DOMAIN ),
					'edit_item'          => __( 'Edit Form', THEME_DOMAIN ),
					'view_item'          => __( 'View Form', THEME_DOMAIN ),
					'all_items'          => __( 'All Forms', THEME_DOMAIN ),
					'search_items'       => __( 'Find form', THEME_DOMAIN ),
					'parent_item_colon'  => __( 'Form:', THEME_DOMAIN ),
					'not_found'          => __( 'No form found', THEME_DOMAIN ),
					'not_found_in_trash' => __( 'No form in the trash', THEME_DOMAIN )
				),
				'menu_icon'           => 'dashicons-feedback', // See https://developer.wordpress.org/resource/dashicons
				'public'              => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_in_nav_menus'   => false,
				'has_archive'         => false, // false = do not generate an archive (list) page
				'rewrite'             => array(
					'slug'       => 'form',
					'with_front' => false,
					'pages'      => false,
				),
				'supports'            => array(
					'title',
					'revisions'
				),
				'capability_type'     => self::MODEL_NAME,
				'map_meta_cap'        => true,
			)
		);
	}
	
	public static function register_acf_fields() {
		require_once __DIR__ . '/acf/input.php';
		require_once __DIR__ . '/acf/form-details.php';
		require_once __DIR__ . '/acf/mail-template.php';
		require_once __DIR__ . '/acf/form-local-settings.php';
	}
	
	public static function register_actions_filters() {
		add_action( 'admin_enqueue_scripts',
			array( __CLASS__, 'admin_enqueue_scripts' ) );
		
		add_filter( 'manage_edit-' . self::MODEL_NAME . '_columns',
			array( __CLASS__, 'register_columns_fields' ) );
		add_action( 'manage_' . self::MODEL_NAME . '_posts_custom_column',
			array( __CLASS__, 'populate_columns_fields' ),
			10, 2 );
		add_action( 'request',
			array( __CLASS__, 'order_by_title' ) );
		
		add_action( 'admin_menu',
			array( __CLASS__, 'add_sent_page_menu' ) );
	}
	
	/**
	 * Enqueue custom scripts & styles
	 */
	public static function admin_enqueue_scripts() {
		
		if ( self::MODEL_NAME != get_post_type() ) {
			return;
		}
		
		$folder_uri = get_template_directory_uri() . '/lib/form';
		wp_enqueue_script( 'slugify', "$folder_uri/scripts/slug.min.js", false, THEME_VERSION, true );
		wp_enqueue_script('form_populate_placeholder_tags', "$folder_uri/scripts/input-populate-placeholder-tags.js", false, THEME_VERSION, true );
	}
	
	
	/**
	 * Add extra columns in edit table
	 *
	 * @param array $columns An array of column names.
	 *
	 * @return array
	 */
	public static function register_columns_fields( $columns ) {
		return array_merge(
			array_slice( $columns, 0, 2 ),
			array( self::COLUMN_FIELD_NAME => __( 'Fields', THEME_DOMAIN ) ),
			array_slice( $columns, 2 )
		);
	}
	
	/**
	 * Populate extra columns with specific content, if available
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int $post_id The current post ID.
	 */
	public static function populate_columns_fields( $column_name, $post_id ) {
		if ( $column_name == self::COLUMN_FIELD_NAME ) {
			$fields = get_field( 'form_fields', $post_id );
			
			echo implode( ', ', array_map( function ( $f ) {
				return $f['form_input_label'] . ( $f['form_input_required'] ? '*' : '' );
			}, $fields ) );
			
		}
	}
	
	/**
	 * Set the default order to "Title ASC" if no order set
	 *
	 * @param array $query_vars The array of requested query variables.
	 *
	 * @return array
	 */
	public static function order_by_title( $query_vars ) {
		global $pagenow, $post_type;
		if ( ! is_admin() || $pagenow != 'edit.php' || $post_type != self::MODEL_NAME ) {
			return $query_vars;
		}
		
		if ( empty( filter_input( INPUT_GET, 'orderby' ) ) && empty( filter_input( INPUT_GET, 'order' ) ) ) {
			$query_vars['orderby'] = 'title';
			$query_vars['order']   = 'ASC';
		}
		
		return $query_vars;
	}
	
	public static function add_sent_page_menu() {
		add_submenu_page( 'edit.php?post_type='.self::MODEL_NAME, __( 'Submissions', THEME_DOMAIN ),
			__( 'Submissions', THEME_DOMAIN ), 'edit_pages', 'form_sent', array( __CLASS__, 'render_sent_page' ) );
	}
	
	public static function render_sent_page() {
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => self::MODEL_NAME,
		);
		
		foreach ( get_posts( $args ) as $form ) {
			$fields = array_map(
				function ( $f ) {
					return array(
						'label' => $f['form_input_label'],
						'type'  => $f['form_input_type']
					);
				},
				get_field( 'form_fields', $form->ID )
			);
			
			$context['forms'][] = array(
				'id'     => $form->ID,
				'title'  => $form->post_title,
				'fields' => $fields,
				'sents'  => array_reverse( get_post_meta( $form->ID, FormType::POST_META_NAME_FORM_SENT ) ),
			);
		}
		
		Timber::render( 'form-sent-page.twig', $context );
	}
	
}
