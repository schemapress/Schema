<?php
/**
 * Misc functions
 *
 * @package     Schema
 * @subpackage  Functions/Formatting
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get plugin setting field value 
 *
 * @param string $key setting name, $format, $default value
 * @since 1.0
 * @return string
 */
function schema_wp_get_setting( $key, $default = false ) {
	
	if ( ! isset($key) ) return;
	$schema_wp  			= new stdClass();
	$schema_wp->settings	= new Schema_WP_Settings;
	$setting				= $schema_wp->settings->get( $key, $default );
			
	return $setting;
}


/**
 * Get Currencies
 *
 * @since 1.0
 * @return array $currencies A list of the available currencies
 */
function schema_wp_get_currencies() {

	$currencies = array(
		'USD' => __( 'US Dollars', 'schema-wp' ),
		'EUR' => __( 'Euros', 'schema-wp' ),
		'AUD' => __( 'Australian Dollars', 'schema-wp' ),
		'BDT' => __( 'Bangladeshi Taka', 'schema-wp' ),
		'BRL' => __( 'Brazilian Real', 'schema-wp' ),
		'BGN' => __( 'Bulgarian Lev', 'schema-wp' ),
		'CAD' => __( 'Canadian Dollars', 'schema-wp' ),
		'CLP' => __( 'Chilean Peso', 'schema-wp' ),
		'CNY' => __( 'Chinese Yuan', 'schema-wp' ),
		'COP' => __( 'Colombian Peso', 'schema-wp' ),
		'HRK' => __( 'Croatia Kuna', 'schema-wp' ),
		'CZK' => __( 'Czech Koruna', 'schema-wp' ),
		'DKK' => __( 'Danish Krone', 'schema-wp' ),
		'DOP' => __( 'Dominican Peso', 'schema-wp' ),
		'EGP' => __( 'Egyptian Pound', 'schema-wp' ),
		'HKD' => __( 'Hong Kong Dollar', 'schema-wp' ),
		'HUF' => __( 'Hungarian Forint', 'schema-wp' ),
		'ISK' => __( 'Icelandic Krona', 'schema-wp' ),
		'IDR' => __( 'Indonesia Rupiah', 'schema-wp' ),
		'INR' => __( 'Indian Rupee', 'schema-wp' ),
		'ILS' => __( 'Israeli Shekel', 'schema-wp' ),
		'JPY' => __( 'Japanese Yen', 'schema-wp' ),
		'KIP' => __( 'Lao Kip', 'schema-wp' ),
		'MYR' => __( 'Malaysian Ringgits', 'schema-wp' ),
		'MXN' => __( 'Mexican Peso', 'schema-wp' ),
		'NPR' => __( 'Nepali Rupee', 'schema-wp' ),
		'NGN' => __( 'Nigerian Naira', 'schema-wp' ),
		'NOK' => __( 'Norwegian Krone', 'schema-wp' ),
		'NZD' => __( 'New Zealand Dollar', 'schema-wp' ),
		'PYG' => __( 'Paraguayan Guaraní', 'schema-wp' ),
		'PHP' => __( 'Philippine Pesos', 'schema-wp' ),
		'PLN' => __( 'Polish Zloty', 'schema-wp' ),
		'GBP' => __( 'Pounds Sterling', 'schema-wp' ),
		'RON' => __( 'Romanian Leu', 'schema-wp' ),
		'RUB' => __( 'Russian Ruble', 'schema-wp' ),
		'SGD' => __( 'Singapore Dollar', 'schema-wp' ),
		'ZAR' => __( 'South African Rand', 'schema-wp' ),
		'KRW' => __( 'South Korean Won', 'schema-wp' ),
		'SEK' => __( 'Swedish Krona', 'schema-wp' ),
		'CHF' => __( 'Swiss Franc', 'schema-wp' ),
		'TWD' => __( 'Taiwan New Dollars', 'schema-wp' ),
		'THB' => __( 'Thai Baht', 'schema-wp' ),
		'TRY' => __( 'Turkish Lira', 'schema-wp' ),
		'AED' => __( 'United Arab Emirates Dirham', 'schema-wp' ),
		'VND' => __( 'Vietnamese Dong', 'schema-wp' ),
	);

	return apply_filters( 'schema_wp_currencies', $currencies );
}


/**
 * Get the store's set currency
 *
 * @since 1.0
 * @return string The currency code
 */
