(function() {

	var titleLabelEl = document.querySelector( "#title-prompt-text" );
	var titleEl = document.querySelector( "#title" );

	var formTitleEl = document.querySelector( "#acf-field_59f33e98216af" );

	/**
	 * Copy the content of the uptitle & title inputs into page title input
	 */
	function formType_updateTitle() {

		titleEl.value = formTitleEl.value;

		if ( titleEl.value == '' ) {
			titleLabelEl.classList.remove('screen-reader-text');
		}
		else {
			titleLabelEl.classList.add('screen-reader-text');
		}
	}

	// Run once DOM is ready
	document.addEventListener("DOMContentLoaded", function() {
		// disable page title input
		titleEl.setAttribute("disabled", "true");

		// Bind events
		formTitleEl.addEventListener("keyup", formType_updateTitle);
		
		// Focus uptitle input
		formTitleEl.focus();

		// Run first time
		formType_updateTitle();
	});
})();
