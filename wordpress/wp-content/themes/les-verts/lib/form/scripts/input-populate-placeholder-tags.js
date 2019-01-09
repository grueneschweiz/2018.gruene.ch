( function() {

	var fields_container = document.querySelector('.form_fields_repeater');
	var fields = document.querySelectorAll( '.form_input_label input' );
	var descriptions = document.querySelectorAll( '.form_mail_template_placeholders' );

	// mutation observer to handle addition and removal of fields
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function() {
			fields = document.querySelectorAll( '.form_input_label input' );
			bind();
			populateInit();
		});
	});

	// Rebind events if the fields get added or removed
	observer.observe(fields_container, {childList: true, subtree: true});

	/**
	 * Add the placeholder tags to the mail template descriptions
	 */
	function updatePlaceholders() {
		var placeholders = [];
		var placeholder_string;
		var tmp;

		for(var i=0; i<fields.length; i++) {
			if (fields[i].value){
				tmp = slugify( fields[ i ].value ).replace( /-/g, '_' );
				placeholders.push('{{'+tmp+'}}');
			}
		}

		placeholder_string = placeholders.join(', ');

		for(var j=0; j<descriptions.length; j++) {
			descriptions[j].innerHTML = placeholder_string;
		}
	}

	// Run once DOM is ready
	document.addEventListener( 'DOMContentLoaded', function() {
		bind();
		populateInit();
	} );

	// Bind events
	function bind() {
		for(var i=0; i<fields.length; i++) {
			fields[i].removeEventListener('blur', updatePlaceholders); // prevent multiple binding
			fields[i].addEventListener('blur', updatePlaceholders);
		}
	}

	// Populate initially
	function populateInit() {
		updatePlaceholders();
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
		var to   = 'aaaaeeeeiiiioooouuuunc______';

		for (var i = 0, l = from.length; i < l; i ++) {
			str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
		}

		str = str.replace( '.', '_' ) // replace a dot by an underline
			.replace( /[^a-z0-9 _-]/g, '' ) // remove invalid chars
			.replace( /\s+/g, '_' ) // collapse whitespace and replace by an underline
			.replace( /-+/g, '_' ); // collapse underlines

		return str;
	}
} )();
