<?php
/**
 * Exclude post from Schema
 *
 * @package     Schema
 * @subpackage  Schema Post Meta
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.6
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'current_screen', 'schema_wp_exclude_post_meta' );
/**
 * Add exclude post meta box
 *
 * @since 1.5.6
 */
function schema_wp_exclude_post_meta() {
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	global $post;
	
	$prefix = '_schema_';

	/**
	* Create meta box on active post types edit screens
	*/
	$fields = apply_filters( 'schema_wp_exclude', array(
		array( // Single checkbox
			'label'	=> __('Turn Schema OFF', 'schema-wp'), // <label>
			'desc'	=> __('Tick this checkbox to turn off Schema output on this entry.', 'schema-wp'), // description
			'id'	=> $prefix.'exclude', // field id and name
			'type'	=> 'checkbox' // type of field
		),
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

	
			$schema_wp_exclude = new Schema_Custom_Add_Meta_Box( 'schema_exclude', __('Schema Exclude','schema-wp'), $fields, $post_type, 'normal', 'low', true );

		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
}
