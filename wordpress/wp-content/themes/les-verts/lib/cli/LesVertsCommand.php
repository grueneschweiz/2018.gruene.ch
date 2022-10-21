<?php

namespace SUPT;

use SUPT\Migrations\EventContent\Migrator;
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

	/**
	 * Update events created with version < 0.32.0 of this theme.
	 *
	 * @subcommand update-event-format
	 */
	public function update_event_acf_format() {
		require_once __DIR__ . '/../migrations/event-content.php';
		( new Migrator() )->migrate_all();
	}
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'les-verts', new LesVertsCommand() );
}
