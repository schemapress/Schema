<?php
/**
 * Schema Output
 *
 * @since 1.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.4
 * @return schema json-ld final output
 */
function schema_wp_output() {
	
	global $post;
	
	// @since 1.7.3
	if ( ! isset($post->ID) ) return;
	
	// do not run on front, home page, archive pages, search result pages, and 404 error pages
	if ( is_archive() || is_home() || is_front_page() || is_search() || is_404() ) return;
	
	// exclude entry, do not output the schema markup
	// @since 1.6
	$exclude = get_post_meta( $post->ID, '_schema_exclude' , true );
	if ( $exclude )
		return;
		
	$pttimestamp 	 	= time() + get_option('gmt_offset') * 60*60;
	$pttimestamp_old 	= get_post_meta( $post->ID, '_schema_json_timestamp', true );
	$json 				= array();
	
	// compare time stamp and check if json post meta value already exists
	// @since 1.5.9.7
	if ( isset($pttimestamp_old) && is_numeric($pttimestamp_old) ) {
		$time_diff = $pttimestamp - $pttimestamp_old;
		if ( $time_diff <= DAY_IN_SECONDS ) { 
			$json = get_post_meta( $post->ID, '_schema_json', true );
		} else {
			//delete_post_meta( $post->ID, '_schema_json' );
			//$json = array();
			
		}
	} 	
	
	if ( !isset($json) || empty($json) ) {
		// get fresh schema
		$json = schema_wp_get_enabled_json( $post->ID );
		// update post meta with new generated json value and time stamp 
		// @since 1.5.9.7 
		update_post_meta( $post->ID, '_schema_json', $json );
		update_post_meta( $post->ID, '_schema_json_timestamp', $pttimestamp );
	}
			
	if ( ! empty($json) ) {
		$output = "\n\n";
		$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - https://schema.press -->';
		$output .= "\n";
		$output .= '<script type="application/ld+json">' . json_encode($json, JSON_UNESCAPED_UNICODE) .'</script>';
		$output .= "\n\n";
		echo $output;
	}
}

/**
 * Get enabled schema json-ld 
 *
 * @since 1.7.1
 * @param string $post_id post id
 * @return array 
 */
function schema_wp_get_enabled_json( $post_id ) {
	
	$json = array();
	$schemas_enabled = array();
	
		// get Schema enabled array
		$schemas_enabled = schema_wp_cpt_get_enabled();
	
		if ( empty($schemas_enabled) ) return;
	
		$post_type = get_post_type();
	
		foreach( $schemas_enabled as $schema_enabled ) : 
		
			// debug
			//print_r($schema_enabled);
		
			// get Schema enabled post types array
			$schema_cpt = $schema_enabled['post_type'];
		
			if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
			
				// get enabled categories
				$categories = schema_wp_get_categories( $post_id );
				$categories_enabled = $schema_enabled['categories'];
				// Get an array of common categories between the two arrays
				$categories_intersect = array_intersect($categories, $categories_enabled);
				//print_r($result); exit;
			
				if ( empty($categories_enabled) ) {
				
					// apply on all posts
					$type = ($schema_enabled['type_sub'] && $schema_enabled['type']=='Article') ? $schema_enabled['type_sub'] : $schema_enabled['type'];
					$json = schema_wp_get_schema_json( $type );
			
				} else {
				
					// Apply only on enabled categories
					$cat_enabled = array_intersect_key( $categories, $categories_enabled );
				
					if ( ! empty($cat_enabled) && ! empty($categories_intersect) ) {
					
						//foreach( $categories as $key => $value  ){
    					
						//	if ( in_array( $value, $cat_enabled, true ) ) {
							
								//print_r($value); exit;
					
								$type = ($schema_enabled['type_sub'] && $schema_enabled['type']=='Article') ? $schema_enabled['type_sub'] : $schema_enabled['type'];
								$json = schema_wp_get_schema_json( $type );
					
						//	} // end if
						//} // end foreach
					
					}
				
				}
			
			}
		
		
		// debug
		//echo'<pre>';print_r($schema_enabled);echo'</pre>';
		
		endforeach;
	
	//echo'<pre>';print_r($json);echo'</pre>'; exit;
	
	return $json;
}

/**
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: Article)
 * @since 1.4
 * @return schema output
 */
function schema_wp_get_schema_json( $type ) {
	
	global $post;
	
	if ( ! isset($type) ) return array();
	
	$schema = array();
	
	// Get schema json array 
	$json = schema_wp_get_schema_json_prepare( $post->ID );
	
	// Debug
	//echo '<pre>'; print_r($json); echo '</pre>';
	
	// Start our schema array
	// @since 1.4
	
	// Stuff for any page
	$schema["@context"] = "http://schema.org/";

	$schema["@type"] = $type;
	
	$schema["mainEntityOfPage"] = array(
		"@type" => "WebPage",
		"@id" => $json['permalink']
		);
	
	$schema["url"] = $json['permalink'];
	
	if ( ! empty( $json["author"] ) ) {
		//$schema["author"] = $json['author'];
	}
	
	// get supported article types
	$support_article_types = schema_wp_get_support_article_types();
	
	// check if this type is supported Article, or sub of Article
	// if so, add required markup
	if ( in_array( $type, $support_article_types) ) {
		$schema["headline"]			= $json["headline"];
		$schema["datePublished"]	= $json["datePublished"];
		$schema["dateModified"]		= $json["dateModified"];
	
		if ( ! empty( $json["publisher"] ) ) {
			$schema["publisher"] = $json["publisher"];
		}
	}
	
	if ( ! empty( $json["media"] ) ) {
		$schema["image"] = $json["media"];
	}
	
	if ( $json['category'] != '' ) {
		$schema["articleSection"] = $json['category'];
	}
	
	if ( $json['keywords'] != '' && $type == 'BlogPosting' ) {
		$schema["keywords"] = $json['keywords'];
	}
	
	if ( $json["description"] != '' )  {
		$schema["description"] = $json["description"];
	}
	
	return apply_filters( 'schema_output', $schema );
}

/**
 * Prepare for json array
 *
 * @param string $id post id
 * @since 1.4
 * @return an array
 */
function schema_wp_get_schema_json_prepare( $post_id = null ) {
	
	global $post;
	
	// Set post ID
	If ( ! isset($post_id) ) $post_id = $post->ID;
	
	$json = array();
	
	
	// Get post content
	$content_post		= get_post($post_id);
	
	// Get description
	$description 		= schema_wp_get_description( $post_id );
	
	// Stuff for any page, if it exists
	$permalink			= get_permalink( $post_id) ;
	$category			= schema_wp_get_post_category( $post_id );
	$keywords			= schema_wp_get_post_tags( $post_id );
	
	// Get publisher array
	$publisher			= schema_wp_get_publisher_array();
	
	// Truncate headline 
	$headline			= schema_wp_get_truncate_to_word( $content_post->post_title );
	
	//
	// Putting all together
	//
	$json["headline"]		= apply_filters ( 'schema_wp_filter_headline', $headline );
	
	$json['description']	= $description;
	$json['permalink']		= $permalink;
	
	$json["datePublished"]	= get_the_date( 'c', $post_id );
	$json["dateModified"]	= get_post_modified_time( 'c', false, $post_id, false );
	
	$json['category']		= $category;
	$json['keywords']		= $keywords;
	
	$json['media'] 			= schema_wp_get_media($post_id);
	
	$json['publisher']		= $publisher;
	
	// debug
	//echo '<pre>'; print_r($json); echo '</pre>';
	
	return apply_filters( 'schema_json', $json );
}
