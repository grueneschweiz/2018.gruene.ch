<?php

/**
 * Include the SVG icon sprite in the footer
 */
add_action( 'wp_footer', function() {
	echo '<div style="display: none">'. file_get_contents( __DIR__ .'/../../static/img/icons.svg' ) .'</div>';
} );
