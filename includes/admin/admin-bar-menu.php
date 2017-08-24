<?php
/*
*
*	Admin Bar Menu
*	
*	@since 1.5.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'admin_bar_menu', 'schema_wp_admin_bar_menu_items', 99 );
/*
* Add Google Rich Snippet Test Tool link to admin bar menu
*	
* @since 1.5.4
*/
function schema_wp_admin_bar_menu_items( $admin_bar ) {
		
	/* This print_r will show you the current contents of the admin menu bar, use it if you want to examine the $admin_bar array
	* echo "<pre>";
	* print_r($admin_bar);
	* echo "</pre>";
	*/
		
	// If it's admin page, then get out!
	if (is_admin()) return;
	
	// Get current page url
	$url =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
	// If user can't publish posts, then get out
	if ( ! current_user_can( 'publish_posts' ) ) return;
	
	$admin_bar->add_menu( array(
		'id'	=> 'schema-test-item',
		'title'	=> __('', 'schema-wp'),
		'href'	=> 'https://developers.google.com/structured-data/testing-tool/?url='.$url,
		'meta'	=> array(
			'title'		=> __('Structured Data Testing Tool', 'schema-wp'),
			'class'		=> 'schema_google_developers',
			'target'	=> __('_blank')
		),
	) );
}


// on backend area
//add_action( 'admin_head', 'schema_wp_admin_bar_styles' );
// on frontend area
add_action( 'wp_head', 'schema_wp_admin_bar_styles' );
/*
* Add styles to admin bar
*
* @since 1.5.4
*/
function schema_wp_admin_bar_styles() {
	
	if ( ! is_admin_bar_showing() ) return;
	
	?>
	<style type="text/css">
		/* admin bar */
		.schema_google_developers a {
			padding-left:30px !important;
			background:	transparent url('<?php echo SCHEMAWP_PLUGIN_URL; ?>assets/images/admin-bar/google-developers.png') 8px 50% no-repeat !important;
		}
		.schema_google_developers a:hover {
			background:	transparent url('<?php echo SCHEMAWP_PLUGIN_URL; ?>assets/images/admin-bar/google-developers-hover.png') 8px 50% no-repeat !important;
		}
	</style>
<?php
}
