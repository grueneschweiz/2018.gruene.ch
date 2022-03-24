<?php

namespace WP_Prometheus_Metrics\metrics;

class Multisite_User_Count extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'multisite_cached_active_user_count', 'gauge' );
	}

	public function get_metric_value(): int {
		return get_user_count();
	}

	public function get_help_text(): string {
		return 'Cached total number of active users in multisite network';
	}
}

