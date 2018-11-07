<?php

	/**
	 * Check documentation for example
	 * https://www.advancedcustomfields.com/resources/options-page/
	 */

	if( function_exists( 'acf_add_options_page' ) && function_exists( 'acf_add_options_sub_page' ) ) {

		// Main Options Page
		$parent = acf_add_options_page(array(
			'page_title'  => 'Options',
			'menu_title'  => 'Options',
			'capability'  => 'manage_options',
			'redirect'    => 'Global Options'
		));

		// Global Options
		// Same options on all languages. e.g., social profiles links
		acf_add_options_sub_page( array(
			'page_title' =>__('Global Options', THEME_DOMAIN),
			'menu_title' => __('Global Options', THEME_DOMAIN),
			'menu_slug'  => 'acf-options',
			'capability' => 'manage_options',
			'parent'     => $parent['menu_slug']
		) );

		// acf_add_options_sub_page( array(
		// 	'page_title' => __('Advanced Options', THEME_DOMAIN),
		// 	'menu_title' => __('Advanced Options', THEME_DOMAIN),
		// 	'menu_slug'  => 'acf-options-advanced',
		// 	'capability' => 'manage_options',
		// 	'parent'     => $parent['menu_slug']
		// ) );

		// Language Specific Options
		// Translatable options specific languages. e.g., social profiles links
		if ( function_exists('pll_languages_list') ) {
			$languages = pll_languages_list();
			foreach ( $languages as $lang ) {
				acf_add_options_sub_page( array(
					'page_title' => __('Options (' . strtoupper( $lang ) . ')', THEME_DOMAIN),
					'menu_title' => __('Options (' . strtoupper( $lang ) . ')', THEME_DOMAIN),
					'menu_slug'  => 'acf-options-lang-' . $lang,
					'capability' => 'manage_options',
					'post_id'    => $lang,
					'parent'     => $parent['menu_slug']
				) );
			}
		}

		add_action( 'admin_head', function() {
			echo '<style>
				.wp-admin .card img { max-width: 100%; }
			</style>';
		} );

	}
