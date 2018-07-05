const gulp = require('gulp');
const modernizr = require('gulp-modernizr');
const buffer = require('vinyl-buffer');
const uglify = require('gulp-uglify');

module.exports = function (config) {
	return gulp.src(config.src)
		.pipe(modernizr(config.settings))
		.pipe(buffer())
		.pipe(uglify())
		.pipe(gulp.dest(config.dest));
};
