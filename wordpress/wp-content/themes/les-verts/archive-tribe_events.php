<?php

/**
 * The template for displaying the list view of tribe events.
 */

$event_display = tribe_get_request_var(
	'tribe_event_display',
	'list'
);

$event_cat_slug = get_query_var( 'tribe_events_cat' );
$term           = get_term_by( 'slug', $event_cat_slug, 'tribe_events_cat' );
$term_id        = $term instanceof WP_Term ? $term->term_id : null;

$context          = Timber::get_context();
$context['posts'] = tribe_get_events( array(
	'eventDisplay'   => $event_display,
	'posts_per_page' => - 1,
	'category'       => $term_id,
) );
$context['title'] = __( 'Events', THEME_DOMAIN );
$templates        = array( 'archive.twig' );

if ( tribe_is_past() ) {
	$context['events_link'] = [
		'link'  => tribe_get_listview_link( $term_id ),
		'label' => __( 'Upcoming events', THEME_DOMAIN )
	];
} else {
	$context['events_link'] = [
		'link'  => tribe_get_listview_past_link( $term_id ),
		'label' => __( 'Previous events', THEME_DOMAIN )
	];
}


Timber::render( $templates, $context );
