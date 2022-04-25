<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 20.09.18
 * Time: 18:44
 */

namespace SUPT;


use Timber\Post;
use Timber\Timber;

class SUPTTribeEvent extends ACFPost {
	private $venue;
	private $venue_string = '';
	private $time_string = '';
	private $timestamp = '';

	/**
	 * Return the venue object of the event
	 *
	 * @return Post|null
	 */
	public function venue() {
		if ( $this->venue || empty($this->_EventVenueID)) {
			return $this->venue;
		}

		// load venue
		$this->venue = Timber::get_post( $this->_EventVenueID );

		return $this->venue;
	}

	/**
	 * If there is a venue return a nicely formatted address string
	 *
	 * @return string
	 */
	public function venue_string() {
		if ( $this->venue_string ) {
			return $this->venue_string;
		}

		if ( ! $this->venue() ) {
			return '';
		}
		
		$venue[] = $this->venue()->title();

		if ( $this->venue->_VenueAddress ) {
			$venue[] = $this->venue()->get_field( '_VenueAddress' );
		}

		if ( $this->venue->_VenueCity ) {
			$venue[] = $this->venue()->get_field( '_VenueCity' );
		}

		$this->venue_string = implode( ', ', $venue );

		return $this->venue_string;
	}

	/**
	 * Return a nicely formatted and localized string
	 * with the events time indication.
	 *
	 * If we have a all day event, only the start date will be returned.
	 *
	 * @return string
	 */
	public function time_string() {
		if ($this->time_string) {
			return $this->time_string;
		}

		$start_date = date_i18n( get_option( 'date_format' ), strtotime( $this->_EventStartDate ) );

		if ( $this->_EventAllDay ) {
			$this->time_string = $start_date;

			return $this->time_string;
		}

		$start_time = date_i18n( get_option( 'time_format' ), strtotime( $this->_EventStartDate ) );
		$stop_time  = date_i18n( get_option( 'time_format' ), strtotime( $this->_EventEndDate ) );

		$this->time_string = $start_date . ' | ' . $start_time . ' &ndash; ' . $stop_time;

		return $this->time_string;
	}

	/**
	 * Return a american formatted time string of the
	 * beginning of the event.
	 *
	 * If we have a all day event, only the start date will be returned.
	 *
	 * @return string
	 */
	public function timestamp() {
		if ($this->timestamp) {
			return $this->timestamp;
		}

		if ( $this->_EventAllDay ) {
			$format = 'Y-m-d';
		} else {
			$format = 'Y-m-d H:i';
		}

		$this->timestamp = date( $format, strtotime( $this->_EventStartDate ) );

		return $this->timestamp;
	}
}
