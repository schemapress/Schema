<?php
/**
 * AMP plugin integration
 *
 *
 * plugin url: https://wordpress.org/plugins/amp/
 * @since 1.3
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter('amp_post_template_metadata', 'schema_wp_amp');
/**
 * Override schema json-ld for AMP plugin
 *
 * @since 1.3
 * @return schema json-ld final output
 */
function schema_wp_amp( $jason_array ) {
	
	global $post;
	
	// Check if AMP plugin is active
	if ( ! defined( 'AMP__FILE__' ) ) return;
	
	// Check if AMP function exists
	if ( ! function_exists('is_amp_endpoint') ) return;
	
	// Check if an AMP version of a post is being viewed
	if ( is_amp_endpoint() && is_single() ) {
		$json = array();
		// Get ref of Schema type in post meta 
		// @since 1.5.3
		$ref = get_post_meta( $post->ID, '_schema_ref', true );
		if ( $ref != '' ) {
			$schema_type 		= get_post_meta( (int)$ref, '_schema_type', true );
			$schema_sub_type 	= get_post_meta( (int)$ref, '_schema_article_type', true );
			$type = ($schema_sub_type != '') ? $schema_sub_type : $schema_type;
			$json = schema_wp_get_schema_json( $type );
			return $json;
		}
	}
	
	// Return the un-filtered array
	return $jason_array;
}




