<?php

/**
 *  Author extention
 *
 *  Adds schema Author for Article types
 *
 *  @since 1.5.9.7
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


add_filter( 'schema_output', 'schema_wp_do_author' );
/**
 * Filter schema markup output, via schema_output filter  
 *
 * @since 1.5.9.7
 * @return array 
 */
function schema_wp_do_author( $schema ) {
	
	global $post;
	
	$schema_type			= $schema["@type"];
	$support_article_types 	= schema_wp_get_support_article_types();
	
	$author			= schema_wp_get_author_array($post->ID);
	
	if ( in_array( $schema_type, $support_article_types, false) )
		$schema["author"] = schema_wp_get_author_array($post->ID);
	
	return $schema;
}


/**
 * Get author array
 *
 * @since 1.5.3
 * @return array
 */
function schema_wp_get_author_array( $post_id = null ) {
	
	global $post;
	
	// Set post ID
	If ( ! isset($post_id) ) $post_id = $post->ID;
	
	$jason = array();
	
	// Get author from post content
	$content_post	= get_post($post_id);
	$post_author	= get_userdata($content_post->post_author);
	$email 			= $post_author->user_email; 
	
	// Debug
	//print_r($post_author);exit;
	
	$author = array (
		'@type'	=> 'Person',
		'name'	=> apply_filters ( 'schema_wp_filter_author_name', $post_author->display_name ),
		'url'	=> esc_url( get_author_posts_url( $post_author->ID ) )
	);
	
	if ( get_the_author_meta( 'description', $post_author->ID ) ) {
		$author['description'] = strip_tags( get_the_author_meta( 'description', $post_author->ID ) );
	}
	
	if ( schema_wp_validate_gravatar($email) ) {
		// Default = 96px, since it is a squre image, width = height
		$image_size	= apply_filters( 'schema_wp_get_author_array_img_size', 96); 
		$image_url	= get_avatar_url( $email, $image_size );

		if ( $image_url ) {
			$author['image'] = array (
				'@type'		=> 'ImageObject',
				'url' 		=> $image_url,
				'height' 	=> $image_size, 
				'width' 	=> $image_size
			);
		}
	}
	
	
	// sameAs
	$website 	= esc_attr( stripslashes( get_the_author_meta( 'user_url', $post_author->ID ) ) );
	$google 	= esc_attr( stripslashes( get_the_author_meta( 'google', $post_author->ID ) ) );
	$facebook 	= esc_attr( stripslashes( get_the_author_meta( 'facebook', $post_author->ID) ) );
	$twitter 	= esc_attr( stripslashes( get_the_author_meta( 'twitter', $post_author->ID ) ) );
	$instagram 	= esc_attr( stripslashes( get_the_author_meta( 'instagram', $post_author->ID ) ) );
	$youtube 	= esc_attr( stripslashes( get_the_author_meta( 'youtube', $post_author->ID ) ) );
	$linkedin 	= esc_attr( stripslashes( get_the_author_meta( 'linkedin', $post_author->ID ) ) );
	$myspace 	= esc_attr( stripslashes( get_the_author_meta( 'myspace', $post_author->ID ) ) );
	$pinterest 	= esc_attr( stripslashes( get_the_author_meta( 'pinterest', $post_author->ID ) ) );
	$soundcloud = esc_attr( stripslashes( get_the_author_meta( 'soundcloud', $post_author->ID ) ) );
	$tumblr 	= esc_attr( stripslashes( get_the_author_meta( 'tumblr', $post_author->ID ) ) );
	$github 	= esc_attr( stripslashes( get_the_author_meta( 'github', $post_author->ID ) ) );
	
	$sameAs_links = array( $website, $google, $facebook, $twitter, $instagram, $youtube, $linkedin, $myspace, $pinterest, $soundcloud, $tumblr, $github);
	
	$social = array();
	
	// Remove empty fields
	foreach( $sameAs_links as $sameAs_link ) {
		if ( $sameAs_link != '' ) $social[] = $sameAs_link;
	}
	
	if ( ! empty($social) ) {
		$author["sameAs"] = $social;
	}
	
	return apply_filters( 'schema_wp_author', $author );
}


/**
 * Validate gravatar by email
 *
 * Check if email has a gravatar photo
 * @since 1.5.3
 * @return true or false
 */
function schema_wp_validate_gravatar($email) {
	// Craft a potential url and test its headers
	$hash = md5($email);
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = FALSE;
	} else {
		$has_valid_avatar = TRUE;
	}
	return $has_valid_avatar;
}
