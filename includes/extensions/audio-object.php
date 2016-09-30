<?php

/**
 *  AudioObject extention
 *
 *  Adds schema AudioObject to oEmbed
 *
 *  @since 1.5.9.6
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


add_action( 'admin_init', 'schema_wp_audio_object_admin_init' );
/**
 * Schema AudioObject init
 *
 * @since 1.5.9.6
 */
function schema_wp_audio_object_admin_init() {
	
	if ( ! is_admin() ) return;
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	$audio_object_enable = schema_wp_get_option( 'audio_object_enable' );
	
	if ( $audio_object_enable != true )
		return;
		
	$prefix = '_schema_audio_object_';

	$fields = array(
	
		array ( // Radio group
		'label'	=> __('Audio Markups', 'schema-wp'), // <label>
		'tip'	=> __('Select audio markup type.', 'schema-wp'),
		'desc'	=> __('Note: You can enable markups to multiple audios on the same page. However, this may slow down your site, make sure your site is hosted on a reliable web host and cache your site pages by a good caching plugin. (Recommended setting: Single Audio)', 'schema-wp'), // description
		'id'	=> $prefix.'type', // field id and name
		'type'	=> 'radio', // type of field
		'options' => array ( // array of options
			'none' => array ( // array key needs to be the same as the option value
				'label' => __('None', 'schema-wp'), // text displayed as the option
				'value'	=> 'none' // value stored for the option
				),
			'one' => array (
				'label' => __('Single audio', 'schema-wp'),
				'value'	=> 'single'
				),
			'two' => array (
				'label' => __('Multiple audios', 'schema-wp'),
				'value'	=> 'multiple'
				)
			)
		)
	);

	/**
	* Instantiate the class with all variables to create a meta box
	* var $id string meta box id
	* var $title string title
	* var $fields array fields
	* var $page string|array post type to add meta box to
	* var $context string context where to add meta box at (normal, side)
	* var $priority string meta box priority (high, core, default, low) 
	* var $js bool including javascript or not
	*/
	$schema_wp_audio_object = new Schema_Custom_Add_Meta_Box( 'schema_audio_object', 'AudioObject', $fields, 'schema', 'normal', 'high', true );
}


add_action( 'current_screen', 'schema_wp_audio_object_post_meta' );
/**
 * Create AudioObject post meta box for active post types edit screens
 *
 * @since 1.5.9.6
 */
function schema_wp_audio_object_post_meta() {
	
	if ( ! is_admin() ) return;
	
	if ( ! class_exists( 'Schema_WP' ) ) return;
	
	global $post;
	
	$prefix = '_schema_audio_object_';

	/**
	* Create meta box on active post types edit screens
	*/
	$fields = array(
		
		array( 
			'label'	=> '', 
			'desc'	=> __('You have enabled AudioObject, if you see an error in the <a target="_blank" href="https://search.google.com/structured-data/testing-tool">testing tool</a>, use the fields below to fill the missing fields, correct markup errors, and add additional details about the audio embedded in your content editor.', 'schema-wp'), 
			'id'	=> $prefix.'headline', 
			'type'	=> 'desc' 
		),
		array( // Text Input
			'label'	=> __('Title', 'schema-wp'), // <label>
			'tip'	=> __('Audio title', 'schema-wp'), // tooltip
			'desc'	=> __('', 'schema-wp'), // description
			'id'	=> $prefix.'name', // field id and name
			'type'	=> 'text' // type of field
		),
		array( 
			'label'	=> __('Upload Date', 'schema-wp'),  
			'tip'	=> __('Audio upload date in ISO 8601 format YYYY-MM-DD example: 2016-06-23', 'schema-wp'), 
			'desc'	=> __('', 'schema-wp'), 
			'id'	=> $prefix.'upload_date', 
			'type'	=> 'text' 
		),
		array( 
			'label'	=> __('Duration', 'schema-wp'), 
			'tip'	=> __('Audio duration, example: if duration is 1 Hour 35 MIN, use: PT1H35M', 'schema-wp'),  
			'desc'	=> __('', 'schema-wp'), 
			'id'	=> $prefix.'duration', 
			'type'	=> 'text' 
		),
		array( // Textarea
			'label'	=> __('Description', 'schema-wp'), 
			'tip'	=> __('Audio short description.', 'schema-wp'), 
			'desc'	=> __('', 'schema-wp'), 
			'id'	=> $prefix.'description',  
			'type'	=> 'textarea'  
		),
	);
	
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
		
		$type = (isset($schema_enabled['audio_object_type']) && $schema_enabled['audio_object_type'] != '') ? $schema_enabled['audio_object_type'] : '';
		
		// Add meta box only for type signle, preset an entry with one embed audio
		if ( $type == 'single' )  {
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
		
			if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
		
				$schema_wp_audio_object_active = new Schema_Custom_Add_Meta_Box( 'schema_audio_object', 'AudioObject', $fields, $schema_cpt, 'normal', 'high', true );
			}
		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
}



