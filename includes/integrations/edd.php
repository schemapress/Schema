<?php
/**
 * Easy Digital Downloads (EDD)
 *
 *
 * Integrate with EDD plugin
 *
 * plugin url: https://wordpress.org/plugins/easy-digital-downloads/
 * @since 1.6.9.8
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//add_filter( 'schema_wp_breadcrumb_enabled', 'schema_wp_breadcrumb_edd_product_disable' );
/*
* Disable breadcrumbs on WooCommerce 
*
* @since 1.6.9.5
*/
function schema_wp_breadcrumb_edd_product_disable( $breadcrumb_enabled ){
	
	if ( function_exists( 'edd_add_schema_microdata' ) ) { 
		if ( edd_add_schema_microdata() ) return false;
	}
	return true;
}

add_action( 'schema_wp_action_post_type_archive', 'schema_wp_edd_add_schema_microdata_disable' );
/*
* Disable EDD Product markup output , it's hook to the post type archive function
*
* @since 1.6.9.8
*/
function schema_wp_edd_add_schema_microdata_disable(){
	
	if ( function_exists( 'edd_add_schema_microdata' ) ) { 
		add_filter( 'edd_add_schema_microdata', '__return_false' );
	}
}
