const gulp = require('gulp');

module.exports = function( config ) {
	// Watch changes on files
	gulp.watch( config.sass,      ['sass']      );
	gulp.watch( config.fonts,     ['fonts']     );
	gulp.watch( config.images,    ['images']    );
	gulp.watch( config.scripts,   [ 'lint', 'scripts']   );
	gulp.watch( config.svgsprite, ['svgsprite'] );
	gulp.watch( config.sg_custom, ['sg:assets'] );
}
