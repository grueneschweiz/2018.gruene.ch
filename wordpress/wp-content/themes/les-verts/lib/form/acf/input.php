<?php

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_59e71d68896a7',
		'title' => 'a-input',
		'fields' => array(
			array(
				'key' => 'field_59f33777cf0db',
				'label' => 'Title',
				'name' => 'label',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => 'a-input__title',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5a86967afd21e',
				'label' => 'Placeholder',
				'name' => 'placeholder',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_59f33814cf0dc',
				'label' => 'Type',
				'name' => 'type',
				'type' => 'select',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'text' => 'Text',
					'email' => 'Email',
					'tel' => 'Phone',
					'textarea' => 'Textarea',
					'radio' => 'Radio button',
					'checkbox' => 'Check box',
				),
				'default_value' => array(
					0 => 'text',
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'ajax' => 0,
				'return_format' => 'value',
				'placeholder' => '',
			),
			array(
				'key' => 'field_59f339868a4ad',
				'label' => 'Name',
				'name' => 'name',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => 'a-input__name',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5ab20e7f14b9a',
				'label' => 'Choices',
				'name' => 'choices',
				'type' => 'textarea',
				'instructions' => 'Enter each choice on a new line.
	
	For more control, you may specify both a value and label like this:
	
	red : Red',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_59f33814cf0dc',
							'operator' => '==',
							'value' => 'radio',
						),
					),
					array(
						array(
							'field' => 'field_59f33814cf0dc',
							'operator' => '==',
							'value' => 'checkbox',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 4,
				'new_lines' => '',
			),
			array(
				'key' => 'field_59f3388dcf0de',
				'label' => 'Required',
				'name' => 'required',
				'type' => 'true_false',
				'instructions' => 'Set the field as mandatory',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_59f33814cf0dc',
							'operator' => '!=',
							'value' => 'checkbox',
						),
					),
				),
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_59f338a0cf0df',
				'label' => 'Large',
				'name' => 'full_width',
				'type' => 'true_false',
				'instructions' => 'Uses the full width of the form.',
				'required' => 0,
				'conditional_logic' => array(
					// array(
					// 	array(
					// 		'field' => 'field_59f33814cf0dc',
					// 		'operator' => '!=',
					// 		'value' => 'radio',
					// 	),
					// 	array(
					// 		'field' => 'field_59f33814cf0dc',
					// 		'operator' => '!=',
					// 		'value' => 'checkbox',
					// 	),
					// ),
				),
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'form',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'acf_after_title',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 0,
		'description' => '',
	));
	
	endif;
