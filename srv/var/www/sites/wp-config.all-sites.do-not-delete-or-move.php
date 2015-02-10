<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
if ( !defined( 'DATACENTER' ) ) {
	define( 'DATACENTER', 'wp-dev01' );
}

// work around for wp cli script document root with symlink
if ( defined('WP_CLI') ) {
	$options = getopt( '', array('path::') );
	if ( !empty( $options['path'] ) ) {
		$path = $options['path'];
	} else {
		$path = $_SERVER['PWD'];
	}
	if ( !empty( $path ) && strpos( $path, '/wordpress' ) !== false ) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '/wordpress','', $path );
	}
}

if ( file_exists( __DIR__ . "/wp-config.php-{$_SERVER['HTTP_HOST']}" ) ) {
	require ( __DIR__ . "/wp-config.php-{$_SERVER['HTTP_HOST']}" );
}

if ( preg_match( "/^(.*?\.qa)./i", $_SERVER['HTTP_HOST'], $matches ) ) {
	if ( file_exists( __DIR__ . "/wp-config.php-{$matches[1]}" ) ) {
		require ( __DIR__ . "/wp-config.php-{$matches[1]}" );
	}
}

if ( ! defined ( 'TARGETING_AD_HOST' ) ) {
	define( 'TARGETING_AD_HOST', preg_replace ('/[^\.]+\.(qa.|adops.)/','$1',$_SERVER['HTTP_HOST']) );
}


if ( ! defined( 'WP_SITEURL' ) ) {
	// Backwards compatibility for old qa environments, can be removed after Aug 1 2013
	if ( ! file_exists( $_SERVER['DOCUMENT_ROOT'] . '/wordpress' ) ) {
		$wp_siteurl = $_SERVER['HTTP_HOST'];
	} else {
		$wp_siteurl = $_SERVER['HTTP_HOST'] . '/wordpress';
	}
	define( 'WP_SITEURL', 'http://' . $wp_siteurl );
}

if ( ! defined( 'WP_HOME' ) ) {
	define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/wp-content' );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', WP_HOME . '/wp-content' );
}

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', '/var/www/sites/wordpress-plugins' );
}

if ( ! defined( 'WP_PLUGIN_URL' ) ) {
	define( 'WP_PLUGIN_URL', WP_HOME . '/wp-content/plugins' );
}

if ( ! defined( 'WPMU_PLUGIN_URL' ) ) {
	define( 'WPMU_PLUGIN_URL', '/var/www/sites/wordpress-mu-plugins' );
}

if ( ! defined( 'WPMU_PLUGIN_URL' ) ) {
	define( 'WPMU_PLUGIN_URL', WP_HOME . '/wp-content/mu-plugins' );
}

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
	define( 'JETPACK_DEV_DEBUG', true );
}
if ( ! WP_DEBUG ) {
    ini_set( 'display_errors', 0 );
    error_reporting( E_ERROR | E_PARSE );
}
if ( ! defined( 'SCRIPT_DEBUG' ) && WP_DEBUG ) {
	define( 'SCRIPT_DEBUG', true );
}
if ( ! defined( 'SAVEQUERIES' ) && WP_DEBUG ) {
	define( 'SAVEQUERIES', true );
}
if ( ! defined( 'DISABLE_WP_CRON' ) ) {
	define( 'DISABLE_WP_CRON', true );
}
define( 'AUTOMATIC_UPDATER_DISABLED', true );
if ( isset($_SERVER['DB_NAME']) && ! defined( 'DB_NAME' ) ) {
	define( 'DB_NAME', $_SERVER['DB_NAME'] );
}
elseif ( ! defined( 'DB_NAME' ) ) {
	$db_name = preg_replace( "/^.*[\-\.]qa./i","qa_", $_SERVER['HTTP_HOST'] );
	$db_name = str_replace( '.', '_', $db_name );
	define( 'DB_NAME', $db_name );
}

if ( isset($_SERVER['DB_USER']) && ! defined( 'DB_USER' ) ) {
	define( 'DB_USER', $_SERVER['DB_USER'] );
}
elseif ( ! defined( 'DB_USER' ) ) {
	define( 'DB_USER', 'wp-dev' );
}

if ( isset($_SERVER['DB_PASSWORD']) && ! defined( 'DB_PASSWORD' ) ) {
	define( 'DB_PASSWORD', $_SERVER['DB_PASSWORD']);
}
elseif ( ! defined( 'DB_PASSWORD' ) ) {
	define( 'DB_PASSWORD', 'W0BwMmg14HEU96U' );
}

if ( isset($_SERVER['DB_HOST']) && ! defined( 'DB_HOST' ) ) {
	define( 'DB_HOST', $_SERVER['DB_HOST'] );
}
elseif ( ! defined( 'DB_HOST' ) ) {
	define( 'DB_HOST', 'localhost' );
}

unset( $_SERVER['DB_NAME'], $_SERVER['DB_USER'],  $_SERVER['DB_PASSWORD'], $_SERVER['DB_HOST'] );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Xw=]?0el21B.|hgy#LB].OglMV:|vz2:kl:_#T$bYGt|L&e vr-y2N((p36:u[m{' );
define( 'SECURE_AUTH_KEY',  'K`:<[]6/YAE/ $-6r%H|uo!?[(O51[<{5VH}mRH@i9%6b<QPS;B0tc!i}/ZQ?2^E' );
define( 'LOGGED_IN_KEY',    'UO/X-ikLr-F1lX`20Oix$dYh$+Gg}k@DQ(r^G|9IYU@@>L99yMb>pL,+)_y,*0|I' );
define( 'NONCE_KEY',        '5@gL4.tk4WQ4v_P2&=7u^hf!2)AnMut4&K.uU|_2e1WdxxsW+90h/)qSb32b48G~' );
define( 'AUTH_SALT',        'QpNAnV_z1>`8Jn@|Q0kW-wUN2r4u>$L|hh{0Ol+@sbVPRI&W7A|%Z<gJT,Piy?k*' );
define( 'SECURE_AUTH_SALT', 'N@ 0b#a=xA2JI[S-F7bINSQB <z@Zhps-1?}L_tvWid:]&`d{n*S]4$s9AfxrN~?' );
define( 'LOGGED_IN_SALT',   '1AvhS?,vN3xmU!T(Ct/ftpOTXexIJ_5pHE1$Sr7BKL@7EHfBv9rD9-UPmq8GK|&B' );
define( 'NONCE_SALT',       '0t3p!yhC(RY^<6-h7{E+S-K!c0Z3-_@^/V.!|;3)~L+UKH!-UHopsi JR^b6o7|( ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */

// define ( 'WPLANG', '' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname(__FILE__) . '/' );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php' );

// show move this code to mu-plugin
if ( ! defined( 'WP_CLI' ) ) {
	add_filter( 'plugins_url', function( $url, $path, $plugin) {
		$url = str_replace( WP_PLUGIN_DIR, '', $url );
		return $url;
	} , 10, 3 );
}



