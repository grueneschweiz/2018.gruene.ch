<?php

/**
 * Set ACF settings on init
 *
 * Read more: https://www.advancedcustomfields.com/resources/acf-settings/
 */
add_action( 'acf/init', function () {
	acf_update_setting( 'l10n_textdomain', THEME_DOMAIN );

	// Add Tracking Scripts options page under Settings
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title'  => __( 'Tracking Scripts', THEME_DOMAIN ),
			'menu_title'  => __( 'Tracking Scripts', THEME_DOMAIN ),
			'menu_slug'   => 'tracking-scripts-settings',
			'capability'  => 'manage_options',
			'parent_slug' => 'options-general.php',
		] );
	}
} );

/**
 * Remove code block, if acf-code-field plugin is missing
 */
add_filter( 'acf/load_field/key=field_5cee95b5caff7', function ( $field ) {
	if ( ! is_plugin_active( 'acf-code-field/acf-code-field.php' ) ) {
		unset( $field['layouts']['layout_5c4f21cbd787c'] );
	}

	return $field;
} );

/**
 * Conditionally hide Facebook Pixel enable field if no Pixel ID is configured
 */
add_filter( 'acf/prepare_field/key=field_enable_facebook_pixel', function ( $field ) {
	if ( ! $field ) {
		return $field;
	}

	$pixel_id = get_field( 'tracking_facebook_pixel_id', 'option' );

	if ( empty( $pixel_id ) ) {
		return false; // Hide field
	}

	return $field;
} );

/**
 * Conditionally hide custom script enable fields based on configured scripts
 */
add_filter( 'acf/prepare_field', function ( $field ) {
	// Validate field exists and is an array
	if ( ! $field || ! is_array( $field ) || ! isset( $field['key'] ) ) {
		return $field;
	}

	// Check if this is a custom script enable field
	if ( strpos( $field['key'], 'field_enable_custom_script_' ) === 0 ) {
		// Extract the script index from the field key
		preg_match( '/field_enable_custom_script_(\d+)/', $field['key'], $matches );

		if ( isset( $matches[1] ) ) {
			$script_index = intval( $matches[1] );
			$custom_scripts = get_field( 'tracking_custom_scripts', 'option' );

			// Hide field if this script index doesn't exist in configuration
			if ( ! is_array( $custom_scripts ) || ! isset( $custom_scripts[ $script_index ] ) || empty( $custom_scripts[ $script_index ]['script_url'] ) ) {
				return false;
			}

			// Update field label with the actual script name
			if ( ! empty( $custom_scripts[ $script_index ]['script_name'] ) ) {
				$field['label'] = 'Enable ' . $custom_scripts[ $script_index ]['script_name'];
				$field['message'] = 'Load ' . $custom_scripts[ $script_index ]['script_name'] . ' on this page';
			}
		}
	}

	return $field;
} );
