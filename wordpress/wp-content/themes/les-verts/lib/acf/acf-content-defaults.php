<?php

// add default layouts for the Default Template
add_filter( 'acf/load_value/key=field_5b6c41217bf2b', 'supt_default_template_add_default_layouts' );


/**
 * Add the 'in short' block and a 'text' block to evey new Default Template post
 *
 * @param $value
 *
 * @return array
 */
function supt_default_template_add_default_layouts( $value ) {
	if ( $value !== null ) {
		// $value will only be NULL on a new post
		return $value;
	}
	
	// add default layouts
	$value = array(
		array(
			'acf_fc_layout'                           => 'in_short',
			'field_5b6c49f24c11d_field_5b6c426cd8fbe' => array(
				array( 'acf_fc_layout' => 'bullet_points' ),
				array( 'acf_fc_layout' => 'call_to_action' ),
			),
		),
		array(
			'acf_fc_layout' => 'text',
		),
	);
	
	return $value;
}
