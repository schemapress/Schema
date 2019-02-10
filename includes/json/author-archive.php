<?php
/**
 * Author Archive
 *
 * @since 1.4.5
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_author');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.4.5
 * @return schema json-ld final output
 */
function schema_wp_output_author() {
		
	// Run only on author pages
	if (is_author() ) {
		
		$json = schema_wp_get_author_json( 'Person' );
		
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
 * The main function responsible for putting schema array all together
 *
 * @param string $type for schema type (example: Person)
 * @since 1.4.5
 * @return schema output
 */
function schema_wp_get_author_json( $type ) {
	
	if ( ! isset($type) ) return;
	
	// Get current author data
	if(get_query_var('author_name')) :
    	$curauth = get_user_by('slug', get_query_var('author_name'));
	else :
    	$curauth = get_userdata(get_query_var('author'));
	endif;
	
	// debug
	//echo '<pre>'; print_r($curauth); echo '</pre>'; exit;
	
	$schema = array();
	
	$name	= $curauth->display_name;
	$email	= $curauth->user_email;
	$url	= $curauth->user_url;
	$desc	= $curauth->description;
	
	if ( empty($name) || empty($email) ) return;
	
	$schema['@context'] = "http://schema.org";
	$schema['@type'] = $type;
	
	if ( !empty($name) ) $schema['name'] = $name;
	//if ( !empty($email) ) $schema['email'] = $email;
	if ( !empty($url) )  {
	    $schema['url'] = $url;
	    $schema['@id'] = $url;
    }
	if ( !empty($desc) ) $schema['description'] = $desc;
	
	return apply_filters( 'schema_author_output', $schema );
}
