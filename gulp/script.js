const gulp = require('gulp');
const browserify = require('browserify');
const babelify = require('babelify');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
// const plumber = require('gulp-plumber');


module.exports = function (config, done) {

	const stream = browserify({
			entries: config.entries,
			paths: config.paths,
			debug: true
		})
		.transform(babelify, { presets: ["env"] })
		.bundle()
		// .on( 'error', plumber.stop)		// Handle errors -> avoid gulp to crash on error
		// .pipe(plumber())
		.pipe(source(config.build_name))
		.pipe(gulp.dest(config.dest))

		// Only minify for production (cause it take too much time ~30s !!!)
		if ( process.env.NODE_ENV === 'production' ) {
			stream
				.pipe(buffer())
				.pipe(uglify())
				.pipe(rename({ extname: '.min.js' }))
				.pipe(gulp.dest(config.dest));
		}

		stream.on( 'end', done );
};
