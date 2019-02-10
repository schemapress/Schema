<?php
/**
 * Roles and Capabilities
 *
 * @package     Schema
 * @subpackage  Classes/Roles
 * @copyright   Copyright (c) 2012, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

/**
 * Schema_WP_Capabilities Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 * @since 1.0
 */
class Schema_WP_Capabilities {

	/**
	 * Get things going
	 *
	 * @since 1.0
	 */
	public function __construct() { /* Do nothing here */ }
	
	/**
	 * Add new shop roles with default WP caps
	 *
	 * @access public
	 * @since 1.5.9.3
	 * @return void
	 */
	public function add_roles() {
		add_role( 'manage_schema_options', __( 'Manage Schema Options', 'schema-wp' ), array(
			'read'                   => true,
			'edit_posts'             => true,
			'delete_posts'           => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'delete_others_pages'    => true,
			'delete_others_posts'    => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'edit_others_pages'      => true,
			'edit_others_posts'      => true,
			'edit_pages'             => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_published_pages'   => true,
			'edit_published_posts'   => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'publish_pages'          => true,
			'publish_posts'          => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'activate_plugins'		 => true,
			'manage_options'     	 => true
		) );
	}
	
	/**
	 * Add new capabilities
	 *
	 * @access public
	 * @since  1.0
	 * @global obj $wp_roles
	 * @return void
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			
			/** Site Administrator Capabilities */
			$wp_roles->add_cap( 'administrator', 'manage_schema_options' );
			$wp_roles->add_cap( 'super_admin', 'manage_schema_options' );
		
		}
	}


	/**
	 * Remove core post type capabilities (called on uninstall)
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function remove_caps() {

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		
		if ( is_object( $wp_roles ) ) {

			/** Site Administrator Capabilities */
			$wp_roles->remove_cap( 'administrator', 'manage_schema_options' );
			$wp_roles->remove_cap( 'super_admin', 'manage_schema_options' );

		}
	}
}
