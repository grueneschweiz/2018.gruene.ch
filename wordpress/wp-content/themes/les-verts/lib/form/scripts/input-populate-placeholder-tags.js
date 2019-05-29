( function() {
	var max_slug_len = 50; // plus '-X' for multiple identical slugs

	var fields_container = document.querySelector( '.form_fields_repeater' );
	var fields = document.querySelectorAll( '.form_input_label input' );
	var $slugFields = acf.getFields( { key: 'field_5c0fad19blkjh' } );
	var descriptions = document.querySelectorAll(
		'.form_mail_template_placeholders' );
	var slugs = [];
	var tabs = document.querySelectorAll(
		'#acf-group_59f33aa9b4e97 .acf-tab-group' );
	var typeFields = document.querySelectorAll( '.form_input_type select' );

	// mutation observer to handle addition and removal of fields
	var observer = new MutationObserver( function( mutations ) {
		mutations.forEach( function() {
			fields = document.querySelectorAll( '.form_input_label input' );
			$slugFields = acf.getFields( { key: 'field_5c0fad19blkjh' } );
			tabs = document.querySelectorAll(
				'#acf-group_59f33aa9b4e97 .acf-tab-group' );
			typeFields = document.querySelectorAll( '.form_input_type select' );

			bind();
			populateInit();
			setFieldLabelFieldVisibility();
			hideSlugFields();
		} );
	} );

	// Rebind events if the fields get added or removed
	observer.observe( fields_container, { childList: true, subtree: true } );

	/**
	 * Calculate the field slugs and update them
	 */
	function updateFieldSlugs() {
		var placeholders = [];
		var label, slug;

		for (var i = 0; i < fields.length; i ++) {
			if (!fields[ i ].value) {
				continue;
			}

			label = fields[ i ].value.replace( /<\/?[^>]+(>|$)/g, '' );
			slug = getUniqueSlug( label, i );
			slugs[ i ] = slug;

			placeholders.push( '{{' + slug + '}}' );
			$slugFields[ i ].val( slug );
		}

		updateMailPlaceholders( placeholders );
	}

	/**
	 * Pipe the wysiwyg content to the label field
	 *
	 * @param event
	 */
	function updateWysiwygLabelField( event ) {
		var content = this.getContent();
		var $target = jQuery( event.target.container ).
			closest( '.acf-fields' ).
			find( '.form_input_label input' );
		$target.val( content );
		updateFieldSlugs();
	}

	/**
	 * Make sure the same slug never exists twice
	 *
	 * @param field_name
	 * @param index
	 * @returns {string}
	 */
	function getUniqueSlug( field_name, index ) {
		var slug;
		var slug_exists;
		var j = 0;

		slug = slugify( field_name );

		if (slug.length > max_slug_len) {
			slug = slug.substr( 0, max_slug_len );
		}

		slug_exists = slugs.indexOf( slug ) || '_meta_' === slug ||
			'submission_url' === slug; // _meta_ and submission_url are reserved

		while (- 1 !== slug_exists && slug_exists !== index) {
			j ++;
			slug_exists = slugs.indexOf( slug + '-' + j );
		}

		if (j) {
			slug += '-' + j;
		}

		return slug;
	}

	// Run once DOM is ready
	document.addEventListener( 'DOMContentLoaded', function() {
		bind();
		populateInit();
		setFieldLabelFieldVisibility();
		hideSlugFields();
		legacy_addOldConfirmationData();
	} );

	// Bind events
	function bind() {
		// for fields with tinymce
		acf.add_action( 'wysiwyg_tinymce_init',
			function( editor, id, mceInit, field ) {
				if ('form_input_confirmation_text' === field.data( 'name' )) {
					editor.off( 'Blur', updateWysiwygLabelField );
					editor.on( 'Blur', updateWysiwygLabelField );
				}
			} );

		// for all normal fields
		for (var k = 0; k < fields.length; k ++) {
			fields[ k ].removeEventListener( 'blur', updateFieldSlugs );
			fields[ k ].addEventListener( 'blur', updateFieldSlugs );
		}

		for (var j = 0; j < tabs.length; j ++) {
			tabs[ j ].addEventListener( 'click', hideSlugFields );
		}

		for (var l = 0; l < typeFields.length; l ++) {
			typeFields[ l ].addEventListener( 'change',
				setFieldLabelFieldVisibility );
		}
	}

	// Populate initially
	function populateInit() {
		updateFieldSlugs();
	}

	// hide slug fields
	function hideSlugFields() {
		for (var i = 0; i < $slugFields.length; i ++) {
			$slugFields[ i ].hide();
		}
	}

	function setFieldLabelFieldVisibility() {
		var $target;

		for (var i = 0; i < typeFields.length; i ++) {
			$target = jQuery( typeFields[ i ] ).
				closest( '.acf-fields' ).
				find( '.form_input_label' );

			if ('confirmation' === typeFields[ i ].value) {
				$target.hide();
			}
			else {
				$target.show();
			}
		}
	}

	/**
	 * Add the placeholder tags to the mail template descriptions
	 */
	function updateMailPlaceholders( placeholders ) {
		var placeholder_string = placeholders.join( ', ' );

		for (var j = 0; j < descriptions.length; j ++) {
			descriptions[ j ].innerHTML = placeholder_string;
		}
	}

	/**
	 * Add the confirmation data of non WYSIWYG fields to the WYSIWYG field
	 */
	function legacy_addOldConfirmationData() {
		acf.add_action( 'wysiwyg_tinymce_init',
			function( editor, id, mceInit, field ) {
				if ('form_input_confirmation_text' !== field.data( 'name' )) {
					return;
				}

				var container = field.closest( '.acf-fields' );
				var type = container.find( '.form_input_type select' ).val();

				if ('confirmation' === type) {
					var labelField = container.find( '.form_input_label input' );
					var value = labelField[ 0 ].defaultValue;

					if (value && '' === editor.getContent()) {
						editor.setContent( value );
						labelField.val( value );
						updateFieldSlugs();
					}
				}
			} );
	}

	/**
	 * Mimics supt_slugify function written in php
	 *
	 * todo: replace with library function (p.ex. slug or slugify from npm)
	 *
	 * @param str
	 * @returns {*}
	 */
	function slugify( str ) {
		str = str.replace( /^\s+|\s+$/g, '' ); // trim
		str = str.toLowerCase();

		// remove accents, swap ñ for n, etc
		var from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;';
		var to = 'aaaaeeeeiiiioooouuuunc______';

		for (var i = 0, l = from.length; i < l; i ++) {
			str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
		}

		str = str.replace( '.', '_' ) // replace a dot by an underline
			.replace( /[^a-z0-9 _-]/g, '' ) // remove invalid chars
			.replace( /\s+/g, '_' ) // collapse whitespace and replace by an
			// underline
			.replace( /_+/g, '_' ); // collapse underlines

		return str;
	}
} )();
