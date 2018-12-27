<?php

/**
 *  Deprecated Functions
 * 
 * This file is made to keep older non-used functions as a refrence. 
 *
 *
 *  @since 1.6
 *  @return void
 */

/**
 * Get First Post Date Function
 *
 * @since 1.6.9.8
 * @param  $format Type of date format to return, using PHP date standard, default Y-m-d
 * @return Date of first post
 */
function schema_wp_first_post_date( $format = 'Y-m-d' ) {
	// Setup get_posts arguments
	$ax_args = array(
		'numberposts' => -1,
		'post_status' => 'publish',
		'order' => 'ASC'
	);

	// Get all posts in order of first to last
	$ax_get_all = get_posts($ax_args);

	// Extract first post from array
	$ax_first_post = $ax_get_all[0];

	// Assign first post date to var
	$ax_first_post_date = $ax_first_post->post_date;

	// return date in required format
	$output = date($format, strtotime($ax_first_post_date));

	return $output;
}


//add_filter( 'schema_wp_filter_content', 'remove_visual_composer_shortcodes' );
/**
 * Remove VC shortcodes from content
 *
 * @since 1.5.9.3
 * @return string
 */
function remove_visual_composer_shortcodes( $content ) {
	
	global $post;
	
	$vc_enabled = get_post_meta($post->ID, '_wpb_vc_js_status', true);
	
	if ( isset($vc_enabled) && $vc_enabled == 'true') {
	
    	$content = preg_replace('/\[\/?vc_.*?\]/', '', $content);
	}
	
    return $content;
}


//add_filter( 'schema_wp_filter_content', 'schema_wp_remove_divi_shortcodes' );
/**
 * Remove Divi shortcodes from content
 *
 * @since 1.5.9
 * @return string
 */
function schema_wp_remove_divi_shortcodes( $content ) {
	
	$my_theme = wp_get_theme();
	
	if ( $my_theme == 'Divi') {
	
    	$content = preg_replace('/\[\/?et_pb.*?\]/', '', $content);
	}
	
    return $content;
}
