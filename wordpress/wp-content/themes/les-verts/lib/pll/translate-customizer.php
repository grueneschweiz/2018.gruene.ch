<?php

/**
 * Make the customizer translatable
 */
if ( is_admin() ) {
	add_filter( 'scp_js_path_url', function ( $path_url ) {
		$path_url = get_stylesheet_directory_uri() . '/vendor/soderlind/customizer-polylang/js';

		return $path_url;
	} );
}
