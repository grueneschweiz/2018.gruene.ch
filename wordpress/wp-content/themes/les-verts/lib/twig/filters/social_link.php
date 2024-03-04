<?php

namespace SUPT;

use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
		new TwigFilter( 'social_link', function ( $string, $type ) {

			$string = trim( $string );

			if ( 0 === strpos( $string, 'http' ) ) {
				return $string;
			}

			if ( 'facebook' === $type ) {
				$base   = 'https://www.facebook.com/';
				$string = preg_replace( '/^(www)?\.facebook\.com\/?/', '', $string );
			} elseif ( 'twitter' === $type ) {
				$base   = 'https://twitter.com/';
				$string = preg_replace( '/^(www)?\.twitter\.com\/?/', '', $string );
				$string = str_replace( '@', '', $string );
			} elseif ( 'tiktok' === $type ) {
				$base   = 'https://tiktok.com/';
				$string = preg_replace( '/^(www)?\.tiktok\.com\/?/', '', $string );
				$string = str_replace( '@', '', $string );
				$string = "@" . $string;
			} elseif ( 'bluesky' === $type ) {
				$base   = 'https://bsky.app/profile/';
				$string = preg_replace( '/^(www)?\.bsky\.app\/?/', '', $string );
				$string = str_replace( '@', '', $string );
				$string = $string . ".bsky.social";
			} elseif ( 'instagram' === $type ) {
				$base   = 'https://www.instagram.com/';
				$string = preg_replace( '/^(www)?\.instagram\.com\/?/', '', $string );
				$string = str_replace( '@', '', $string );
			} elseif ( 'mastodon' === $type ) {
				if ( preg_match( '/^@(\S+)@(\S+)$/', $string, $matches ) ) {
					$base   = 'https://' . $matches[2];
					$string = '@' . $matches[1];
				}
			} elseif ( 'tumblr' === $type ) {
				$base   = 'https://www.tumblr.com/';
				$string = preg_replace( '/^(www)?\.twitter\.com\/?/', '', $string );
				$string = str_replace( '@', '', $string );
			}
			
			return $base . $string;
		} )
	);
	
	return $twig;
} );
