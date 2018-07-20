const gulp = require('gulp');
const eslint = require('gulp-eslint');
const plumber = require('gulp-plumber');

module.exports = function( src ) {
	return gulp.src( src )
		.pipe(plumber())
		.pipe(eslint()) // eslint() attaches the lint output to the "eslint" property of the file object so it can be used by other modules.
		.pipe(eslint.format()) // eslint.format() outputs the lint results to the console. Alternatively use eslint.formatEach() (see Docs).
}
