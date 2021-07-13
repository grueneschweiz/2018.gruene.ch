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

	/**
	 * Controls if the page should be rendered distraction free
	 *
	 * @var bool
	 */
	private static $distraction_free;

	public static function register() {
		add_filter( 'timber_context', array( __CLASS__, 'add_to_context' ) );
		add_filter( 'sidebars_widgets', array( __CLASS__, 'filter_widgets' ) );
	}

	public static function add_to_context( $context ) {
		$context['distraction_free'] = self::should_be_distraction_free();

		return $context;
	}

	/**
	 * Determines if the page should be displayed distraction free.
	 *
	 * Returns true if the page uses the single_landingpage.php template
	 * AND satisfies any of the following criteria:
	 * - the url query string `focus` is present
	 * - the referrer is not the current site
	 *
	 * Therefore visitors following internal links do not see the
	 * distraction free page, unless the `focus` query string was set.
	 *
	 * Subsequent calls of this function return a cached result.
	 *
	 * @return bool
	 */
	public static function should_be_distraction_free() {
		if ( isset( self::$distraction_free ) ) {
			return self::$distraction_free;
		}

		if ( ! is_page_template( 'single_landingpage.php' ) ) {
			self::$distraction_free = false;

			return self::$distraction_free;
		}

		if ( isset( $_GET['focus'] ) ) {
			self::$distraction_free = true;

			return self::$distraction_free;
		}

		if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			self::$distraction_free = true;

			return self::$distraction_free;
		}

		$referer = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );
		$site    = parse_url( site_url(), PHP_URL_HOST );

		self::$distraction_free = $referer !== $site;

		return self::$distraction_free;
	}

	/**
	 * Removes any widget that is not explicitly allowed if the page
	 * is rendered distraction free.
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	public static function filter_widgets( array $widgets ): array {
		if ( ! self::should_be_distraction_free() ) {
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


