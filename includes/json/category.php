<?php
/**
 * Category
 *
 * @since 1.5.7
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_category');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.5.7
 * @return schema json-ld final output
 */
function schema_wp_output_category() {
	
	// filter this and return false to disable the function
	$enabled = apply_filters('schema_wp_output_category_enabled', true);
	if ( ! $enabled)
		return;
		
	if ( is_admin() ) return;
		
	// Run only on category pages
	if ( is_category() ) {
		
		$output = '';
		
		$json = schema_wp_get_category_json();
		
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
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: CollectionPage)
 * @since 1.5.7
 * @return array json 
 */
function schema_wp_get_category_json() {
		
	global $post, $query_string;
	
	// debug
	//echo'<pre>';print_r($query_string);echo'</pre>';exit;
	
	$blogPost 	= array();
	$json 		= array();
	
	$secondary_loop = new WP_Query( $query_string );
	
	if ( $secondary_loop->have_posts() ):
	   
	   // Faster way to get markup data
	   // @since 1.6.9.4
	   if ( ! empty($secondary_loop->posts) ) {
			foreach ($secondary_loop->posts as $schema_post) {
				$schema_json = get_post_meta( $schema_post->ID, '_schema_json', true );
				if ( isset($schema_json) ) {
					$blogPost[] = $schema_json;
				}		
			}
		}
		
		/*
		while( $secondary_loop->have_posts() ): $secondary_loop->the_post();
			
            $blogPost[] = apply_filters( 'schema_output_category_post', array
            (
				'@type' 			=> 'BlogPosting',
				'headline' 			=> get_the_title(),
				'url' 				=> get_the_permalink(),
				'datePublished' 	=> get_the_date('c'),
				'dateModified' 		=> get_the_modified_date('c'),
				'mainEntityOfPage' 	=> get_the_permalink(),
				'author' 			=> schema_wp_get_author_array(),
				'publisher' 		=> schema_wp_get_publisher_array(),
				'image' 			=> schema_wp_get_media(),
				'keywords' 			=> schema_wp_get_post_tags($post->ID),
				'commentCount' 		=> get_comments_number(),
				'comment' 			=> schema_wp_get_comments(),
            ));
			
        endwhile;
		*/
		
		wp_reset_postdata();
			
		$category 			= get_the_category(); 
		
		$category_id 		= intval($category[0]->term_id); 
       	$category_link 		= get_category_link( $category_id );
		//$category_link 	= get_term_link( $category[0]->term_id , 'category' );
       	$category_headline 	= single_cat_title( '', false ) . __(' Category', 'schema-wp');
		$sameAs 			= get_term_meta( $category_id, 'schema_wp_sameAs' );

		$json = array
       		(
				'@context' 		=> 'http://schema.org/',
				'@type' 		=> "CollectionPage",
				'headline' 		=> $category_headline,
				'description' 	=> strip_tags(category_description()),
				'url'		 	=> $category_link,
				'sameAs' 		=> $sameAs,
				'hasPart' 		=> $blogPost
       		);
				
	endif;
	
	return apply_filters( 'schema_category_json', $json );
}
