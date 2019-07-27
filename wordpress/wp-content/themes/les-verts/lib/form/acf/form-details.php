<?php

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_59f33aa9b4e97',
		'title'                 => 'Form details',
		'fields'                => array(
			array(
				'key'               => 'field_5a8697e89ae0c',
				'label'             => __( 'Form fields', THEME_DOMAIN ),
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'left',
				'endpoint'          => 1,
			),
			array(
				'key'               => 'field_5ab20d91c9a54',
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
				'message'           => __( 'Define the input fields.', THEME_DOMAIN ),
				'new_lines'         => 'wpautop',
				'esc_html'          => 0,
			),
			array(
				'key'               => 'field_5a869960c1cf2',
				'label'             => '',
				'name'              => 'form_fields',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => 'form_fields_repeater',
					'id'    => '',
				),
				'collapsed'         => '',
				'min'               => 1,
				'max'               => 0,
				'layout'            => 'block',
				'button_label'      => __( 'Add field', THEME_DOMAIN ),
				'sub_fields'        => array(
					array(
						'key'               => 'field_5c0fabb0bdbd4',
						'label'             => __( 'Standard', THEME_DOMAIN ),
						'name'              => '',
						'type'              => 'tab',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'placement'         => 'top',
						'endpoint'          => 1,
					),
					array(
						'key'               => 'field_5a86996cc1cf3',
						'label'             => '',
						'name'              => 'form_field',
						'type'              => 'clone',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'clone'             => array(
							0 => 'group_59e71d68896a7',
						),
						'display'           => 'seamless',
						'layout'            => '',
						'prefix_label'      => 0,
						'prefix_name'       => 0,
					),
					array(
						'key'               => 'field_5c0fca09d021a',
						'label'             => __( 'Advanced', THEME_DOMAIN ),
						'name'              => '',
						'type'              => 'tab',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'placement'         => 'top',
						'endpoint'          => 0,
					),
					array(
						'key'               => 'field_5c0facf5bdbd8',
						'label'             => __( 'DOM id', THEME_DOMAIN ),
						'name'              => 'dom_id',
						'type'              => 'text',
						'instructions'      => __( 'Id in the HTML markup', THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => 120,
					),
					array(
						'key'               => 'field_5c0fad19bdbd9',
						'label'             => __( 'DOM Class', THEME_DOMAIN ),
						'name'              => 'dom_class',
						'type'              => 'text',
						'instructions'      => __( 'Class in the HTML markup (appended)', THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => 120,
					),
					array(
						'key'               => 'field_5c0fad19blkjh',
						'label'             => __( 'Field slug', THEME_DOMAIN ),
						'name'              => 'slug',
						'type'              => 'text',
						'instructions'      => __( 'Read Only. Hacky field to get the slug.', THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '100',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => 120,
					),
					array(
						'key'               => 'field_5c0fac32bdbd6',
						'label'             => __( 'Webling', THEME_DOMAIN ),
						'name'              => '',
						'type'              => 'tab',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'placement'         => 'top',
						'endpoint'          => 0,
					),
					array(
						'key'               => 'field_5c0fac61bdbd7',
						'label'             => __( 'Webling Field', THEME_DOMAIN ),
						'name'              => 'crm_field',
						'type'              => 'select',
						'instructions'      => __( 'Select the field in Webling that matches this field.', THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'firstName'                => 'Vorname / prénom',
							'lastName'                 => 'Name / nom',
							'recordCategory'           => 'Datensatzkategorie / type d’entrée',
							'language'                 => 'Sprache / langue',
							'salutationInformal'       => 'Anrede / appel (informel)',
							'address1'                 => 'Strasse / rue',
							'zip'                      => 'PLZ / code postal',
							'city'                     => 'Ort / localité',
							'email1'                   => 'E-Mail / courriel 1',
							'mobilePhone'              => 'Mobile / mobile',
							'newsletterCountryD'       => 'Newsletter national DE',
							'newsletterCountryF'       => 'infolettre nationale FR',
							'newsletterCantonD'        => 'Newsletter kantonal DE',
							'newsletterCantonF'        => 'infolettre cantonale FR',
							'newsletterMunicipality'   => 'Newsletter Kommunal / infolettre communale',
							'pressReleaseCountryD'     => 'MM National DE',
							'pressReleaseCountryF'     => 'CP national FR',
							'pressReleaseCantonD'      => 'MM Kantonal DE',
							'pressReleaseCantonF'      => 'CP cantonal FR',
							'pressReleaseMunicipality' => 'MM Kommunal / CP communal',
							'memberStatusCountry'      => 'Mitgliedschaft National / affiliation nationale',
							'memberStatusCanton'       => 'Mitgliedschaft Kantonal / affiliation cantonale',
							'memberStatusRegion'       => 'Mitgliedschaft Bezirk / affiliation d’un district',
							'memberStatusMunicipality' => 'Mitgliedschaft Kommunal / affiliation communale',
							'memberStatusYoung'        => 'Mitgliedschaft Junge Grüne / affiliation Jeunes Verts',
							'entryChannel'             => 'Gewinnungskanal / acquisition',
							'interests'                => 'Interessen / intérêts',
							'request'                  => 'Anfragen für / disponibilités',
							'profession'               => 'Beruf / profession',
							'notesCountry'             => 'Notizen National / notes nationales',
							'notesCanton'              => 'Notizen Kanton / notes cantonales',
							'notesMunicipality'        => 'Notizen Kommunal / notes communales',
						),
						'default_value'     => array(),
						'allow_null'        => 1,
						'multiple'          => 0,
						'ui'                => 1,
						'ajax'              => 0,
						'return_format'     => 'value',
						'placeholder'       => '',
					),
					array(
						'key'               => 'field_5alk49sf14pd4',
						'label'             => __( 'Webling Choices', THEME_DOMAIN ),
						'name'              => 'choice_map',
						'type'              => 'textarea',
						'instructions'      => __( 'Enter the exact corresponding options in Webling in the same order.', THEME_DOMAIN ),
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '==',
									'value'    => 'recordCategory',
								),
							),
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '==',
									'value'    => 'language',
								),
							),
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '==',
									'value'    => 'salutationInformal',
								),
							),
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '==',
									'value'    => 'interests',
								),
							),
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '==',
									'value'    => 'request',
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
						'key'               => 'field_5c0fc373bdf7b',
						'label'             => __( 'Insertion Modus', THEME_DOMAIN ),
						'name'              => 'insertion_modus',
						'type'              => 'select',
						'instructions'      => __( '<strong>Warning</strong>: Selecting the wrong modus results in data loss!
<br><br>
Select replace for single value fields like the address line and append for multi value fields like the interests field or a note field.',
							THEME_DOMAIN ),
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '!=empty',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'replace'      => __( 'Replace', THEME_DOMAIN ),
							'append'       => __( 'Append', THEME_DOMAIN ),
							'replaceEmpty' => __( 'Add if empty', THEME_DOMAIN ),
							'addIfNew'     => __( 'Add if new', THEME_DOMAIN ),
						),
						'default_value'     => array(),
						'allow_null'        => 0,
						'multiple'          => 0,
						'ui'                => 0,
						'return_format'     => 'value',
						'ajax'              => 0,
						'placeholder'       => '',
					),
					array(
						'key'               => 'field_5c0fc5b4eece5',
						'label'             => __( 'Hidden Field', THEME_DOMAIN ),
						'name'              => 'hidden_field',
						'type'              => 'true_false',
						'instructions'      => __( 'Use this option to add a value to every persons record that submits this form.',
							THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '!=empty',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'message'           => __( 'Hide this field from the website visitor', THEME_DOMAIN ),
						'default_value'     => 0,
						'ui'                => 1,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
					array(
						'key'               => 'field_5c0fc84a086db',
						'label'             => __( 'Field Value', THEME_DOMAIN ),
						'name'              => 'hidden_field_value',
						'type'              => 'text',
						'instructions'      => __( 'Make sure this value is accepted by Webling.
<br><br>
Example interest field: Only the interests that can be selected in Webling are accepted values.', THEME_DOMAIN ),
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c0fac61bdbd7',
									'operator' => '!=empty',
								),
								array(
									'field'    => 'field_5c0fc5b4eece5',
									'operator' => '==',
									'value'    => '1',
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
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
				),
			),
			array(
				'key'               => 'field_5a8698679ae0f',
				'label'             => __( 'Submit', THEME_DOMAIN ),
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'left',
				'endpoint'          => 1,
			),
			array(
				'key'               => 'field_21bqwspmxyk5r',
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
				'message'           => __( 'Configure the submit buttons.', THEME_DOMAIN ),
				'new_lines'         => 'wpautop',
				'esc_html'          => 0,
			),
			array(
				'key'               => 'field_59f33e578c494',
				'label'             => __( 'Button', THEME_DOMAIN ),
				'name'              => 'form_submit',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => 'Send',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_59f33e699d494',
				'label'             => __( 'Button Submitting', THEME_DOMAIN ),
				'name'              => 'form_submitting',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => __( 'Sending', THEME_DOMAIN ),
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '...',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5a869c56c1cf7',
				'label'             => __( 'Description', THEME_DOMAIN ),
				'name'              => 'form_desc',
				'type'              => 'text',
				'instructions'      => __( 'Why someone should fill in this form. Displayed next to the submit button.',
					THEME_DOMAIN ),
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'maxlength'         => '',
				'prepend'           => '',
				'append'            => '',
			),
			array(
				'key'               => 'field_5ab226578508a',
				'label'             => __( 'After Submit', THEME_DOMAIN ),
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'left',
				'endpoint'          => 1,
			),
			array(
				'key'               => 'field_59f33f04216b0',
				'label'             => __( 'Success', THEME_DOMAIN ),
				'name'              => 'form_success',
				'type'              => 'group',
				'instructions'      => __( 'What should happen after the form was successfully submitted?', THEME_DOMAIN ),
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'layout'            => 'block',
				'sub_fields'        => array(
					array(
						'key'               => 'field_5c3e2cc30be3f',
						'label'             => '',
						'name'              => 'after_success_action',
						'type'              => 'button_group',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'inline'   => __( 'Show message (inline)', THEME_DOMAIN ),
							'redirect' => __( 'Redirect to post / page', THEME_DOMAIN ),
						),
						'allow_null'        => 0,
						'default_value'     => 'inline',
						'layout'            => 'horizontal',
						'return_format'     => 'value',
					),
					array(
						'key'               => 'field_5ab13d9745e81',
						'label'             => __( 'Title', THEME_DOMAIN ),
						'name'              => 'form_success_title',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c3e2cc30be3f',
									'operator' => '==',
									'value'    => 'inline',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => __( 'Thank you for your support!', THEME_DOMAIN ),
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5ab13dbf45e82',
						'label'             => __( 'Message', THEME_DOMAIN ),
						'name'              => 'form_success_text',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c3e2cc30be3f',
									'operator' => '==',
									'value'    => 'inline',
								),
							),
						),
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
					array(
						'key'               => 'field_5c3e2dc30be40',
						'label'             => __( 'Redirect to', THEME_DOMAIN ),
						'name'              => 'redirect',
						'type'              => 'post_object',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5c3e2cc30be3f',
									'operator' => '==',
									'value'    => 'redirect',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'post_type'         => array(
							0 => 'post',
							1 => 'page',
							2 => 'tribe_events',
						),
						'taxonomy'          => '',
						'allow_null'        => 0,
						'multiple'          => 0,
						'return_format'     => 'id',
						'ui'                => 1,
					),
				),
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
		'menu_order'            => 20,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [ 'permalink' ],
		'active'                => 1,
		'description'           => '',
	) );

endif;
