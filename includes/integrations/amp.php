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

add_filter( 'amp_post_template_metadata', 'schema_wp_amp_modify_json_output', 10, 2 );
/**
 * Modify AMP json-ld output
 *
 * @since 1.6.9.5
 */
function schema_wp_amp_modify_json_output( $metadata, $post ) {
	
	$json = schema_wp_get_jsonld( $post->ID );
	
	if ( $json ) {
		return $json;
	}
	
	// Return the un-filtered array
	return $metadata;
}
