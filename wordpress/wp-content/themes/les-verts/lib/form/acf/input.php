<?php

if ( function_exists( 'acf_add_local_field_group' ) ):
	
	acf_add_local_field_group( array(
		'key'                   => 'group_59e71d68896a7',
		'title'                 => 'a-input',
		'fields'                => array(
			array(
				'key'               => 'field_59f33777cf0db',
				'label'             => __( 'Field Label', THEME_DOMAIN ),
				'name'              => 'form_input_label',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '50',
					'class' => 'form_input_label',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_59f33814cf0dc',
				'label'             => __( 'Type', THEME_DOMAIN ),
				'name'              => 'form_input_type',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'choices'           => array(
					'text'         => __( 'Text', THEME_DOMAIN ),
					'email'        => __( 'Email', THEME_DOMAIN ),
					'tel'          => __( 'Phone', THEME_DOMAIN ),
					'number'       => __( 'Number', THEME_DOMAIN ),
					'textarea'     => __( 'Textarea', THEME_DOMAIN ),
					'radio'        => __( 'Radio button', THEME_DOMAIN ),
					'checkbox'     => __( 'Check box', THEME_DOMAIN ),
					'select'       => __( 'Drop down', THEME_DOMAIN ),
					'confirmation' => __( 'Confirmation', THEME_DOMAIN ),
				),
				'default_value'     => array(
					0 => 'text',
				),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_5a86967afd21e',
				'label'             => __( 'Help Text (optional)', THEME_DOMAIN ),
				'name'              => 'form_input_help_text',
				'type'              => 'text',
				'instructions'      => __( 'Displayed below the input field (small)', THEME_DOMAIN ),
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'radio',
						),
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'checkbox',
						),
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'confirmation',
						),
					),
				),
				'wrapper'           => array(
					'width' => '100',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5ab20e7f14b9a',
				'label'             => __( 'Choices', THEME_DOMAIN ),
				'name'              => 'form_input_choices',
				'type'              => 'textarea',
				'instructions'      => __( 'Enter each choice on a new line.', THEME_DOMAIN ),
				'required'          => 1,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '==',
							'value'    => 'radio',
						),
					),
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '==',
							'value'    => 'checkbox',
						),
					),
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '==',
							'value'    => 'select',
						),
					),
				),
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'maxlength'         => '',
				'rows'              => 4,
				'new_lines'         => '',
			),
			array(
				'key'               => 'field_59f3388dcf0de',
				'label'             => __( 'Required', THEME_DOMAIN ),
				'name'              => 'form_input_required',
				'type'              => 'true_false',
				'instructions'      => __( 'Set the field as mandatory', THEME_DOMAIN ),
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'checkbox',
						),
					),
				),
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 0,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_59f338a0cf0df',
				'label'             => __( 'Full Width', THEME_DOMAIN ),
				'name'              => 'form_input_full_width',
				'type'              => 'true_false',
				'instructions'      => __( 'Uses the full width of the form.', THEME_DOMAIN ),
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'radio',
						),
						array(
							'field'    => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value'    => 'checkbox',
						),
					),
				),
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 1,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_nkb3j4rzyurkd',
				'label'             => __( 'Width %', THEME_DOMAIN ),
				'name'              => 'form_input_width',
				'type'              => 'number',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f338a0cf0df',
							'operator' => '==',
							'value'    => '0',
						),
					),
				),
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '50',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '%',
				'min'               => '0',
				'max'               => '100',
				'step'              => '1',
			),
			array(
				'key'               => 'field_b9mi13o7801jf',
				'label'             => __( 'Min width (optional)', THEME_DOMAIN ),
				'name'              => 'form_input_min_width',
				'type'              => 'number',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_59f338a0cf0df',
							'operator' => '==',
							'value'    => '0',
						),
					),
				),
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => 'px',
				'min'               => '0',
				'max'               => '',
				'step'              => '1',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'theme_form',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => 0,
		'description'           => '',
	) );

endif;
