module.exports = function( context ) {
	return JSON.stringify(context, null, 2) || JSON.stringify(this, null, 2);
};
