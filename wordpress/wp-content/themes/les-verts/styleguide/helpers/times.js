/**
 * Usage <ul class="my-list">{{#times 5}}<li>Item: {{ index }}</li>{{/times}}</ulv>
 */
module.exports = function(n, block) {
	var accum = '';
	for(var i = 1; i <= n; ++i) {
		block.data.index = i;
		block.data.first = i === 1;
		block.data.last = i === n;
		accum += block.fn( this );
	}
	return accum;
};
