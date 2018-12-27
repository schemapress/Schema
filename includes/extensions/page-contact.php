<?php
/**
 * Page - Contact
 *
 * @since 1.5.2
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'schema_output', 'schema_wp_no_schema_output_if_page_contact' );
/**
 * Do not output schema default json-ld if this is the About page
 *
 * @since 1.5.2
 * @return schema json-ld array or an empy array
 */
function schema_wp_no_schema_output_if_page_contact( $schema ) {
	
	$contact_page_id = schema_wp_get_option( 'contact_page' );
	
	if ( ! $contact_page_id ) return $schema;
	
	if ( is_page( $contact_page_id ) ) {
		return array();
	}
	
	return $schema;
}


add_action('wp_head', 'schema_wp_output_page_contact');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.4.5
 * @return schema json-ld final output
 */
function schema_wp_output_page_contact() {
	
	$contact_page_id = schema_wp_get_option( 'contact_page' );
	
	if ( ! $contact_page_id ) return;
 		
	// Run only on author pages
	if ( is_page( $contact_page_id ) ) {
		
		$json = schema_wp_get_page_contact_json( 'ContactPage' );
		
		$output = '';
		
		if ($json) {
			$output .= "\n\n";
			$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - http://schema.press -->';
			$output .= "\n";
			$output .= '<script type="application/ld+json">' . json_encode($json, JSON_UNESCAPED_UNICODE) . '</script>';
			$output .= "\n\n";
		}
		
		echo $output;
	}
}


/**
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: ContactPage)
 * @since 1.5.1
 * @return schema output
 */
function schema_wp_get_page_contact_json( $type ) {
	
	global $post;
	
	if ( ! isset($type) ) return array();
	
	$schema = array();
	
	// Get schema json array 
	$json = schema_wp_get_schema_json_prepare( $post->ID );
	
	// Debug
	//echo '<pre>'; print_r($json); echo '</pre>';
	
	$schema['@context'] = "http://schema.org";
	$schema['@type'] = $type;
	
	$schema["mainEntityOfPage"] = array(
		"@type" => "WebPage",
		"@id" => $json['permalink']
		);
	
	$schema["url"] = $json['permalink'];
	
	/*
	$schema["author"] = array(
		"@type"	=> "Person",
		"name"	=> $json['author']['author_name'],
		"url"	=> $json['author']['author_posts_link'],
		);
	*/
	
	$schema["headline"] = $json["headline"];
	
	//$schema["datePublished"]	= $json["datePublished"];
	//$schema["dateModified"]	= $json["dateModified"];
	
	if ( ! empty( $json["media"] ) ) {
		$schema["image"] = array(
    		"@type"		=> "ImageObject",
    		"url"		=> isset($json["media"]["url"]) ? $json["media"]["url"] : '',
    		"width"		=> isset($json["media"]["width"]) ? $json["media"]["width"] : '',
			"height"	=> isset($json["media"]["height"]) ? $json["media"]["height"] : ''
		);
	}
	
	if ( ! empty( $json["publisher"] ) ) {
		$schema["publisher"] = $json["publisher"];
	}
	
	
	if ( $json["description"] != '' )  {
		$schema["description"] = $json["description"];
	}
	
	return apply_filters( 'schema_contact_page_output', $schema );
}
