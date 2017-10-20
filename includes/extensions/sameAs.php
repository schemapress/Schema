<?php
/**
 * Schema sameAs
 *
 * @package     Schema
 * @subpackage  Schema sameAs
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'current_screen', 'schema_wp_sameAs_post_meta' );
/**
 * Add exclude post meta box
 *
 * @since 1.5.9.9
 */
function schema_wp_sameAs_post_meta() {
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	// filter this and return false to disable the function
	$enabled = apply_filters('schema_wp_sameAs_post_meta_enabled', true);
	if ( ! $enabled)
		return;
	
	global $post;
	
	$prefix = '_schema_';

	/**
	* Create meta box on active post types edit screens
	*/
	$fields = apply_filters( 'schema_wp_sameAs', array(
		array( // Single checkbox
			'label'	=> __('URLs', 'schema-wp'), // <label>
			'tip'	=> __("URL of a reference Web page that unambiguously indicates the item's identity. E.g. the URL of the item's Wikipedia page, Freebase page, or official website.", 'schema-wp'), // description
			'desc'	=> __('Enter sameAs URLs, one per line.', 'schema-wp'), // description
			'id'	=> $prefix.'sameAs', // field id and name
			'type'	=> 'textarea', // type of field
			'sanitizer'	=> 'no_santitize' // do not santitize field value
		)
	));
	
	
	/**
	* Get enabled post types to create a meta box on
	*/
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return;

	// Get post type from current screen
	$current_screen = get_current_screen();
	$post_type = $current_screen->post_type;
	
	foreach( $schemas_enabled as $schema_enabled ) : 
		
		// debug
		//echo '<pre>'; print_r($current_screen); echo '</pre>'; 
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
		
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {

	
			$schema_wp_exclude = new Schema_Custom_Add_Meta_Box( 'schema_sameAs', __('sameAs','schema-wp'), $fields, $post_type, 'normal', 'low', true );

		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
}

add_filter('schema_output',					'schema_wp_sameAs_output' );
add_filter('schema_about_page_output',		'schema_wp_sameAs_output' );
add_filter('schema_contact_page_output',	'schema_wp_sameAs_output' );
/**
 * sameAs Schema output
 *
 * @since 1.5.9.9
 */
function schema_wp_sameAs_output( $schema ) {
	
	// filter this and return false to disable the function
	$enabled = apply_filters('schema_wp_sameAs_output_enabled', true);
	if ( ! $enabled)
		return $schema;
		
	global $post;
	
	if ( empty($schema) ) return;
	
	$sameAs = get_post_meta( $post->ID, '_schema_sameAs' , true );
	
	// make sure is set and it is not empty array
	if ( !isset($sameAs) || empty($sameAs) ) return $schema;
	
	//$sameAs_array = explode("\n", $sameAs);
	//$sameAs_array = preg_split ('/$\R?^/m', $sameAs);
	$sameAs_array = preg_split("/\r\n|\n|\r/", $sameAs);
	
	// debug
	//echo '<pre>'; print_r($sameAs_array); echo '</pre>';exit;
	
	$schema['sameAs'] =  $sameAs_array;
	
	return $schema;
}

/**
 * Get sameAs 
 *
 * @since 1.6
 */
function schema_wp_get_sameAs( $post_id = null ) {
	
	global $post;
	
	// Set post ID
	If ( ! isset($post_id) ) $post_id = $post->ID;
	
	$sameAs = get_post_meta( $post_id, '_schema_sameAs' , true );
	
	// make sure is set and it is not empty array
	if ( !isset($sameAs) || empty($sameAs) ) return;
	
	//$sameAs_array = explode("\n", $sameAs);
	//$sameAs_array = preg_split ('/$\R?^/m', $sameAs);
	$sameAs_array = preg_split("/\r\n|\n|\r/", $sameAs);
	
	// debug
	//echo '<pre>'; print_r($sameAs_array); echo '</pre>';exit;
	
	return $sameAs_array;
}
