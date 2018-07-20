(function() {

	var formTitleEl = document.querySelector( "#acf-field_59f33e98216af" );
	var formIdEl = document.querySelector( "#acf-field_59f33aad62189" );

	/**
	 * Copy the content of the title input into placeholder attribute of id input
	 */
	function formType_populateId() {
		formIdEl.setAttribute( 'placeholder', slug(formTitleEl.value, { lower: true }) );
	}

	// Run once DOM is ready
	document.addEventListener("DOMContentLoaded", function() {

		// Bind events
		formTitleEl.addEventListener( "keyup", formType_populateId );

		// Run first time
		formType_populateId();
	});

})();
