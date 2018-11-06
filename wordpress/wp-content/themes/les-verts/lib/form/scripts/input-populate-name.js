( function() {

	var fieldsRepeaterEls = [].slice.call( document.querySelectorAll( '.fields-repeater' ) );

	/**
	 * Copy the content of the title input into placeholder attribute of name input
	 */
	function input_populateName( event ) {
		event.delegateTarget.parentNode.querySelector( '.a-input__name input' ).setAttribute( 'placeholder', slug( event.target.value, {
			lower: true,
			replacement: '_',
		} ) );
	}

	// Run once DOM is ready
	document.addEventListener( 'DOMContentLoaded', function() {

		fieldsRepeaterEls.forEach( el => {

			// Bind the event keyUp to all inputs title
			delegate( el, '.a-input__title', 'keyup', input_populateName.bind( el ) );

			[].slice.call( el.querySelectorAll( '.a-input__title' ) ).forEach( inputTitle => {

				// Run first time
				input_populateName( {
					target: inputTitle.querySelector( 'input' ),
					delegateTarget: inputTitle,
				} );

			} );
		} );
	} );

} )();
