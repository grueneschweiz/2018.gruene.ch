<?php

namespace WP_Prometheus_Metrics\metrics;

use SUPT\Mailer;

class Forms_Mail_Queue_Metric extends Abstract_Metric {

	public function __construct() {
		parent::__construct( 'supt_forms_mail_queue', 'gauge' );
	}

	public function get_metric_value(): int {
		require_once dirname( __DIR__ ) . '/form/include/Mailer.php';
		$queue = Mailer::get_queue();
		return $queue->length();
	}

	public function get_help_text(): string {
		return 'Total number of form emails to be sent';
	}
}

