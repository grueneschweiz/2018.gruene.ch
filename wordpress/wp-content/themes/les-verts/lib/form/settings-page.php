<?php

require_once __DIR__ . '/acf/settings.php';

if ( function_exists( 'acf_add_options_page' ) ) {
	
	acf_add_options_sub_page( array(
		'page_title'  => 'Global form settings',
		'menu_title'  => 'Settings',
		'menu_slug'   => 'form-general-settings',
		'parent_slug' => 'edit.php?post_type=' . \SUPT\FormType::MODEL_NAME,
		'capability'  => 'edit_generaloptions',
	) );
	
}


