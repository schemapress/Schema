<?php
/**
 * Class Menu - admin menues
 *
 * @package     Schema
 * @subpackage  Admin Functions/Formatting
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/ 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Schema_WP_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_main_menus' 		),	10 );
		add_action( 'admin_menu', array( $this, 'register_types_menus' 		),  20 );
		add_action( 'admin_menu', array( $this, 'register_extensions_menus' ),  30 );
		add_action( 'admin_menu', array( $this, 'schema_premium_submenu' 	),  40 );
		add_action( 'admin_menu', array( $this, 'register_about_menus' 		),  50 );
	}

	public function register_main_menus() {
		
		global $schema_wp_options_page;
		
		$schema_wp_options_page = add_menu_page(
			__( 'Schema', 'schema-wp' ),
			__( 'Schema', 'schema-wp' ),
			'manage_schema_options',
			'schema',
			'schema_wp_options_page'
		);
		
		add_submenu_page(
			'schema',
			__( 'Schema Settings', 'schema-wp' ),
			__( 'Settings', 'schema-wp' ),
			'manage_schema_options',
			'schema',
			'schema_wp_options_page'
		);
		
		// Contextual Help
		// @since 1.5.9.3
		if ( $schema_wp_options_page )
		add_action( 'load-' . $schema_wp_options_page, 'schema_wp_settings_contextual_help' );	
	}
	
	public function register_types_menus() {
		
		add_submenu_page(
			'schema',
			__( 'Types', 'schema-wp' ),
			__( 'Types', 'schema-wp' ),
			'manage_schema_options',
			'edit.php?post_type=schema'
		);
	}
	
	public function register_extensions_menus() {
		
		add_submenu_page(
			'schema',
			__( 'Extensions', 'schema-wp' ),
			__( 'Extensions', 'schema-wp' ),
			'manage_schema_options',
			'schema-extensions',
			'schema_wp_admin_extensions_page'
		);
	}
	
	public function register_about_menus() {
		
		add_submenu_page(
			'schema',
			__( 'About', 'schema-wp' ),
			__( 'About', 'schema-wp' ),
			'manage_schema_options',
			'admin.php?page=schema-wp-what-is-new'
		);
	}
	
	public function schema_premium_submenu() {
	    
		global $submenu;

	    $submenu['schema'][] = array( __('Premium', 'schema-wp'), 'manage_options', 'https://schema.press/downloads/schema-premium/');
	}

}

$schema_wp_menu = new Schema_WP_Admin_Menu;
