/**
 * Usage <div class="my-div {{modifiers_classes}}"></div>
 * NOTE Do not forget to put a space before the helper
 */
module.exports = function( context ) {
	// let component_name = context.data.root._self.baseHandle;

	// Use filename instead of baseHandle cause the baseHandle is overriden
	// by the "name" param in the config.js file
	const viewPath = ( (context.data.root._self.viewPath === undefined) ? context.data.root._target.viewPath : context.data.root._self.viewPath );
	const component_name = viewPath.match(/\/([^/]+)\.hbs$/)[1];

	let classes = [], modifiers;
	if ( Array.isArray(this.modifiers) ) {
		modifiers = this.modifiers;
	}
	else if ( typeof this.modifiers != 'undefined' && this.modifiers != "" ) {
		modifiers = this.modifiers.split(/[,\s]/);
	}

	if ( modifiers != null ) {
		modifiers.forEach( (m) => {
			classes.push( `${component_name}--${m}` );
		} );
	}

	if ( Array.isArray(this.extra_classes) ) {
		this.extra_classes.forEach( (c) => {
			classes.push( c );
		} );
	}
	else if ( typeof this.extra_classes != 'undefined' && this.extra_classes != "" ) {
		const spl = this.extra_classes.split(/[,\s]/)
		classes = classes.concat( spl );
	}

	return classes.join(' ');
};
