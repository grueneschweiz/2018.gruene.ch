<?php

/**
 * Detect if an ICS file is for a single event and then ensure only that event's ID is used for the ICS file
 *
 * This fixes https://theeventscalendar.com/known-issues/#dl_TEC-4469 for the Events Calendar before 6.0.6.
 *
 * @link https://docs.theeventscalendar.com/reference/hooks/tribe_ical_template_event_ids/
 */
add_filter( 'tribe_ical_template_event_ids', function ( $ids ) {
	if ( class_exists( 'Tribe__Events__Main' )
	     && Tribe__Events__Main::VERSION
	     && version_compare( Tribe__Events__Main::VERSION, '6.0.6', '>=' )
	) {
		// only apply the filter to the Events Calendar before 6.0.6
		return $ids;
	}

	$query = tribe_get_global_query_object();
	if ( $query && isset( $query->is_single ) && $query->is_single ) {
		$ids = $query->queried_object->ID;
	}

	return $ids;
} );

/**
 * Use the 'archive-tribe_events.php' template file for event archives.
 */
add_filter( 'tribe_events_views_v2_use_wp_template_hierarchy', function ( $load, $template, $context, $query ) {
	return $query->is_archive() && strpos( $template, 'archive-tribe_events.php' );
}, 10, 4 );

/**
 * Force ugly links for tribe events as the pretty permalinks
 * were constantly buggy, especially in combination with polylang.
 */
add_filter( 'tribe_events_force_ugly_link', '__return_true' );

/**
 * Force ugly links: To really get the ugly links, we do also have to disable url rewriting.
 */
add_filter( 'tribe_events_register_event_cat_type_args', function ( $args ) {
	$args['rewrite'] = false;

	return $args;
} );

/**
 * Set the correct base url, as it does not in every case work properly with polylang.
 */
add_filter( 'tribe_events_ugly_link_baseurl', function ( $base_url ) {
	if ( function_exists( 'pll_home_url' ) ) {
		$default_home_url   = rtrim( pll_home_url( pll_default_language() ), '/' );
		$localized_home_url = rtrim( pll_home_url(), '/' );

		return str_replace( $default_home_url, $localized_home_url, $base_url );
	}

	return $base_url;
} );

/**
 * Fix wrong link from and for the events calendar 5.5.0.1
 * (maybe older version are affected too).
 */
add_filter( 'tribe_events_ugly_link', function ( $url ) {
	return str_replace( 'tribe_event_category', 'tribe_events_cat', $url );
} );


/**
 * Fix tribe events using the regular archive.php template instead of archive-tribe_events.php
 * if an event category was queried
 */
add_filter( 'template_include', function ( $template ) {
	if ( strpos( $template, 'archive.php' ) && get_query_var( 'tribe_events_cat', false ) ) {
		return str_replace( 'archive.php', 'archive-tribe_events.php', $template );
	}

	return $template;
} );

/**
 * Since The Events Calendar 6.15.6, `Tribe\Events\Views\V2\Hooks::disabled_views_redirect()`
 * redirects any archive request whose `eventDisplay`/`tribe_event_display` value is not a
 * registered View slug (e.g. 'list', 'month', 'day') to the default view, stripping the
 * requested display along the way.
 *
 * Because the theme forces ugly links (see `tribe_events_force_ugly_link` above), past events
 * are requested via the `tribe_event_display=past` query var handled by our own
 * `archive-tribe_events.php` template. 'past' is a legitimate display *mode* of the List View,
 * not a separate View, but the context reads it into `event_display` too and the redirect
 * treats it as an unknown/disabled View, breaking the past events list
 * (e.g. https://gruene.ch/?post_type=tribe_events&tribe_event_display=past was redirected to
 * https://gruene.ch/events/liste?tribe_redirected=1).
 *
 * @link https://docs.theeventscalendar.com/reference/hooks/tec_events_views_v2_should_redirect/
 */
add_filter( 'tec_events_views_v2_should_redirect', function ( $should_redirect ) {
	if ( 'past' === tribe_get_request_var( 'tribe_event_display' ) ) {
		return false;
	}

	return $should_redirect;
} );

/**
 * Use first text content blocks text as excerpt for events.
 * This is also picked up by Yoast SEO.
 */
add_filter( 'get_the_excerpt', function ( string $post_excerpt, WP_Post $post ) {
	if ( $post->post_type !== 'tribe_events' ) {
		return $post_excerpt;
	}

	$content = get_field( 'event_content', $post->ID );

	if ( empty( $content['content'] ) ) {
		return $post_excerpt;
	}

	foreach ( $content['content'] as $block ) {
		if ( $block['acf_fc_layout'] === 'text' ) {
			return $block['text'];
		}
	}

	return $post_excerpt;
}, 10, 2 );

/**
 * Use text as meta description
 */
add_filter( 'wpseo_replacements', function ( $replacements, $args ) {
	if ( is_object( $args )
	     && property_exists( $args, 'post_type' )
	     && 'tribe_events' === $args->post_type
	     && property_exists( $args, 'ID' )
	) {
		$replacements = [ '%%cf_description%%' => get_the_excerpt( $args->ID ) ];
	} else if ( is_array( $args )
	            && isset( $args['post_type'], $args['ID'] )
	            && 'tribe_events' === $args['post_type']
	) {
		$replacements = [ '%%cf_description%%' => get_the_excerpt( $args['ID'] ) ];
	}

	return $replacements;
}, 10, 2 );
