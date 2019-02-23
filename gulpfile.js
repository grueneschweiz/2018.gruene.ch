const gulp = require('gulp');

const CONFIG = require('./config.json');

/**
 * Load Tasks.
 */
const fractal    = require( './gulp/fractal' );
const js_task    = require( './gulp/script' );
const sass_task  = require( './gulp/sass' );
const copy_task  = require( './gulp/copy' );
const lint_task  = require( './gulp/lint' );
const svgsprite  = require( './gulp/svg-sprite' );
const sg_custom  = require( './gulp/sg-custom' );
const watch_task = require( './gulp/watch' );
const modernizr_task = require( './gulp/modernizr' );

/**
 * Define Tasks.
 */
gulp.task( 'lint', lint_task.bind(null, CONFIG.javascript.src) );
gulp.task( 'scripts', js_task.bind(null, CONFIG.javascript) );	// Script task: compile the javascript code.
gulp.task( 'sass', sass_task.bind(null, CONFIG.sass) ); // Sass tasks: compile & minify Sass files
gulp.task( 'images', copy_task.bind(null, CONFIG.images) ); // Image task: copy the images to the public folder.
gulp.task( 'svgsprite', svgsprite.bind(null, CONFIG.svgsprite ) );
gulp.task( 'fonts', copy_task.bind(null, CONFIG.fonts) ); // Font task: copy the font(s) to the public folder.
gulp.task( 'modernizr', modernizr_task.bind(null, CONFIG.modernizr) );

gulp.task( 'watch', watch_task.bind(null, CONFIG.watch) );

gulp.task( 'sg:robot', sg_custom.robot.bind( null, CONFIG.fractal.overides.dest) );
gulp.task( 'sg:overides', sg_custom.overides.bind( null, {src: CONFIG.fractal.overides.src, dest: CONFIG.fractal.overides.dest}) );
gulp.task( 'sg:custom', gulp.series('sg:robot', 'sg:overides') );

gulp.task( 'build', gulp.parallel(gulp.series('lint', 'scripts', 'modernizr'), 'sass', 'svgsprite', 'images', 'fonts') );
gulp.task( 'production', (done) => {process.env.NODE_ENV = 'production'; done();} ); // Set to production mode
gulp.task( 'build:production', gulp.series('production', 'build') );

gulp.task( 'fractal:start', gulp.series('build', 'sg:custom', 'watch'), fractal.start.bind(gulp, CONFIG.fractal.server) ); // Fractal task: start the fractal dev server
gulp.task( 'fractal:build', gulp.series('build:production'), fractal.build );

gulp.task( 'default', gulp.series('fractal:start') );
