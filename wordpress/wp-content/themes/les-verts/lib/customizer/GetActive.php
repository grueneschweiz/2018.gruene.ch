<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 31.07.18
 * Time: 12:41
 */

namespace SUPT\Customizer;


/**
 * Adds the get active section to the customizer
 *
 * @package SUPT\Customizer
 */

class GetActive {
	const PRIORITY = 101;
	
	const SECTION = THEME_DOMAIN. '_section';
	const SETTING_GET_ACTIVE_LABEL = 'get_active_label';
	const SETTING_GET_ACTIVE_LINK = 'get_active_link';
	
	public static function register() {
		add_action( 'customize_register', function ( $wp_customize ) {
			self::add_section( $wp_customize );
			self::add_settings( $wp_customize );
			self::add_controls( $wp_customize );
		} );
		add_action( 'admin_init',  array(__CLASS__, 'add_strings_for_translation') );
	}
	
	public static function add_section( $wp_customize ) {
		$wp_customize->add_section( self::SECTION, array(
			'title'    => __( 'Get Active Button', THEME_DOMAIN ),
			'priority' => self::PRIORITY,
		) );
	}
	
	public static function add_settings( $wp_customize ) {
		$wp_customize->add_setting( self::SETTING_GET_ACTIVE_LABEL, array(
			'default' => __('Get Active', THEME_DOMAIN),
		) );
		
		$wp_customize->add_setting( self::SETTING_GET_ACTIVE_LINK, array(
			'default' => null,
		) );
	}
	
	public static function add_controls( $wp_customize ) {
		$wp_customize->add_control(
			new \WP_Customize_Control(
				$wp_customize,
				self::SETTING_GET_ACTIVE_LABEL,
				array(
					'label'          => __( 'Button Label', THEME_DOMAIN ),
					'description'    => __( 'Set the label text for the get active button.', THEME_DOMAIN ),
					'section'        => self::SECTION,
					'settings'       => self::SETTING_GET_ACTIVE_LABEL,
					'type'           => 'text',
				)
			)
		);
		
		$wp_customize->add_control(
			new \WP_Customize_Control(
				$wp_customize,
				self::SETTING_GET_ACTIVE_LINK,
				array(
					'label'          => __( 'Button Link', THEME_DOMAIN ),
					'description'    => __( 'Add the url, where the get active button should lead to.', THEME_DOMAIN ),
					'section'        => self::SECTION,
					'settings'       => self::SETTING_GET_ACTIVE_LINK,
					'type'           => 'url',
					'allow_addition' => true,
				)
			)
		);
	}

	public static function add_strings_for_translation() {
		if ( function_exists( 'pll_register_string' ) ) {
			$label = get_theme_mod( self::SETTING_GET_ACTIVE_LABEL, false );
			pll_register_string( self::SETTING_GET_ACTIVE_LABEL, $label, THEME_DOMAIN );

			$link = get_theme_mod( self::SETTING_GET_ACTIVE_LINK, false );
			pll_register_string( self::SETTING_GET_ACTIVE_LINK, $link, THEME_DOMAIN );
		}
	}
}
