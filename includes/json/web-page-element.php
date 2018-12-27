<?php
/**
 * WebPageElement > WPHeader
 * WebPageElement > WPFooter
 *
 * @since 1.6.9.8
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_web_page_element');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.6.9.8
 * @return schema json-ld final output
 */
function schema_wp_output_web_page_element() {
	
	global $post;
	
	$enable = schema_wp_get_option( 'web_page_element_enable' );

	if ( $enable != true )
		return;
	
	// Check if Genesis function exists
	// Remove Genesis site Header and Footer markup
	if ( function_exists('genesis_attr') ) { 
		// disable Genesis header markup
		add_filter( 'genesis_attr_site-header', 'schema_wp_genesis_attributes_removal_function', 20 );
		// disable Genesis footer markup
		add_filter( 'genesis_attr_site-footer', 'schema_wp_genesis_attributes_removal_function', 20 );
	}
	
	$json = schema_wp_get_web_page_element_json();
		
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

/**
 * The main function responsible for putting header array all together
 *
 * @since 1.6.9.8
 * @return schema output
 */
function schema_wp_get_web_page_element_json() {
	
	global $wp_query;
	
	// get post type
	$post_type 		= get_post_type();
	
	// set defaults
	$headline  		= wp_filter_nohtml_kses( get_the_title() );
	$description 	= get_bloginfo( 'description' );
	$url			= get_bloginfo( 'url' );
	
	if ( is_404() ) {
		// 404
        $headline		= __( 'Page not found', 'schema-wp' );
		$description 	= __('It looks like nothing was found at this location!', 'schema-wp');
		$url			= '';
	} elseif ( is_front_page() && is_home() ) {
		// Default homepage
		$headline 		= get_bloginfo( 'name' );
		$description 	= get_bloginfo( 'description' );
		$url			= get_bloginfo( 'url' );
	} elseif ( is_front_page() ) {
		// static homepage
		$headline 		= get_bloginfo( 'name' );
		$description 	= get_bloginfo( 'description' );
		$url			= get_bloginfo( 'url' );
	} elseif ( is_home() ) {
		// blog page
		$headline 		= get_bloginfo( 'name' );
		$description 	= get_bloginfo( 'description' );
		$url			= schema_wp_get_blog_posts_page_url();
	} else {
		//everything else
		
		// get enabled post types
		$schema_enabled = schema_wp_cpt_get_enabled_post_types();
		
		if ( in_array( $post_type , $schema_enabled ) ) {
			if ( is_single() || is_singular() ) {
				// single and singular pages
				$headline 		= wp_filter_nohtml_kses( get_the_title() );
				$description 	= schema_wp_get_description();
				$url			= get_permalink();
			}
		}
		
	}
	
	if ( is_archive() ) {
		// archive pages
		$headline 		= get_the_archive_title();
		$description 	= get_the_archive_description();
		$url			= '';
	}
	
	if ( is_post_type_archive() ) {
		// post type archive pages
		$headline 		= post_type_archive_title( __(''), false );
		$obj 			= get_post_type_object($post_type);
		$description 	= isset($obj->description) ? $obj->description : '';
		$url			= schema_wp_get_archive_link($post_type) ? schema_wp_get_archive_link($post_type) : get_home_url();
	}
	
	if ( is_search() ) {
    	// search
		$query			= get_search_query();
		$headline 		= sprintf( __( 'Search Results for &#8220;%s&#8221;' ), $query );
		$url			= get_search_link( $query );
		$description	= $wp_query->found_posts.' search results found for "'.$query.'".';
	}
	
	/*
	*	WPHeader
	*/
	$header = array(
		'@context' 		=> 'http://schema.org/',
		'@type'			=> 'WPHeader',
		'url'			=> $url,
      	'headline'		=> wp_strip_all_tags($headline),
      	'description'	=> wp_trim_words( wp_strip_all_tags($description), 18, '...' ),
	);
	
	/*
	*	WPFooter
	*/
	$footer = array(
		'@context' 			=> 'http://schema.org/',
		'@type'				=> 'WPFooter',
		'url'				=> $url,
      	'headline'			=> wp_strip_all_tags($headline),
      	'description'		=> wp_trim_words( wp_strip_all_tags($description), 18, '...' ),
	);
	
	// Add copyrightYear to Footer singulars
	// @since 1.7.1
	if (is_singular() ) {
		$footer['copyrightYear'] = 	get_the_date('Y');
	}
	
	$page_element_output = array($header, $footer);
	
	// debug
	//echo'<pre>';print_r($schema);echo'</pre>';exit;
	
	return apply_filters( 'schema_web_page_element_output', $page_element_output );
}
