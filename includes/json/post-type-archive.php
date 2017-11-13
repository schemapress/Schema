<?php
/**
 * Post Type Archives
 *
 * @since 1.6.9.8
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_post_type_archive');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.6.9.8
 * @return schema json-ld final output
 */
function schema_wp_output_post_type_archive() {
	
	global $post;
	
	// Run only on blog list page
	if ( is_post_type_archive() ) { 
	
		$post_type = get_post_type();
	
		$enabled = schema_wp_is_post_type_enabled( $post_type ) ;
		if ( ! $enabled) return;
	
		//add_filter( 'edd_add_schema_microdata', '__return_false' );
		// add action to hook to this function
		do_action('schema_wp_action_post_type_archive');

		$json = schema_wp_get_post_type_archive_json( $post_type );
		
		$output = '';
		
		// debug
		//echo'<pre>';print_r($json);echo'</pre>';
		
		if ( $json ) {
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
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: Person)
 * @since 1.6.9.8
 * @return schema output
 */
function schema_wp_get_post_type_archive_json( $post_type ) {
	
	global $post, $wp_query, $query_string;
	
	// debug
	//echo'<pre>';print_r($wp_query);echo'</pre>';exit;
	//var_dump( $GLOBALS['wp_query'] );
	
	if ( empty($wp_query->query_vars) ) return;
	
	$blogPost 	= array();
	$schema 	= array();
	$url		= schema_wp_get_archive_link( $post_type ) ? schema_wp_get_archive_link($post_type) : get_home_url();
	
	$secondary_loop = new WP_Query( $wp_query->query_vars );
	
	if ( $secondary_loop->have_posts() ):
	   
	   // get markup data for each post in the query
	   if ( ! empty($secondary_loop->posts) ) {
		   
			$i = 1;
			
			foreach ($secondary_loop->posts as $schema_post) {
				
				// pull json from post meta
				$schema_json = get_post_meta( $schema_post->ID, '_schema_json', true );
				
				if ( isset($schema_json) && is_array($schema_json) ) {
					
					// override urls, fix for: All values provided for url must point to the same page.
					$schema_json['url'] = $url.'#'.$schema_post->post_name;
					
					$blogPost[] = array(
						'@type'		=> 'ListItem',
						//'url'		=> '', // ListItem with url and ListItem with item are incompatible.
      					'position'	=> $i,
      					'item' 		=> $schema_json
					);
				}
				
				$i++;
			}// end foreach
		}
		
		wp_reset_postdata();
		
		// get post type details	
		$post_type_archive_title = post_type_archive_title( __(''), false );
		$obj = get_post_type_object( $post_type );
		
		if ( ! empty($blogPost)) {
			// put all together
			$schema = array
        	(
				'@context' 			=> 'http://schema.org/',
				//'@type' 			=> array('ItemList', 'CreativeWork', 'WebPage'),
				'@type' 			=> array('ItemList', 'CreativeWork'),
				'name' 				=> isset($post_type_archive_title) ? $post_type_archive_title : get_bloginfo( 'name' ),
				'description' 		=> isset($obj->description) ? $obj->description : '',
				'url' 				=> $url,
				'itemListOrder' 	=> 'http://schema.org/ItemListOrderAscending',
				'numberOfItems' 	=> count($blogPost),
				'itemListElement' 	=> $blogPost,
        	);
		}
				
	endif;
	
	// debug
	//echo'<pre>';print_r($schema);echo'</pre>';exit;
	
	return apply_filters( 'schema_post_type_archive_output', $schema );
}