function schema_wp_get_currency() {
	$currency = schema_wp()->settings->get( 'currency', 'USD' );
	return apply_filters( 'schema_wp_currency', $currency );
}


/**
 * Get corporate contacts types
 *
 * @since 1.0
 * @return array $corporate_contacts_types A list of the available types
 */
function schema_wp_get_corporate_contacts_types() {

	$corporate_contacts_types = array(
		'customer_support'		=> __( 'Customer Support', 'schema-wp' ),
		'technical_support'		=> __( 'Technical Support', 'schema-wp' ),
		'billing_support'		=> __( 'Billing Support', 'schema-wp' ),
		'bill_payment'			=> __( 'Bill Payment', 'schema-wp' ),
		'sales'					=> __( 'Sales', 'schema-wp' ),
		'reservations'			=> __( 'Reservations', 'schema-wp' ),
		'credit_card_support'	=> __( 'Credit Card Support', 'schema-wp' ),
		'emergency'				=> __( 'Emergency', 'schema-wp' ),
		'baggage_tracking'		=> __( 'Baggage Tracking', 'schema-wp' ),
		'roadside_assistance'	=> __( 'Roadside Assistance', 'schema-wp' ),
		'package_tracking'		=> __( 'Package Tracking', 'schema-wp' ),
	);

	return apply_filters( 'schema_wp_corporate_contacts_types', $corporate_contacts_types );
}


/**
 * Convert an object to an associative array.
 *
 * Can handle multidimensional arrays
 *
 * @since 1.0
 *
 * @param unknown $data
 * @return array
 */
function schema_wp_object_to_array( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		$result = array();
		foreach ( $data as $key => $value ) {
			$result[ $key ] = schema_wp_object_to_array( $value );
		}
		return $result;
	}
	return $data;
}


/**
 * Get publisher array
 *
 * @since 1.2
 * @return array
 */
function schema_wp_get_publisher_array() {
	
	$publisher = array();
	
	$name = schema_wp_get_setting( 'name' );
	
	if ( empty($name) ) return;
	
	$logo = esc_attr( stripslashes( schema_wp_get_setting( 'logo'  ) ) );
	
	$publisher = array(
		"@type"	=> "Organization",	// default required value
		"name"	=> $name,
		"logo"	=> array(
    		"@type" => "ImageObject",
    		"url" => $logo,
    		"width" => 600,
			"height" => 60
		)
	);
	
	return apply_filters( 'schema_wp_publisher', $publisher );
}


/**
 * Get author array
 *
 * @since 1.5.3
 * @return array
 */
function schema_wp_get_author_array( $post_id = null ) {
	
	global $post;
	
	// Set post ID
	If ( ! isset($post_id) ) $post_id = $post->ID;
	
	$jason = array();
	
	// Get author from post content
	$content_post	= get_post($post_id);
	$post_author	= get_userdata($content_post->post_author);
	$email 			= $post_author->user_email; 
	
	// Debug
	//print_r($post_author);exit;
	
	$author = array (
		'@type'	=> 'Person',
		'name'	=> apply_filters ( 'schema_wp_filter_author_name', $post_author->display_name ),
		'url'	=> esc_url( get_author_posts_url( $post_author->ID ) )
	);
	
	if ( get_the_author_meta( 'description', $post_author->ID ) ) {
		$author['description'] = strip_tags( get_the_author_meta( 'description', $post_author->ID ) );
	}
	
	if ( schema_wp_validate_gravatar($email) ) {
		// Default = 96px, since it is a squre image, width = height
		$image_size	= apply_filters( 'schema_wp_get_author_array_img_size', 96); 
		$image_url	= get_avatar_url( $email, $image_size );

		if ( $image_url ) {
			$author['image'] = array (
				'@type'		=> 'ImageObject',
				'url' 		=> $image_url,
				'height' 	=> $image_size, 
				'width' 	=> $image_size
			);
		}
	}
	
	
	// sameAs
	$website 	= esc_attr( stripslashes( get_the_author_meta( 'user_url', $post_author->ID ) ) );
	$google 	= esc_attr( stripslashes( get_the_author_meta( 'google', $post_author->ID ) ) );
	$facebook 	= esc_attr( stripslashes( get_the_author_meta( 'facebook', $post_author->ID) ) );
	$twitter 	= esc_attr( stripslashes( get_the_author_meta( 'twitter', $post_author->ID ) ) );
	$instagram 	= esc_attr( stripslashes( get_the_author_meta( 'instagram', $post_author->ID ) ) );
	$youtube 	= esc_attr( stripslashes( get_the_author_meta( 'youtube', $post_author->ID ) ) );
	$linkedin 	= esc_attr( stripslashes( get_the_author_meta( 'linkedin', $post_author->ID ) ) );
	$myspace 	= esc_attr( stripslashes( get_the_author_meta( 'myspace', $post_author->ID ) ) );
	$pinterest 	= esc_attr( stripslashes( get_the_author_meta( 'pinterest', $post_author->ID ) ) );
	$soundcloud = esc_attr( stripslashes( get_the_author_meta( 'soundcloud', $post_author->ID ) ) );
	$tumblr 	= esc_attr( stripslashes( get_the_author_meta( 'tumblr', $post_author->ID ) ) );
	$github 	= esc_attr( stripslashes( get_the_author_meta( 'github', $post_author->ID ) ) );
	
	$sameAs_links = array( $website, $google, $facebook, $twitter, $instagram, $youtube, $linkedin, $myspace, $pinterest, $soundcloud, $tumblr, $github);
	
	$social = array();
	
	// Remove empty fields
	foreach( $sameAs_links as $sameAs_link ) {
		if ( $sameAs_link != '' ) $social[] = $sameAs_link;
	}
	
	if ( ! empty($social) ) {
		$author["sameAs"] = $social;
	}
	
	return apply_filters( 'schema_wp_author', $author );
}


