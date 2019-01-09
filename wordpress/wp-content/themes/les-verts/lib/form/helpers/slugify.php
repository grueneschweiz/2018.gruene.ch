<?php
/**
* Transform given string in slug (as used in the slugify twig function)
*
* @param array|string $string
*
* @return array|string
*/
function supt_slugify( $string ) {
	$string = trim( $string );
	$string = strtolower( $string );
	
	// remove accents, swap ñ for n, etc
	$from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;.';
	$to   = 'aaaaeeeeiiiioooouuuunc_______';
	
	for ( $i = 0; $i < strlen( $from ); $i ++ ) {
		$string = str_replace( substr( $from, $i, 1 ), substr( $to, $i, 1 ), $string );
	}
	
	$string = preg_replace( "/[^a-z0-9 _-]/", '', $string ); // remove invalid chars
	$string = preg_replace( "/\s+/", '_', $string ); // collapse whitespace and replace by a underline
	$string = preg_replace( "/_+/", '_', $string ); // collapse underlines
	
	return $string;
}
