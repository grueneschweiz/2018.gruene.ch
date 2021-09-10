<?php

/**
 * In order to link existing wordpress accounts automatically with
 * their counterparts in keycloak, we have to enable the oidc settings
 * 'link existing users' and 'create user if does not exist' in wordpress.
 * Returning false here, prevents the creation of new user accounts in
 * wordpress, if the user does not exist. Opposed to the name of the
 * setting, we must however enable 'create user if does not exist' to
 * LINK the accounts ('new' means non oidc users in this case).
 *
 * WARNING: Removing this filter or returning true allows any keycloak
 * user to login to wordpress, regardless if he had an account in
 * wordpress or not!
 */
add_filter( 'openid-connect-generic-user-creation-test', '__return_false' );


/**
 * Disable authentication via username and password
 */
add_filter( 'wp_authenticate_user', function ( $user ) {
	return new WP_Error( 'user_not_verified', __( 'Default login disabled.' ) );
} );


/**
 * Set OIDC login button text
 */
add_filter( 'openid-connect-generic-login-button-text', function () {
	return __( 'Connect with GREEN-Login', THEME_DOMAIN );
} );
