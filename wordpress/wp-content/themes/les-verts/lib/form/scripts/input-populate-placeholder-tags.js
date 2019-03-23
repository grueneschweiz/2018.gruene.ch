( function() {
	var max_slug_len = 50; // plus '-X' for multiple identical slugs

	var fields_container = document.querySelector( '.form_fields_repeater' );
	var fields = document.querySelectorAll( '.form_input_label input' );
	var $slugFields = acf.getFields( { key: 'field_5c0fad19blkjh' } );
	var descriptions = document.querySelectorAll(
		'.form_mail_template_placeholders' );
	var slugs = [];

	// mutation observer to handle addition and removal of fields
	var observer = new MutationObserver( function( mutations ) {
		mutations.forEach( function() {
			fields = document.querySelectorAll( '.form_input_label input' );
			$slugFields = acf.getFields( { key: 'field_5c0fad19blkjh' } );
			bind();
			populateInit();
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
		var slug;

		for (var i = 0; i < fields.length; i ++) {
			if (! fields[ i ].value) {
				continue;
			}

			slug = getUniqueSlug( fields[ i ].value, i );
			slugs[ i ] = slug;

			placeholders.push( '{{' + slug + '}}' );
			$slugFields[ i ].val( slug );
		}

		updateMailPlaceholders( placeholders );
	}

	function getUniqueSlug( field_name, index ) {
		var slug;
		var slug_exists;
		var j = 0;

		slug = slugify( field_name );

		if (slug.length > max_slug_len) {
			slug = slug.substr( 0, max_slug_len );
		}

		slug_exists = slugs.indexOf( slug );

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
	} );

	// Bind events
	function bind() {
		for (var i = 0; i < fields.length; i ++) {
			fields[ i ].removeEventListener( 'blur', updateFieldSlugs ); // prevent
			// multiple binding
			fields[ i ].addEventListener( 'blur', updateFieldSlugs );
		}
	}

	// Populate initially
	function populateInit() {
		updateFieldSlugs();
	}

	// Make slug fields read only
	function hideSlugFields() {
		for (var i = 0; i < $slugFields.length; i ++) {
			$slugFields[ i ].hide();
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
	 * Mimics supt_slugify function written in php
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
			.replace( /-+/g, '_' ); // collapse underlines

		return str;
	}
} )();
