<?php

namespace SUPT;

use \Twig_SimpleFilter;

/**
 * Tribe Events Post Type: Return a nicely formatted and localized string
 * with the events time indication.
 *
 * If we have a all day event, only the start date will be returned.
 *
 * Make sure to pass in a post object of the post type 'tribe_events'
 *
 * USAGE: `{{ event|time }}`
 */
add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'event_time', function ( $obj ) {
			$start_date = date_i18n( get_option( 'date_format' ), strtotime( $obj->_EventStartDate ) );
			
			if ( $obj->_EventAllDay ) {
				return $start_date;
			}
			
			$start_time = date_i18n( get_option( 'time_format' ), strtotime( $obj->_EventStartDate ) );
			$stop_time  = date_i18n( get_option( 'time_format' ), strtotime( $obj->_EventEndDate ) );
			
			return $start_date . ' | ' . $start_time . ' &ndash; ' . $stop_time;
		} )
	);
	
	return $twig;
} );


/**
 * Tribe Events Post Type: Return a american formatted time string of the
 * beginning of the event.
 *
 * If we have a all day event, only the start date will be returned.
 *
 * Make sure to pass in a post object of the post type 'tribe_events'
 *
 * USAGE: `{{ event|time }}`
 */
add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new Twig_SimpleFilter( 'event_timestamp', function ( $obj ) {
			if ( $obj->_EventAllDay ) {
				$format = 'Y-m-d';
			} else {
				$format = 'Y-m-d H:i';
			}
			
			return date( $format, strtotime( $obj->_EventStartDate ) );
		} )
	);
	
	return $twig;
} );
