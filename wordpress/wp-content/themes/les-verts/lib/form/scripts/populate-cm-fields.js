(function() {

	function populateCmFields( $el ) {

		var $fieldEl = $el.closest('.acf-field');
		if ( $fieldEl.data('name') != 'fields' ) {
			return;
		}

		var $labels = $fieldEl.find( '.acf-field-59f33777cf0db input' );
		var $names = $fieldEl.find( '.acf-field-59f339868a4ad input' );

		var emailEl = document.querySelector('.acf-field-5afc3c59bfbcb select');
		var firstEl = document.querySelector('.acf-field-5afc3c81bfbcc select');
		var lastEl = document.querySelector('.acf-field-5afc3ccfbfbcd select');

		var emailValue = emailEl.options[emailEl.selectedIndex] ? emailEl.options[emailEl.selectedIndex].value : '';
		var firstValue = firstEl.options[firstEl.selectedIndex] ? firstEl.options[firstEl.selectedIndex].value : '';
		var lastValue = lastEl.options[lastEl.selectedIndex] ? lastEl.options[lastEl.selectedIndex].value : '';

		var optPlaceholder = '<option value="" disabled selected>Select field</option>';
		emailEl.innerHTML = optPlaceholder;
		firstEl.innerHTML = optPlaceholder;
		lastEl.innerHTML = optPlaceholder;

		$labels.each( function(id, label) {

			if ( label.value == "" ) {
				return;
			}

			var nameEl = $names.get(id);
			var name = ( nameEl.value == "" ? nameEl.getAttribute('placeholder') : nameEl.value );

			emailEl.innerHTML += '<option value="'+ name +'"'+ ( emailValue == name ? " selected" : '' ) +'>'+ label.value +'</option>';
			firstEl.innerHTML += '<option value="'+ name +'"'+ ( firstValue == name ? " selected" : '' ) +'>'+ label.value +'</option>';
			lastEl.innerHTML += '<option value="'+ name +'"'+ ( lastValue == name ? " selected" : '' ) +'>'+ label.value +'</option>';
		} );
	}

	acf.add_action( 'append', function($el) {
		console.log('append');
		_.debounce(populateCmFields, 500)($el);
	} );
	acf.add_action( 'remove', function($el) {
		console.log('remove');
		_.debounce(populateCmFields, 500)($el);
	} );

	setTimeout( function() { populateCmFields( $('.acf-field-5a869960c1cf2') ) }, 1000 );
})();
