<?php

namespace WP_Prometheus_Metrics\metrics;

class Form_Submissions_Count_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'supt_form_submissions', 'gauge' );
	}

	public function get_metric_value(): int {
		global $wpdb;

		return $wpdb->get_var( "SELECT count(*) FROM {$wpdb->postmeta} WHERE meta_key='theme_form'" ); // phpcs:ignore WordPress.DB
	}

	public function get_help_text(): string {
		return 'Total number of form submissions';
	}
}

