/**
 * Styleguide Custom elements (style, scripts, ...)
 */

const gulp = require('gulp');
const file = require('gulp-file');
const plumber = require('gulp-plumber');

const robotTxt = `User-agent: *
Disallow: /`;

module.exports = {
	/**
	 * Create robot.txt file to avoid "No matching route in the console"
	 */
	robot: function( dest, done ) {
		file( 'robot.txt', robotTxt, { src: true } )
			.pipe(plumber())
			.pipe( gulp.dest(dest) )
			.on('end', done);
	},

	/**
	 *  Copy custom styleguide overides
	 */
	overides: function( conf, done ) {
		gulp.src( conf.src  )
			.pipe(plumber())
			.pipe( gulp.dest(conf.dest) )
			.on('end', done);
	}
}
