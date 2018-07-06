<?php

/**
 * Set ACF settings on init
 * 
 * Read more: https://www.advancedcustomfields.com/resources/acf-settings/
 */
add_action( 'acf/init', function() {
	acf_update_setting('l10n_textdomain', THEME_DOMAIN);
} );
