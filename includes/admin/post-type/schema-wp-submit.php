<?php
/**
 * Replace default submit box on Schema post type
 *
 * @since 1.4.7
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


  /*=========================================
         Custom Submit Box
  ==========================================*/
      /**
      * Loop throught custom post types and 
      * replace default submit box
      *
	  * @link	https://gist.github.com/zutigrm/226ca14074133d30320a
      * @since  1.0
      *
      */      
      function schema_wp_replace_submit_meta_box() 
      {
         // create a multidimensional array that will hold 
         // each custom post_type as a key, and custom 
         // post_type name will be it's value.
          $items = array( 
		   'schema' => 'Schema'
          );
          
          // now loop through $items array and remove, then
          // add submit meta box for each post type, by using
          // values from array to complete this.  
          foreach( $items as $item => $value )
          {
             remove_meta_box('submitdiv', $item, 'core'); // $item represents post_type
             add_meta_box('submitdiv', sprintf( __('%s'), $value ), 'schema_wp_submit_meta_box', $item, 'side', 'low'); // $value will be the output title in the box
          }
      }

      add_action( 'admin_menu', 'schema_wp_replace_submit_meta_box' );

 
      /**
      * Custom edit of default wordpress publish box callback
      * loop through each custom post type and remove default
      * submit box, replacing it with custom one that has
      * only submit button with custom text on it (add/update)
      *
      * @global $action, $post
      * @see wordpress/includes/metaboxes.php
      * @since  1.0
      *
      */ 
      function schema_wp_submit_meta_box() {
        global $action, $post;
       
        $post_type = $post->post_type; // get current post_type
        $post_type_object = get_post_type_object($post_type);
        $can_publish = current_user_can($post_type_object->cap->publish_posts);

        // again, use the same array. It is important
        // to put it in same order, so that it can
        // follow the right meta box
        $items = array( 
			'schema' => 'Schema'
        );

        // now create var $item that will take only right
        // post_type information for currently displayed
        // post_type. Because $post_type var will store
        // only current post_type, it will correspond to
        // the appropriate 'key' from the $items array.
        // This $item will hold only the string name of 
        // the post_type which will be used further in context
        // on appropriate places.
        $item = $items[$post_type];
		
        ?>
        <div class="submitbox" id="submitpost">
         <div id="major-publishing-actions">
         <?php
         do_action( 'post_submitbox_start' );
         ?>
         <div id="delete-action">
         <?php
         if ( current_user_can( "delete_post", $post->ID ) ) {
           if ( !EMPTY_TRASH_DAYS )
                $delete_text = __('Delete Permanently');
           else
                $delete_text = __('Move to Trash');
         ?>
         <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
         } //if ?>
        </div>
         <div id="publishing-action">
         <span class="spinner"></span>
         <?php
         if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private') ) || 0 == $post->ID ) {
			  if ( $can_publish ) : ?>
                
                <input name="original_publish" type="hidden" id="original_publish" accesskey="p" value="<?php esc_attr_e('Publish') ?>" />
        		<?php submit_button( __( 'Save' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
         <?php   
              endif;
			  
			   
         } else { ?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update ') . $item; ?>" />
                <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Save ') . $item; ?>" />
         <?php
         } //if ?>
         </div>
         <div class="clear"></div>
         </div>
         </div>
        <?php
      } //schema_wp_submit_meta_box()