<?php

namespace SUPT;

use WP_CLI;

/**
 * Theme specific commands.
 */
class LesVertsCommand {
	/**
	 * Converts posts, pages and events to the themes ACF format.
	 *
	 * @subcommand migrate-content
	 */
	public function migrate_content( $args, $assoc_args ) {
		require_once __DIR__ . '/../admin/import-handler.php';
		Importer::import();
	}
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'les-verts', new LesVertsCommand() );
}
