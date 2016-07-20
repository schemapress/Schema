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
		
	// Run only on category pages
	if ( is_category() ) {
		
		$json = schema_wp_get_category_json( 'Category' );
		
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
 * @since 1.5.7
 * @return schema output
 */
function schema_wp_get_category_json( $type ) {
	
	if ( ! isset($type) ) return;
	
	global $post;
	
	$blogPost = array();
        
        while ( have_posts() ) : the_post();

            $blogPost[] = array
            (
				'@type' => 'BlogPosting',
				'headline' => get_the_title(),
				'url' => get_the_permalink(),
				'datePublished' => get_the_date('c'),
				'dateModified' => get_the_modified_date('c'),
				'mainEntityOfPage' => get_the_permalink(),
				'author' => schema_wp_get_author_array(),
				'publisher' => schema_wp_get_publisher_array(),
				'image' => schema_wp_get_media(),
				'keywords' => schema_wp_get_post_tags($post->ID),
				'commentCount' => get_comments_number(),
				'comment' => schema_wp_get_comments(),
            );

        endwhile;
		
        $category_link = get_category_link( get_the_category() );
        $category_headline = single_cat_title('', false) . " Category";

        $schema = array
        (
			'@context' => 'http://schema.org/',
			'@type' => "CollectionPage",
			'headline' => $category_headline,
			'description' => category_description(),
			'url' => $category_link,
			'hasPart' => $blogPost
        );

        return $schema;
}
