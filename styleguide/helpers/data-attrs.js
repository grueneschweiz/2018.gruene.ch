const isObject =  require('lodash.isobject');

/**
 * Usage <div class="my-div" {{{data_attrs}}}></div>
 * NOTE Do not forget to put a space before the helper
 *
 * Config:
 * data: [ 'attr', 'attr2', { name: 'attr3', value: 'value1'} ]
 *
 * Result
 * <div class="" data-attr data-attr2 data-attr3="value1"></div>
 */
module.exports = function() {
	let attrs = [];
	if ( Array.isArray(this.data) ) {
		this.data.forEach( (d) => {
			attrs.push( 'data-'+ ( isObject(d) ? `${d.name}="${d.value}"` : d ) );
		} );
	}

	return attrs.join(' ');
};
