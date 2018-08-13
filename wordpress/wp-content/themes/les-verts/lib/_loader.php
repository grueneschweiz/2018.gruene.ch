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

//require_once __DIR__ .'/admin/acf-option-pages.php';
require_once __DIR__ .'/admin/acf-protect-admin.php';
require_once __DIR__ . '/admin/customize-tinymce.php';
require_once __DIR__ .'/admin/admin-hide-pll-sync.php';
require_once __DIR__ .'/admin/lower-yoast-metabox.php';
require_once __DIR__ .'/admin/smush.php';
require_once __DIR__ .'/admin/svg-support.php';
require_once __DIR__ .'/admin/timmy-config.php';
require_once __DIR__ .'/admin/translatable-post-types.php';
require_once __DIR__ . '/admin/tweak-tribe-events.php';

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
	\SUPT\PeopleType::register_post_types(THEME_DOMAIN);
} );


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

require_once __DIR__ . '/customizer/logo.php';

Customizer\Logo::register();


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
require_once __DIR__ .'/twig/functions/component_classes.php';
require_once __DIR__ .'/twig/functions/get_lang.php';
// require_once __DIR__ .'/twig/functions/get_languages_switcher.php';
// require_once __DIR__ .'/twig/functions/pll__.php';

// filters
// require_once __DIR__ .'/twig/filters/newline.php';

/**
 * FORM LIBRARY
 */
if( class_exists( 'acf' ) ) {
	require_once __DIR__ . '/form/_loader.php';
}
