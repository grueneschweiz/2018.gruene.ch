<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter('timber/twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'license', function ( $string ) {
			$links = [
				'CC0' => 'https://creativecommons.org/publicdomain/zero/1.0/',
				'CC BY' => 'https://creativecommons.org/licenses/by/4.0/',
				'CC BY-SA' => 'https://creativecommons.org/licenses/by-sa/4.0/',
				'CC BY-NC' => 'https://creativecommons.org/licenses/by-nc/4.0/',
				'CC BY-NC-SA' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
				'CC BY-ND' => 'https://creativecommons.org/licenses/by-nd/4.0/',
				'CC BY-NC-ND' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
			];

			$patterns = array_map(function($licence){
				return "/\b($licence)\b/";
			}, array_keys($links));

			$replacements = array_map(function($link){
				return "<a href='$link' target='_blank' rel='noopener'>$0</a>";
			}, array_values($links));

			$string = preg_replace($patterns, $replacements, $string);
			
			return $string;
		} )
	);
	
	return $twig;
} );
