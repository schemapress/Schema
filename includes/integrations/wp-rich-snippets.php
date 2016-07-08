<?php
/**
 * WPRichSnippets 
 *
 *
 * Integrate with WPRichSnippets plugin
 *
 * plugin url: https://wprichsnippets.com/
 * @since 1.5.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'init', 'schema_wp_wprs_remove_admin_bar_menu' );
/*
* Remove bar menu
*
* @since 1.5.4
*/
function schema_wp_wprs_remove_admin_bar_menu() {
	
	// Check if AMP function exists
	if ( ! function_exists('wprs_admin_bar_menu_items') ) return;
	
	remove_action( 'admin_bar_menu', 'wprs_admin_bar_menu_items', 99 );
	
}
