<?php
if(defined('DISABLE_DEBUG_DISPLAY') && DISABLE_DEBUG_DISPLAY ){
  ini_set('error_reporting',0);
}elseif (WP_DEBUG && WP_DEBUG_DISPLAY) 
{
   ini_set('error_reporting', E_ALL );
}
$GLOBALS['wp_plugin_paths'] = array();
$GLOBALS['pmc_required_plugins'] = array(
	'debug-bar-console/debug-bar-console.php',
	'debug-bar-cron/debug-bar-cron.php',
	'debug-bar-extender/debug-bar-extender.php',
	'debug-bar-super-globals/debug-bar-super-globals.php',
	'debug-bar/debug-bar.php',
	'developer/developer.php',
	'jetpack/jetpack.php',
	'log-deprecated-notices/log-deprecated-notices.php',
	'log-viewer/log-viewer.php',
	'polldaddy/polldaddy.php',
	'regenerate-thumbnails/regenerate-thumbnails.php',
	'rewrite-rules-inspector/rewrite-rules-inspector.php',
	'user-switching/user-switching.php',
	'vip-scanner/vip-scanner-wpcom.php',
	'vip-scanner/vip-scanner.php',
	'wordpress-importer/wordpress-importer.php',
);

add_filter( 'option_active_plugins', function( $active_plugins ) {
	$active_plugins = array_merge( $active_plugins, $GLOBALS['pmc_required_plugins'] );

	$active_plugins = array_unique( $active_plugins );

	return $active_plugins;
} );

add_filter( 'plugin_action_links', function( $actions, $plugin_file, $plugin_data, $context ) {
	// Remove edit link for all plugins
	if ( array_key_exists( 'edit', $actions ) ) {
		unset( $actions['edit'] );
	}

	// Remove deactivate link for important plugins
	if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, $GLOBALS['pmc_required_plugins'] ) ) {
		unset( $actions['deactivate'] );
	}

	return $actions;
}, 10, 4 );

add_filter( 'pre_site_transient_update_core', '__return_null' );

add_filter( 'plugins_url', function( $url, $path, $plugin) {
	$url = str_replace( WP_PLUGIN_DIR, '', $url );
	return $url;
} , 10, 3 );

add_filter('validate_current_theme', '__return_false');

//EOF
