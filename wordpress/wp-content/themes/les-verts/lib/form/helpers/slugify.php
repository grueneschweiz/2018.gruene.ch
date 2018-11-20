<?php
/**
* Transform given string in slug (as used in the slugify twig function)
*
* @param array|string $string
*
* @return array|string
*/
function supt_slugify( $string ) {
	return str_replace( '-', '_', sanitize_title( trim( $string ) ) );
}
