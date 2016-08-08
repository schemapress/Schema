<?php
/**
 * Visual Composer plugin integration
 *
 *
 * plugin url: https://vc.wpbakery.com/
 * @since 1.5.9.3
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'schema_wp_filter_content', 'remove_visual_composer_shortcodes' );
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
