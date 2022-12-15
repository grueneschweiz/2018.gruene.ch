<?php

namespace SUPT\Customizer;


use WP_Customize_Manager;

class Branding {
	private const SECTION = 'title_tagline';
	public const SHOW_TAGLINE = 'show_tagline';

	public static function register(): void {
		add_action( 'customize_register', function ( $wp_customize ) {
			self::add_settings( $wp_customize );
			self::add_controls( $wp_customize );
		} );
	}

	public static function add_settings( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_setting( self::SHOW_TAGLINE, array(
			'default' => true,
		) );
	}

	public static function add_controls( $wp_customize ): void {
		$wp_customize->add_control(
			self::SHOW_TAGLINE,
			array(
				'type'              => 'checkbox',
				'label'             => __( 'Show tagline', THEME_DOMAIN ),
				'description'       => __( 'If checked, the green bar on top is shown, the first time someone visits the page.', THEME_DOMAIN ),
				'section'           => self::SECTION,
				'sanitize_callback' => function ( $val ) {
					return (bool) $val;
				}
			)
		);
	}
}
