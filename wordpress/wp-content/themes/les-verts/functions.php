<?php

// Composer dependencies
use function SUPT\get_lang;

require_once __DIR__ . '/vendor/autoload.php';

// Constants
$theme = wp_get_theme();
define( 'THEME_VERSION', $theme['Version'] );
define( 'THEME_DOMAIN', 'lesverts' );
define( 'THEME_URI', get_template_directory_uri() );

// Initialize Timber
new Timber\Timber();

// Initialize Timmy (Timber image manipulation)
new Timmy\Timmy();

// Configure Timber
Timber::$dirname = array( 'templates', 'views' );

// used by the uniqueid twig filter. keep this in global scope.
$supt_unique_id_counter = 0;

// This is where the magic happens
require_once __DIR__ . '/lib/_loader.php';


/**
 * ENTRYPOINT
 */
class StarterSite extends TimberSite {

	function __construct() {
		Locale::setDefault( get_lang() );

		// Theme supports
		// -> more info: https://developer.wordpress.org/reference/functions/add_theme_support/
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'yoast-seo-breadcrumbs' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

		// Filters
		// -> more info: https://developer.wordpress.org/reference/functions/add_filter/
		add_filter( 'timber_context', array( $this, 'add_to_context' ), - 1 );

		// Actions
		// -> more info: https://developer.wordpress.org/reference/functions/add_action/
		add_action( 'init', array( $this, 'register_menu_locations' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'setup_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_assets' ) );
		add_action( 'widgets_init', array( $this, 'register_widget_zones' ) );
		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

		// // For debug purpose only: shows all the hooks & registered actions
		// add_action('wp', function(){ echo '<pre>';print_r($GLOBALS['wp_filter']); echo '</pre>';exit; } );

		parent::__construct();
	}


	// HELPERS

	function add_to_context( $context ) {
		// $context['notes'] = 'These values are available everytime you call Timber::get_context();';
		// The registered controllers may also populate the context. Have a look at lib/_loader.php

		// Some plugins rely on this action, and it is usually called by the get_header() template tag,
		// which timber doesn't use. It must be called on every page and before any output is echoed.
		// It must also be included outside of output buffering. Therefore, it's a good spot here, even
		// if it feels hacky.
		do_action( 'get_header', null, array() );

		// Inception
		$context['site'] = $this;

		// Active languages
		if ( function_exists( 'pll_languages_list' ) ) {
			$context['langs'] = pll_languages_list();
		}

		// Theme base URI
		$context['theme_uri'] = THEME_URI;

		// Theme domain
		$context['theme_domain'] = THEME_DOMAIN;

		// Global options
		$context['OPTIONS'] = get_fields( 'options' );

		// Widgets
		$context['widgets']['footer'] = Timber::get_widgets( 'footer-widget-area' );

		// Are we in debut mode?
		$context['WP_DEBUG'] = defined( 'WP_DEBUG' ) && WP_DEBUG;

		return $context;
	}

	function register_menu_locations() {
		register_nav_menus( array(
			'main-nav'        => __( 'Main navigation', THEME_DOMAIN ),
			'language-nav'    => __( 'Language navigation', THEME_DOMAIN ),
			'footer-meta-nav' => __( 'Footer meta navigation', THEME_DOMAIN ),
			'get-active-nav'  => __( 'Action button navigation', THEME_DOMAIN ),
		) );
	}

	function setup_assets() {
		// css
		wp_enqueue_style( THEME_DOMAIN . '-screen',
			get_stylesheet_directory_uri() . '/static/style' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' ) . '.css', false,
			THEME_VERSION );

		// js
		wp_enqueue_script( THEME_DOMAIN . '-app',
			get_stylesheet_directory_uri() . '/static/js/app' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' ) . '.js',
			array(), THEME_VERSION, true
		);

		// tweaks
		if ( ! is_user_logged_in() ) {
			// dont load block styles
			wp_dequeue_style( 'wp-block-library' );
			wp_deregister_style( 'wp-block-library' );
		}

		// admin bar styles
		if ( is_admin_bar_showing() ) {
			wp_enqueue_style( 'adminbar-style', get_stylesheet_directory_uri() . '/style-adminbar.css', false,
				THEME_VERSION );
		}
	}

	function setup_admin_assets() {
		// global admin styles
		if ( is_admin() ) {
			wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri() . '/style-admin.css', false,
				THEME_VERSION );
		}
	}

	/**
	 * Load the widget zones
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_sidebar
	 */
	function register_widget_zones() {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer', THEME_DOMAIN ),
			'id'            => 'footer-widget-area',
			'description'   => __( 'This is the footer. Use it for your primary calls to action (Button widget)'
			                       . ', your contact details (Contact widget) and the footer menu (Link list widget).',
				THEME_DOMAIN ),
			'before_widget' => '<div class="widget">',
			'after_widget'  => '</div>',
		) );
	}

	/**
	 * Load theme translations
	 */
	function load_textdomain() {
		load_theme_textdomain( THEME_DOMAIN, get_template_directory() . '/languages' );
	}

}


/**
 * GO!
 */
new StarterSite();
