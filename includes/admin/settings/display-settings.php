<?php
/**
 * Admin Options Page
 *
 * @package     Schema
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function schema_wp_settings_admin() {

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], schema_wp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( schema_wp_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $tab_id
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'schema_wp_settings' );
				do_settings_fields( 'schema_wp_settings_' . $active_tab, 'schema_wp_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieve settings tabs
 *
 * @since 1.0
 * @return array $tabs
 */
function schema_wp_get_settings_tabs() {

	$tabs						= array();
	$tabs['general']			= __( 'General',			'schema-wp' );
	$tabs['knowledge_graph']	= __( 'Knowledge Graph',	'schema-wp' );
	$tabs['search_results']		= __( 'Search Results',		'schema-wp' );
	$tabs['misc']				= __( 'Misc',				'schema-wp' );
	
	//if( schema_wp()->settings->get( 'debug_mode', false ) ) {	
	//	$tabs['schema_wp_debug']     = __( 'Debug Assistant', 'schema-wp' );
	//}
	
	return apply_filters( 'schema_wp_settings_tabs', $tabs );
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.0
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function schema_wp_get_pages( $force = false ) {

	$pages_options = array( 0 => '' ); // Blank option

	if( ( ! isset( $_GET['page'] ) || 'schema' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}
