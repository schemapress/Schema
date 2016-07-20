<?php
/**
 * Generate post meta fields
 *
 * @package     Schema
 * @subpackage  Schema Post Meta
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.8
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'template_redirect', 'schema_wp_generator_init_function' );

function schema_wp_generator_init_function() {
    $schema_post_meta_generator = new Schema_Post_Meta_Generator();
}


class Schema_Post_Meta_Generator {
    
	public function __construct() {
		
		global $post;
		
		$ref = isset($post->ID) ? get_post_meta( $post->ID, '_schema_ref', true ) : false;
		
		if ( $ref ) {
	
			$meta = get_post_meta( $ref, '_schema_post_meta_box' , true );
	
			if ( ! empty($meta) ) {
			
				//echo '<pre>'; print_r($meta); echo '</pre>'; exit;
			
				foreach ( $meta as $key => $value) :
		
					if ( isset($value['filter']) && $value['filter'] != '' && isset($value['key']) && $value['key'] != '') {
						
						$filter_name = $value['filter'];
						$meta_key = $value['key'];
						
						$this->filter_name_value = $filter_name;
						$this->meta_key_value 	 = $meta_key;
						$this->post_id		 	 = $post->ID;
						
						add_filter( $filter_name,  function ($field_value) use ( $meta_key ) { 
							$new_field_value = get_post_meta( $this->post_id, $meta_key, true );
							if ( isset($new_field_value) && $new_field_value != '' ) $field_value = $new_field_value;
							return $field_value;
						} );
							
					}
		
				endforeach;
			}
		}
    } // end of function
    
	
} // end of class
 


add_action( 'current_screen', 'schema_wp_generate_custom_post_meta_box' );
/**
 * Add exclude post meta box
 *
 * @since 1.5.8
 */
function schema_wp_generate_custom_post_meta_box() {
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	global $post;
	
	/**
	* Get enabled post types to create a meta box on
	*/
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return;
	
	//echo '<pre>'; print_r($schemas_enabled); echo '</pre>'; 
	
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
				
				$title = get_post_meta( $ref, '_schema_post_meta_box_title', true );
				if ( ! isset($title) || $title == '' ) $title = __('Schema', 'schema-wp');
				
				$repeated = get_post_meta( $ref, '_schema_post_meta_box', true );
				
				//echo '<pre>'; print_r($repeated); echo '</pre>'; exit;
				
				if ( ! empty($repeated) ) {
				
					//echo '<pre>'; print_r($repeated); echo '</pre>'; exit;
					
					// Add to fields array
					foreach ( $repeated as $repeated_key => $repeated_value) :
						
						//echo '<pre>'; print_r($repeated_value); echo '</pre>'; exit;
						
						if ( isset($repeated_value['field']) && $repeated_value['field'] == 1 ) {
							
							$id 	= isset($repeated_value['key']) ? $repeated_value['key'] : '';
							$label 	= isset($repeated_value['label']) ? $repeated_value['label'] : '';
							$desc 	= isset($repeated_value['desc']) ? $repeated_value['desc'] : '';
							$type	= isset($repeated_value['type']) ? $repeated_value['type'] : '';
							
							if ( $id )
							
								$fields[] = array
										( 
											'label'	=> $label, // <label>
											'desc'	=> $desc, // description
											'id'	=> $id, // field id and name
											'type'	=> $type, // type of field
										); 
						}
					
					endforeach;
					
					//echo '<pre>'; print_r($fields); echo '</pre>'; exit;
					
					$meta = new Schema_Custom_Add_Meta_Box( 'schema_custom_post_meta', $title, $fields, $post_type, 'normal', 'high', true );
				}
			}
			
			endforeach;
		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
	
}




$prefix = '_schema_';
/**
 * Post Meta Keys to Filters - post meta 
 *
 * @since 1.5.8
 */
