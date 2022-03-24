<?php

namespace WP_Prometheus_Metrics\metrics;

class Node_User_Proc_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'node_user_proc_count', 'gauge' );
	}

	public function get_metric_value(): int {
		$proc_files = scandir( '/proc', SCANDIR_SORT_NONE );

		return count( array_filter( $proc_files, static function ( $name ) {
			return is_numeric( $name );
		} ) );
	}

	public function get_help_text(): string {
		return 'Number of processes of the current user running on the current node';
	}
}

