module.exports = function( values, glue = ',' ) {
	if ( !Array.isArray( values ) ) {
		return values;
	}
	return values.join(glue);
};
