<?php

namespace SUPT;

use function add_action;
use function wp_json_encode;

/**
 * Tracking Scripts Loader
 * 
 * Outputs tracking scripts configuration to the footer.
 * Scripts are only loaded if user has given consent via the cookie banner.
 */

/**
 * Output tracking scripts configuration
 */
add_action( 'wp_footer', function() {
	// Get active scripts for current page
	$active_scripts = Cookie_Banner_controller::get_active_scripts();
	
	// Only output if there are active scripts
	if ( empty( $active_scripts ) ) {
		return;
	}

	// Prepare scripts data for JavaScript
	$scripts_data = [];
	
	foreach ( $active_scripts as $key => $script ) {
		if ( $script['type'] === 'inline' && $key === 'facebook_pixel' ) {
			// Facebook Pixel
			$scripts_data['facebookPixel'] = [
				'type' => 'inline',
				'id' => $script['id']
			];
		} elseif ( $script['type'] === 'external' ) {
			// Custom external script
			$scripts_data[ $key ] = [
				'type' => 'external',
				'url' => $script['url'],
				'name' => $script['name'] ?? $key
			];
		}
	}

	// Output scripts configuration as JSON
	if ( ! empty( $scripts_data ) ) {
		?>
		<script>
		window.trackingScripts = <?php echo wp_json_encode( $scripts_data ); ?>;
		</script>
		<?php
	}
}, 99 );
