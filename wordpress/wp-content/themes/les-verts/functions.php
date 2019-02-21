<?php

// Composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Constants
$theme = wp_get_theme();
define( 'THEME_VERSION', $theme['Version'] );
define( 'THEME_DOMAIN', 'lesverts' );
define( 'THEME_URI', get_template_directory_uri() );

// Stop if Timber not defined
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	} );
	
	return;
}

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
		\Locale::setDefault( \SUPT\get_lang() );
		
		// Theme supports
		// -> more info: https://developer.wordpress.org/reference/functions/add_theme_support/
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'yoast-seo-breadcrumbs' );
		
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
		
		// Localized options
		//$context['localized_options'] = get_fields( \SUPT\get_lang() );
		
		// Options overrides
//		if (
//			isset( $context['localized_options'] )
//			&& isset( $context['localized_options']['gtm_override'] )
//			&& $context['localized_options']['gtm_override']
//		) {
//			foreach ( [ 'gtm_enable', 'gtm_id' ] as $key ) {
//				$val                        = isset( $context['localized_options'][ $key ] ) ? $context['localized_options'][ $key ] : null;
//				$context['OPTIONS'][ $key ] = $val;
//			}
//		}
		
		// Are we in debut mode?
		$context['WP_DEBUG'] = WP_DEBUG;
		
		return $context;
	}
	
	function register_menu_locations() {
		register_nav_menus( array(
			'main-nav'        => __( 'Main navigation', THEME_DOMAIN ),
			'language-nav'    => __( 'Language navigation', THEME_DOMAIN ),
			'footer-meta-nav' => __( 'Footer meta navigation', THEME_DOMAIN ),
		) );
	}
	
	function setup_assets() {
		// css
		wp_enqueue_style( THEME_DOMAIN . '-screen',
			get_stylesheet_directory_uri() . '/static/style' . ( WP_DEBUG ? '' : '.min' ) . '.css', false,
			THEME_VERSION );
		
		// js
		$polyfills = 'HTMLPictureElement,CustomEvent,Element.prototype.closest,Object.assign,NodeList.prototype.forEach,Promise,matchMedia,Object.entries,URL';
		wp_enqueue_script( THEME_DOMAIN . '-polyfills',
			'https://cdn.polyfill.io/v2/polyfill' . ( WP_DEBUG ? '' : '.min' ) . '.js?features=' . $polyfills,
			false, null, true
		);
		wp_enqueue_script( THEME_DOMAIN . '-app',
			get_stylesheet_directory_uri() . '/static/js/app' . ( WP_DEBUG ? '' : '.min' ) . '.js',
			array( THEME_DOMAIN . '-polyfills' ), THEME_VERSION, true
		);
		
		// tweaks
		if ( ! is_admin() ) {
			// don't load jquery
			wp_deregister_script( 'jquery' );
			
			// dont load block styles
			wp_dequeue_style( 'wp-block-library' );
			wp_deregister_style( 'wp-block-library' );
			
			// live reload of css for development
//			if ( WP_DEBUG ) {
//				// TODO find a way to load dynymically the BS file (cause the port number could be somthing else)
//				if ( is_multisite() ) { // Removing trailing slash to attach browser-sync
//					wp_enqueue_script( 'bs',
//						substr( network_site_url(), 0, - 1 ) . ':4000/browser-sync/browser-sync-client.js', false, null,
//						true );
//				} else {
//					wp_enqueue_script( 'bs', get_site_url() . ':4000/browser-sync/browser-sync-client.js', false, null,
//						true );
//				}
//			}
		}
		
		// admin bar styles
		if ( is_admin_bar_showing() ) {
			wp_enqueue_style( 'adminbar-style', get_stylesheet_directory_uri() . '/style-adminbar.css', false,
				THEME_VERSION );
		}
		
		// load scripts on specific pages
		// if( is_page() ){
		// 	global $wp_query;
		
		// 	if( in_array( get_page_template_slug($wp_query->post->ID), array( 'page-contact.php' ) )) {
		// 		$gmap_key = get_field( 'maps_api_key', 'option' );
		// 		if ( !empty($gmap_key) ) {
		// 			wp_enqueue_script( 'gmap', "https://maps.googleapis.com/maps/api/js?key=$gmap_key", null, null, true );
		// 		}
		// 	}
		// }
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
