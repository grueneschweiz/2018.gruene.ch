<?php

/**
 * We cannot bundle ACF Pro in a free theme, so we just add a fat notice!
 */
if ( ! class_exists( 'acf_pro' ) ) {
	add_action( 'admin_notices', function () {
		$class   = 'notice notice-error';
		$message = sprintf(
			__( "This theme heavily depends on %sAdvanced Custom Fields Pro%s. You cannot run it without this plugin. Install it now (we haven't bundled it, because it's a premium plugin).", THEME_DOMAIN ),
			'<a href="https://www.advancedcustomfields.com/pro/" target="_blank">',
			'</a>'
		);

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	} );
}

/**
 * We cannot bundle SearchWP in a free theme, so we just add a notice!
 */
if ( ! defined( 'SEARCHWP_VERSION' ) ) {
	add_action( 'admin_notices', function () {
		$class   = 'notice notice-info';
		$message = sprintf(
			__( "The WordPress' search is very limited and searches titles only. We therefore suggest installing the %sSearchWP%s.", THEME_DOMAIN ),
			'<a href="https://searchwp.com/" target="_blank">',
			'</a>'
		);

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	} );
}

/**
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @version    2.6.1 for parent theme Les Verts
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

add_action( 'tgmpa_register', 'supt_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function supt_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => 'SVG Support',
			'slug'     => 'svg-support',
			'required' => true,
		),
		array(
			'name'        => 'Yoast SEO',
			'slug'        => 'wordpress-seo',
			'required'    => false,
			'is_callable' => 'wpseo_init',
		),
		array(
			'name'     => 'ACF Content Analysis for Yoast SEO',
			'slug'     => 'acf-content-analysis-for-yoast-seo',
			'required' => false,
		),
		array(
			'name'     => 'Disable Comments',
			'slug'     => 'disable-comments',
			'required' => false,
		),
		array(
			'name'     => 'The Events Calendar',
			'slug'     => 'the-events-calendar',
			'required' => false,
		),
		array(
			'name'     => 'Classic Editor',
			'slug'     => 'classic-editor',
			'required' => true,
		),
		array(
			'name'     => 'ACF Code Field',
			'slug'     => 'acf-code-field',
			'required' => false,
		),
		array(
			'name'     => 'Classic Widgets',
			'slug'     => 'classic-widgets',
			'required' => true,
		)
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'lesverts',
		// Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',
		// Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',
		// Menu slug.
		'parent_slug'  => 'themes.php',
		// Parent menu slug.
		'capability'   => 'edit_theme_options',
		// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,
		// Show admin notices or not.
		'dismissable'  => true,
		// If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',
		// If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,
		// Automatically activate plugins after installation or not.
		'message'      => '',
		// Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
