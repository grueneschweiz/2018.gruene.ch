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
	return supt_is_multi_category_url( $requested_url ) ? $requested_url : $redirect_url;
}, 10, 2 );


/**
 * Fix pagination links for multi-category URLs when using Polylang
 *
 * When using multi-category URLs with Polylang, the pagination links are
 * in the default language instead of the current language. This filter
 * fixes this by replacing the default home url with the current (and thus
 * translated) home url.
 */
add_filter( 'paginate_links', function ( $link ) {
	// Fix pagination links for multi-category urls when using Polylang
	if (
		function_exists( 'pll_the_languages' )
		&& function_exists( 'pll_default_language' )
		&& function_exists( 'pll_current_language' )
		&& supt_is_multi_category_url( $link )
	) {
		$langs            = pll_the_languages( [ 'raw' => true, 'force_home' => true ] );
		$default_home_url = $langs[ pll_default_language() ]['url'];
		$current_home_url = $langs[ pll_current_language() ]['url'];

		$link = $current_home_url . str_replace( $default_home_url, '', $link );
	}

	return $link;
} );

function supt_is_multi_category_url( $url ): bool {
	$query = parse_url( $url, PHP_URL_QUERY );
	if ( empty( $query ) || strpos( $query, 'category_name=' ) === false ) {
		return false;
	}

	parse_str( $query, $query_vars );
	if ( empty( $query_vars['category_name'] ) ) {
		return false;
	}

	$query_vars['category_name'] = str_replace( ' ', '+', $query_vars['category_name'] );

	return strpos( $query_vars['category_name'], '+' ) !== false;
}
