<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * Mailchimp-Service configuration (optional)
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wordpress' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wordpress' );

/** MySQL hostname */
define( 'DB_HOST', 'db:3306' );

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'l2Z4}zzvs9@aO!-`i!).e$9-?<qDh~v}-WXRn!I8xGsQL0*qvxNq*ss4Lu1XScL?');
define('SECURE_AUTH_KEY',  'fMCp<k360hnI-78Q:KeP;XwC@^J-ru}{& /1U);p7j1Kg9097H7P<aVp%84eeEnX');
define('LOGGED_IN_KEY',    '<h=<Q04yR2%3zKA/Qc09xf0ouWHCA#:v<ZzV`CQl#tl:<c#~VuD0|=ZikiL7W)%B');
define('NONCE_KEY',        '+3J2L(z)L8&lo:BpC/+U>]S*PxdECrnLLpK3CoH[;Yhby}<~$TZ,?,$~|xGodk5e');
define('AUTH_SALT',        'JY4_YnB$;<yi2>z$-i_,=6)?(df9#@qX:S7czi`ax@^(ene1*)V@%mm1&Uzt-%ON');
define('SECURE_AUTH_SALT', 'f/`UcL#NZWWA9t8=.F<HPxB%-M1_xG+BRxuL2|!C+`n]uGF@Si!qc-WTZ$-C7dLD');
define('LOGGED_IN_SALT',   '<nUlAlK9VWG?-<I#V6{i<+Z;`*o/yzG|j?$QD_E|}Lu9KaQ5]%Um@;oDVBuPU+c;');
define('NONCE_SALT',       'Ho0LV]lXE<[r8T4A)tp#,|3TV!HzO>=Y%Vn49o)h@ @vN}oBQEG9JW-]pCtOP[+:');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', getenv( 'WORDPRESS_DEBUG' ) );
define( 'WP_DEBUG_LOG', getenv( 'WORDPRESS_DEBUG' ) );

// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

define( 'WP_CACHE', true );

define( 'MULTISITE', false );
define( 'SUBDOMAIN_INSTALL', false ); // Set this to true for sub-domain installations.
define( 'DOMAIN_CURRENT_SITE', 'localhost' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'SUPT_FORM_ASYNC', ! ( (bool) getenv( 'WORDPRESS_DEBUG' ) ) );

// Mailchimp-Service configuration (leave empty to disable integration)
define( 'MAILCHIMP_SERVICE_ENDPOINT', getenv('MAILCHIMP_SERVICE_ENDPOINT') ?: '' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
