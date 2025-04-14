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
        if ($this->venue || empty($this->_EventVenueID)) {
            return $this->venue;
        }

        // load venue
        if ($this->_EventVenueID) {
            $this->venue = Timber::get_post($this->_EventVenueID);
        }

        return $this->venue;
    }

    /**
     * If there is a venue return a nicely formatted address string
     *
     * @return string
     */
    public function venue_string() {
        if ($this->venue_string) {
            return $this->venue_string;
        }

        $venue = $this->venue();
        if (!$venue) {
            return '';
        }

        $this->venue_string = $venue->post_title;

        if ($street = $venue->_VenueAddress) {
            $this->venue_string .= ", $street";
        }

        if ($city = $venue->_VenueCity) {
            $this->venue_string .= ", $city";
        }

        return $this->venue_string;
    }

    /**
     * Return nicely formatted time string
     *
     * @return string
     */
    public function time_string() {
        if ($this->time_string) {
            return $this->time_string;
        }

        $this->time_string = tribe_get_start_date($this->ID, false, 'j.n.Y');

        if ($end_date = tribe_get_end_date($this->ID, false, 'j.n.Y')) {
            if ($end_date !== $this->time_string) {
                $this->time_string .= ' - ' . $end_date;
            }
        }

        return $this->time_string;
    }

    /**
     * Return timestamp for sorting
     *
     * @return string
     */
    public function timestamp() {
        if ($this->timestamp) {
            return $this->timestamp;
        }

        $this->timestamp = tribe_get_start_date($this->ID, false, 'U');

        return $this->timestamp;
    }
}
