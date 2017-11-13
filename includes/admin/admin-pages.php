<?php
/**
 *  Determines whether the current admin page is an Schema admin page.
 *
 *  Only works after the `wp_loaded` hook, & most effective
 *  starting on `admin_menu` hook.
 *
 *  @since 1.0
 *  @return bool True if Schema admin page.
 */
function schema_wp_is_admin_page() {

	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		$ret = false;
	}

	if( ! isset( $_GET['page'] ) ) {
		$ret = false;
	}

	$page  = isset( $_GET['page'] ) ? $_GET['page'] : '';
	
	$pages = array(
		'schema',
		'schema-extensions',
		'schema-wp-getting-started',
		'schema-wp-what-is-new',
		'schema-wp-credits'
	);

	$ret = in_array( $page, $pages );

	return apply_filters( 'schema_wp_is_admin_page', $ret );
}
