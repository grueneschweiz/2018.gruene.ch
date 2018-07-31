<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 31.07.18
 * Time: 12:41
 */

namespace SUPT\Customizer;


class Logo {
	const PRIORITY = 60;
	const WIDTH_LIGHT = 296; // doubled because of retina
	const WIDTH_DARK = 232; // same here
	
	const SECTION = THEME_DOMAIN . '_logo';
	const SETTING_LOGO_LIGHT = 'logo_light';
	const SETTING_LOGO_DARK = 'logo_dark';
	
	public static function register() {
		add_action( 'customize_register', function ( $wp_customize ) {
			self::add_section( $wp_customize );
			self::add_settings( $wp_customize );
			self::add_controls( $wp_customize );
		} );
	}
	
	public static function add_section( $wp_customize ) {
		$wp_customize->add_section( self::SECTION, array(
			'title'    => __( 'Logos', THEME_DOMAIN ),
			'priority' => self::PRIORITY,
		) );
	}
	
	public static function add_settings( $wp_customize ) {
		// white logo
		$wp_customize->add_setting( self::SETTING_LOGO_LIGHT, array(
			'default' => false,
		) );
		
		// green logo
		$wp_customize->add_setting( self::SETTING_LOGO_DARK, array(
			'default' => false,
		) );
	}
	
	public static function add_controls( $wp_customize ) {
		// green logo
		$wp_customize->add_control(
			new \WP_Customize_Media_Control(
				$wp_customize,
				self::SETTING_LOGO_LIGHT,
				array(
					'label'       => __( 'Choose or upload the logo in the light variant.', THEME_DOMAIN ),
					'description' => __( sprintf( 'A compressed SVG is preferred. If you use a PNG, make sure its at least %d pixels wide.',
						self::WIDTH_LIGHT ), THEME_DOMAIN ),
					'section'     => self::SECTION,
					'mime_type'   => 'image',
				)
			)
		);
		
		// green logo
		$wp_customize->add_control(
			new \WP_Customize_Media_Control(
				$wp_customize,
				self::SETTING_LOGO_DARK,
				array(
					'label'       => __( 'Choose or upload the logo in the dark variant.', THEME_DOMAIN ),
					'description' => __( sprintf( 'A compressed SVG is preferred. If you use a PNG, make sure its at least %d pixels wide.',
						self::WIDTH_DARK ), THEME_DOMAIN ),
					'section'     => self::SECTION,
					'mime_type'   => 'image',
				)
			)
		);
	}
}