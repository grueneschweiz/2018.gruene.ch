<?php

namespace SUPT;

use function add_filter;
use function get_field;
use function get_the_ID;

/**
 * Cookie Banner Controller
 *
 * Prepares data for the cookie banner and determines if it should be shown
 * based on which tracking scripts are enabled for the current page.
 */
class Cookie_Banner_controller {

	/**
	 * Register the controller
	 */
	public static function register() {
		add_filter( 'timber_context', [ __CLASS__, 'add_to_context' ] );
	}

	/**
	 * Add cookie banner data to Timber context
	 *
	 * @param array $context
	 * @return array
	 */
	public static function add_to_context( $context ) {
		// Get global tracking scripts settings
		$banner_text = get_field( 'tracking_banner_text', 'option' );
		$privacy_link = get_field( 'tracking_privacy_link', 'option' );
		$facebook_pixel_id = get_field( 'tracking_facebook_pixel_id', 'option' );
		$custom_scripts = get_field( 'tracking_custom_scripts', 'option' );

		// Get current page/post ID
		$post_id = get_the_ID();

		// Check which scripts are enabled on this page
		$active_scripts = [];
		$has_active_scripts = false;

		if ( $post_id ) {
			// Check Facebook Pixel
			if ( ! empty( $facebook_pixel_id ) ) {
				$fb_enabled = get_field( 'enable_facebook_pixel', $post_id );
				if ( $fb_enabled ) {
					$active_scripts['facebook_pixel'] = [
						'type' => 'inline',
						'id' => $facebook_pixel_id
					];
					$has_active_scripts = true;
				}
			}

			// Check custom scripts
			if ( is_array( $custom_scripts ) && ! empty( $custom_scripts ) ) {
				foreach ( $custom_scripts as $index => $script ) {
					if ( ! empty( $script['script_url'] ) ) {
						$script_key = 'custom_script_' . $index;
						$field_name = 'enable_custom_script_' . $index;
						$script_enabled = get_field( $field_name, $post_id );

						if ( $script_enabled ) {
							$active_scripts[ $script_key ] = [
								'type' => 'external',
								'url' => $script['script_url'],
								'name' => $script['script_name'] ?? 'Custom Script ' . ( $index + 1 )
							];
							$has_active_scripts = true;
						}
					}
				}
			}
		}

		// Only add to context if there are active scripts
		if ( $has_active_scripts ) {
			$context['cookie_banner'] = [
				'enabled' => true,
				'text' => $banner_text ?: __( 'Wir verwenden Tracking-Skripte, um unsere Website zu verbessern und dir relevante Inhalte anzuzeigen.', THEME_DOMAIN ),
				'privacy_link' => $privacy_link ?: '',
				'active_scripts' => $active_scripts
			];
		} else {
			$context['cookie_banner'] = [
				'enabled' => false
			];
		}

		return $context;
	}

	/**
	 * Get active scripts for current page
	 * Helper method that can be called from other contexts
	 *
	 * @param int|null $post_id
	 * @return array
	 */
	public static function get_active_scripts( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return [];
		}

		$active_scripts = [];

		// Get global settings
		$facebook_pixel_id = get_field( 'tracking_facebook_pixel_id', 'option' );
		$custom_scripts = get_field( 'tracking_custom_scripts', 'option' );

		// Check Facebook Pixel
		if ( ! empty( $facebook_pixel_id ) && get_field( 'enable_facebook_pixel', $post_id ) ) {
			$active_scripts['facebook_pixel'] = [
				'type' => 'inline',
				'id' => $facebook_pixel_id
			];
		}

		// Check custom scripts
		if ( is_array( $custom_scripts ) && ! empty( $custom_scripts ) ) {
			foreach ( $custom_scripts as $index => $script ) {
				if ( ! empty( $script['script_url'] ) ) {
					$script_key = 'custom_script_' . $index;
					$field_name = 'enable_custom_script_' . $index;

					if ( get_field( $field_name, $post_id ) ) {
						$active_scripts[ $script_key ] = [
							'type' => 'external',
							'url' => $script['script_url'],
							'name' => $script['script_name'] ?? 'Custom Script ' . ( $index + 1 )
						];
					}
				}
			}
		}

		return $active_scripts;
	}
}
