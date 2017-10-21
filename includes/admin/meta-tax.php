<?php
/**
 * Schema Tax Meta
 *
 * @package     Schema
 * @subpackage  Schema Tax Meta
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (is_admin()){
  /* 
   * prefix of meta keys, optional
   */
  $prefix = 'schema_wp_';
  /* 
   * configure your meta box
   */
  $config = array(
    'id' => 'schema_wp_meta_box',				// meta box id, unique per meta box
    'title' => __('Schema', 'schema-wp'),		// meta box title
    'pages' => array('category', 'post_tag'),	// taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),						// list of meta fields (can be added by field arrays)
    'local_images' => false,					// Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false					// change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );
  
  
  /*
   * Initiate your meta box
   */
  $my_meta =  new Schema_Custom_Add_Meta_Tax($config);
  
  /*
   * Add fields to your meta box
   */
  
  //text field
  $my_meta->addText( $prefix.'sameAs', array('name' => __('sameAs ','schema-wp'),'desc' => __("URL of a reference Web page that unambiguously indicates the item's identity. E.g. the URL of the item's Wikipedia page, Freebase page, or official website.", 'schema-wp') ));
   
  /*
   * Don't Forget to Close up the meta box decleration
   */
  //Finish Meta Box Decleration
  $my_meta->Finish();
}
