<?php
/**
 * Blog
 *
 * @since 1.5.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_blog');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.5.4
 * @return schema json-ld final output
 */
function schema_wp_output_blog() {
		
	// Run only on blog list page
	//if ( ! is_front_page() && is_home() || is_home() ) {
	
	if ( schema_wp_is_blog() ) {
		
		$json = schema_wp_get_blog_json( 'Blog' );
		
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
 * @since 1.6.9.5
 * @return schema output
 */
function schema_wp_get_blog_json( $type ) {
	
	global $post, $wp_query, $query_string;
	
	// debug
	//echo'<pre>';print_r($wp_query);echo'</pre>';exit;
	//var_dump( $GLOBALS['wp_query'] );
	
	if ( empty($wp_query->query_vars) ) return;
	
	$blogPost 	= array();
	$schema 	= array();
	
	$secondary_loop = new WP_Query( $wp_query->query_vars );
	
	if ( $secondary_loop->have_posts() ):
	   
	   // get markup data for each post in the query
	   if ( ! empty($secondary_loop->posts) ) {
			foreach ($secondary_loop->posts as $schema_post) {
				
				// pull json from post meta
				$schema_json = get_post_meta( $schema_post->ID, '_schema_json', true );
				
				if ( isset($schema_json) && is_array($schema_json) ) {
					
					$blogPost[] = $schema_json;
				
				} else { 
				
					// create it
					$blogPost[] = apply_filters( 'schema_output_blog_post', array
           			(
						'@type' => 'BlogPosting',
						'headline' => wp_filter_nohtml_kses( get_the_title() ),
						'description' => schema_wp_get_description($schema_post->ID),
						'url' => get_the_permalink(),
						'sameAs' => schema_wp_get_sameAs($schema_post->ID),
						'datePublished' => get_the_date('c'),
						'dateModified' => get_the_modified_date('c'),
						'mainEntityOfPage' => get_the_permalink(),
						'author' => schema_wp_get_author_array(),
						'publisher' => schema_wp_get_publisher_array(),
						'image' => schema_wp_get_media($schema_post->ID),
						'keywords' => schema_wp_get_post_tags($schema_post->ID),
						'commentCount' => get_comments_number(),
						'comment' => schema_wp_get_comments(),
            		));
				}
			}
		}
		
		wp_reset_postdata();
		
		// put all together
		$schema = array
        (
			'@context' => 'http://schema.org/',
			'@type' => "Blog",
			'headline' => get_option( 'page_for_posts' ) ? wp_filter_nohtml_kses( get_the_title( get_option( 'page_for_posts' ) ) ) : get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url' => get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : get_home_url(),
			'blogPost' => $blogPost,
        );
				
	endif;
	
	// debug
	//echo'<pre>';print_r($schema);echo'</pre>';exit;
	
	return apply_filters( 'schema_blog_output', $schema );
}
