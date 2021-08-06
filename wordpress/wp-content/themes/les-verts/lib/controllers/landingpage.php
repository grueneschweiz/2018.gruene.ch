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
		add_filter( 'dynamic_sidebar_params', array( __CLASS__, 'add_widget_class' ) );
	}

	/**
	 * Wrap widgets that are not in the allow list in a div with the class distracting_widget
	 *
	 * @param array $widget
	 *
	 * @return array
	 */
	public static function add_widget_class( array $widget ): array {
		if ( ! is_page_template( 'single_landingpage.php' ) ) {
			return $widget;
		}

		if (
			// if widget in footer widget area
			array_key_exists( 'id', $widget[0] )
			&& 'footer-widget-area' === $widget[0]['id']

			// and widget is not in allow list
			&& array_key_exists( 'widget_id', $widget[0] )
			&& ! self::is_distracting_widget( $widget[0]['widget_id'] )
		) {
			$widget[0]['before_widget'] = '<div class="distracting_widget">' . $widget[0]['before_widget'];
			$widget[0]['after_widget']  = $widget[0]['after_widget'] . '</div>';
		}

		return $widget;
	}

	private static function is_distracting_widget( string $widget_id ): bool {
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


