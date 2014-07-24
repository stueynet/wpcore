<?php
/**
 * Plugin Name: WPCore Plugin Manager
 * Plugin URI: http://wpcore.com
 * Description: Keep all your favorite plugins in one place and install them instantly on any site
 * Version: 1.3.0
 * Author: Stuart Starr
 * Author URI: http://stuey.net
 * License: GPL2
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



/**
 * Build settings
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-wpcore.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/lib/class-tgm-plugin-activation.php' );
	add_action( 'plugins_loaded', array( 'wpcore', 'get_instance' ) );
}