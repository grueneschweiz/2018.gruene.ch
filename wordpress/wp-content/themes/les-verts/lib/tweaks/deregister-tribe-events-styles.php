<?php

/**
 * Don't load unused styles of the tribe events plugin
 */
add_action( 'tribe_asset_enqueue', function ( $enqueue, $asset ) {
	$remove = array(
		"tribe-events-full-calendar-style",
		"tribe-events-calendar-full-mobile-style",
		"tribe-events-views-v2-full",
		"tribe-events-views-v2-skeleton",
		"tribe-events-calendar-style",
		"tribe-events-calendar-mobile-style",
		"tribe-events-admin-menu",
		"tribe-events-views-v2-bootstrap-datepicker-styles",
		"tribe-tooltip",
		"promoter"
	);

	return ! in_array( $asset->slug, $remove );
}, 10, 2 );
