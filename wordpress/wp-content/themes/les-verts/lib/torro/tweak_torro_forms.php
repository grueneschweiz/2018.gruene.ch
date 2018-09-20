<?php
/**
 * To tweak the torro forms markup, change the files in the
 * wp-content/themes/themefolder/torro_templates folder.
 */

add_filter('torro_required_indicator', function() {
	return '<span class="screen-reader-text">' . _x( '(required)', 'field required indicator', THEME_DOMAIN ) . '</span>';
});

add_filter('torro_form_button_class', function() {
	return 'a-button a-button--secondary';
});

add_filter('torro_form_button_primary_class', function() {
	return 'a-button--secondary';
});


