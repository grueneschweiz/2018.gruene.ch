<?php

/**
 * Remove the option meta box of tribe events, as these options are not supported
 */
add_action( 'do_meta_boxes', function() {
	remove_meta_box('tribe_events_event_options', 'tribe_events', 'side');
} );
