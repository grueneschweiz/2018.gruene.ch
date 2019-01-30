<?php

namespace SUPT;

/**
 * ACF
 * ===
 *
 * All filters & actions that alter Advanced Custom Fields
 *
 * Examples:
 * - custom rendering of a specific field
 */

if( class_exists( 'acf' ) ) {
	require_once __DIR__ .'/acf/acf-init.php';
	//require_once __DIR__ .'/acf/acf-content-defaults.php';

	// custom locations (= where to show which field groups)
	// require_once __DIR__ .'/acf/custom-locations/localized-options.php';
	// require_once __DIR__ .'/acf/custom-locations/localized-menu.php';
}


/**
 * ADMIN
 * =====
 *
 * Adding or altering features in the admin
 *
 * Examples:
 * - hide a feature we don't use
 * - rename a default menu label
 * - customize the wysiwyg editor
 * - add a "help" section
 */

if ( is_admin() ) {
	require_once __DIR__ . '/admin/acf-protect-admin.php';
	require_once __DIR__ . '/admin/acf-featured-image.php';
	require_once __DIR__ . '/admin/customize-tinymce.php';
	require_once __DIR__ . '/admin/admin-hide-pll-sync.php';
	require_once __DIR__ . '/admin/smush.php';
	require_once __DIR__ . '/admin/svg-support.php';
	require_once __DIR__ . '/admin/tweak-tribe-events.php';
	require_once __DIR__ . '/admin/allow-code.php';
}

require_once __DIR__ . '/admin/timmy-config.php';
require_once __DIR__ . '/admin/custom-menu-metabox.php';
//require_once __DIR__ . '/admin/translatable-post-types.php';
//require_once __DIR__ .'/admin/acf-option-pages.php';

if ( defined( 'WPSEO_FILE' ) ) {
	require_once __DIR__ . '/admin/tweak-yoast-seo.php';
}

/**
 * CONTROLLERS
 * ===========
 *
 * For complex/global components, we need a controller to fetch/process the data
 * and inject it in the Timber context. Think of them as a kind of view-model.
 *
 * Examples:
 * - prepare data for the header banner, based on the page/post informations
 * - prepare data for a global alert, that is configured on a site scope
 *   and should show only if certain cookie/session conditions are met
 */

require_once __DIR__ .'/controllers/branding.php';
require_once __DIR__ .'/controllers/navigation.php';

// register
Branding_controller::register();
Navigation_controller::register();


/**
 * CUSTOM POST TYPES
 * =================
 *
 * Custom post types and their associated taxonomies
 *
 * Notes:
 * - all of these classes have to extend the class `Model` from ./post-types/Model.php
 */

require_once __DIR__ .'/post-types/PeopleType.php';

// register post types & taxonomies
add_action( 'init', function() {
	\SUPT\PeopleType::register_post_types();
	\SUPT\PeopleType::register_taxonomy();
} );


/**
 * CUSTOM TIMBER CLASSES
 * =====================
 *
 * Include your timber classes here.
 *
 * @see https://timber.github.io/docs/guides/extending-timber/
 */

require_once __DIR__ . '/timber/ACFPost.php';
require_once __DIR__ . '/timber/SUPTPostQuery.php';
require_once __DIR__ . '/timber/SUPTTribeEvent.php';
require_once __DIR__ . '/timber/SUPTPerson.php';
require_once __DIR__ . '/timber/EFPConfiguration.php';

/**
 * WIDGETS
 * =======
 *
 * Custom widgets
 */

require_once __DIR__ .'/widgets/Widget.php';
require_once __DIR__ .'/widgets/ContactWidget.php';
require_once __DIR__ .'/widgets/ButtonWidget.php';
require_once __DIR__ .'/widgets/LinkListWidget.php';

// register post types & taxonomies
add_action( 'widgets_init', function() {
	register_widget('\SUPT\ContactWidget');
	register_widget('\SUPT\ButtonWidget');
	register_widget('\SUPT\LinkListWidget');
} );


/**
 * CUSTOMIZER
 * ==========
 *
 * Register theme specific customizer classes
 */

require_once __DIR__ . '/customizer/Logo.php';
require_once __DIR__ . '/customizer/GetActive.php';

Customizer\Logo::register();
Customizer\GetActive::register();


/**
 * TWEAKS
 * ======
 *
 * Rendering tweaks, hacks,…
 */

require_once __DIR__ .'/tweaks/inject-svg-sprite.php';


/**
 * TWIG
 * ====
 *
 * Twig extensions, filters and functions
 */

// functions
require_once __DIR__ .'/twig/functions/get_lang.php';
require_once __DIR__.'/twig/functions/register_timber_custom_post_types.php';
require_once __DIR__.'/twig/functions/get_people.php';

// filters
require_once __DIR__ .'/twig/filters/email.php';
require_once __DIR__ .'/twig/filters/phone.php';
require_once __DIR__ .'/twig/filters/social_link.php';
require_once __DIR__ .'/twig/filters/hexencode.php';
require_once __DIR__ .'/twig/filters/nice_link.php';
require_once __DIR__ .'/twig/filters/wptexturize.php';

/**
 * FORM LIBRARY
 * ============
 */
if( class_exists( 'acf' ) ) {
	require_once __DIR__ . '/form/_loader.php';
}