/**
 * Validate gravatar by email
 *
 * Check if email has a gravatar photo
 * @since 1.5.3
 * @return true or false
 */
function schema_wp_validate_gravatar($email) {
	// Craft a potential url and test its headers
	$hash = md5($email);
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = FALSE;
	} else {
		$has_valid_avatar = TRUE;
	}
	return $has_valid_avatar;
}

/**
 * Get an array of enabled post types
 *
 * @since 1.4
 * @return array of enabled post types, schema type
 */
function schema_wp_cpt_get_enabled() {
	
	$cpt_enabled = array();
	
	$args = array(
					'post_type'			=> 'schema',
					'post_status'		=> 'publish',
					'posts_per_page'	=> -1
				);
				
	$schemas_query = new WP_Query( $args );
	
	$schemas = $schemas_query->get_posts();
	
	// If there is no schema types set, return and empty array
	if ( empty($schemas) ) return array();
	
	foreach( $schemas as $schema ) : 
		
		// Get post meta
		$schema_type			= get_post_meta( $schema->ID, '_schema_type'			, true );
		$schema_type_sub_pre	= get_post_meta( $schema->ID, '_schema_article_type'	, true );
		$schema_type_sub		= ( $schema_type_sub_pre == 'General' ) ? $schema_type : $schema_type_sub_pre;
		$schema_post_types 		= get_post_meta( $schema->ID, '_schema_post_types'	, true );
		$schema_categories 		= schema_wp_get_categories( $schema->ID );
		
		// Build our array
		$cpt_enabled[] = array (
									'id'			=>	$schema->ID,
									'type'			=>	$schema_type,
									'type_sub'		=>	$schema_type_sub,
									'post_type'		=>	$schema_post_types,
									'categories'	=>	$schema_categories
								);
		
	endforeach;
 	
	// debug
	//echo '<pre>'; print_r($cpt_enabled); echo '</pre>'; exit;
	
	return apply_filters('schema_wp_cpt_enabled', $cpt_enabled);
}



/**
 * Get an array of enabled post types
 *
 * @since 1.4
 * @return array of enabled post types, schema type
 */
