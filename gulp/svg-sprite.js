const gulp = require('gulp');
const svgSprite = require('gulp-svg-sprite');

module.exports = function( options ) {
	gulp.src(options.src)
		.pipe(svgSprite({
			mode: {
				symbol: { // symbol mode to build the SVG
					render: {
						css: false, // CSS output option for icon sizing
						scss: false // SCSS output option for icon sizing
					},
					dest: '.',
					sprite: options.sprite, //generated sprite name
					example: false // Build a sample page, please!
				}
			},
			shape: {
				id: {
					separator: '_', // Folder separation
					whitespace: '-', // Whitespace
				},
				transform: [
					/*{svgo		: {
						plugins	: [
							{removeAttrs: { attrs: '(fill|stroke)' }},
							{removeTitle: true},
							{mergePaths: false},
							{removeDesc: true},
							{removeXMLNS: true},
							{cleanupAttrs: true},
							{collapseGroups: true},
							{removeEmptyText: true},
							{removeEmptyContainers: true},
							{removeEmptyAttrs: true},
							{removeUselessDefs: true},
							{convertShapeToPath: true},
							{moveElemsAttrsToGroup: true},
							{cleanupEnableBackground: true},
							{moveGroupAttrsToElems: false}
						]
					}}*/
				]
			},
			svg: {
				xmlDeclaration: false, // strip out the XML attribute
				doctypeDeclaration: false // don't include the !DOCTYPE declaration
			}
		}))
		.pipe( gulp.dest(options.dest) );
}
