<?php

/**
 * Reduce caching time
 */

use WP_Prometheus_Metrics\metrics\Form_Submissions_Count_Metric;
use WP_Prometheus_Metrics\metrics\Forms_CRM_Queue_Metric;
use WP_Prometheus_Metrics\metrics\Forms_Mail_Queue_Metric;
use WP_Prometheus_Metrics\metrics\Multisite_Site_Count;
use WP_Prometheus_Metrics\metrics\Multisite_User_Count;

add_filter( 'prometheus-metrics-for-wp/timeout', function () {
	return 60; // cache metrics for one minute
} );

/**
 * Add forms
 */
add_filter( 'prometheus-metrics-for-wp/wp_post_types_total/type', function ( array $post_types ) {
	if ( ! in_array( 'theme_form', $post_types, true ) ) {
		$post_types[] = 'theme_form';
	}

	return $post_types;
} );

/**
 * Add custom metrics
 */
add_action( 'prometheus_custom_metrics', function ( $metrics = [] ) {
	include_once __DIR__ . '/Form_Submissions_Count_Metric.php';
	include_once __DIR__ . '/Forms_CRM_Queue_Metric.php';
	include_once __DIR__ . '/Forms_Mail_Queue_Metric.php';

	new Form_Submissions_Count_Metric();
	new Forms_CRM_Queue_Metric();
	new Forms_Mail_Queue_Metric();

	if ( is_multisite() ) {
		include_once __DIR__ . '/Multisite_Site_Count.php';
		include_once __DIR__ . '/Multisite_User_Count.php';

		new Multisite_Site_Count();
		new Multisite_User_Count();
	}

	return $metrics;
} );
