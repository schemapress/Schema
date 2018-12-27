<?php
/**
 * Admin Options Page
 *
 * @package     EDD
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, Pippin Williamson
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
function schema_wp_options_page() {

	$settings_tabs = schema_wp_get_settings_tabs();
	$settings_tabs = empty($settings_tabs) ? array() : $settings_tabs;
	$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tabs ) ? $_GET['tab'] : 'general';
	$sections      = schema_wp_get_settings_tab_sections( $active_tab );
	$key           = 'main';

	if ( is_array( $sections ) ) {
		$key = key( $sections );
	}

	$registered_sections = schema_wp_get_settings_tab_sections( $active_tab );
	$section             = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'], $registered_sections ) ? $_GET['section'] : $key;

	// Unset 'main' if it's empty and default to the first non-empty if it's the chosen section
	$all_settings = schema_wp_get_registered_settings();

	// Let's verify we have a 'main' section to show
	ob_start();
	do_settings_sections( 'schema_wp_settings_' . $active_tab . '_main' );
	$has_main_settings = strlen( ob_get_contents() ) > 0;
	ob_end_clean();

	$override = false;
	if ( false === $has_main_settings ) {
		unset( $sections['main'] );

		if ( 'main' === $section ) {
			foreach ( $sections as $section_key => $section_title ) {
				if ( ! empty( $all_settings[ $active_tab ][ $section_key ] ) ) {
					$section  = $section_key;
					$override = true;
					break;
				}
			}
		}
	}

	ob_start();
	?>
	<div class="wrap <?php echo 'wrap-' . $active_tab; ?>">
        <h1 class="wp-heading-inline"><?php _e('Schema', 'schema-wp'); echo ' <span style="font-size:12px;">Ver '.SCHEMAWP_VERSION.'</span>'; ?></h1>
		<h1 class="nav-tab-wrapper">
			<?php
			foreach( schema_wp_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
				) );

				// Remove the section from the tabs so we always end up at the main section
				$tab_url = remove_query_arg( 'section', $tab_url );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
            
            <a class="button-primary schema_wiz_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=schema-setup' ) ); ?>"><?php _e( 'Quick Configuration Wizard', 'schema-wp' ); ?></a>

		</h1>
		<?php

		$number_of_sections = is_array($sections) ? count( $sections ) : 0;
		$number = 0;
		if ( $number_of_sections > 1 ) {
			echo '<div><ul class="subsubsub">';
			foreach( $sections as $section_id => $section_name ) {
				echo '<li>';
				$number++;
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $active_tab,
					'section' => $section_id
				) );
				$class = '';
				if ( $section == $section_id ) {
					$class = 'current';
				}
				echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';

				if ( $number != $number_of_sections ) {
					echo ' | ';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		}
		?>
        
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php

				settings_fields( 'schema_wp_settings' );

				if ( 'main' === $section ) {
					do_action( 'schema_wp_settings_tab_top', $active_tab );
				}

				do_action( 'schema_wp_settings_tab_top_' . $active_tab . '_' . $section );

				do_settings_sections( 'schema_wp_settings_' . $active_tab . '_' . $section );

				do_action( 'schema_wp_settings_tab_bottom_' . $active_tab . '_' . $section  );

				// For backwards compatibility
				if ( 'main' === $section ) {
					do_action( 'schema_wp_settings_tab_bottom', $active_tab );
				}

				// If the main section was empty and we overrode the view with the next subsection, prepare the section for saving
				if ( true === $override ) {
					?><input type="hidden" name="schema_wp_section_override" value="<?php echo $section; ?>" /><?php
				}
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}
