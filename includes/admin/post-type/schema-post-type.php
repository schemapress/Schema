<?php
/**
 * Schema Custom Post Type 
 *
 * @package     Schema
 * @subpackage  Schema Custom Post Type
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

 
add_action( 'init', 'schema_wp_cpt_init' );
/**
 * Register Schema post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @since 1.3
 */
function schema_wp_cpt_init() {
	$labels = array(
		'name'               => _x( 'Schema', 'post type general name', 'schema-wp' ),
		'singular_name'      => _x( 'Schema', 'post type singular name', 'schema-wp' ),
		'menu_name'          => _x( 'Schema', 'admin menu', 'schema-wp' ),
		'name_admin_bar'     => _x( 'Schema', 'add new on admin bar', 'schema-wp' ),
		'add_new'            => _x( 'Add New', 'schema', 'schema-wp' ),
		'add_new_item'       => __( 'Add New Schema', 'schema-wp' ),
		'new_item'           => __( 'New Schema', 'schema-wp' ),
		'edit_item'          => __( 'Edit Schema', 'schema-wp' ),
		'view_item'          => __( 'View Schema', 'schema-wp' ),
		'all_items'          => __( 'All Schemas', 'schema-wp' ),
		'search_items'       => __( 'Search Schemas', 'schema-wp' ),
		'parent_item_colon'  => __( 'Parent Schemas:', 'schema-wp' ),
		'not_found'          => __( 'No schema found.', 'schema-wp' ),
		'not_found_in_trash' => __( 'No schema found in Trash.', 'schema-wp' )
	);

	$args = array(
		'labels'             	=> $labels,
        'description'        	=> __( 'Description.', 'schema-wp' ),
		'public'             	=> false,
		'publicly_queryable' 	=> false,
		'show_ui'            	=> true,
		'show_in_menu'       	=> false,
		'show_in_nav_menus'  	=> false,
		'show_in_admin_bar'  	=> false,
		'query_var'         	=> true,
		//'rewrite'				=> array( 'slug' => 'schema' ),
		'rewrite'            	=> false,
		'capability_type'    	=> 'post',
		'map_meta_cap'       	=> true, // Set to false, if users are not allowed to edit/delete existing schema
		'has_archive'        	=> false,
		'can_export'		 	=> true,
		'hierarchical'       	=> false,
		'exclude_from_search'	=> true,
		'menu_position'     	=> null,
		'taxonomies'			=> array( 'category' ),
		'supports' 				=> array( 'title' )
	);

	register_post_type( 'schema', $args );
}


add_filter( 'post_updated_messages', 'schema_wp_cpt_updated_messages' );
/**
 * Book update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 * @since 1.3
 */
function schema_wp_cpt_updated_messages( $messages ) {
	
	global $current_screen;
	
	if ( $current_screen->post_type != 'schema' ) return $messages;
	
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['schema'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Schema updated.', 'schema-wp' ),
		2  => __( 'Custom field updated.', 'schema-wp' ),
		3  => __( 'Custom field deleted.', 'schema-wp' ),
		4  => __( 'Schema saved.', 'schema-wp' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Schema restored to revision from %s', 'schema-wp' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Schema created.', 'schema-wp' ),
		7  => __( 'Schema saved.', 'schema-wp' ),
		8  => __( 'Schema added.', 'schema-wp' ),
		9  => sprintf(
			__( 'Schema scheduled for: <strong>%1$s</strong>.', 'schema-wp' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'schema-wp' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Schema draft updated.', 'schema-wp' )
	);

	if ( $post_type_object->publicly_queryable ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View schema', 'schema-wp' ) );
		$view_link = '';
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview schema', 'schema-wp' ) );
		$preview_link = '';
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}


//add_filter( 'post_row_actions', 'schema_wp_cpt_remove_row_actions', 10, 2 );
/**
 * Remove quick edit and preview links for custom post type schema
 *
 * @param array $actions and $post
 *
 * @return array
 * @link https://wordpress.org/support/topic/remove-quick-edit-from-custom-post-type?replies=11#post-2253706
 * @since 1.3
 */
function schema_wp_cpt_remove_row_actions( $actions, $post ) {
	
	global $current_screen;
	
	//if ( $current_screen->post_type != 'schema' ) return $actions;
	
	if( get_post_type() === 'schema' ) {
	
		//unset( $actions['edit'] );
		unset( $actions['view'] );
		//unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		//$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
		}
	return $actions;
}


// Not used, found a better function: schema_wp_replace_submit_meta_box()
//add_filter( 'gettext', 'schema_wp_change_publish_button', 10, 2 );
/**
 * Modify Publish button on Schema post type
 *
 * @param array $translation and $text
 *
 * @return string
 * @link http://wordpress.stackexchange.com/questions/3578/change-the-text-on-the-publish-button
 * @since 1.4.7
 */
function schema_wp_change_publish_button( $translation, $text ) {
	
	if ( ! isset($_GET['post_type']) ||  $_GET['post_type'] != 'schema' ) return $translation;

	if ( $text == 'Publish' )
    	return __('Create Schema', 'schema-wp');

	return $translation;
}

// Not used, found a better option below...
add_action( 'transition_post_status', 'schema_wp_set_post_status_to_publish', 10, 3 );
/**
 * Make sure that Schema post status is set to publish
 *
 * @since 1.4.8
 */
function schema_wp_set_post_status_to_publish( $new_status, $old_status, $post ) { 
    if ( $post->post_type == 'schema' && $new_status == 'draft' && $old_status  != $new_status ) {
        $post->post_status = 'publish';
        wp_update_post( $post );
    }
}

//add_filter( 'wp_insert_post_data', 'schema_wp_force_type_publish' );
/**
 * Make sure that Schema post status is set to publish
 *
 * @since 1.4.8
 */
function schema_wp_force_type_publish($post) {
    if ($post['post_type'] == 'schema')
    $post['post_status'] = 'publish';
    return $post;
}
