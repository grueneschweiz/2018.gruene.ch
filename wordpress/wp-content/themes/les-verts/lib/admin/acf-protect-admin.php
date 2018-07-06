<?php

/**
 * Show ACF in admin only if the user has the appropriate rights
 * 
 * See https://www.advancedcustomfields.com/resources/how-to-hide-acf-menu-from-clients/
 */
add_filter( 'acf/settings/show_admin', function( $show ) {
	return current_user_can('manage_acf');
} );
