<?php

namespace WP_Prometheus_Metrics\metrics;

class Node_Meminfo_Metric extends Abstract_Metric {

	private $value;
	private $key;

	public function __construct( string $key, int $value ) {
		parent::__construct( "node_memory_{$key}_bytes", 'gauge' );
		$this->value = $value;
		$this->key   = $key;
	}

	public static function init() {
		$mem_fh = fopen( '/proc/meminfo', 'rb' );
		while ( $line = fgets( $mem_fh ) ) {
			$parts = [];
			if ( preg_match( '/^([^:]+):\s+(\d+)\s+kB/', $line, $parts ) ) {
				$key   = str_replace( [ '(', ')' ], [ '_', '' ], $parts[1] );
				$value = $parts[2] * 1024;
				new self( $key, (int) $value );
			}
		}

		fclose( $mem_fh );
	}

	public function get_metric_value(): int {
		return $this->value;
	}

	public function get_help_text(): string {
		return "Node Memory {$this->key} in bytes";
	}
}

