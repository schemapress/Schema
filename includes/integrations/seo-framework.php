<?php
/**
 * The SEO Framework 
 *
 *
 * Integrate with SEO Framework plugin
 *
 * plugin url: https://wordpress.org/plugins/autodescription/
 * @since 1.5.6
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'schema_wp_filter_output_knowledge_graph', 'schema_wp_seo_framework_knowledge_graph_remove' );
/*
* Remove Knowledge Graph
*
* @since 1.5.6
*/
function schema_wp_seo_framework_knowledge_graph_remove( $knowledge_graph ) {
	// Run only on front page and make sure Yoast SEO isn't active
	if (is_front_page() && defined('THE_SEO_FRAMEWORK_VERSION') ) return;
	return $knowledge_graph;
}

add_filter( 'schema_wp_output_sitelinks_search_box', 'schema_wp_seo_framework_sitelinks_search_box_remove' );
/*
* Remove SiteLinks Search Box
*
* @since 1.5.6
*/
function schema_wp_seo_framework_sitelinks_search_box_remove( $sitelinks_search_box ) {
	// Run only on front page and make sure Yoast SEO isn't active
	if (is_front_page() && defined('THE_SEO_FRAMEWORK_VERSION') ) return;
	return $sitelinks_search_box;
}