add_filter('schema_wp_cpt_enabled', 'schema_wp_schema_audio_object_extend_cpt_enabled');
/**
 * Extend the CPT Enabled array
 *
 * @since 1.5.9.6
 */
function schema_wp_schema_audio_object_extend_cpt_enabled( $cpt_enabled ) {
		
	if ( empty($cpt_enabled) )
		return;
	
	$audio_object_enable = schema_wp_get_option( 'audio_object_enable' );
	
	if ( $audio_object_enable != true )
		return $cpt_enabled;
		
	$args = array(
					'post_type'			=> 'schema',
					'post_status'		=> 'publish',
					'posts_per_page'	=> -1
				);
				
	$schemas_query = new WP_Query( $args );
	
	$schemas = $schemas_query->get_posts();
	
	// If there is no schema types set, return and empty array
	if ( empty($schemas) ) 
		return array();
	
	$i = 0;
	
	foreach ( $schemas as $schema ) : 
		
		// Get post meta
		$type = get_post_meta( $schema->ID, '_schema_audio_object_type', true );
		
		if ( ! isset($type) ) $type = 'none'; // default
	
		if ( $type != 'none' ) {
			// Append audio object type
			$cpt_enabled[$i]['audio_object_type']  = $type;
		}
		
		// Or maybe use...
		/*$cpt_enabled[$i]['misc']  = array (
									'review_type'	=>	$schema_review_type
								);*/
								
		$i++;
			
	endforeach;
 	
	// debug
	//echo '<pre>'; print_r($cpt_enabled); echo '</pre>';
	
	return $cpt_enabled;
}



add_filter( 'schema_output', 'schema_wp_audio_object_output' );
/**
 * Audio qoject output, filter the schema_output
 *
 * @param array $schema
 * @since 1.5.9.6
 * @return array $schema 
 */
