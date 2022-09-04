<?php

/**
 * Fires when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if Keep Data and Settings on Uninstall option is activated. If activated, do not erase data and settings
$keep_data_setting = boolval(get_option( 'marketking_keepdata_setting', 1 ));

// If "keep data" option is NOT activated
if (!$keep_data_setting) {

	// clear options
	global $wpdb;
	$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%marketking%'" );
	foreach( $plugin_options as $option ) {
	    delete_option( $option->option_name );
	}

	// clear all custom posts
	$post_types = array('marketking_group', 'marketking_grule', 'marketking_announce', 'marketking_message');
	foreach ($post_types as $type){
		$allposts= get_posts( array('post_type'=> $type,'numberposts'=>-1) );
		foreach ($allposts as $eachpost) {
			wp_delete_post( $eachpost->ID, true );
		}
	}

	// clear user metadata
	$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE '%marketking%'");

	// clear product metadata
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%marketking%'");
	  
}