function schema_wp_get_media( $id = null) {
	
	global $post;
	
	if ( ! isset( $id ) ) $id = $post->ID;
	
	$media = array();
	
	// Featured image
	$image_attributes	= wp_get_attachment_image_src( get_post_thumbnail_id($id), 'full' );
	$image_url			= $image_attributes[0];
	$image_width		= ( $image_attributes[1] > 696 ) ? $image_attributes[1] : 696; // Images should be at least 696 pixels wide
	$image_height		= $image_attributes[2];
	
	// Thesis 2.x Post Image
	$my_theme = wp_get_theme();
	if ( $my_theme->get( 'Name' ) == 'Thesis') {
		$image_attributes	= get_post_meta( $id, '_thesis_post_image', true);
		if ( ! empty($image_attributes) ) {
			$image_url			= $image_attributes['image']['url'];
			// Make sure url is valid
			if ( filter_var( $image_url, FILTER_VALIDATE_URL ) === FALSE ) {
    			//die('Not a valid URL');
				$image_url			= get_site_url() . $image_attributes['image']['url'];
			}
			$image_width		= ( $image_attributes['image']['width'] > 696 ) ? $image_attributes['image']['width'] : 696;
			$image_height		= $image_attributes['image']['height'];
		}
	}
	
	// Try something else...
	// @since 1.5.4
	if ( ! isset($image_url) || $image_url == '' ) {
		if ( $post->post_content ) {
			$Document = new DOMDocument();
			@$Document->loadHTML( $post->post_content );
			$DocumentImages = $Document->getElementsByTagName( 'img' );

			if ( $DocumentImages->length ) {
				$image_url 		= $DocumentImages->item( 0 )->getAttribute( 'src' );
				$image_width 	= ( $DocumentImages->item( 0 )->getAttribute( 'width' ) > 696 ) ? $DocumentImages->item( 0 )->getAttribute( 'width' ) : 696;
				$image_height	= $DocumentImages->item( 0 )->getAttribute( 'height' );
			}
		}
	}
			
	// Check if there is no image, then return an empy array
	// @since 1.4.3 
	if ( ! isset($image_url) || $image_url == '' ) return $media;
	// @since 1.4.4
	if ( ! isset($image_width) || $image_width == '' ) return $media;
	if ( ! isset($image_height) || $image_height == '' ) return $media;
	
	$media = array (
		'@type' => 'ImageObject',
			'url' => $image_url,
			'width' => $image_width,
			'height' => $image_height,
		);
	
	// debug
	//echo '<pre>'; print_r($media); echo '</pre>';
	
	return apply_filters( 'schema_wp_filter_media', $media );
}



add_action( 'wp_insert_post', 'schema_wp_add_ref', 10, 1 );
/**
 * Add schema reference Id
 * 
 * To allow extentions to add their own meta boxes to a specific Schema type
 * @since 1.4.4
 * @return array of enabled post types, schema type
 */
function schema_wp_add_ref($post_id) {
	
	if ( ! isset( $_POST['post_status'] ) ) return false;
    
	if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
		
		schema_update_meta_ref( $post_id );
    }
	
	return true;
}


/**
 * Update post meta with a ref Schema Id
 *
 * @param int $post_id The post ID.
 * @since 1.4.4
 */
function schema_update_meta_ref( $post_id ) {
	
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return false;

	// Get post type from current screen
	$current_screen = get_current_screen();
	$post_type = $current_screen->post_type;
	
	foreach( $schemas_enabled as $schema_enabled ) : 
	
		// Debug
		//echo '<pre>'; print_r($schema_enabled); echo '</pre>';exit; 
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
	
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
			
			// Get schema post id
			$schema_id = $schema_enabled['id'];
			// insert schema post id into post mea
			update_post_meta( $post_id, '_schema_ref', $schema_id);
		}
		
	endforeach;
	
	return true;	
}


add_action( 'save_post', 'schema_save_ref', 10, 3 );
/**
 * Save post metadata when a Schema post is saved.
 * Add schema reference Id
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 * @since 1.4.4
 */
function schema_save_ref( $post_id, $post, $update ) {
	
	if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) 
		return $post_id;
		
	$slug = 'schema';

    // If this isn't a 'schema' post, don't update it.
    if ( $slug != $post->post_type ) {
        return $post_id;
    }
	
	// If this is just a revision, don't save ref.
	if ( wp_is_post_revision( $post_id ) )
		 return $post_id;
		
    // - Update the post's metadata.
	schema_update_all_meta_ref( $post_id );
	
	// Debug
	//$msg = 'Is this un update? ';
  	//$msg .= $update ? 'Yes.' : 'No.';
  	//wp_die( $msg );
	
	 return $post_id;
}



/**
 * Update post meta with a ref Schema Id for post types
 *
 * @param int $schema_id The schema post ID.
 * @since 1.4.4
 */
