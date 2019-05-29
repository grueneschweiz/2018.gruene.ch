<?php

/**
 * Ignore accents and umlauts
 */
add_filter( 'searchwp_lenient_accents', '__return_true' );

/**
 * Hide admin bar button
 */
add_filter( 'searchwp_admin_bar', '__return_false' );

/**
 * Order search results by date
 */
add_filter( 'searchwp_return_orderby_date', '__return_true' );

/**
 * Set initial config on plugin activation
 */
add_filter( 'searchwp_initial_engine_settings', function ( $settings ) {

	// post
	$settings['default']['post']['weights']['cf'] = array(
		'swppv4dff695361f02e59e1755d73c44613e1' =>
			array(
				'metakey' => 'teaser',
				'weight'  => 40,
			),
		'swppve2c4bcc5c238270fb2ff3917c6d90393' =>
			array(
				'metakey' => 'main_content_%',
				'weight'  => 5,
			),
	);

	$settings['default']['post']['weights']['tax']['category'] = 50;
	$settings['default']['post']['weights']['tax']['post_tag'] = 50;
	$settings['default']['post']['options']['stem']            = 1;
	$settings['default']['post']['weights']['comment']         = 0;


	// page
	$settings['default']['page']['weights']['cf'] = array(
		'swppv53ea7e950a6b32e7585dfa8f745b025d' =>
			array(
				'metakey' => 'teaser',
				'weight'  => 40,
			),
		'swppv041755c70f3f76f0befae15c935adbe3' =>
			array(
				'metakey' => 'main_content_%',
				'weight'  => 5,
			),
	);

	$settings['default']['page']['weights']['tax']['category'] = 50;
	$settings['default']['page']['options']['stem']            = 1;
	$settings['default']['post']['weights']['comment']         = 0;


	// event
	$settings['default']['tribe_events']['weights']['cf'] = array(
		'swppvcbc5be1c4083ea4a3beba9b9a280a2a5' =>
			array(
				'metakey' => 'description',
				'weight'  => 5,
			),
	);

	$settings['default']['tribe_events']['options']['stem']    = 1;
	$settings['default']['tribe_events']['weights']['comment'] = 0;


	// people
	$settings['default']['people']['enabled'] = false;


	// attachment
	$settings['default']['attachment']['enabled'] = false;

	return $settings;
} );
