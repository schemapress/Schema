<?php
/**
 * Tag
 *
 * @since 1.6.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_tag');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.6.9.5
 * @return schema json-ld final output
 */
function schema_wp_output_tag() {
	
	// filter this and return false to disable the function
	$enabled = apply_filters('schema_wp_output_tag_enabled', true);
	if ( ! $enabled)
		return;
		
	if ( is_admin() ) return;
	
	// Run only on category pages
	if ( is_tag() ) {
		
		$output = '';
		
		$json = schema_wp_get_tag_json();
		
		if ($json) {
			$output .= "\n\n";
			$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - https://schema.press -->';
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
 * @param string $type for schema type (example: CollectionPage)
 * @since 1.6.9.5
 * @return array json 
 */
function schema_wp_get_tag_json() {
		
	global $post, $query_string;
	
	// debug
	//echo'<pre>';print_r($query_string);echo'</pre>';exit;
	
	$blogPost 	= array();
	$json 		= array();
	
	$secondary_loop = new WP_Query( $query_string );
	
	if ( $secondary_loop->have_posts() ):
	   
	   // Get the markup data
	   if ( ! empty($secondary_loop->posts) ) {
			foreach ($secondary_loop->posts as $schema_post) {
				$schema_json = get_post_meta( $schema_post->ID, '_schema_json', true );
				if ( isset($schema_json) ) {
					$blogPost[] = $schema_json;
				}		
			}
		}
		
		wp_reset_postdata();
			
		$tag 			= get_the_tags(); 
		
		$tag_id 		= intval($tag[0]->term_id); 
       	$tag_link 		= get_tag_link( $tag_id );
       	$tag_headline 	= single_tag_title( '', false ) . __(' Tag', 'schema-wp');
		$sameAs 		= get_term_meta( $tag_id, 'schema_wp_sameAs' );

		$json = array
       		(
				'@context' 		=> 'http://schema.org/',
				'@type' 		=> "CollectionPage",
				'headline' 		=> $tag_headline,
				'description' 	=> strip_tags(tag_description()),
				'url'		 	=> $tag_link,
				'sameAs' 		=> $sameAs,
				'hasPart' 		=> $blogPost
       		);
				
	endif;
	
	return apply_filters( 'schema_tag_json', $json );
}
