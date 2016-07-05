<?php

/**
 *  Determines whether the current admin page is an Schema  admin page.
 *
 *  Only works after the `wp_loaded` hook, & most effective
 *  starting on `admin_menu` hook.
 *
 *  @since 1.0
 *  @return bool True if Schema admin page.
 */
function schema_wp_is_admin_page() {

	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		$ret = false;
	}

	if( ! isset( $_GET['page'] ) ) {
		$ret = false;
	}

	$page  = isset( $_GET['page'] ) ? $_GET['page'] : '';
	$pages = array(
		'schema',
		'schema-wp-getting-started',
		'schema-wp-what-is-new',
		'schema-wp-credits'
	);

	$ret = in_array( $page, $pages );

	return apply_filters( 'schema_wp_is_admin_page', $ret );
}

/**
 *  Load the admin scripts
 *
 *  @since 1.0
 *  @return void
 */
function schema_wp_admin_scripts() {

	if( ! schema_wp_is_admin_page() ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'schema-wp-admin', SCHEMAWP_PLUGIN_URL . 'assets/js/admin' . $suffix . '.js', array( 'jquery' ), SCHEMAWP_VERSION );
	
	// For media uploader
	wp_enqueue_media();
	
	wp_enqueue_script( 'jquery-ui-datepicker' );
}
add_action( 'admin_enqueue_scripts', 'schema_wp_admin_scripts' );

/**
 *  Load the admin styles
 *
 *  @since 1.0
 *  @return void
 */
function schema_wp_admin_styles() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Dashicons and our main admin CSS need to be on all pages for the menu icon
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'schema-wp-admin', SCHEMAWP_PLUGIN_URL . 'assets/css/admin' . $suffix . '.css', SCHEMAWP_VERSION );

	if( ! schema_wp_is_admin_page() ) {
		return;
	}

	// jQuery UI styles are loaded on our admin pages only
	$ui_style = ( 'classic' == get_user_option( 'admin_color' ) ) ? 'classic' : 'fresh';
	wp_enqueue_style( 'jquery-ui-css', SCHEMAWP_PLUGIN_URL . 'assets/css/jquery-ui-' . $ui_style . '.min.css' );
}
add_action( 'admin_enqueue_scripts', 'schema_wp_admin_styles' );

/**
 *  Load the frontend scripts and styles
 *
 *  @since 1.0
 *  @return void
 */
function schema_wp_frontend_scripts_and_styles() {

	global $post;

	if ( ! is_object( $post ) ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

}
add_action( 'wp_enqueue_scripts', 'schema_wp_frontend_scripts_and_styles' );


