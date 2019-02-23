const gulp = require('gulp');

module.exports = function (config) {
	// Watch changes on files
	gulp.watch(config.sass, gulp.series('sass'));
	gulp.watch(config.images, gulp.series('images'));
	gulp.watch(config.svgsprite, gulp.series('svgsprite'));
	gulp.watch(config.fonts, gulp.series('fonts'));
	gulp.watch(config.scripts, gulp.series('lint', 'scripts'));
	gulp.watch(config.sg_custom, gulp.series('sg:custom'));
};
