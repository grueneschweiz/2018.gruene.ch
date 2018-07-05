const fractal = require('../fractal');

function sleep (time) {
	return new Promise((resolve) => setTimeout(resolve, time));
}

/**
 * Function tasks for gulp to run
 *
 * NOTE: this has to refer to gulp
 */
module.exports = {
	start: function( config ) {

		// Set and start the dev server
		const server = fractal.web.server( config );
		server.on( 'error', err => fractal.cli.console.error(err.message) );
		return server.start().then(() => {
			fractal.cli.console.success(`Fractal server is now running at ${server.url}`);
		});
	},

	/*
	 * Run a static export of the project web UI.
	 *
	 * This task will report on progress using the 'progress' event emitted by the
	 * builder instance, and log any errors to the terminal.
	 *
	 * The build destination will be the directory specified in the 'builder.dest'
	 * configuration option set above.
	 */
	build: function(){
		const builder = fractal.web.builder();
		builder.on('progress', (completed, total) => fractal.cli.console.update(`Exported ${completed} of ${total} items`, 'info'));
		builder.on('error', err => fractal.cli.console.error(err.message));
		return sleep(500).then(() => { // this is a temporary hack because sometimes fractal copies /static to /build before the svgsprite is done
			builder.build().then(() => {
				fractal.cli.console.success('Fractal build completed!');
			});
		});

	}
}