function schema_update_all_meta_ref( $schema_id ) {
	
	if ( ! isset( $schema_id ) ) return;
	
	// Get enabled post types array
	$schema_type = get_post_meta( $schema_id, '_schema_post_types' , true );
	
	// Debug
	//echo '<pre>'; print_r($schema_type); echo '</pre>';exit; 
	
	if ( ! is_array( $schema_type ) || empty( $schema_type) ) return false;
	 
	foreach( $schema_type as $schema_enabled ) :  
		
		// Get all posts within this specific post type
		$posts = get_posts( array( 'post_type' => $schema_enabled, 'numberposts' => -1 ) );
		
		foreach($posts as $p) :
			// - Update the post's metadata.
			update_post_meta( $p->ID, '_schema_ref', $schema_id);
		 endforeach;

    endforeach;
	
	return true;
	
}


/**
 * Get post single category,
 * the first category
 *
 * @param int $post_id The post ID.
 * @since 1.4.5
 */
function schema_wp_get_post_category( $post_id ) {
	
	global $post;
	
	if ( ! isset( $post_id ) ) $post_id = $post->ID;
	
	$cats		= get_the_category($post_id);
	$cat		= !empty($cats) ? $cats : array();
	$category	= (isset($cat[0]->cat_name)) ? $cat[0]->cat_name : '';
   
   return $category;
}

	
/**
 * Get post tags separate by commas,
 * to be used as schema keywords for BlogPosting
 *
 * @param int $post_id The post ID.
 * @since 1.4.5
 */
function schema_wp_get_post_tags( $post_id ) {
	
	global $post;
	
	if ( ! isset( $post_id ) ) $post_id = $post->ID;
	
	$tags = '';
	$posttags = get_the_tags();
    if ($posttags) {
       $taglist = "";
       foreach($posttags as $tag) {
           $taglist .=  $tag->name . ', '; 
       }
      $tags =  rtrim($taglist, ", ");
   }
   
   return $tags;
}



add_action('the_post',				'schema_wp_auto_featured_img_featured');
add_action('save_post',				'schema_wp_auto_featured_img_featured');
add_action('draft_to_publish',		'schema_wp_auto_featured_img_featured');
add_action('new_to_publish',		'schema_wp_auto_featured_img_featured');
add_action('pending_to_publish',	'schema_wp_auto_featured_img_featured');
add_action('future_to_publish', 	'schema_wp_auto_featured_img_featured');
/**
 * Set Featured image automatically while adding or updating the post.
 *
 * @since 1.4.6
 */
function schema_wp_auto_featured_img_featured() {
	
	global $post;
	
	$auto_featured_img = schema_wp_get_setting( 'auto_featured_img' );
	
	if ( $auto_featured_img == true ) {
		
		$schema_enabled_spt = schema_wp_cpt_get_enabled();
		
		if ( empty($schemas_enabled) ) return;
		
		$post_type = get_post_type();
		
		foreach( $schemas_enabled as $schema_enabled ) : 
		
			// Get Schema enabled post types array
			$schema_cpt = $schema_enabled['post_type'];
		
			if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {

				$already_has_thumb = has_post_thumbnail($post->ID);
		
				if ( ! $already_has_thumb )  {
					$attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );
					if ($attached_image) {
						foreach ($attached_image as $attachment_id => $attachment) {
							set_post_thumbnail($post->ID, $attachment_id);
						} // end foreach
					} // end if
				} // end if
			}
		
		endforeach;
	} // end if
}


/**
 * Get an array of schema enabed categories
 * 
 * @since 1.4.7
 * @return array of enabled categories, schema type
 */

function schema_wp_get_categories( $post_id ) {
	
	global $post;
	
	if ( ! isset($post_id) ) $post_id = $post->ID;
	
	$post_categories	= wp_get_post_categories( $post_id );
	$categories			= array();
     
	if ( empty($post_categories) ) return $categories;
		
	$cats = array();
		
	foreach( $post_categories as $c ){
    	$cat	= get_category( $c );
		$cats[]	= $cat->slug;
	}
	
	if ( empty($cats) ) return $categories;
	
	// Flat
	$categories = schema_wp_array_flatten($cats);
	
	return apply_filters( 'schema_wp_filter_categories', $categories );
}


/**
 * Flatten an array
 * 
 * @since 1.4.7
 * @return flat array
 */
function schema_wp_array_flatten($array) {

	$return = array();
	foreach ($array as $key => $value) {
	if (is_array($value)){ $return = array_merge($return, array_flatten($value));}
		else {$return[$key] = $value;}
	}
	
	return $return;
}


