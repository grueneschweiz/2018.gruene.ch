'use strict';
const fs = require('fs');

const SOURCE_FILE = './styleguide/src/components/_loader.scss';

/**
 * Return the relative path
 * for the scss file.
 *
 * @param {String}
 * @return {String}
 */
function getRelativePath(view_path) {
	return '@forward "./' + view_path.replace('.hbs', '') + '";\n';
}

/**
 * Generate the Sass file used to load components.
 * The file will be a list of import.
 */
module.exports = function (args, done) {
	const app = this.fractal;
	let stream = fs.createWriteStream(SOURCE_FILE);
	let import_buffer = '// __ THIS IS AN AUTO-GENERATED FILE __\n';

	for (let item of app.components.flatten()) {
		let view_path = item.relViewPath;
		if (view_path.indexOf('_preview') === -1) {
			import_buffer += getRelativePath(view_path);
		}
	}
	stream.write(import_buffer);
	// Wait for the end event to complete
	// the task, or the write will fail.
	stream.on('end', function() {
		stream.end();
		done();
	});
}
