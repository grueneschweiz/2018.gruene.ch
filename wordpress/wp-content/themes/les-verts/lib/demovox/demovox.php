<?php declare( strict_types=1 );

$lesverts_demovox_shortcode = false;

/**
 * Set global $lesverts_demovox_shortcode to true if the current page contains a demovox shortcode
 */
add_filter( 'do_shortcode_tag', function ( $output, $tag ) {
	if ( 0 === strpos( $tag, 'demovox_' ) ) {
		global $lesverts_demovox_shortcode;
		$lesverts_demovox_shortcode = true;
	}

	return $output;
}, 10, 2 );

/**
 * Disable wpautop for demovox shortcode content
 */
add_filter( 'do_shortcode_tag', function ( $output, $tag ) {
	if ( 0 !== strpos( $tag, 'demovox_' ) ) {
		return $output;
	}

	return '<!-- lesverts-noautop -->'
	       . $output
	       . '<!-- lesverts-end-noautop -->';
}, 10, 2 );

/**
 * Disable cache for demovox pages
 */
add_filter( 'do_shortcode_tag', function ( $output, $tag ) {
	if ( 0 !== strpos( $tag, 'demovox_' ) ) {
		return $output;
	}

	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	return $output;
}, 10, 2 );

/**
 * Manually enqueue the demovox assets if the current page contains a demovox shortcode
 *
 * We have to enqueue the assets manually because our theme lazily evaluates shortcodes
 * and therefore processes them too late for the plugins built-in asset loading mechanism
 * to fire.
 */
add_action( 'wp_footer', function () {
	global $lesverts_demovox_shortcode;

	if ( ! $lesverts_demovox_shortcode ) {
		return;
	}

	if ( defined( 'DEMOVOX_VERSION' )
	     && class_exists( '\Demovox\PublicHandler' )
	) {
		/** @noinspection PhpFullyQualifiedNameUsageInspection */
		$handler = new \Demovox\PublicHandler( 'Demovox', DEMOVOX_VERSION );
		if ( method_exists( $handler, 'enqueueAssets' ) ) {
			$handler->enqueueAssets();

			return;
		}
	}

	trigger_error( 'Failed to enqueue demovox assets' );
} );

/**
 * Register custom demovox styles and scripts if needed
 */
add_action( 'wp_footer', function () {
	global $lesverts_demovox_shortcode;

	if ( ! $lesverts_demovox_shortcode ) {
		return;
	}

	// css
	wp_enqueue_style(
		THEME_DOMAIN . '-demovox',
		get_parent_theme_file_uri( 'lib/demovox/lesverts-demovox.css' ),
		false,
		THEME_VERSION
	);
} );
