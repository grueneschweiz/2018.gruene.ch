<?php

namespace SUPT;

use TimberImage;

class Branding_controller {

	const DELIMITERS = '\.,\-\/';

	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
	}

	public static function add_to_context( $context ) {
		$show_branding = get_theme_mod( Customizer\Branding::SHOW_TAGLINE, true );

		if ( $show_branding ) {
			$context['branding']['unbreakeables'] = self::get_branding();
			$context['branding']['logo']          = self::get_logo();
		}

		return $context;
	}

	public static function get_branding() {
		$tagline       = get_bloginfo( 'description' );
		$unbreakeables = preg_split( '/([' . self::DELIMITERS . '])/', $tagline, - 1,
			PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		foreach ( $unbreakeables as $key => &$item ) {
			$item = trim( $item );

			// append the separators again
			if ( $key % 2 ) {
				$unbreakeables[ $key - 1 ] .= $item;
				unset( $unbreakeables[ $key ] );
			}
		}

		return $unbreakeables;
	}

	public static function get_logo() {
		$img_id = get_theme_mod( Customizer\Logo::SETTING_LOGO_LIGHT, false );

		return [
			'image'  => new TimberImage( $img_id ),
			'srcset' => [ 2, 3 ],
			'resize' => [ 148 ],
		];
	}
}
