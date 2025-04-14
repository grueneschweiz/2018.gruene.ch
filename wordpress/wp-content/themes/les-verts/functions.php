<?php

// Composer dependencies
use Timber\Timber;
use Timber\Site;
use function SUPT\get_lang;

require_once __DIR__ . '/vendor/autoload.php';

// Constants
$theme = wp_get_theme();
define( 'THEME_VERSION', $theme['Version'] );
define( 'THEME_DOMAIN', 'lesverts' );
define( 'THEME_URI', get_template_directory_uri() );

// used by the uniqueid twig filter. keep this in global scope.
$supt_unique_id_counter = 0;

// Initialize Timber and theme on init hook
add_action('init', function() {
    // Load translations
    load_theme_textdomain( THEME_DOMAIN, get_template_directory() . '/languages' );

    // Initialize Timber
    Timber::init();

    // Configure Timber
    Timber::$dirname = ['templates', 'views'];

    // Set up class mapping for posts
    add_filter('timber/post/classmap', function($classmap) {
        // Map all post types to ACFPost by default
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            $classmap[$post_type] = \SUPT\ACFPost::class;
        }
        
        // Override specific post types
        $classmap['tribe_events'] = \SUPT\SUPTTribeEvent::class;
        $classmap['person'] = \SUPT\SUPTPerson::class;
        $classmap['efp_configuration'] = \SUPT\EFPConfiguration::class;
        
        return $classmap;
    });

    // This is where the magic happens
    require_once __DIR__ . '/lib/_loader.php';

    // Initialize the theme
    new StarterSite();
});

/**
 * ENTRYPOINT
 */
class StarterSite extends Site {
    public function __construct() {
        parent::__construct();

        // Theme supports
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_theme_support( 'yoast-seo-breadcrumbs' );
        add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

        // Filters
        add_filter( 'timber/context', array( $this, 'add_to_context' ) );
        add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
        add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );
        add_filter( 'searchwp/email_summaries/disabled', '__return_true');

        // Actions
        add_action( 'wp_enqueue_scripts', array( $this, 'setup_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_assets' ) );
        add_action( 'widgets_init', array( $this, 'register_widget_zones' ) );
        add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
        add_action( 'after_setup_theme', array( $this, 'register_menu_locations' ) );
    }

    public function add_image_sizes() {
        // Add custom image sizes here
        add_image_size('custom-6', 480, 206, true);
        add_image_size('custom-6-2x', 960, 412, true);
        add_image_size('custom-12', 768, 329, true);
        add_image_size('custom-12-2x', 1536, 658, true);
        add_image_size('custom-full', 1400, 600, true);
        add_image_size('custom-full-2x', 2800, 1200, true);
    }

    public function add_to_context( $context ) {
        // Some plugins rely on this action
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

    public function add_to_twig( $twig ) {
        // Add custom twig functions here
        return $twig;
    }

    public function update_twig_environment_options( $options ) {
        // Update twig environment options here
        return $options;
    }

    public function register_menu_locations() {
        register_nav_menus( array(
            'main-nav'        => __( 'Main navigation', THEME_DOMAIN ),
            'language-nav'    => __( 'Language navigation', THEME_DOMAIN ),
            'footer-meta-nav' => __( 'Footer meta navigation', THEME_DOMAIN ),
            'get-active-nav'  => __( 'Action button navigation', THEME_DOMAIN ),
        ) );
    }

    public function setup_assets() {
        // css
        wp_enqueue_style( THEME_DOMAIN . '-screen',
            get_stylesheet_directory_uri() . '/static/style' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' ) . '.css', 
            false,
            THEME_VERSION 
        );

        // js
        wp_enqueue_script( THEME_DOMAIN . '-app',
            get_stylesheet_directory_uri() . '/static/js/app' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' ) . '.js',
            array(), 
            THEME_VERSION, 
            true
        );

        // tweaks
        if ( ! is_user_logged_in() ) {
            // dont load block styles
            wp_dequeue_style( 'wp-block-library' );
            wp_deregister_style( 'wp-block-library' );
        }

        // admin bar styles
        if ( is_admin_bar_showing() ) {
            wp_enqueue_style( 'adminbar-style', 
                get_stylesheet_directory_uri() . '/style-adminbar.css', 
                false,
                THEME_VERSION 
            );
        }
    }

    public function setup_admin_assets() {
        // global admin styles
        if ( is_admin() ) {
            wp_enqueue_style( 'admin-style', 
                get_stylesheet_directory_uri() . '/style-admin.css', 
                false,
                THEME_VERSION 
            );
        }
    }

    public function register_widget_zones() {
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
}
