<?php


namespace SUPT;

class Landingpage_controller {
	/**
	 * The base_id of the widgets that are not removed from the landing page.
	 *
	 * Use the 'supt_allowed_landing_page_widgets' filter to customize.
	 *
	 * @var string[]
	 */
	public static $allowed_widgets = array(
		'supt_contact_widget',
		'text',
	);

	public static function register() {
		add_filter( 'sidebars_widgets', array( __CLASS__, 'filter_widgets' ) );
	}

	public static function filter_widgets( array $widgets ): array {
		if ( ! is_page_template( 'single_landingpage.php' ) ) {
			return $widgets;
		}

		if ( array_key_exists( 'footer-widget-area', $widgets )
		     && is_array( $widgets['footer-widget-area'] ) ) {

			$widgets['footer-widget-area'] = array_filter(
				$widgets['footer-widget-area'],
				array( __CLASS__, 'is_allowed_widget' )
			);
		}

		return $widgets;
	}

	private static function is_allowed_widget( string $widget_id ): bool {
		$allowed = self::allowed_widgets();

		foreach ( $allowed as $base_id ) {
			if ( false !== strpos( $widget_id, $base_id ) ) {
				return true;
			}
		}

		return false;
	}

	private static function allowed_widgets(): array {
		/**
		 * Defines the widgets allowed to be shown on a landing page.
		 *
		 * @param array $allowed_widgets list of the allowed widget's $id_base
		 *
		 * @since 0.22.0
		 *
		 */
		return apply_filters( 'supt_allowed_landing_page_widgets', self::$allowed_widgets );
	}
}


