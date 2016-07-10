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


add_action( 'schema_wp_output_knowledge_graph', 'schema_wp_yoast_knowledge_graph_remove' );
/*
* Remove Knowledge Graph
*
* @since 1.5.6
*/
function schema_wp_yoast_knowledge_graph_remove( $knowledge_graph ) {
	// Run only on front page and make sure Yoast SEO isn't active
	if (is_front_page() && defined('WPSEO_VERSION') ) return;
	return $knowledge_graph;
}


add_action( 'schema_wp_output_sitelinks_search_box', 'schema_wp_yoast_sitelinks_search_box_remove' );
/*
* Remove bar menu
*
* @since 1.5.6
*/
function schema_wp_yoast_sitelinks_search_box_remove( $sitelinks_search_box ) {
	// Run only on front page and make sure Yoast SEO isn't active
	if (is_front_page() && defined('WPSEO_VERSION') ) return;
	return $sitelinks_search_box;
}
