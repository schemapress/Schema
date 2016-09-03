<?php
/**
 * Deprecated functions
 *
 * @package     Schema Deprecated
 * @subpackage  Functions/Formatting
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'save_post', 'schema_save_ref', 10, 3 );
/**
 * Save post metadata when a Schema post is saved.
 * Add schema reference Id
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 * @since 1.4.4
 */
function schema_save_ref( $post_id, $post, $update ) {
	
	if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) 
		return $post_id;
		
	$slug = 'schema';

    // If this isn't a 'schema' post, don't update it.
    if ( $slug != $post->post_type ) {
        return $post_id;
    }
	
	// If this is just a revision, don't save ref.
	if ( wp_is_post_revision( $post_id ) )
		 return $post_id;
		
    // - Update the post's metadata.
	schema_update_all_meta_ref( $post_id );
	
	// Debug
	//$msg = 'Is this un update? ';
  	//$msg .= $update ? 'Yes.' : 'No.';
  	//wp_die( $msg );
	
	 return $post_id;
}


/**
 * Update post meta with a ref Schema Id
 *
 * @param int $post_id The post ID.
 * @since 1.4.4
 */
function schema_update_meta_ref( $post_id ) {
	
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return false;

	// Get post type from current screen
	$current_screen = get_current_screen();
	$post_type = $current_screen->post_type;
	
	foreach( $schemas_enabled as $schema_enabled ) : 
	
		// Debug
		//echo '<pre>'; print_r($schema_enabled); echo '</pre>';exit; 
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
	
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
			
			// Get schema post id
			$schema_id = $schema_enabled['id'];
			// insert schema post id into post mea
			update_post_meta( $post_id, '_schema_ref', $schema_id);
		}
		
	endforeach;
	
	return true;	
}


/**
 * Update post meta with a ref Schema Id for post types
 *
 * @param int $schema_id The schema post ID.
 * @since 1.4.4
 */
function schema_update_all_meta_ref( $schema_id ) {
	return;
	global $wpdb;
	
	if ( ! isset( $schema_id ) ) return;
	
	// Get enabled post types array
	$schema_type = get_post_meta( $schema_id, '_schema_post_types' , true );
	
	// Debug
	//echo '<pre>'; print_r($schema_type); echo '</pre>';exit; 
	
	if ( ! is_array( $schema_type ) || empty( $schema_type) ) return false;
	 
	foreach( $schema_type as $schema_enabled ) :  
		
		// Get all posts within this specific post type
		$posts = get_posts( array( 'post_type' => $schema_enabled, 'numberposts' => -1 ) );
		
		foreach($posts as $p) :
			// - Update the post's metadata.
			$old_ref = get_post_meta( $p->ID, '_schema_ref', true );
			if ( isset($old_ref) ) {
				if ( $old_ref != $p->ID )
					update_post_meta( $p->ID, '_schema_ref', $schema_id);
			} else {	
				update_post_meta( $p->ID, '_schema_ref', $schema_id);
			}
		 endforeach;
	
    endforeach;
	
	return true;
	
}


/**
* Create post post box
*
* Uses class Schema_Custom_Add_Meta_Box
*
* @since 1.5.7
* @return true 
*/
function schema_wp_getAuthor() {	
	
	$Author = array
	(
		'@type' => 'Person',
		'name' => get_the_author(),
		'url' => esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	);

	if ( get_the_author_meta( 'description' ) )	{
		$Author['description'] = get_the_author_meta( 'description' );
	}
	
	$author_img_url = get_avatar_url( get_the_author_meta( 'user_email' ), 96 );
	
	if ( $AuthorImage ) {
		$Author['image'] = array
		(
			'@type' => 'ImageObject',
			'url' => $author_img_url,
			'height' => 96,
			'width' => 96
		);
	}

	return $Author;
}
