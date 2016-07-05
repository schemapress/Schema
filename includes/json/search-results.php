<?php
/**
 * Main Search Results
 *
 * @since 1.0
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_sitelinks_search_box');
/**
 * The main function responsible for output schema json-ld into
 *
 * @since 1.0
 * @return schema json-ld final output
 */
function schema_wp_output_sitelinks_search_box() {
	
	// Run only on front page and make sure Yoast SEO isn't active
	if ( is_front_page() && !defined('WPSEO_VERSION') ) {
		
		$sitelinks_search_box	= schema_wp_get_setting( 'sitelinks_search_box' );
		$site_name_enable		= schema_wp_get_setting( 'site_name_enable' );
		$site_name				= schema_wp_get_setting( 'site_name' );
		$site_alternate_name	= schema_wp_get_setting( 'site_alternate_name' );
		
		if ( ! isset($sitelinks_search_box) ) return;
		
		echo PHP_EOL . '<script type="application/ld+json">' . PHP_EOL;
		echo '{' . PHP_EOL;
		echo '  "@context": "http://schema.org",' . PHP_EOL;
		echo '  "@type": "WebSite",' . PHP_EOL;
		
		if ( $site_name_enable ) {
			echo '  "name": "' . $site_name . '",' . PHP_EOL;
			if ( $site_alternate_name ) echo '  "alternateName": "' . $site_alternate_name . '",' . PHP_EOL;
		}
		
		echo '  "url": "' . get_site_url() . '/",' . PHP_EOL;
		echo '  "potentialAction": {' . PHP_EOL;
		echo '    "@type": "SearchAction",' . PHP_EOL;
		echo '    "target": "' . get_home_url() . '/?s={search_term}",' . PHP_EOL;
		echo '    "query-input": "required name=search_term"' . PHP_EOL;
		echo '  }' . PHP_EOL;
		echo '}' . PHP_EOL;
		echo '</script>' . PHP_EOL . PHP_EOL;
	}
}

add_action('wp_head', 'schema_wp_output_sitelinks_search_box_disable');
/**
 * Disable SiteLinks Search Box
 *
 * @since 1.0
 * @return meta
 */
function schema_wp_output_sitelinks_search_box_disable() {
	
	// Run only on front page and make sure Yoast SEO isn't active
	if ( is_front_page() && !defined('WPSEO_VERSION') ) {
		
		$sitelinks_search_box_disable	= schema_wp_get_setting( 'sitelinks_search_box_disable' );
		
		if ( ! isset($sitelinks_search_box_disable) ) return;
		
		echo "\n";
		echo '<!-- Tell Google not to show a Sitelinks search box -->';
		echo "\n";
		echo '<meta name="google" content="nositelinkssearchbox" />';
		echo "\n\n";
	}
}
