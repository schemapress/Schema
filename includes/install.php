<?php
/**
 * Schema Install
 *
 * @since 1.0
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function schema_wp_install() {

	// Create caps
	$roles = new Schema_WP_Capabilities;
	$roles->add_roles();
	$roles->add_caps();

	$older_plugin_version = get_option( 'schema_wp_version' );
	
	// Add Upgraded From Option
	if ( $older_plugin_version ) {
		update_option( 'schema_wp_version_upgraded_from', $older_plugin_version );
	}
	
	if ( ! get_option( 'schema_wp_is_installed' ) || $older_plugin_version < 1.4 ) {
		
		// Auto create Schema entries for Post and Page post types 
		// @since 1.4
		
		// Check if Schema post type exists,
		// if not then initiate the function so we can insert post 
		//if ( ! post_type_exists( 'schema' ) )  schema_wp_cpt_init();
		
		// Check if Post already exists
		// @since 1.5.9.6
		//$check_old_post = get_page_by_title( 'Post' );
		// @since 1.6
		$check_old_post = schema_wp_get_post_by_title( 'Post', 'schema' );
		
		/*
		*	Insert schema for posts
		*/
		$schema_post = ($check_old_post == null) ? wp_insert_post(
			array(
				'post_title'     => __( 'Post', 'schema-wp' ),
				'post_content'   => '',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'schema'
			)
		) : false; // set to false if already exists
	
		
		// update post meta
		if ($schema_post) {
			set_post_type( $schema_post, 'schema');	
			update_post_meta( $schema_post, '_schema_type',  			__('Article') );
			update_post_meta( $schema_post, '_schema_article_type',		__('BlogPosting') );
			$schema_types = array();
			$schema_types[0] = 'post';
			update_post_meta( $schema_post, '_schema_post_types',		$schema_types );
			
			// Add reference to every post
			// @since 1.4.4
			$posts = get_posts( array( 'post_type' => 'post', 'numberposts' => -1 ) );
		
			foreach( $posts as $p ) :
				// - Update the post's metadata.
				$check_ref = get_post_meta( $p->ID, '_schema_ref', true );
				if ( ! isset($check_ref) || $check_ref =='' ) {
					update_post_meta( $p->ID, '_schema_ref', $schema_post);
				}
			endforeach;
		}
		
		
		// Check if Page already exists
		// @since 1.5.9.6
		//$check_old_page = get_page_by_title( 'Page' );
		// @since 1.6
		$check_old_page = schema_wp_get_post_by_title( 'Page', 'schema' );
		
		/*
		*	Insert schema for pages
		*/
		$schema_page = ($check_old_page == null) ? wp_insert_post(
			array(
				'post_title'     => __( 'Page', 'schema-wp' ),
				'post_content'   => '',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'schema'
			)
		) : false; // set to false if already exists
		
		// Update post meta
		if ( $schema_page ) {
			set_post_type( $schema_page, 'schema');	
			update_post_meta( $schema_page, '_schema_type',  __('Article') );
			$schema_types = array();
			$schema_types[0] = 'page';
			update_post_meta( $schema_page, '_schema_post_types',		$schema_types );
			
			// Add reference to every page
			// @since 1.4.4
			$pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );
		
			foreach( $pages as $p ) :
				// - Update the page's metadata.
				$check_ref = get_post_meta( $p->ID, '_schema_ref', true );
				if ( ! isset($check_ref) || $check_ref == '' ) {
					update_post_meta( $p->ID, '_schema_ref', $schema_post);
				}
		 	endforeach;
		}
		
		// Update plugin settings
		$options = schema_wp_get_settings();
		$options['schema_wp_post'] = $schema_post;
		$options['schema_wp_page'] = $schema_page;
		update_option( 'schema_wp_settings', $options );
	}

	// Update pliugin version
	update_option( 'schema_wp_is_installed', '1' );
	update_option( 'schema_wp_version', SCHEMAWP_VERSION );
	
	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect
	set_transient( '_schema_wp_activation_redirect', true, 30 );

}
register_activation_hook( SCHEMAWP_PLUGIN_FILE, 'schema_wp_install' );


function schema_wp_check_if_installed() {

	// this is mainly for network activated installs
	if(  ! get_option( 'schema_wp_is_installed' ) ) {
		schema_wp_install();
	}
}
add_action( 'admin_init', 'schema_wp_check_if_installed' );


/**
 * Install user roles on sub-sites of a network
 *
 * Roles do not get created when Schema is network activation so we need to create them during admin_init
 *
 * @since 1.5.9.3
 * @return void
 */
function schema_wp_install_roles_on_network() {

	global $wp_roles;

	if( ! is_object( $wp_roles ) ) {
		return;
	}

	if( ! in_array( 'manage_schema', $wp_roles->roles ) ) {

		// Create EDD shop roles
		$roles = new Schema_WP_Capabilities;
		$roles->add_roles();
		$roles->add_caps();
	}

}
add_action( 'admin_init', 'schema_wp_install_roles_on_network' );
