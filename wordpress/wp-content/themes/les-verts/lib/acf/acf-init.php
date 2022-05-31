<?php

/**
 * Set ACF settings on init
 *
 * Read more: https://www.advancedcustomfields.com/resources/acf-settings/
 */
add_action( 'acf/init', function () {
	acf_update_setting( 'l10n_textdomain', THEME_DOMAIN );
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
