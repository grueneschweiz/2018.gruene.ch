<?php

namespace SUPT;

use function add_action;
use function class_exists;
use function defined;
use function is_admin;
use function register_widget;
use function version_compare;

/**
 * ACF
 * ===
 *
 * All filters & actions that alter Advanced Custom Fields
 *
 * Examples:
 * - custom rendering of a specific field
 */

if ( class_exists( 'acf_pro' ) ) {
	require_once __DIR__ . '/acf/cached-oembeds.php';

	if ( is_admin() ) {
		require_once __DIR__ . '/acf/acf-init.php';
		require_once __DIR__ . '/acf/acf-people-title.php';
	}
}


/**
 * SearchWP
 * ========
 *
 * Add repeater and flexible content fields
 */
if ( defined( 'SEARCHWP_VERSION' ) ) {
	if ( version_compare( SEARCHWP_VERSION, '4.0.0', '<' ) ) {
		require_once __DIR__ . '/searchwp/fine-tune-3.php';
	} else {
		require_once __DIR__ . '/searchwp/fine-tune-4.php';
	}
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
	if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
		require_once __DIR__ . '/admin/acf-protect-admin.php';
	}

	require_once __DIR__ . '/admin/acf-featured-image.php';
	require_once __DIR__ . '/admin/customize-tinymce.php';
	require_once __DIR__ . '/admin/admin-hide-pll-sync.php';
	require_once __DIR__ . '/admin/smush.php';
	require_once __DIR__ . '/admin/svg-support.php';
	require_once __DIR__ . '/admin/tweak-tribe-events.php';
	require_once __DIR__ . '/admin/custom-menu-metabox.php';
	require_once __DIR__ . '/admin/leverage-caps.php';

	if ( defined( 'WPSEO_FILE' ) ) {
		require_once __DIR__ . '/admin/tweak-yoast-seo.php';
	}

	require_once __DIR__ . '/admin/suggest-plugins.php';
	require_once __DIR__ . '/admin/check-updates.php';
	require_once __DIR__ . '/admin/import-handler.php';
}

require_once __DIR__ . '/admin/rest-disable-user.php';
require_once __DIR__ . '/admin/update-handler.php';

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

require_once __DIR__ . '/controllers/branding.php';
require_once __DIR__ . '/controllers/navigation.php';
require_once __DIR__ . '/controllers/progress_bar.php';
require_once __DIR__ . '/controllers/frontpage.php';
require_once __DIR__ . '/controllers/landingpage.php';

// register
Branding_controller::register();
Navigation_controller::register();
Progress_controller::register();
Frontpage_controller::register();
Landingpage_controller::register();

/**
 * CUSTOM POST TYPES
 * =================
 *
 * Custom post types and their associated taxonomies
 *
 * Notes:
 * - all of these classes have to extend the class `Model` from ./post-types/Model.php
 */

require_once __DIR__ . '/post-types/PeopleType.php';
require_once __DIR__ . '/post-types/BlockType.php';

// register post types & taxonomies
add_action( 'init', function () {
	PeopleType::register_post_type();
	PeopleType::register_taxonomy();
	BlockType::register_post_type();
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

require_once __DIR__ . '/widgets/Widget.php';
require_once __DIR__ . '/widgets/ContactWidget.php';
require_once __DIR__ . '/widgets/ButtonWidget.php';
require_once __DIR__ . '/widgets/LinkListWidget.php';

// register post types & taxonomies
add_action( 'widgets_init', function () {
	register_widget( ContactWidget::class );
	register_widget( ButtonWidget::class );
	register_widget( LinkListWidget::class );
} );


/**
 * CUSTOMIZER
 * ==========
 *
 * Register theme specific customizer classes
 */

require_once __DIR__ . '/customizer/Logo.php';
require_once __DIR__ . '/customizer/Branding.php';
Customizer\Branding::register();
Customizer\Logo::register();


/**
 * TWEAKS
 * ======
 *
 * Rendering tweaks, hacks,…
 */

require_once __DIR__ . '/tweaks/limit-image-upload-dims.php';
require_once __DIR__ . '/tweaks/inject-svg-sprite.php';
require_once __DIR__ . '/tweaks/responsive-local-video.php';
require_once __DIR__ . '/tweaks/fix-multi-category-urls.php';

if ( defined( 'WPSEO_FILE' ) ) {
	require_once __DIR__ . '/tweaks/tweak-yoast-social-media-stuff.php';
	require_once __DIR__ . '/tweaks/tweak-yoast-breadcrumbs.php';
}

if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
	require_once __DIR__ . '/tweaks/tribe-events.php';
}

if ( defined( 'PROMETHEUS_PLUGIN_FILE' ) ) {
	require_once __DIR__ . '/prometheus/prometheus-metrics-for-wp.php';
}

if ( defined( 'DEMOVOX_VERSION' ) ) {
	require_once __DIR__ . '/demovox/demovox.php';
}

/**
 * TWIG
 * ====
 *
 * Twig extensions, filters and functions
 */

// functions
require_once __DIR__ . '/twig/functions/get_lang.php';
require_once __DIR__ . '/twig/functions/register_timber_custom_post_types.php';
require_once __DIR__ . '/twig/functions/get_people.php';
require_once __DIR__ . '/twig/functions/link_props.php';
require_once __DIR__ . '/twig/functions/image-filters.php';
require_once __DIR__ . '/twig/functions/image-handling.php';

// filters
require_once __DIR__ . '/twig/filters/shortcode.php';
require_once __DIR__ . '/twig/filters/email.php';
require_once __DIR__ . '/twig/filters/phone.php';
require_once __DIR__ . '/twig/filters/social_link.php';
require_once __DIR__ . '/twig/filters/hexencode.php';
require_once __DIR__ . '/twig/filters/nice_link.php';
require_once __DIR__ . '/twig/filters/wptexturize.php';
require_once __DIR__ . '/twig/filters/uniqueid.php';
require_once __DIR__ . '/twig/filters/pll.php';
require_once __DIR__ . '/twig/filters/l10n_date.php';
require_once __DIR__ . '/twig/filters/esc_form_value.php';
require_once __DIR__ . '/twig/filters/license.php';
require_once __DIR__ . '/twig/filters/disableable_autop.php';

/**
 * FORM LIBRARY
 * ============
 */
if ( class_exists( 'acf_pro' ) ) {
	require_once __DIR__ . '/form/_loader.php';
}

/**
 * SHORTCODES
 * ==========
 */
require_once __DIR__ . '/shortcodes/progress.php';
Progress_shortcode::register();


/**
 * MIGRATIONS
 * ==========
 */
require_once __DIR__ . '/migrations/Migrator.php';
$dbVersion = Migrations\Migrator::getDbVersion();
if ( 1 === version_compare( '0.26.2', $dbVersion ) ) {
	require_once __DIR__ . '/migrations/get-active-button.php';
}
if ( 1 === version_compare( '0.32.0', $dbVersion ) ) {
	require_once __DIR__ . '/migrations/event-content.php';
}
if ( 1 === version_compare( THEME_VERSION, $dbVersion ) ) {
	Migrations\Migrator::setCurrentVersion();
}


/**
 * CLI Commands
 * ============
 */
require_once __DIR__ . '/cli/LesVertsCommand.php';
