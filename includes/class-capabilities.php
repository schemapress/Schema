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

			$wp_roles->add_cap( 'administrator', 'export_schema_data' );
			$wp_roles->add_cap( 'administrator', 'manage_schema_options' );
			$wp_roles->add_cap( 'administrator', 'manage_schema' );
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
			$wp_roles->remove_cap( 'administrator', 'export_schema_data' );
			$wp_roles->remove_cap( 'administrator', 'manage_schema_options' );
			$wp_roles->remove_cap( 'administrator', 'manage_schema' );

		}
	}
}