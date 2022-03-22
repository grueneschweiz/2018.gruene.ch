<?php

namespace WP_Prometheus_Metrics\metrics;

class Multisite_Site_Count extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'multisite_cached_active_site_count', 'gauge' );
	}

	public function get_metric_value(): int {
		return get_blog_count();
	}

	public function get_help_text(): string {
		return 'Cached total number of active blogs in multisite network';
	}
}

