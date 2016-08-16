<?php

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
	
	wp_localize_script( 'schema-wp-admin', 'schema_wp_vars', array(
		'post_id'                     => isset( $post->ID ) ? $post->ID : null,
		'schema_wp_version'                 => SCHEMAWP_VERSION,
		'use_this_file'               => __( 'Use This File', 'schema-wp' ),
		'remove_text'                 => __( 'Remove', 'schema-wp' ),
		'new_media_ui'                => apply_filters( 'schema_wp_use_35_media_ui', 1 ),
		'unsupported_browser'         => __( 'We are sorry but your browser is not compatible with this kind of file upload. Please upgrade your browser.', 'schema-wp' ),
	));
	
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	// For media uploader
	wp_enqueue_media();
	
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-tooltip' );
	
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );
	
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
