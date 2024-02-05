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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'zahir_blog' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '}{(#rb0qTR,TA_q0{|fQBXi8OD `c`^G,Ob)kSW&5@ bfMuP~A#-IrvCJ<McVc&,' );
define( 'SECURE_AUTH_KEY',  '<K$cB;xpM3lt{@qK[_.M|b-d?U1_#[Pt{fNIT[KS`WF-%&u5n:<AX3SX+9Cg`WV6' );
define( 'LOGGED_IN_KEY',    '#}J<>zH)q:8J+`706U1+nbmRN;QX^k5DY[#6=v+U._{#>UE{D#Lymd@ZVFhYk?-<' );
define( 'NONCE_KEY',        '/zM~R-NQzs^MDIg0~$LtbX#ctlyHQBxIMc{-*r]ARjX`]vhj5*~z+]L&W:Y;_XM]' );
define( 'AUTH_SALT',        ':-yCCm?gjKF~az*e+&b:G^25c0#]];F/Y~qLCAN/vK~NtMJ0>7#Gvh<~l8jb{ETA' );
define( 'SECURE_AUTH_SALT', 'g*eHeW5ZWw+z0w~{}1r5y#v(u:!r_j,d++~Wc^znH_3gbOV_|3r7HHb)TFj__%#;' );
define( 'LOGGED_IN_SALT',   ':nmRpAaoOSp_8jhB(]3E;{[F<7r-%mB&Vho$4s?x^+J)lg}}PIA}Cv7*Fx&&aG8y' );
define( 'NONCE_SALT',       '}Qgy7];G8C@W[pa6[t^6(xV@}-GB2YFGt r-J[f1LVVkblu:mk[r~g>Cj{(8Mp3G' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
