<?php

/**
 * Little helper function to sanitize a sring
 * with underscores instead of dashes.
 *
 * @param		string	$string	The string to sanitize
 * @return	string
 *
 * @uses	sanitize_title
 */
function supt_form_sanitize_with_underscore( $string ) {
	return str_replace( '-', '_', sanitize_title($string) );
}