function schema_wp_audio_object_output( $schema ) {
	
	//echo'<pre>'; print_r( $schema ); echo'</pre>';
	//exit;
			
	// Debug - start of script
	//$time_start = microtime(true);

	if ( empty($schema) ) 
		return;
	
	$audio_object_enable = schema_wp_get_option( 'audio_object_enable' );
	
	if ( $audio_object_enable != true )
		return $schema;
	
	global $wp_query, $post, $wp_embed;
	
	// Maybe this is not needed!
	if ( ! $wp_query->is_main_query() )
		return $schema;
	
	// This didn't work, that's why it's commented
	//if ( $wp_embed->last_url == '' || ! isset($wp_embed->last_url) ) return $schema;
	
	// Get post meta
	$schema_ref = get_post_meta( $post->ID, '_schema_ref', true );
	
	// Check for ref, if is not presented, then get out!
	if ( ! isset($schema_ref) || $schema_ref  == '' ) return $schema;
	
	// Get audio object type value from enabled Schema post type
	$type = get_post_meta( $schema_ref, '_schema_audio_object_type', true );
	
	//if ( ! isset($enabled) ) $enabled = false; // default
	//if ( ! isset($audio_object_type_enabled)  || $audio_object_type_enabled == '' )	$audio_object_type_enabled	= false;		// default
	if ( ! isset($type) ) $type = 'none'; // default
	
	
	if ( $type != 'none' ) {
		
		require_once( ABSPATH . WPINC . '/class-oembed.php' );
	
		// Get content
		$post_object = get_post( $post->ID );
		$content = $post_object->post_content;
		
		// Replace line breaks from all HTML elements with placeholders.
		//$content = wp_replace_in_html_tags( $content, array( "\n" => '<!-- wp-line-break -->' ) );
		
		// Get regex 
		//$regex = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS';
		$regex = '|^\s*(https?://[^\s"]+)\s*$|im';
		
		
		if ( $type == 'single') {
		
			// Get one audio
			$reg = preg_match( $regex, $content, $matches );
			//$matches = schema_wp_get_string_urls($content);
			
			if ( ! $reg ) return $schema;
			
			$autoembed = new WP_oEmbed();
			
			$url = trim($matches[0]); // also, use trim to remove white spaces if any
			$provider = $autoembed->discover( $url );
			if (filter_var($provider, FILTER_VALIDATE_URL) != FALSE) {
				$data = $autoembed->fetch( $provider, $url );
				if (!empty($data) ) {
					$schema['audio'] = schema_wp_get_audio_object_array( $data );
				}
			}
		
			/*
			// Or we can use...
			foreach ( $matches as $key => $url ) {
				$provider = $autoembed->discover( $url );
				if (filter_var($provider, FILTER_VALIDATE_URL) != FALSE) {
					$data = $autoembed->fetch( $provider, $url );
					if (!empty($data) ) {
						$schema['audio'] = schema_wp_get_audio_object_array( $data );
					}
				}
			}*/
	
		} else {
		
			// Get them all
			//$reg = preg_match_all( $regex, $content, $matches );
			// Or we can use this
			$matches = wp_extract_urls( $content );
			
			if ( empty($matches) ) return $schema;
			
			//$matches = schema_wp_get_string_urls($content);
			$autoembed = new WP_oEmbed();
			$schema['audio'] = array();
			foreach ( $matches as $key => $url ) {
				$url = trim($url); // remove white spaces if any
				$provider = $autoembed->discover( $url );
				if (filter_var($provider, FILTER_VALIDATE_URL) != FALSE) {
					$data = $autoembed->fetch( $provider, $url );
					if (!empty($data) ) {
						$schema['audio'][] = schema_wp_get_audio_object_array( $data );
					}
				}
			}
		}
	
	}
	
	// Debug
	//if (current_user_can( 'manage_options' )) {
			//echo'<pre>'; print_r( $schema ); echo'</pre>';
			//exit;
			//echo 'Execution time in seconds: ' . (microtime(true) - $time_start) . '<br>';
	//}
	
	// finally!
	return $schema;
}
	


/**
 * Get audio qoject array 
 *
 * @param array $data
 * @since 1.5
 * @return array 
 */
function schema_wp_get_audio_object_array( $data ) {
	
	global $post;
	
	// Check for WPRichSnippets
	//if (function_exists('wprs_is_enabled')) {
	//	if ( wprs_is_enabled($post->ID) ) return;
	//}
	
	//echo'<pre>'; print_r( $data ); echo'</pre>'; //exit;
	
	$audio_id		= '';		
	$name			= '';
	$description	= '';
	$image			= '';
	$thumbnail_url	= '';
	$upload_date	= '';		
	$duration 		= '';
			
	$host 			= isset($data->provider_name) ? $data->provider_name : '';
	
	$supported_hosts = array ( 'SoundCloud', 'Mixcloud', 'Spotify' ); // Spotify not tested!
	
	if ( ! in_array( $host, $supported_hosts) ) return;
	
	// Get values from post meta
	$meta_name			= get_post_meta( $post->ID, '_schema_audio_object_name', true );
	$meta_description	= get_post_meta( $post->ID, '_schema_audio_object_description', true );
	$meta_upload_date	= get_post_meta( $post->ID, '_schema_audio_object_upload_date', true );
	$meta_duration		= get_post_meta( $post->ID, '_schema_audio_object_duration', true );
	
	// Override values if found via parsing the data
	$audio_id		= isset($data->audio_id) ? $data->audio_id : '';
	$name			= isset($data->title) ? $data->title : $meta_name;
	$description	= isset($data->description) ? $data->description : $meta_description;
	$image			= isset($data->image) ? $data->image : '';
	$thumbnail_url	= isset($data->thumbnail_url) ? $data->thumbnail_url : '';
	$upload_date	= isset($data->upload_date) ? $data->upload_date : $meta_upload_date;
	$duration		= isset($data->duration) ? schema_wp_get_time_second_to_iso8601_duration( $data->duration ) : $meta_duration;
	
	$schema = array( 
						'@type'			=> 'AudioObject',
						"name"			=> $name,
						"description"	=> $description,
						"image"			=> $image,
						"thumbnailUrl"	=> $thumbnail_url,
						'uploadDate'	=> $upload_date,
						"duration"		=> $duration
					);
					
	return $schema;
}
