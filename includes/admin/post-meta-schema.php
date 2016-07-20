<?php
/**
 * Schema Post Meta Box
 *
 * @package     Schema
 * @subpackage  Schema Post Meta
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Schema post meta
 *
 * @since 1.4
 */
$prefix = '_schema_';

/**
 * Schema main post meta 
 *
 * @since 1.4
 */
 

$fields_main = array(
	
	array( // Select box
		'label'	=> __('Type', 'schema-wp'), // <label>
		'desc'	=> __('Select Schema type.', 'schema-wp'), // description
		'id'	=> $prefix.'type', // field id and name
		'type'	=> 'select', // type of field
		'options' => apply_filters( 'schema_wp_types', array ( // array of options
			'Article' => array ( // array key needs to be the same as the option value
				'label' => __('Article', 'schema-wp'), // text displayed as the option
				'value'	=> __('Article', 'schema-wp'), // value stored for the option
			)
		)),
	), // end of array
);


/**
 * Schema Article post meta 
 *
 * @since 1.4
 */
$fields_article = array(
	
	array( // Select box
		'label'	=> __('Article Type', 'schema-wp'), // <label>
		'desc'	=> __('Select more specific article type.', 'schema-wp'), // description
		'tip'	=> __('It is recommended to set type BlogPosting for posts, and leave empty or set to General for page post type', 'schema-wp'),
		'id'	=> $prefix.'article_type', // field id and name
		'type'	=> 'select', // type of field
		'options' => array ( // array of options
			'General' => array ( // array key needs to be the same as the option value
				'label' => 'General', // text displayed as the option
				'value'	=> 'General' // value stored for the option
			),
			'BlogPosting' => array (
				'label' => 'BlogPosting',
				'value'	=> 'BlogPosting'
			),
			'NewsArticle' => array (
				'label' => 'NewsArticle',
				'value'	=> 'NewsArticle'
			),
			'Report' => array (
				'label' => 'Report',
				'value'	=> 'Report'
			),
			'ScholarlyArticle' => array (
				'label' => 'ScholarlyArticle',
				'value'	=> 'ScholarlyArticle'
			),
			'TechArticle' => array (
				'label' => 'TechArticle',
				'value'	=> 'TechArticle'
			)
		)
	),
);


/**
 * Post Types 
 *
 * @since 1.4
 */
$fields_post_types = array(
	
	array( // Post Types Select box
		'label'	=> '', // <label>
		'desc'	=> __('Enabled on specific custom post types', 'schema-wp'),  // description
		'id'	=> $prefix.'post_types', // field id and name
		'type'	=> 'cpt' // type of field
	),
);





/**
 * Instantiate the class with all variables to create a meta box
 * var $id string meta box id
 * var $title string title
 * var $fields array fields
 * var $page string|array post type to add meta box to
 * var $context string context where to add meta box at (normal, side)
 * var $priority string meta box priority (high, core, default, low) 
 * var $js bool including javascript or not
 */
$schema_box = new Schema_Custom_Add_Meta_Box( 'schema', __('Settings', 'schema-wp'), $fields_main, 'schema', 'normal', 'high', true );
$schema_article_box = new Schema_Custom_Add_Meta_Box( 'schema_article', __('Article', 'schema-wp'), $fields_article, 'schema', 'normal', 'high', true );
$schema_cpt_box = new Schema_Custom_Add_Meta_Box( 'schema_cpt', __('Post Types', 'schema-wp'), $fields_post_types, 'schema', 'side', 'default', true );
