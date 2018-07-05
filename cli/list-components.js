/**
 * List the components registered
 * in the styleguide.
 */
module.exports = function (args, done) {
	const app = this.fractal;

	for (let item of app.components.flatten()) {
		this.log(`${item.handle} - ${item.status.label}`);
	}

	done();
};
