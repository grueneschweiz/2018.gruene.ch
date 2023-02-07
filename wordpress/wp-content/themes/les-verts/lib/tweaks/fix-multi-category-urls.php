<?php

/**
 * Fix multi-category urls
 *
 * Links to an archive of multiple categories with an AND conjunction are not
 * correctly converted to pretty permalinks (e.g. /?category_name=cat1+cat2 is
 * converted to /category/cat1/ instead of /category/cat1+cat2/). We therefore
 * disable the pretty permalink conversion for category_name queries if they
 * contain an AND conjunction.
 */
add_filter( 'redirect_canonical', function ( $redirect_url, $requested_url ) {
	$query = parse_url( $requested_url, PHP_URL_QUERY );
	if ( empty( $query ) || strpos( $query, 'category_name=' ) === false ) {
		return $redirect_url;
	}

	parse_str( $query, $query_vars );
	if ( empty( $query_vars['category_name'] ) ) {
		return $redirect_url;
	}

	$query_vars['category_name'] = str_replace( ' ', '+', $query_vars['category_name'] );

	if ( strpos( $query_vars['category_name'], '+' ) === false ) {
		return $redirect_url;
	}

	return $requested_url;
}, 10, 2 );
