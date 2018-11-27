<?php
if ( function_exists( 'acf_add_local_field_group' ) ):
	
	acf_add_local_field_group( array(
		'key'                   => 'group_5be2e2d8a21b0',
		'title'                 => '[Template]Form Mail Config',
		'fields'                => array(
			array(
				'key'               => 'field_5be2e2e23fdf4',
				'label'             => '',
				'name'              => '',
				'type'              => 'message',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => __( 'Use the following tags as placeholders in your mail. They will be replaced automatically: <strong><div class="form_mail_template_placeholders"></div></strong>

<i>Advanced: You may use <a href="https://twig.symfony.com/" target="_blank">Twig</a> in this mail template.</i>',
					THEME_DOMAIN ),
				'new_lines'         => 'wpautop',
				'esc_html'          => 0,
			),
			array(
				'key'               => 'field_5be2e5b13fdf6',
				'label'             => __( 'Subject', THEME_DOMAIN ),
				'name'              => 'form_mail_subject',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
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
				'key'               => 'field_5be2e5d83fdf7',
				'label'             => __( 'Template', THEME_DOMAIN ),
				'name'              => 'form_mail_template',
				'type'              => 'wysiwyg',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'tabs'              => 'all',
				'toolbar'           => 'basic',
				'media_upload'      => 0,
				'delay'             => 0,
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => 0,
		'description'           => '',
	) );
endif;
