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
	if ( ! is_front_page() && is_home() || is_home() ) {
		
		$json = schema_wp_get_blog_json( 'Blog' );
		
		$output = '';
		
		if ($json) {
			$output .= "\n\n";
			$output .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - http://schema.press -->';
			$output .= "\n";
			$output .= '<script type="application/ld+json">' . json_encode($json) . '</script>';
			$output .= "\n\n";
		}
		
		echo $output;
	}
}


/**
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: Person)
 * @since 1.5.4
 * @return schema output
 */
function schema_wp_get_blog_json( $type ) {
	
	if ( ! isset($type) ) return;
	
	global $post;
	
	$blogPost = array();
        
	while ( have_posts() ) : the_post();
	
	// check JSON-LD in post meta
	// @since 1.6
	$blog_post_json = get_post_meta( $post->ID, '_schema_json', true );
	
	if ( isset($blog_post_json) && !empty($blog_post_json) ) {
		
		$blogPost[] = $blog_post_json;
		
	} else {
    
		$blogPost[] = apply_filters( 'schema_output_blog_post', array
            (
				'@type' => 'BlogPosting',
				'headline' => get_the_title(),
				//'description' => strip_shortcodes( get_the_excerpt($post->ID) ),
				'url' => get_the_permalink(),
				'sameAs' => schema_wp_get_sameAs($post->ID),
				'datePublished' => get_the_date('c'),
				'dateModified' => get_the_modified_date('c'),
				'mainEntityOfPage' => get_the_permalink(),
				'author' => schema_wp_get_author_array(),
				'publisher' => schema_wp_get_publisher_array(),
				'image' => schema_wp_get_media(),
				'keywords' => schema_wp_get_post_tags($post->ID),
				'commentCount' => get_comments_number(),
				'comment' => schema_wp_get_comments(),
            ));
	}
	
	endwhile;

	$schema = array
        (
			'@context' => 'http://schema.org/',
			'@type' => "Blog",
			'headline' => get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url' => get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : get_site_url(),
			'blogPost' => $blogPost,
        );

	return apply_filters( 'schema_blog_output', $schema );
}
