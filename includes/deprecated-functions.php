<?php

/**
 *  Deprecated Functions
 *
 *  @since 1.6
 *  @return void
 */



add_filter('schema_output', 'schema_wp_exclude_output' );
/**
 * Exclude Schema 
 *
 * @since 1.5.6
 */
function schema_wp_exclude_output( $schema ) {
	
	global $post;
	
	if ( empty($schema) ) return;
	
	$exclude = get_post_meta( $post->ID, '_schema_exclude' , true );
	
	//if ( isset($exclude) && $exclude  == '1' ) return array();
	if ( $exclude ) return;
	return $schema;
}