<?php
/**
 * Taxonomy
 *
 * @since 1.6.9.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_taxonomy');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.6.9.4
 * @return schema json-ld final output
 */
function schema_wp_output_taxonomy() {
	
	// filter this and return false to disable the function
	$enabled = apply_filters('schema_wp_output_taxonomy_enabled', true);
	if ( ! $enabled)
		return;
		
	if ( is_admin() ) return;
	
	// Run only on taxonomy pages
	if ( is_tax() ) {
		
		$output = '';
		
		$json = schema_wp_get_taxonomy_json();
		
		if ($json) {
			$output = "\n\n";
			$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - https://schema.press -->';
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
 * @since 1.6.9.4
 * @return array json 
 */
function schema_wp_get_taxonomy_json() {
	
	global $post, $query_string;
		
	$json = array();
	
	$secondary_loop = new WP_Query( $query_string );
	
	if ( $secondary_loop->have_posts() ):
	    
		while( $secondary_loop->have_posts() ): $secondary_loop->the_post();
    		
			$schema_json = get_post_meta( $post->ID, '_schema_json', true );
			
			if ( isset($schema_json) ) {
				$json[] = $schema_json;
			}
			
        endwhile;
		
		wp_reset_postdata();
					
	endif;
	
	return apply_filters( 'schema_taxonomy_json', $json );
}
