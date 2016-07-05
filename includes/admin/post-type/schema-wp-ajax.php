<?php
/**
 * AJAX to validate event before publishing
 *
 * @package     Schema
 * @subpackage  Schema Custom Post Type
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

 
add_action('admin_enqueue_scripts-post.php', 'schema_wp_load_post_edit_jquery_js');   
add_action('admin_enqueue_scripts-post-new.php', 'schema_wp_load_post_edit_jquery_js');   
/**
 * Make sure that jQuery is loaded in the post edit page
 *
 * @link http://wordpress.stackexchange.com/questions/42013/prevent-post-from-being-published-if-custom-fields-not-filled?answertab=votes#tab-top
 * @since 1.4.7
 */
function schema_wp_load_post_edit_jquery_js(){
	global $post;
	if ( $post->post_type == 'schema' ) {
    	wp_enqueue_script('jquery');
	}
}


add_action('admin_head-post.php','schema_wp_publish_admin_hook');
add_action('admin_head-post-new.php','schema_wp_publish_admin_hook');
/**
 * Print script to post edit
 *
 * @since 1.4.7
 */
function schema_wp_publish_admin_hook(){
	
	global $post;
	
	if ( is_admin() && $post->post_type == 'schema' ) {
    
	?>
    <script language="javascript" type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#publish').click(function() {
                if(jQuery(this).data("valid")) {
                    return true;
                }
                var form_data = jQuery('#post').serializeArray();
                var data = {
                    action: 'schema_wp_pre_submit_validation',
                    security: '<?php echo wp_create_nonce( 'schema_wp_pre_publish_validation' ); ?>',
                    form_data: jQuery.param(form_data),
                };
                jQuery.post(ajaxurl, data, function(response) {
                    if (response.indexOf('true') > -1 || response == true) {
                        jQuery("#post").data("valid", true).submit();
                    } else {
                        alert("Error: " + response);
                        jQuery("#post").data("valid", false);

                    }
                    //hide loading icon, return Publish button to normal
                    jQuery('#ajax-loading').hide();
                    jQuery('#publish').removeClass('button-primary-disabled');
                    jQuery('#save-post').removeClass('button-disabled');
                });
                return false;
            });
        });
    </script>
    <?php
	}
}


add_action('wp_ajax_schema_wp_pre_submit_validation', 'schema_wp_pre_submit_validation');
/**
 * Pre submit validation
 *
 * @since 1.4.7
 */
function schema_wp_pre_submit_validation() {
	//simple Security check
	check_ajax_referer( 'schema_wp_pre_publish_validation', 'security' );
	
	//convert the string of data received to an array
	//from http://wordpress.stackexchange.com/a/26536/10406
	parse_str( $_POST['form_data'], $vars );
  
	if ( empty( $vars['_schema_post_types'] ) ) {
        _e('You must select at leat one post type!', 'schema-wp');
        die();
    }
	
	//everything ok, allow submission
	echo 'true';
	die();
}
