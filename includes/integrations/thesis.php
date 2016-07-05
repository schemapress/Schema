<?php
/**
 * Thesis Theme 2.x integration
 *
 *
 * plugin url: http://diythemes.com/
 * @since 1.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Remove Thesis post meta from Schema post type
 *
 * @since 1.3
 * @return schema json-ld final output
 */
if (is_admin()) :
function my_remove_meta_boxes() {
	
	// Check if Thesis theme is active,
		// Popup comments do not work with Thesis theme
	$my_theme = wp_get_theme();
	if ( $my_theme->get( 'Name' ) == 'Thesis') {
		
		remove_meta_box('thesis_title_tag', 'schema', 'normal');
		remove_meta_box('thesis_meta_description', 'schema', 'normal');
		remove_meta_box('thesis_meta_keywords', 'schema', 'normal');
		remove_meta_box('thesis_meta_robots', 'schema', 'normal');
		remove_meta_box('thesis_canonical_link', 'schema', 'normal');
		remove_meta_box('thesis_html_body', 'schema', 'normal');
		remove_meta_box('thesis_post_content', 'schema', 'normal');
		remove_meta_box('thesis_post_image', 'schema', 'normal');
		remove_meta_box('thesis_post_thumbnail', 'schema', 'normal');
		remove_meta_box('thesis_redirect', 'schema', 'normal');
	}
}
add_action( 'do_meta_boxes', 'my_remove_meta_boxes', 99 );
endif;