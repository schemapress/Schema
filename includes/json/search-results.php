<?php
/**
 *	SiteLinks Search Box
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
	
	// Run only on front page 
	if ( is_front_page() ) {
		$output 				= '';
		$sitelinks_search_box	= schema_wp_get_option( 'sitelinks_search_box' );
		$site_name_enable		= schema_wp_get_option( 'site_name_enable' );
		$site_name				= schema_wp_get_option( 'site_name' );
		$site_alternate_name	= schema_wp_get_option( 'site_alternate_name' );
		
		if ( ! isset($sitelinks_search_box) || ! $sitelinks_search_box ) return;
		
		$output .= PHP_EOL . '<script type="application/ld+json">' . PHP_EOL;
		$output .= '{' . PHP_EOL;
		$output .= '  "@context": "http://schema.org",' . PHP_EOL;
		$output .= '  "@type": "WebSite",' . PHP_EOL;
		$output .= '  "@id": "#website",' . PHP_EOL;
		
		if ( $site_name_enable ) {
			$output .= '  "name": "' . $site_name . '",' . PHP_EOL;
			if ( $site_alternate_name ) $output .= '  "alternateName": "' . $site_alternate_name . '",' . PHP_EOL;
		}
		
		$output .= '  "url": "' . get_home_url() . '/",' . PHP_EOL;
		$output .= '  "potentialAction": {' . PHP_EOL;
		$output .= '    "@type": "SearchAction",' . PHP_EOL;
		$output .= '    "target": "' . get_home_url() . '/?s={search_term_string}",' . PHP_EOL;
		$output .= '    "query-input": "required name=search_term_string"' . PHP_EOL;
		$output .= '  }' . PHP_EOL;
		$output .= '}' . PHP_EOL;
		$output .= '</script>' . PHP_EOL . PHP_EOL;
		
		$output = apply_filters( 'schema_wp_output_sitelinks_search_box', $output );;
		
		echo $output;
	}
}


//add_action('wp_head', 'schema_wp_output_sitelinks_search_box_disable');
/**
 * Disable SiteLinks Search Box
 *
 * This function was disabled @since 1.5.9.2, I don't see it important!
 * @since 1.0
 * @return meta
 */
 /*
function schema_wp_output_sitelinks_search_box_disable() {
	
	// Run only on front page 
	if ( is_front_page() ) {
		
		$sitelinks_search_box_disable	= schema_wp_get_option( 'sitelinks_search_box_disable' );
		
		if ( isset($sitelinks_search_box_disable) && $sitelinks_search_box_disable == 1 ) {
			echo "\n";
			echo '<!-- Tell Google not to show a Sitelinks search box -->';
			echo "\n";
			echo '<meta name="google" content="nositelinkssearchbox" />';
			echo "\n\n";
		}
	}
}
*/
