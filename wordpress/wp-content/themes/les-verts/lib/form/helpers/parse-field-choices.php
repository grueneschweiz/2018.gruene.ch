<?php
/**
 * Parse the field choices given from a string to array
 *
 * @param  string $choices  The choices in a string format, every choice on each line
 * @return array            Associative array with key/value of the choices
 */
function supt_form_parse_field_choices( $choices ) {

	$lines = explode( "\n", $choices );
	$choices = array();

	foreach ($lines as $i => $line) {
		$matches = explode(':', $line);

		if ( count($matches) == 1 ) {
			$choices[$i] = trim($matches[0]);
		}
		else {
			$choices[ trim($matches[0])] = trim($matches[1]);
		}
	}

	return $choices;
}
