<?php

namespace SUPT;

use WP_CLI;

require_once __DIR__ . '/FormMailCommand.php';
require_once __DIR__ . '/FormCrmCommand.php';

/**
 * Manages form associated data.
 */
class FormCommand {
	/**
	 * ## OPTIONS
	 *
	 * <command>
	 */
	public function mail( $args, $assoc_args ) {
	}

	/**
	 * ## OPTIONS
	 *
	 * <command>
	 */
	public function crm( $args, $assoc_args ) {
	}
}

WP_CLI::add_command( 'form', new FormCommand() );
