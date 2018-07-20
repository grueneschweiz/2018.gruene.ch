const svg = require( 'handlebars-helper-svg' );

module.exports = function( name, opts ) {
	return svg( opts.data.root._config.web.static.path + '/' + name, opts );
};
