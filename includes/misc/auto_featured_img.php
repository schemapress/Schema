<?php
/**
 * Auto Featured Image functions
 *
 * @package     Schema Auto Featured Image
 * @subpackage  Auto Featured Image
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9.7
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action('the_post',				'schema_wp_auto_featured_img_featured');
add_action('save_post',				'schema_wp_auto_featured_img_featured');
add_action('draft_to_publish',		'schema_wp_auto_featured_img_featured');
add_action('new_to_publish',		'schema_wp_auto_featured_img_featured');
add_action('pending_to_publish',	'schema_wp_auto_featured_img_featured');
add_action('future_to_publish', 	'schema_wp_auto_featured_img_featured');
/**
 * Set Featured image automatically while adding or updating the post.
 *
 * @since 1.4.6
 */
function schema_wp_auto_featured_img_featured() {
	
	global $post;
	
	$auto_featured_img = schema_wp_get_option( 'auto_featured_img' );
	
	if ( $auto_featured_img == true ) {
		
		$schema_enabled_spt = schema_wp_cpt_get_enabled();
		
		if ( empty($schemas_enabled) ) return;
		
		$post_type = get_post_type();
		
		$args = array(
			'post_parent' 		=> $post->ID,
			'post_type'   		=> 'attachment', 
			'post_mime_type'   	=> 'image', 
			'numberposts' 		=> 1,
			'post_status' 		=> 'any' 
		);

		foreach( $schemas_enabled as $schema_enabled ) : 
		
			// Get Schema enabled post types array
			$schema_cpt = $schema_enabled['post_type'];
		
			if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
				
				$already_has_thumb = has_post_thumbnail($post->ID);
		
				if ( ! $already_has_thumb )  {
					
					// check for children images
					$attached_image = get_children( $args);
					
					if ($attached_image) {
						foreach ($attached_image as $attachment_id => $attachment) {
							echo  $attachment_id;
							set_post_thumbnail($post->ID, $attachment_id);
						} // end foreach
					} // end if
				} // end if
			}
		
		endforeach;
	} // end if
}
