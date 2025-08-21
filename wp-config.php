<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'pb]b!E{KzE!d=0fe%hUi-kTLDD;.&>GA >WS:$Mg^;B~r`H# ;Tx#Z>:^we%|v}^' );
define( 'SECURE_AUTH_KEY',   '%rQ9EI^@$0p8q`@!tm2Iw`4Q<a_QmuayH`DEa{G@d>>gVc%@|}t}Oy](|mu[z1lV' );
define( 'LOGGED_IN_KEY',     '-3+!uAUgx#xWkdZGK**:J[@%(bgfAKMl3q||KPSHYL:OFX?L77sG461zO4m)?>gu' );
define( 'NONCE_KEY',         'KrZ8N^?ppwt|Lm4gQ)u+6hiE`*!,E#YcIK-bgc)tUyA4,H0V7whhGP@$ G<pP&}y' );
define( 'AUTH_SALT',         'otxa`w%Bcz:qkAYRs0Rvm.Y=hr0_Ml3%/G(1]2:m.i[?wOvoOnNhe_}T*&[63fz5' );
define( 'SECURE_AUTH_SALT',  '&{$F.x,M95Uh/[4&tH)xzJJ2[`*XzlcTV-f-^2f~|<rH82,#p`@YqX;ltSR%Z=xZ' );
define( 'LOGGED_IN_SALT',    ')rk/>ckBXE{2Es>@!<2$9b?T4!mU9a*$gnvA*q%rEL1zlPMd<6>X,W0_+RQ1?dn>' );
define( 'NONCE_SALT',        ' pa2Ow@|nb>&C.SeEND}I>5q%!Q4eQfV;~n;5 _;j~?F;@gccUFUkY.5GA;e97KE' );
define( 'WP_CACHE_KEY_SALT', '!_&YE|/{6LY86rJ`)Y[/Pnty:Q+UW2dfh#u%uT77MJa`->K&%a h}6KY3jU=6uGp' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

// Increase upload limits
@ini_set('upload_max_filesize', '500M');
@ini_set('post_max_size', '1000M');
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', '1800');
@ini_set('max_input_time', '1800');

/* WordPress Memory Limits */
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'WP_DEBUG_DISPLAY', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
