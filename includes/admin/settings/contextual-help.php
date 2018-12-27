<?php
/**
 * Contextual Help
 *
 * @package     Schema
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings contextual help.
 *
 * @since       1.5.9.3
 * @return      void
 */
function schema_wp_settings_contextual_help() {
	
	$screen = get_current_screen();

	$screen->set_help_sidebar(
		'<p><strong>' . sprintf( __( 'For more information:', 'schema-wp' ) . '</strong></p>' .
		'<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the schema.press website.', 'schema-wp' ), esc_url( 'https://schema.press/docs/' ) ) ) . '</p>' .
		'<p>' . sprintf(
					__( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>. View <a href="%s">extensions</a>', 'schema-wp' ),
					esc_url( 'https://github.com/schemapress/Schema/issues' ),
					esc_url( 'https://github.com/schemapress/Schema' ),
					esc_url( 'https://schema.press/docs/?utm_source=plugin-settings-page&utm_medium=contextual-help-sidebar&utm_term=extensions&utm_campaign=ContextualHelp' )
					) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'schema-wp-settings-general',
		'title'	    => __( 'General', 'schema-wp' ),
		'content'	=> '<p>' . __( 'This screen provides the most basic settings for configuring Schema plugin on your site. You can set Schema for About and Contact pages, and turn automatic <em>Feature image</em> on and off...etc', 'schema-wp' ) . '</p>'
	) );
	
	$screen->add_help_tab( array(
		'id'	    => 'schema-wp-settings-knowledge-graph',
		'title'	    => __( 'Knowledge Graph', 'schema-wp' ),
		'content'	=> '<p>' . __( 'This screen provides settings for configuring the Knowledge Graph. You can set Organization Info and Corporate Contacts.', 'schema-wp' ) . '</p>'
	) );
	
	$screen->add_help_tab( array(
		'id'	    => 'schema-wp-settings-search-results',
		'title'	    => __( 'Search Results', 'schema-wp' ),
		'content'	=> '<p>' . __( 'This screen provides settings for configuring Search Results. You can set Sitelinks Search Box and Site Name.', 'schema-wp' ) . '</p>'
	) );

	$screen->add_help_tab( array(
		'id'		=> 'schema-wp-settings-extensions',
		'title'		=> __( 'Extensions', 'schema-wp' ),
		'content'	=> '<p>' . __( 'This screen provides access to settings added by most Schema extensions.', 'schema-wp' ) . '</p>'
	) );

	$screen->add_help_tab( array(
		'id'	    => 'schema-wp-settings-advanced',
		'title'	    => __( 'Advanced', 'schema-wp' ),
		'content'	=>
			'<p>' . __( 'This screen provides advanced options such as deleting plugin data on uninstall.', 'schema-wp' ) . '</p>' .
			'<p>' . __( 'A description of all the options are provided beside their input boxes.', 'schema-wp' ) . '</p>'
	) );

	do_action( 'schema_wp_settings_contextual_help', apply_filters( 'schema_wp_contextual_help', $screen ) );
}
