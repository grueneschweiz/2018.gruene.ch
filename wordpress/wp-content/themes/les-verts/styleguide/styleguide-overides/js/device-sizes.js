(function() {
	const sizes = {
		mobile: "375px",
		tablet: "768px",
		desktop: "110%"
	};

	document.addEventListener( 'click', ( event => {
		if ( event.target.classList.contains('Pen-device-size') ) {
			event.preventDefault();
			document.querySelector( '.Pen-preview .Preview-wrapper').style.width = sizes[ event.target.getAttribute('rel') ];
		}
	}) );
})();