$fields_post_meta_box =  array (
		
	'title' => array(
				'label' => __('Title', 'schema-wp'),
				'desc'	=> __('Post meta box title, default: Schema', 'schema-wp'),
				'tip' => __('This field will allow you to override the Schema post meta box title, default: Schema', 'schema-wp'),
				'id' 	=> $prefix.'post_meta_box_title',
				'type'	=> 'text',
				'size'	=> 'midum',
				'placeholder' => __('Schema', 'schema-wp'),
			),
			
	array( // Repeatable & Sortable Text inputs
		'label'	=> __('Fields', 'schema-wp'), // <label>
		'desc'	=> __('This is where you can define a source for schema.org markups fields to override the markups output. Select a filter name, then define post meta key name to pull data from, or tick the check box to automatically create a new custom post meta field to insert values manually when editing posts.', 'schema-wp'), // description
		'tip' => __('This feature allow you to override the schema.org markups output generated by the Schema plugin.', 'schema-wp'),
		'id'	=> $prefix.'post_meta_box', // field id and name
		'type'	=> 'repeatable_row', // type of field
		'sanitizer' => array( // array of sanitizers with matching kets to next array
			'featured' => 'meta_box_santitize_boolean',
			'title' => 'sanitize_text_field',
			'desc' => 'wp_kses_data'
		),
		'repeatable_fields' => array ( // array of fields to be repeated
			
			array( // Select box
				'label'	=> __('Filter', 'schema-wp'), // <label>
				'desc'	=> __('This is the filter name', 'schema-wp'), // description
				'id'	=> 'filter', // field id and name
				'type'	=> 'select', // type of field
				'selectone'	=> __('Select Filter', 'schema-wp'), // type of field
				'options' => apply_filters( 'schema_wp_post_meta', array ( // array of options
					'author_name' => array ( // array key needs to be the same as the option value
						'label' => __('Author Name', 'schema-wp'), // text displayed as the option
						'value'	=> 'schema_wp_filter_author_name' // value stored for the option
					),
					'headline' => array ( 
						'label' => __('Headline', 'schema-wp'), 
						'value'	=> 'schema_wp_filter_headline' 
					),
					'description' => array ( 
						'label' => __('Description', 'schema-wp'), 
						'value'	=> 'schema_wp_filter_description' 
					),
					
				)),
			), // end of array
	
			'title' => array(
				'label' => __('Key', 'schema-wp'),
				'desc'	=> __('Add post meta key name as source', 'schema-wp'),
				'id' => 'key',
				'type' => 'text',
				'size' => 'small',
				'placeholder' => __('Meta Key Name', 'schema-wp'),
			),
			
			'field' => array(
				'label' => __('Create?', 'schema-wp'),
				'tip'	=> __('Create custom post meta field?', 'schema-wp'),
				'desc'	=> __('Create Field?', 'schema-wp'), 
				'id' 	=> 'field',
				'type'	=> 'checkbox'
			),
			
			array( 
				'label'	=> __('Type', 'schema-wp'),
				'desc'	=> __('Select field type', 'schema-wp'),
				'id'	=> 'type',
				'type'	=> 'select',
				'selectone'	=> __('Select Type', 'schema-wp'),  
				'options' => apply_filters( 'schema_wp_post_meta_type', array ( 
					'text' => array ( 
						'label' => __('Text', 'schema-wp'), 
						'value'	=> 'text' 
					),
					'textarea' => array ( 
						'label' => __('Text Area', 'schema-wp'), 
						'value'	=> 'textarea' 
					),
					'checkbox' => array (  
						'label' => __('Checkbox', 'schema-wp'), 
						'value'	=> 'checkbox' 
					),
										
				)),
			), // end of array
	
			'label' => array(
				'label' => __('Label', 'schema-wp'),
				'desc'	=> __('Field label', 'schema-wp'),
				'id' 	=> 'label',
				'type'	=> 'text',
				'size'	=> 'small',
				'placeholder' => __('Label', 'schema-wp'),
			),
			
			'desc' => array(
				'label' => __('Description', 'schema-wp'),
				'desc'	=> __('Field description', 'schema-wp'), 
				'id' 	=> 'desc',
				'type'	=> 'textarea',
				'placeholder' => __('Description for this field', 'schema-wp'),
			),
			
		),
		
	), //end of field		
		
);

if ( is_admin() ) {
$schema_post_meta_box = new Schema_Custom_Add_Meta_Box( 'schema_post_meta_box', __('Post Meta', 'schema-wp'), $fields_post_meta_box, 'schema', 'normal', 'default', true );
}