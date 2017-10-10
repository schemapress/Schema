<?php
/**
 * Generate post meta fields
 *
 * @package     Schema
 * @subpackage  Schema Post Meta
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Post Meta Generator Class
 *
 * @since 1.5.9
 */
class Schema_Post_Meta_Generator {
    
	public function __construct() {
		
		global $post, $meta_key;
		
		// check if generator is activated
		// @since 1.6.9.4
		$activate = apply_filters('schema_wp_post_meta_generator_activate', true);
		if ( ! $activate )
			return;
		
		// get schema ref
		$ref = isset($post->ID) ? get_post_meta( $post->ID, '_schema_ref', true ) : false;
		
		if ( $ref ) {
			
			// Check if enabled
			//$enabled = get_post_meta( $ref, '_schema_post_meta_box_enabled' , true );
			
			//if ( ! isset($enabled) || $enabled != 1 ) return;
			
			// Start working....

			$meta = get_post_meta( $ref, '_schema_post_meta_box' , true );
	
			if ( ! empty($meta) ) {
			
				//echo '<pre>'; print_r($meta); echo '</pre>'; exit;
			
				foreach ( $meta as $key => $value) :
					
					// This is not needed as it will stop filtering meta keys with no post meta fields
					//if ( isset($value['field']) && $value['field'] == 1 ) { // check if field is enabled
						
						if ( isset($value['filter']) && $value['filter'] != '' && isset($value['key']) && $value['key'] != '' ) {
						
							$filter_name	= $value['filter'];
							$meta_key		= $value['key'];
						
							$this->filter_name_value = $filter_name;
							$this->meta_key_value 	 = $meta_key;
							$this->post_id		 	 = $post->ID;
						
							$post_meta_value = '';
							// Check if has value!
							$post_meta_value = get_post_meta( $this->post_id, $meta_key, true );
						
							if ( isset($post_meta_value) && $post_meta_value != '' ) {
								
								// Anonymous function: automatically use filters to add values to schema output
								add_filter( $filter_name, function ($field_value) use ( $meta_key ) { 
									// Here we can do more conditions
									// we can modify the output based on complix field types 
									$field_value = get_post_meta( $this->post_id, $meta_key, true );
									return $field_value;
								} );
							}
						} // end if
						
					//} // end if
		
				endforeach;
			}
		}
    }

}



add_action( 'template_redirect', 'schema_wp_post_meta_generator_init' );
/**
 * init post meta generator class
 *
 * @since 1.5.9
 */
function schema_wp_post_meta_generator_init() {
    $schema_post_meta_generator = new Schema_Post_Meta_Generator();
}



add_action( 'current_screen', 'schema_wp_generate_custom_post_meta_box' );
/**
 * Generate custom post meta box
 *
 * @since 1.5.9
 */
function schema_wp_generate_custom_post_meta_box() {
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	// check if post meta box generator is activated
	// @since 1.6.9.4
	$activate = apply_filters('schema_wp_post_meta_box_generator_activate', true);
	if ( ! $activate )
		return;
	
	global $post;
	
	/**
	* Get enabled post types to create a meta box on
	*/
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return;
	
	// debug
	//echo'<pre>';print_r($schemas_enabled);echo'</pre>'; 
	
	// Get post type from current screen
	$current_screen = get_current_screen();
	$post_type 		= $current_screen->post_type;
	$fields 		= array();
	
	foreach( $schemas_enabled as $schema_enabled ) : 
		
		// debug
		//echo '<pre>'; print_r($current_screen); echo '</pre>'; 
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
		
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {

			foreach ( $schema_cpt as $key => $value) :
			
			if ( $post_type == $value ) {
				
				$ref = $schema_enabled['id'];
				
				$enabled = get_post_meta( $ref, '_schema_post_meta_box_enabled', true );
				
				if ( isset($enabled) && $enabled == 1 ) {
					
					$title = get_post_meta( $ref, '_schema_post_meta_box_title', true );
					if ( ! isset($title) || $title == '' ) $title = __('Schema', 'schema-wp');
				
					$repeated = get_post_meta( $ref, '_schema_post_meta_box', true );
				
					if ( ! empty($repeated) ) {
					
						// Add to fields array
						foreach ( $repeated as $repeated_key => $repeated_value) :
							
							if ( isset($repeated_value['field']) && $repeated_value['field'] == 1 ) {
							
								$id 	= isset($repeated_value['key']) ? $repeated_value['key'] : '';
								$label 	= isset($repeated_value['label']) ? $repeated_value['label'] : '';
								$type	= isset($repeated_value['type']) ? $repeated_value['type'] : '';
								$desc	= isset($repeated_value['desc']) ? $repeated_value['desc'] : '';
							
								if ( $id )
							
									$fields[] = array
										( 
											'label'	=> $label, 	// <label>
											'desc'	=> $desc, 	// description
											'id'	=> $id, 	// field id and name
											'type'	=> $type, 	// type of field
										); 
							}
					
						endforeach;
					
						//echo '<pre>'; print_r($fields); echo '</pre>'; exit;
						
						if ( empty($fields) ) return;
						
						$meta = new Schema_Custom_Add_Meta_Box( 'schema_custom_post_meta', $title, $fields, $post_type, 'normal', 'high', true );
					} // end if
				} // end if
				
			} // end if
			
			endforeach;
		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
}
