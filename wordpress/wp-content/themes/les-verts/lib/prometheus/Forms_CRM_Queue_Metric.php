<?php

namespace WP_Prometheus_Metrics\metrics;

use SUPT\CrmSaver;

class Forms_CRM_Queue_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'supt_forms_crm_queue', 'gauge' );
	}

	public function get_metric_value(): int {
		require_once dirname( __DIR__ ) . '/form/include/CrmSaver.php';
		$queue = CrmSaver::get_queue();
		return $queue->length();
	}

	public function get_help_text(): string {
		return 'Total number form submissions to be stored in the CRM';
	}
}

