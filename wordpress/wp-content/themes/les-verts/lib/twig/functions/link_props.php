<?php

namespace SUPT;

use \Twig_SimpleFunction;

add_filter( 'get_twig', function( $twig ) {
	$site_host = parse_url(get_site_url(), PHP_URL_HOST);

	$twig->addFunction( new Twig_SimpleFunction( 'link_props', function( $url ) use ($site_host) {
		$host = parse_url($url, PHP_URL_HOST);

		if (empty($host)) {
			return '';
		}

		if ($host === $site_host) {
			return '';
		}

		return 'target="_blank" rel="noopener"';
	} ) );
	return $twig;
});
