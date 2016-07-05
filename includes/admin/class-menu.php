<?php

class Schema_WP_Admin_Menu {


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
	}

	public function register_menus() {
		
		add_menu_page(
			__( 'Schema', 'schema-wp' ),
			__( 'Schema', 'schema-wp' ),
			'manage_schema',
			'schema',
			'schema_wp_settings_admin'
		);
		
		add_submenu_page(
			'schema',
			__( 'Schema Settings', 'schema-wp' ),
			__( 'Settings', 'schema-wp' ),
			'manage_schema',
			'schema',
			'schema_wp_settings_admin'
		);
		
		add_submenu_page(
			'schema',
			__( 'Schema Types', 'schema-wp' ),
			__( 'Schema Types', 'schema-wp' ),
			'manage_schema',
			'edit.php?post_type=schema'
		);
		
	}

}

$schema_wp_menu = new Schema_WP_Admin_Menu;
