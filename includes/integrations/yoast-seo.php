<?php
/**
 * Yoast SEO 
 *
 *
 * Integrate with Yoast SEO plugin
 *
 * plugin url: https://wordpress.org/plugins/wordpress-seo/
 * @since 1.5.6
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'wpseo_json_ld_output', 'schema_wp_remove_yoast_json', 10, 1 );
/*
* Remove Yoast SEO plugin JSON-LD output
*
* @since 1.6.4
*/
function schema_wp_remove_yoast_json( $data ){
	
	$use_yoast_seo_json = schema_wp_get_option( 'use_yoast_seo_json' );
	
	if ( empty($use_yoast_seo_json) ) {
		$data = array();
	}
	
	return $data;
}


add_action( 'admin_init', 'schema_wp_yoast_seo_register_settings', 1 );
/*
* Register Yoast SEo plugin settings 
*
* @since 1.6.4
*/
function schema_wp_yoast_seo_register_settings() {
	
	if ( ! defined('WPSEO_VERSION') ) return;
	
	add_filter( 'schema_wp_settings_knowledge_graph', 'schema_wp_settings_knowledge_graph');
}

/*
* Add Yoast SEo plugin settings 
*
* @since 1.6.4
*/
function schema_wp_settings_knowledge_graph( $settings_knowledge_graph ) {

	$settings_knowledge_graph['organization']['use_yoast_seo_json'] = array(
		'id' => 'use_yoast_seo_json',
		'name' => __( 'Use Yoast SEO markup?', 'schema-wp' ),
		'desc' => '<span class="dashicons dashicons-warning"></span> '. __( 'Yoast SEO plugin is active!', 'schema-wp'). '<p>'. __('By default, Schema plugin will override Yoast SEO output. Check this box if you would like to disable Schema markup and use Yoast SEO output instead. (This will be enabled on Search Results feature as well)', 'schema-wp') . '</p>',
		'type' => 'checkbox'
	);
	
	return $settings_knowledge_graph;
}


add_action( 'schema_wp_output_knowledge_graph', 'schema_wp_yoast_knowledge_graph_remove' );
/*
* Remove Knowledge Graph
*
* @since 1.5.6
*/
function schema_wp_yoast_knowledge_graph_remove( $knowledge_graph ) {
	// Run only on front page and if Yoast SEO is active
	if ( ! is_front_page() ) return;
	
	$use_yoast_seo_json = schema_wp_get_option( 'use_yoast_seo_json' );
	
	if ( ! empty($use_yoast_seo_json) && defined('WPSEO_VERSION') ) {
		$knowledge_graph = array();
	}
	
	return $knowledge_graph;
}


add_action( 'schema_wp_output_sitelinks_search_box', 'schema_wp_yoast_sitelinks_search_box_remove' );
/*
* Remove SiteLinks & Search Box
*
* @since 1.5.6
*/
function schema_wp_yoast_sitelinks_search_box_remove( $sitelinks_search_box ) {
	// Run only on front page and if Yoast SEO is active
	if ( ! is_front_page() ) return;
	
	$use_yoast_seo_json = schema_wp_get_option( 'use_yoast_seo_json' );
	
	if ( ! empty($use_yoast_seo_json) && defined('WPSEO_VERSION') ) {
		$sitelinks_search_box = array();
	}
	
	return $sitelinks_search_box;
}
