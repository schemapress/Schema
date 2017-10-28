<?php
/**
 * @package Schema - Schema Post Type Columns 
 * @category Core
 * @author Hesham Zebida
 * @version 1.6.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('Schema_WP_CPT_columns') ) return;

$post_columns = new Schema_WP_CPT_columns('schema'); // if you want to replace and reorder columns then pass a second parameter as true

//add native column
$post_columns->add_column('title',
  array(
		'label'    => __('Name', 'schema-wp'),
		'type'     => 'native',
		'sortable' => true
	)
);
//custom field column
$post_columns->add_column('schema_type',
  array(
		'label'    => __('Schema Type', 'schema-wp'),
		'type'     => 'post_meta',
		'meta_key' => '_schema_type', //meta_key
		'orderby' => 'meta_value', //meta_value,meta_value_num
		'sortable' => true,
		'prefix' => "",
		'suffix' => "",
		'std' => __('Not set!'), // default value in case post meta not found
	)
);
$post_columns->add_column('schema_post_types',
  array(
		'label'    => __('Post Type', 'schema-wp'),
		'type'     => 'post_meta_array',
		'meta_key' => '_schema_post_types', //meta_key
		'orderby' => 'meta_value', //meta_value,meta_value_num
		'sortable' => true,
		'prefix' => "",
		'suffix' => "",
		'std' => __('-'), // default value in case post meta not found
	)
);
$post_columns->add_column('schema_cpt_post_count',
  array(
		'label'    => __('Content', 'schema-wp'),
		'type'     => 'cpt_post_count',
		'meta_key' => '_schema_post_types', //meta_key
		'orderby' => 'meta_value', //meta_value,meta_value_num
		'sortable' => true,
		'prefix' => "",
		'suffix' => "",
		'std' => __('-'), // default value in case post meta not found
	)
);

//remove columns
$post_columns->remove_column('post_type');
$post_columns->remove_column('categories');
$post_columns->remove_column('date');

// remove columns appended by 
$post_columns->remove_column('gadwp_stats');
$post_columns->remove_column('mashsb_shares');



add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
/**
 * Remove row actions: View.& Quick Edit links
 *
 * @since   1.6.7
 *
 * @param array $actions 
 *
 * @return array
 */
function remove_row_actions( $actions ) {
    if( get_post_type() === 'schema' ) {
        unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );
	}
		 
    return $actions;
}

