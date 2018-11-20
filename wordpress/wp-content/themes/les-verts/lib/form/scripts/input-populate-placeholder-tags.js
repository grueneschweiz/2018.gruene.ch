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
				tmp = slug(fields[i].value, {lower: true, replacement: '_'});
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

} )();
