<?php
/**
 * ThirstyAffiliates 
 *
 *
 * Integrate with ThirstyAffiliates plugin
 *
 * plugin url: https://wprichsnippets.com/
 * @since 1.6.9.1
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'schema_wp_post_types', 'schema_wp_thirstyaffiliates_remove_cpt' );
/*
* Unset ThirstyAffiliates post type "thirstylink", plugin shouldn't ever run here at this pont!
*
* @since 1.6.9.1
*/
function schema_wp_thirstyaffiliates_remove_cpt( $post_types ) {
	
	if (!is_plugin_active('thirstyaffiliates/thirstyaffiliates.php')) 
		return $post_types;
		
	unset($post_types['thirstylink']);
	
	return $post_types;
}
