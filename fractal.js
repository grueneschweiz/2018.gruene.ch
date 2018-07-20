const path = require( 'path' );
const fs = require( 'fs' );
const fractal = module.exports = require('@frctl/fractal').create();

const CONFIG = require( './config.json' );

/**
 * SETUP FRACTAL
 */
fractal.set( 'project.title', 'Styleguide' );
fractal.set( 'project.version', CONFIG.version );
fractal.web.set( 'builder.dest', path.resolve( __dirname, CONFIG.fractal.build) );
fractal.web.set( 'static.path',path.resolve( __dirname, CONFIG.fractal.static) );
fractal.docs.set( 'path', path.resolve( __dirname, CONFIG.fractal.src.docs) );

fractal.components.set( 'path',  CONFIG.fractal.src.components );
fractal.components.set( 'default.preview', '@preview' ); // preview template name component

/**
 * SETUP THE STYLEGUIDE THEME
 */
const mandelbrot = require('@frctl/mandelbrot');
const myCustomisedTheme = mandelbrot({
	favicon: '/img/favicons/favicon.ico',
	skin: "green",
	nav: ["docs", "components"],
	panels: ["html", "view", "resources", "context", "info", "notes"],
	styles: [ 'default' ].concat( fs.readdirSync( path.resolve(CONFIG.fractal.overides.path, './css') ).map(filename => path.join(CONFIG.fractal.overides.serve, './css', filename)) ),
	scripts: [ 'default' ].concat( fs.readdirSync( path.resolve(CONFIG.fractal.overides.path, './js') ).map(filename => path.join(CONFIG.fractal.overides.serve, './js', filename)) ),
});
myCustomisedTheme.addLoadPath( path.resolve( __dirname, CONFIG.fractal.overides.path, './theme') ); // specify a directory to hold the theme override templates
fractal.web.theme(myCustomisedTheme); // tell Fractal to use the configured theme by default

// Dynamically load Handlebars helpers
const helpers = {};
fs.readdirSync( path.resolve(CONFIG.fractal.helpers) ).forEach( filename => { helpers[filename.replace('.js', '').replace('-','_')] = require(path.resolve(CONFIG.fractal.helpers, filename)) });

const hbs = require('@frctl/handlebars')({ helpers: helpers });
fractal.components.engine(hbs);
fractal.docs.engine(hbs); // add the helper to the doc engine as well

fractal.web.set( 'server.sync', true );
fractal.web.set( 'server.syncOptions', CONFIG.fractal.sync );

/**
 * Register 'list-components' command.
 */
let listComponents = require('./cli/list-components');
fractal.cli.command('list:components', listComponents, {
	description: 'Lists components in the project'
});

/**
 * Register 'generate-sass' command.
 */
let generateSassFile = require('./cli/generate-sass');
fractal.cli.command('sass:generate', generateSassFile, {
	description: 'Generate the SCSS component loader file'
});
