const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const cleanCss = require('gulp-clean-css');
const rename = require('gulp-rename');
const plumber = require('gulp-plumber');

module.exports = function( args ) {
	gulp.src(args.src)
	.pipe(plumber())
	.pipe(sass({
		includePaths : [
			'./node_modules/sass-mediaqueries',
			'./node_modules/sass-flex-mixin',
		],
		errLogToConsole: true
	}))
	.pipe(autoprefixer({
		browsers: [
			'last 2 versions',
			'iOS >= 8',
			'Safari >= 8',
			'>= 5%'
		],
		cascade: false,
		remove: false,
		flexbox: true
	}))
	.pipe(gulp.dest(args.dest))
	.pipe(cleanCss({
		keepSpecialComments: 0,
		processImport: false
	}))
	.pipe(rename({ basename: args.basename, extname: '.min.css' }))
	.pipe(gulp.dest(args.dest));
}
