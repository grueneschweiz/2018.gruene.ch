const gulp = require('gulp');
const plumber = require('gulp-plumber');

module.exports = function(config, done) {
	gulp.src(config.src)
		.pipe(plumber())
		.pipe(gulp.dest(config.dest))
		.on('end', done);
}
