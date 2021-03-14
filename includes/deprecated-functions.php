<?php

/**
 *  Deprecated Functions
 * 
 * This file is made to keep older non-used functions as a refrence. 
 *
 *
 *  @since 1.6
 *  @return void
 */

/**
 * Get First Post Date Function
 *
 * @since 1.6.9.8
 * @param  $format Type of date format to return, using PHP date standard, default Y-m-d
 * @return Date of first post
 */
function schema_wp_first_post_date( $format = 'Y-m-d' ) {
	// Setup get_posts arguments
	$ax_args = array(
		'numberposts' => -1,
		'post_status' => 'publish',
		'order' => 'ASC'
	);

	// Get all posts in order of first to last
	$ax_get_all = get_posts($ax_args);

	// Extract first post from array
	$ax_first_post = $ax_get_all[0];

	// Assign first post date to var
	$ax_first_post_date = $ax_first_post->post_date;

	// return date in required format
	$output = date($format, strtotime($ax_first_post_date));

	return $output;
}


/**
 * Truncate a string of content to 110 characters, respecting full words.
 *
 * @since 1.7.1
 * @return string
 */
function schema_wp_get_truncate_to_word_deprecated( $string, $limit = 110, $end = '...' ) {
	
	$limit 	= apply_filters( 'schema_wp_truncate_to_word_limit', $limit );
	$limit 	= $limit - strlen($end); // Take into account $end string into the limit
	$string = substr($string, 0, $limit);
	$string = substr($string, 0, strrpos($string, ' ')) . $end;
	
	return $string;
}
