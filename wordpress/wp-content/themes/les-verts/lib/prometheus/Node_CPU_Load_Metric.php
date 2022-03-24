<?php

namespace WP_Prometheus_Metrics\metrics;

class Node_CPU_Load_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'node_cpu_load', 'gauge' );
	}

	public function print_metric( $measure_all = false ) {
		if ( ! $this->is_enabled( $measure_all ) ) {
			return;
		}
		echo "# HELP $this->metric_name {$this->get_help_text()}\n";
		echo "# TYPE $this->metric_name $this->type\n";

		$load = sys_getloadavg();
		$ts   = time() * 1000;

		echo "{$this->metric_name}{{$this->get_metric_labels()},average=\"1_minute\"} {$load[0]} $ts\n";
		echo "{$this->metric_name}{{$this->get_metric_labels()},average=\"5_minutes\"} {$load[1]} $ts\n";
		echo "{$this->metric_name}{{$this->get_metric_labels()},average=\"15_minutes\"} {$load[2]} $ts\n";
	}

	public function get_help_text(): string {
		return 'Average CPU load of this node';
	}

	public function get_metric_value(): int {
		return 0;
	}
}

