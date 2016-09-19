<?php
/**
 * Uninstall Schema
 *
 * @package     Schema
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Make sure that we are uninstalling
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

// Load Schema file
include_once( 'schema.php' );

// Leave no trail
$option_name = 'schema_wp_settings';

if ( !is_multisite() ) {
	
    $options = get_option( $option_name );
	
	// Debug
	//echo '<pre>'; print_r($options); echo '</pre>'; exit;
	
	
	if ( isset($options['uninstall_on_delete']) && $options['uninstall_on_delete'] == true ) {
	
		// Remove the Schema entries for posts and pages
		wp_delete_post( $options['schema_wp_post'] );
		wp_delete_post( $options['schema_wp_page'] );
		
		// Delete all meta keys 
		// @since 1.4.4
		delete_post_meta_by_key( '_schema_ref' );
		delete_post_meta_by_key( '_schema_json' );
		delete_post_meta_by_key( '_schema_json_timestamp' );
		delete_post_meta_by_key( '_schema_exclude' );
		
		// Remove all plugin settings
		delete_option( $option_name );
		delete_option( 'schema_wp_version' );
		delete_option( 'schema_wp_is_installed' );
		
		// Remove all capabilities and roles
		$caps = new Schema_WP_Capabilities;
		$caps->remove_caps();
	}

} else { 

	// This is a multisite
	//
	// @since 1.4

    global $wpdb;
	
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) {
		
        switch_to_blog( $blog_id );
		
		$options = get_option( $option_name );
		
		if ( isset($options['uninstall_on_delete']) && $options['uninstall_on_delete'] == true ) {
			
			// Remove the Schema entries for posts and pages
			wp_delete_post( $options['schema_wp_post'] );
			wp_delete_post( $options['schema_wp_page'] );
		
			// Delete all JSON-LD meta keys
			// @since 1.5.9.9
			delete_post_meta_by_key( '_schema_ref' );
			delete_post_meta_by_key( '_schema_json' );
			delete_post_meta_by_key( '_schema_json_timestamp' );
			delete_post_meta_by_key( '_schema_exclude' );
		
			// Remove all plugin settings
			delete_option( $option_name );
			delete_option( 'schema_wp_version' );
			delete_option( 'schema_wp_is_installed' );
			
			// Remove all capabilities and roles
			$caps = new Schema_WP_Capabilities;
			$caps->remove_caps();
		}
    }

    switch_to_blog( $original_blog_id );
}