add_action( 'save_post', 'schema_save_categories', 10, 3 );
/**
 * Save categories when a Schema post is saved.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 * @since 1.4.7
 */
function schema_save_categories( $post_id, $post, $update ) {
	
	if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) 
        return;
		
	$slug = 'schema';

    // If this isn't a 'schema' post, don't update it.
    if ( $slug != $post->post_type ) {
        return;
    }
	
	// If this is just a revision, don't save ref.
	if ( wp_is_post_revision( $post_id ) )
		return;
		
    // - Update the post's metadata.
	$post_categories = schema_wp_get_categories( $post_id );
	
	update_post_meta($post_id, '_schema_categories', $post_categories);
}


/**
 * Get supported Article types  
 *
 * @since 1.5.3
 * @return array 
 */
function schema_wp_get_support_article_types() {

	$support_article_types = array( 'Article', 'BlogPosting', 'NewsArticle', 'Report', 'ScholarlyArticle', 'TechArticle' );
	
	return apply_filters( 'schema_wp_support_article_types', $support_article_types );
}


/**
 * Get comments   
 *
 * @since 1.5.4
 * @return array 
 */
function schema_wp_get_comments() {
		
	global $post;
	
	//$comments_number = get_comments_number($post->ID);
		
	$number	= apply_filters( 'schema_wp_do_comments', '10'); // default = 10
		
	$Comments = array();
	$PostComments = get_comments( array( 'post_id' => $post->ID, 'number' => $number, 'status' => 'approve', 'type' => 'comment' ) );

	if ( count( $PostComments ) ) {
		foreach ( $PostComments as $Item ) {
			$Comments[] = array (
					'@type' => 'Comment',
					'dateCreated' => $Item->comment_date,
					'description' => $Item->comment_content,
					'author' => array (
						'@type' => 'Person',
						'name' => $Item->comment_author,
						'url' => $Item->comment_author_url,
				),
			);
		}

		return apply_filters( 'schema_wp_filter_comments', $Comments );
	}
}


/**
* Create post post box
*
* Uses class Schema_Custom_Add_Meta_Box
*
* @since 1.5.7
* @return true 
*/
function schema_wp_getAuthor() {	
	
	$Author = array
	(
		'@type' => 'Person',
		'name' => get_the_author(),
		'url' => esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	);

	if ( get_the_author_meta( 'description' ) )	{
		$Author['description'] = get_the_author_meta( 'description' );
	}
	
	$author_img_url = get_avatar_url( get_the_author_meta( 'user_email' ), 96 );
	
	if ( $AuthorImage ) {
		$Author['image'] = array
		(
			'@type' => 'ImageObject',
			'url' => $author_img_url,
			'height' => 96,
			'width' => 96
		);
	}

	return $Author;
}


/**
* Create post post box
*
* Uses class Schema_Custom_Add_Meta_Box
*
* @since 1.5.7
* @return true 
*/
function schema_wp_do_post_meta( $args ) {
	
	if ( empty( $args ) ) return;
	
	$id 		 = $args['id'];
	$title 		 = $args['title'];
	$schema_type = $args['type'];
	$fields 	 = $args['fields'];
	
	if ( empty( $fields ) ) return;
	
	/**
	* Get enabled post types to create a meta box on
	*/
	$schemas_enabled = array();
	
	// Get schame enabled array
	$schemas_enabled = schema_wp_cpt_get_enabled();
	
	if ( empty($schemas_enabled) ) return;

	// Get post type from current screen
	$current_screen = get_current_screen();
	$post_type = $current_screen->post_type;
	
	foreach( $schemas_enabled as $schema_enabled ) : 
		
		$type = $schema_enabled['type'];
		
		if ( ! isset($type) || $type == '' ) return;
		
		// Get Schema enabled post types array
		$schema_cpt = $schema_enabled['post_type'];
	
		if ( ! empty($schema_cpt) && in_array( $post_type, $schema_cpt, true ) ) {
			
			if ( $type == $schema_type && $schema_enabled['post_type'][0] == $post_type ) {
				$new_post_meta = new Schema_Custom_Add_Meta_Box( $id, $title, $fields, $post_type, 'normal', 'high', true );
			}

		}
		
		// debug
		//print_r($schema_enabled);
		
	endforeach;
	
	return true;
}
