<?php
/**
 * Divi Theme integration
 *
 *
 * plugin url: http://elegantthemes.com/
 * @since 1.5.9
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'schema_wp_filter_content', 'remove_divi_shortcodes' );
/**
 * Remove Divi shortcodes from content
 *
 * @since 1.5.9
 * @return string
 */
function remove_divi_shortcodes( $content ) {
	
	$my_theme = wp_get_theme();
	
	if ( $my_theme == 'Divi') {
	
    	$content = preg_replace('/\[\/?et_pb.*?\]/', '', $content);
	}
	
    return $content;
}
