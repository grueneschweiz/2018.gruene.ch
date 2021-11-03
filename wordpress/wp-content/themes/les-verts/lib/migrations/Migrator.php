<?php

namespace SUPT\Migrations;

class Migrator {
	const DB_VERSION_OPTION_NAME = 'supt_db_version';

	public static function getDbVersion() {
		return get_option( self::DB_VERSION_OPTION_NAME, 0 );
	}

	public static function setCurrentVersion() {
		update_option( self::DB_VERSION_OPTION_NAME, THEME_VERSION );
	}
}
