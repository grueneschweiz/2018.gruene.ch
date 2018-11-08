<?php

if ( function_exists( 'acf_add_local_field_group' ) ):
	
	acf_add_local_field_group(array(
		'key' => 'group_5ab227ac7540e',
		'title' => __('Mails', THEME_DOMAIN),
		'fields' => array(
			array(
				'key' => 'field_5be2eab21cb60',
				'label' => __('Confirmation', THEME_DOMAIN),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'left',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_5be2e6b40fefa',
				'label' => __('Send confirmation', THEME_DOMAIN),
				'name' => 'form_send_confirmation',
				'type' => 'true_false',
				'instructions' => __('Mail to the person that filled out this form.', THEME_DOMAIN),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
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
				'key' => 'field_5be2ec792f8cb',
				'label' => __('Mail', THEME_DOMAIN),
				'name' => '',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5be2e6b40fefa',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'sub_fields' => array(
					array(
						'key' => 'field_5be2e9d9b43c4',
						'label' => '',
						'name' => 'form_confirmation_template',
						'type' => 'clone',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5be2e6b40fefa',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'clone' => array(
							0 => 'group_5be2e2d8a21b0',
						),
						'display' => 'seamless',
						'layout' => 'block',
						'prefix_label' => 0,
						'prefix_name' => 0,
					),
				),
			),
			array(
				'key' => 'field_5be2eafa1cb61',
				'label' => __('Notification', THEME_DOMAIN),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'left',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_5be2e7620fefb',
				'label' => __('Send notification', THEME_DOMAIN),
				'name' => 'form_send_notification',
				'type' => 'true_false',
				'instructions' => __('Mail with the form data to an address of your choice.', THEME_DOMAIN),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
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
				'key' => 'field_5be2ecc92f8cc',
				'label' => __('Mail', THEME_DOMAIN),
				'name' => '',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5be2e7620fefb',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'sub_fields' => array(
					array(
						'key' => 'field_5be2e7db0fefc',
						'label' => __('Destination', THEME_DOMAIN),
						'name' => 'form_confirmation_destination',
						'type' => 'email',
						'instructions' => __('This email address will receive notifications when a form is completed.', THEME_DOMAIN),
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_5be2e954b43c2',
						'label' => '',
						'name' => 'form_confirmation_template',
						'type' => 'clone',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5be2e7620fefb',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'clone' => array(
							0 => 'group_5be2e2d8a21b0',
						),
						'display' => 'seamless',
						'layout' => 'block',
						'prefix_label' => 0,
						'prefix_name' => 0,
					),
				),
			),
			array(
				'key' => 'field_5afd3494d4154',
				'label' => __('Sender Settings', THEME_DOMAIN),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5be2e6b40fefa',
							'operator' => '==',
							'value' => '1',
						),
					),
					array(
						array(
							'field' => 'field_5be2e7620fefb',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'left',
				'endpoint' => 1,
			),
			array(
				'key' => 'field_5be2e856b43c0',
				'label' => __('Sender Name', THEME_DOMAIN),
				'name' => 'form_sender_name',
				'type' => 'text',
				'instructions' => __('This is the name that will appear as the sender (the e-mail will always be website@yourdomain.tld).', THEME_DOMAIN),
				'required' => 1,
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
				'key' => 'field_5be2e8b6b43c1',
				'label' => __('Reply-To', THEME_DOMAIN),
				'name' => 'form_reply_to',
				'type' => 'email',
				'instructions' => __('The e-mail, where replies to this mail should be sent to.', THEME_DOMAIN),
				'required' => 1,
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
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'theme_form',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

endif;
