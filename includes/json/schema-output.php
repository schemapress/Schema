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
	
	// Do not run on front, home page, archive pages, search result pages, and 404 error pages
	if ( is_archive() || is_home() || is_front_page() || is_search() || is_404() ) return;
	
	// Check for WPRichSnippets plugin
	// @since 1.4.5
	if (function_exists('wprs_is_enabled')) {
		if ( wprs_is_enabled( $post->ID ) ) return;
	}
	
	$output = '';
	$json = array();
	
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return;
	
	$post_type = get_post_type();
	
	foreach( $schemas_enabled as $schema_enabled ) : 
		
		// debug
		//print_r($schema_enabled);
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
		
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
			
			// Get enabled categories
			$categories = schema_wp_get_categories( $post->ID );
			$categories_enabled = $schema_enabled['categories'];
			// Get an array of common categories between the two arrays
			$categories_intersect = array_intersect($categories, $categories_enabled);
			//print_r($result); exit;
			
			if ( empty($categories_enabled) ) {
				
				// Apply on all posts
				$type = $schema_enabled['type_sub'] ? $schema_enabled['type_sub'] : $schema_enabled['type'];
				$json = schema_wp_get_schema_json( $type );
			
			} else {
				
				// Apply only on enabled categories
				$cat_enabled = array_intersect_key( $categories, $categories_enabled );
				
				if ( ! empty($cat_enabled) && ! empty($categories_intersect) ) {
					
					//foreach( $categories as $key => $value  ){
    					
					//	if ( in_array( $value, $cat_enabled, true ) ) {
							
							//print_r($value); exit;
					
							$type = $schema_enabled['type_sub'] ? $schema_enabled['type_sub'] : $schema_enabled['type'];
							$json = schema_wp_get_schema_json( $type );
					
					//	} // end if
					//} // end foreach
					
					
				}
				
			}
			
		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
	
	if ( ! empty($json) ) {
			$output .= "\n\n";
			$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - http://schema.press -->';
			$output .= "\n";
			$output .= '<script type="application/ld+json">' . json_encode($json) .'</script>';
			$output .= "\n\n";
		}

	echo $output;

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
		$schema["author"] = $json['author'];
	}
	
	$schema["headline"]			= $json["headline"];
	$schema["datePublished"]	= $json["datePublished"];
	$schema["dateModified"]		= $json["dateModified"];
	
	if ( ! empty( $json["media"] ) ) {
		$schema["image"] = $json["media"];
	}
	
	if ( $json['category'] != '' ) {
		$schema["ArticleSection"] = $json['category'];
	}
	
	if ( $json['keywords'] != '' && $type == 'BlogPosting' ) {
		$schema["keywords"] = $json['keywords'];
	}
	
	if ( ! empty( $json["publisher"] ) ) {
		$schema["publisher"] = $json["publisher"];
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
	
	$jason = array();
	
	// Get post content
	$content_post		= get_post($post_id);
	
	// Debug
	//echo '<pre>'; print_r($content_post); echo '</pre>';
	
	// Get description
	$full_content		= $content_post->post_content;
	$excerpt			= $content_post->post_excerpt;
	$full_content		= apply_filters('the_content', $full_content);
	$full_content		= str_replace(']]>', ']]&gt;', $full_content);
	$full_content		= strip_tags($full_content);
	$short_content		= wp_trim_words( $full_content, 49, '' ); 
	$description		= ( $excerpt != '' ) ? $excerpt : $short_content; 
	
	// Stuff for any page, if it exists
	$permalink			= get_permalink($post_id);
	$category			= schema_wp_get_post_category($post_id);
	$keywords			= schema_wp_get_post_tags($post_id);
	
	// Get data for the user who wrote that particular item
	$author				= get_userdata($content_post->post_author); 
	//$author_link		= get_the_author_link();
	$author_posts_link	= get_author_posts_url( $author->ID ); 
	
	// Get publisher array
	$publisher			= schema_wp_get_publisher_array();
	
	//
	// Putting all together
	//
	$json["headline"]		= $content_post->post_title;
	$json['description']	= $description;
	$json['permalink']		= $permalink;
	$json["datePublished"]	= $content_post->post_date;
	$json["dateModified"]	= $content_post->post_modified;
	
	$json['author'] 		= schema_wp_get_author_array();
	
	$json['category']		= $category;
	$json['keywords']		= $keywords;
	
	$json['media'] 			= schema_wp_get_media($post_id);
	
	$json['publisher']		= $publisher;
	
	// Debug
	//echo '<pre>'; print_r($json); echo '</pre>';
	
	return apply_filters( 'schema_json', $json );
}

