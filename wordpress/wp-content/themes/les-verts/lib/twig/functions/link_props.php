<?php

namespace SUPT;

use Twig\TwigFunction;

add_filter( 'timber/twig', function( $twig ) {
	$site_host = parse_url(get_site_url(), PHP_URL_HOST);

	$twig->addFunction( new TwigFunction( 'link_props', function( $url ) use ($site_host) {
		$props = [];
		$url_host = parse_url($url, PHP_URL_HOST);

		if ($url_host && $url_host !== $site_host) {
			$props['target'] = '_blank';
			$props['rel'] = 'noopener noreferrer';
		}

		return $props;
	} ) );
	return $twig;
});
