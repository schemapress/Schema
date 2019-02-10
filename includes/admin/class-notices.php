<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Schema_WP_Admin_Notices {

	public function __construct() {

		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'schema_wp_dismiss_notices', array( $this, 'dismiss_notices' ) );
	}


	public function show_notices() {

		$class = 'updated';

		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && isset( $_GET['page'] ) && $_GET['page'] == 'schema' ) {
			$message = __( 'Settings updated.', 'schema-wp' );
			
			// do action after settings updated
			do_action( 'schema_wp_do_after_settings_updated' );
		}

		if ( isset( $_GET['schema_wp_notice'] ) && $_GET['schema_wp_notice'] ) {

			switch( $_GET['schema_wp_notice'] ) {

				case 'settings-imported' :

					$message = __( 'Settings successfully imported', 'schema-wp' );

					break;

			}
		}

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $class ) . '"><p><strong>' .  $message  . '</strong></p></div>';
		}

	}

	/**
	 * Dismiss admin notices when Dismiss links are clicked
	 *
	 * @since 1.0
	 * @return void
	 */
	function dismiss_notices() {
		if( ! isset( $_GET['schema_wp_dismiss_notice_nonce'] ) || ! wp_verify_nonce( $_GET['schema_wp_dismiss_notice_nonce'], 'schema_wp_dismiss_notice') ) {
			wp_die( __( 'Security check failed', 'schema-wp' ), __( 'Error', 'schema-wp' ), array( 'response' => 403 ) );
		}

		if( isset( $_GET['schema_wp_notice'] ) ) {
			update_user_meta( get_current_user_id(), '_schema_wp_' . $_GET['schema_wp_notice'] . '_dismissed', 1 );
			wp_redirect( remove_query_arg( array( 'schema_wp_action', 'schema_wp_notice' ) ) );
			exit;
		}
	}
}
new Schema_WP_Admin_Notices;
