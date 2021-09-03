# SSO Setup

This article shows how single sign on with OpenID Connect and Keycloak can be set up and how it works.

## Preparing Keycloak

Register a new client:

1. `Configure` > `Clients` > `Create`
  1. `Client ID`: We recommend using the domain name
  2. `Client Protocol`: `openid-connect`
  3. `Root URL`: The base URL of your site (WordPress)
2. `Configure` > `Clients` > `{client_id}` > `Settings`
  4. `Access Type`: `confidential`
  5. `Direct Access Grants Enabled`: `off`
  6. The defaults are typically fine for the rest.
3. `Configure` > `Clients` > `{client_id}` > `Credentials`
  1. `Client Authenticator`: `Client Id and Secret`
  2. Copy your `Secret` for later

## Setting up WordPress

*IMPORTANT*: Once you've enabled the OpenID Connect Generic plugin, the login via username and password is blocked. So
make sure you keep an authenticated admin session open while testing your setup. We therefore recommend testing the SSO
authentication in a browser window in private browsing mode.

Configure the OpenID Connect Generic plugin:

1. Install and activate the
   [OpenID Connect Generic](https://wordpress.org/plugins/daggerhart-openid-connect-generic/)
   plugin.
2. Navigate to Settings > OpenID Connect Client and configure the plugin as follows:
  1. `Login Type`: `Auto Login - SSO`
  2. `Client ID`: The client ID you've configured in Keycloak
  3. `Client Secret Key`: The secret you've copied from Keycloak
  4. `OpenID scope`: `email profile openid`
  5. `Login Endpoint URL`: `https://{keycloak-domain-name}/auth/realms/{realm}/protocol/openid-connect/auth` (
     replace `{keycloak-domain-name}` and `{realm}`)
  6. `Userinfo Endpoint URL`: `https://{keycloak-domain-name}/auth/realms/{realm}/protocol/openid-connect/userinfo`
  7. `Token Validation Endpoint URL`: `https://{keycloak-domain-name}/auth/realms/{realm}/protocol/openid-connect/token`
  8. `End Session Endpoint URL`: `https://{keycloak-domain-name}/auth/realms/{realm}/protocol/openid-connect/logout`
  9. `Identity Key`: `preferred_username`
  10. `Nickname Key`: `preferred_username`
  11. `Email Formatting`: `{email}`
  12. `Display Name Formatting`: `{given_name} {family_name}`
  13. `Identify with User Name`: `false`
  14. `Link Existing Users`: `true`
  15. `Create user if does not exist`: `true`

## Notes

This setup assumes that:

- You manage users and their roles entirely in WordPress.
- Every user of WordPress has a corresponding account in Keycloak.
- Only users that have a WordPress account should be able to log in (despite the
  `Create user if does not exist`: `true` setting.)
- The email address of the users in Keycloak and WordPress are (at least initially)
  identical.
- You won't use the default WordPress login anymore (as long as the OpenID Connect Client Generic plugin is activated).

User mapping:

- On their first sso-login the plugin matches the Keycloak user with the WordPress user by the email address. It then
  stores the Keycloak's user id in WordPress so the users email address doesn't matter any longer and can now differ in
  both systems.
- This linking process needs the settings `Link Existing Users`: `true` AND `Create user if does not exist`: `true` to
  work properly.
- There is no role mapping. Keycloak serves solely as identity provider. The users role must be managed in WordPress.

Custom modifications:

- All sso related modifications are located in
  `{theme_directory}/lib/tweaks/openid-connect-generic.php`

- We do three things:
  1. Disable automatic account creation for SSO users
     (`openid-connect-generic-user-creation-test` filter)
  2. Disable WordPress' standard username and password authentication
     (`wp_authenticate_user` filter)
  3. Rename the sso login button (which is only visible after failed tries)
     (`openid-connect-generic-login-button-text` filter)

