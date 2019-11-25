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
 * Load all posts
 *
 * Else we can't grab the categories and tags
 */
add_filter( 'searchwp_search_args', function ( $args ) {
	$args['posts_per_page'] = - 1;

	return $args;
} );

/**
 * Filter search results by category
 */
add_filter( 'searchwp_include', function ( $ids, $engine, $terms ) {
	// Bail early, if no category filters are provided
	if ( empty( $_GET['cat'] ) ) {
		return $ids;
	}

	$category_ids = array_map( 'absint', explode( ',', $_GET['cat'] ) );

	$args = array(
		'category__and' => $category_ids,
		'fields'        => 'ids',
		'nopaging'      => true,
	);

	$ids = get_posts( $args );

	return empty( $ids ) ? array( 0 ) : $ids;
}, 10, 3 );

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

/**
 * Add person name from people blocks to search results
 */
add_filter( 'searchwp_custom_fields', function ( $customFieldValue, $customFieldName ) {
	// match only people blocks (that are part of the default content block)
	if ( ! preg_match( '/^[^_].+content_\d+_person$/', $customFieldName ) ) {
		return $customFieldValue;
	}

	$contentToIndex = '';

	foreach ( $customFieldValue as $person ) {
		if ( is_numeric( $person ) ) {
			$contentToIndex .= get_field( 'full_name', $person ) . ' ';
		}
	}

	return $contentToIndex;
}, 10, 2 );
