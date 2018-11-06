<?php

namespace SUPT;

use \Timber;

require_once realpath(__DIR__ . '/../post-types/Model.php');

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
		register_post_type(self::MODEL_NAME,
			array(
				'labels' => array(
					'name'               => _x( 'Forms', 'post type general name', THEME_DOMAIN ),
					'singular_name'      => _x( 'Form', 'post type singular name', THEME_DOMAIN ),
					'menu_name'          => _x( 'Forms', 'admin menu', THEME_DOMAIN ),
					'name_admin_bar'     => _x( 'Form', 'add new on admin bar', THEME_DOMAIN ),
					'add_new'            => _x( 'Add', 'formulaire', THEME_DOMAIN ),
					'add_new_item'       => __( 'Add form', THEME_DOMAIN ),
					'new_item'           => __( 'New form', THEME_DOMAIN ),
					'edit_item'          => __( 'Edit form', THEME_DOMAIN ),
					'view_item'          => __( 'View form', THEME_DOMAIN ),
					'all_items'          => __( 'All forms', THEME_DOMAIN ),
					'search_items'       => __( 'Find form', THEME_DOMAIN ),
					'parent_item_colon'  => __( 'Form:', THEME_DOMAIN ),
					'not_found'          => __( 'No form found', THEME_DOMAIN ),
					'not_found_in_trash' => __( 'No form in the trash', THEME_DOMAIN )
				),
				'menu_icon' => 'dashicons-feedback', // See https://developer.wordpress.org/resource/dashicons
				'public' => false,
				'show_ui' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'has_archive' => false, // false = do not generate an archive (list) page
				'rewrite' => array(
					'slug'	=> 'form',
					'with_front' => false,
					'pages'	=> false,
				),
				'supports' => array(
					'title',
					'revisions'
				),
				'capability_type' => self::MODEL_NAME,
				'map_meta_cap' => true,
			)
		);
	}

	public static function register_acf_fields() {
		require_once __DIR__ .'/acf/input.php';
		require_once __DIR__ .'/acf/form-details.php';
		require_once __DIR__ .'/acf/form-local-settings.php';

		add_filter( 'acf/prepare_field/name=form_email_to', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_email_from', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_name_from', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_notif_subject', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_notif_template', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_subject', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_template', array( __CLASS__, 'acf_populate_placeholder_local_settings') );
		add_filter( 'acf/prepare_field/name=form_privacy_page', array( __CLASS__, 'acf_populate_placeholder_local_settings') );

		add_filter( 'acf/prepare_field/name=form_notif_subject', array( __CLASS__, 'acf_remove_default_local_settings') );
		add_filter( 'acf/prepare_field/name=form_notif_template', array( __CLASS__, 'acf_remove_default_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_subject', array( __CLASS__, 'acf_remove_default_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_template', array( __CLASS__, 'acf_remove_default_local_settings') );

		add_filter( 'acf/prepare_field/name=form_email_to', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_email_from', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_name_from', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_notif_subject', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_notif_template', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_subject', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_autoreply_template', array( __CLASS__, 'acf_unrequired_local_settings') );
		add_filter( 'acf/prepare_field/name=form_privacy_page', array( __CLASS__, 'acf_unrequired_local_settings') );

		add_filter( 'acf/prepare_field/name=which_type_of_form', array( __CLASS__, 'acf_which_type_values') );

		add_filter( 'acf/validate_value/name=form_email_to', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_email_from', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_name_from', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_notif_subject', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_notif_template', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_autoreply_subject', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_autoreply_template', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
		add_filter( 'acf/validate_value/name=form_privacy_page', array( __CLASS__, 'acf_validate_empty_value_local_settings'), 20, 2 );
	}

	/**
	 * Populate the placholder attribute with the globale Options
	 */
	public static function acf_populate_placeholder_local_settings( $field ) {

		// Avoid the filter to alter global setting field
		$current_screen = get_current_screen();
		if ( $current_screen->base != 'post' ) {
			return $field;
		}

		if ( in_array($field['key'], ['field_5a8822f4c6319', 'field_5a882319c631b']) ) {
			$parent = get_field_object($field['parent'], 'options');
			$global_option_value =  get_field( $parent['_name'], 'options' );
			if ( isset($global_option_value[ $field['_name'] ]) ) {
				$field['placeholder'] = $global_option_value[ $field['_name'] ];
			}
		}
		else {
			$field['placeholder'] = get_field( $field['_name'], 'options' );
		}

		return $field;
	}

	/**
	 * Set required parameter to false
	 */
	public static function acf_remove_default_local_settings( $field ) {

		// Avoid the filter to alter global setting field
		$current_screen = get_current_screen();
		if ( $current_screen->base != 'post' ) {
			return $field;
		}

		if ( $field['value'] == $field['default_value']) {
			$field['value'] = '';
		}

		return $field;
	}

	/**
	 * Set required parameter to false
	 */
	public static function acf_unrequired_local_settings( $field ) {

		// Avoid the filter to alter global setting field
		$current_screen = get_current_screen();
		if ( $current_screen->base != 'post' ) {
			return $field;
		}

		$field['required'] = 0;

		return $field;
	}

	/**
	 * Do not show Campaign monitor option in type of form
	 * If Campaign monitor is not available
	 */
	public static function acf_which_type_values( $field ) {

		if ( !class_exists('CampaignMonitorAPI') ) {
			$field['choices'] = array_splice($field['choices'], 0, 1);
		}

		return $field;
	}

	/**
	 * Avoid the error on validation for local settings fields when they are empty.
	 */
	public static function acf_validate_empty_value_local_settings( $valid, $value ) {

		// Avoid the filter to alter global setting field
		if ( !isset($_POST['post_type']) || $_POST['post_type'] != FormType::MODEL_NAME ) {
			return $valid;
		}

		if ( !$valid && empty($value) ) {
			$valid = true;
		}

		return $valid;
	}

	public static function register_actions_filters() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ));
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'save_title_in_post_content' ), 99, 2 );

		add_filter( 'manage_edit-'. self::MODEL_NAME .'_columns', array(__CLASS__, 'register_columns_fields' ) );
		add_action( 'manage_'. self::MODEL_NAME .'_posts_custom_column', array(__CLASS__, 'populate_columns_fields'), 10, 2 );
		add_action( 'request', array( __CLASS__, 'order_by_title') );

		add_action( 'admin_menu', array(__CLASS__, 'add_sent_page_menu') );
	}

	/**
	 * Enqueue custom scripts & styles
	 */
	public static function admin_enqueue_scripts( ) {
		
		// todo: remove if not needed
		
		if ( self::MODEL_NAME != get_post_type() ) {
			return;
		}

		$folder_uri = get_template_directory_uri() .'/lib/form';

		//wp_enqueue_script( 'slugify', "$folder_uri/scripts/slug.min.js", false, THEME_VERSION, true );
	}

	/**
	 * Make sure the title is saved
	 * Because the title field is disabled and not send in the form submisstion
	 */
	public static function save_title_in_post_content( $data , $postarr ) {
		if ( $postarr['post_type'] === self::MODEL_NAME && isset($postarr['acf']) ) {
			$data['post_title'] = $postarr['acf']['field_59f33e98216af'];
		}
		return $data;
	}



	/**
	 * Add extra columns in edit table
	 *
	 * @param array $post_columns An array of column names.
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
	 * @param int    $post_id     The current post ID.
	 */
	public static function populate_columns_fields( $column_name, $post_id ) {
		if ( $column_name == self::COLUMN_FIELD_NAME ) {
			$fields = get_field( 'fields', $post_id );
			$confirm_fields = get_field( 'confirmation_fields', $post_id );

			echo implode(', ', array_map( function($f) {
				return $f['label']. ($f['required'] ? '*' : '');
			}, $fields) );

		}
	}

	/**
	 * Set the default order to "Title ASC" if no order set
	 *
	 * @param array $query_vars The array of requested query variables.
	 */
	public static function order_by_title( $query_vars ) {
		global $pagenow, $post_type;
		if ( !is_admin() || $pagenow != 'edit.php' || $post_type != self::MODEL_NAME ) {
			return $query_vars;
		}

		if ( empty(filter_input(INPUT_GET, 'orderby')) && empty(filter_input(INPUT_GET, 'order')) ) {
			$query_vars['orderby'] = 'title';
			$query_vars['order'] = 'ASC';
		}

		return $query_vars;
	}

	public static function add_sent_page_menu() {
		if ( get_field('form_save_db', 'options') ) {
			add_submenu_page( 'edit.php?post_type=theme_form', __('Form submissions', THEME_DOMAIN), __('Submissions', THEME_DOMAIN), 'edit_pages', 'form_sent', array( __CLASS__, 'render_sent_page' ) );
		}
	}

	public static function render_sent_page() {

		$args = array(
			'posts_per_page'	=> -1,
			'post_type'				=> self::MODEL_NAME,
		);

		foreach ( get_posts($args) as $form) {
			$fields = array_map(
				function($f) {
					return array(
						'name'	=> ( empty($f['name']) ? supt_form_sanitize_with_underscore($f['label']) : $f['name'] ),
						'label'	=> $f['label'],
						'type'	=> $f['type']
					);
				},
				get_field( 'fields', $form->ID )
			);

			$confirm_fields = get_field('confirmation_fields', $form->ID);
			if ( !is_array($confirm_fields) ) $confirm_fields = array();
			$confirm_fields = array_map(
				function($f) {
					return array(
						'name'	=> ( empty($f['name']) ? supt_form_sanitize_with_underscore($f['label']) : $f['name'] ),
						'label'	=> $f['label'],
						'type'	=> 'checkbox'
					);
				},
				$confirm_fields
			);

			$context['forms'][] = array(
				'id'			=> $form->ID,
				'title'		=> get_field( 'title', $form->ID ),
				'fields'	=> array_merge( $fields, $confirm_fields ),
				'sents'		=> array_reverse( get_post_meta( $form->ID, FormType::POST_META_NAME_FORM_SENT ) ),
			);
		}

		Timber::render( 'form-sent-page.twig', $context );
	}

}
