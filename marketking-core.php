<?php
/*
/**
 * Plugin Name:       MarketKing Core
 * Plugin URI:        https://wordpress.org/plugins/marketking-multivendor-marketplace-for-woocommerce
 * Description:       MarketKing is the complete solution for turning WooCommerce into a powerful multivendor marketplace. Core plugin.
 * Version:           1.8.14
 * Author:            WebWizards
 * Author URI:        webwizards.dev
 * Text Domain:       marketking-multivendor-marketplace-for-woocommerce
 * Domain Path:       /languages
 * WC requires at least: 5.0.0
 * WC tested up to: 8.3.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define ( 'MARKETKINGCORE_VERSION', 'v1.8.14' );
define( 'MARKETKINGCORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MARKETKINGCORE_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'MARKETKINGCORE_LANG' ) ){
    define( 'MARKETKINGCORE_LANG', basename( dirname( __FILE__ ) ) . '/languages' );
}

function marketking_activate() {
	require_once MARKETKINGCORE_DIR . 'includes/class-marketking-core-activator.php';
	Marketkingcore_Activator::activate();

}
register_activation_hook( __FILE__, 'marketking_activate' );


require MARKETKINGCORE_DIR . 'includes/class-marketking-core.php';
require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-order-splitter.php' );


// Load plugin language
add_action( 'plugins_loaded', 'marketking_load_language');
function marketking_load_language() {
	load_plugin_textdomain( 'marketking-multivendor-marketplace-for-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages');
}

// Begins execution of the plugin.
function marketking_run() {
	global $marketking_plugin;
	$marketking_plugin = new Marketkingcore();
}

marketking_run();
/** * @return Marketkingcore_Helper */
function marketking() {
    return Marketkingcore_Helper::init();
}




