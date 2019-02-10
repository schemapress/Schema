<?php
/**
 * Admin Extensions
 *
 * @package     Schema
 * @subpackage  Admin Functions/Extensions
 * @copyright   Copyright (c) 2017, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.6.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Extensions Page
 *
 * Renders the extensions page content.
 *
 * @since 1.6.9.8
 *
 * @return void
 */
function schema_wp_admin_extensions_page() {
	/**
	 * Filters the extensions tabs.
	 *
	 * @param array $tabs Extensions tabs.
	 */
	$add_ons_tabs = (array) apply_filters( 'schema_wp_extensions_tabs', array(
		'premium'       => __('Premium', 'schema-wp'),
		'official-free' => __('Official Free', 'schema-wp')
	) );

	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $add_ons_tabs ) ? $_GET['tab'] : 'premium';

	ob_start();
	?>
	<div class="wrap" id="schema-wp-extensions">
		<h1>
			<?php _e( 'Extensions for Schema', 'schema-wp' ); ?>
			<span>
				&nbsp;&nbsp;<a href="https://schema.press/downloads/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=All%20Extensions" class="button-primary" title="<?php _e( 'Browse all extensions', 'schema-wp' ); ?>" target="_blank"><?php _e( 'Browse all extensions', 'schema-wp' ); ?></a>
			</span>
		</h1>
		<p><?php _e( 'These extensions <em><strong>add functionality</strong></em> to your Schema-powered site.', 'schema-wp' ); ?></p>
		<h2 class="nav-tab-wrapper">
			<?php schema_wp_admin_navigation_tabs( $add_ons_tabs, $active_tab, array( 'settings-updated' => false ) ); ?>
		</h2>
		<div id="tab_container">

			<?php if ( 'premium' === $active_tab ) : ?>
				<p><?php printf( __( 'Premium Extensions are only available with a Professional or Ultimate license. If you already have one of these licenses, simply <a href="%s">log in to your account</a> to download any of these extensions.', 'schema-wp' ), 'https://schema.press/account/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=Account' ); ?></p>
				<p><?php printf( __( 'If you have a Personal or Plus license, you can easily upgrade from your account page to <a href="%s">get access to all of these extensions</a>!', 'schema-wp' ), 'https://schema.press/account/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=Account' ); ?></p>
			<?php else : ?>
				<p><?php _e( 'Our official free extensions are available to all license holders!', 'schema-wp' ); ?></p>
			<?php endif; ?>

			<?php echo schema_wp_extensions_get_rest( $active_tab ); ?>
			<div class="schema-wp-extensions-footer">
				<a href="https://schema.press/downloads/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=All%20Extensions" class="button-primary" title="<?php _e( 'Browse all extensions', 'schema-wp' ); ?>" target="_blank"><?php _e( 'Browse all extensions', 'schema-wp' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	echo ob_get_clean();
}

/**
 * Extensions Get Product with REST
 *
 * Gets the extensions "products" with REST API, and filter results by category.
 *
 * @since 1.6.9.8
 *
 * @return void
 */
function schema_wp_extensions_get_rest( $tab = 'premium' ) {

	$cache = get_transient( 'schema_wp_extensions_feed_' . $tab );
	
	// debug
	//$cache = false;
	
	if ( false === $cache ) {
		
		$url 			= 'https://schema.press/downloads/';
		$api_request	= 'https://schema.press/edd-api/v2/products/';
		$api_response	= wp_remote_get( $api_request );
		
		if ( ! is_wp_error( $api_response ) ) {
			
			$extensions = json_decode( wp_remote_retrieve_body( $api_response ), true );
		
			if ( $extensions && ! empty($extensions['products'] ) ) {
		
				foreach ( $extensions['products'] as $key => $extension ) {
				
					// get extension info
					$info = $extension['info'];
				
					// exclude Schema core plugin
					if ($info['slug'] === 'schema' ) continue;
					
					// add premium
					if ( $tab === 'premium' ) {
						// exclude if not in this category
						$category = isset($info['category'][0]['slug']) ? $info['category'][0]['slug'] : '';
						if ( $category != $tab && $info['slug'] != 'schema-premium' ) continue;
					} else {
						// exclude if not in this category
						$category = isset($info['category'][0]['slug']) ? $info['category'][0]['slug'] : '';
						if ( $category != $tab ) continue;
					}
					
					
					// prepare info
					$ext_url 		= $url.$info['slug'].'/';
					$excerpt		= wp_trim_words( $info['excerpt'], 18, '...' );
					$get_extention 	= ($info['slug'] === 'schema-premium') ? __('Get Schema Premium', 'schema-wp') : __('Get this Extension', 'schema-wp');
				
					// prepare our output
					$cache .= '<div class="schema-wp-extension">';
					$cache .= '<h3 class="schema-wp-extension-title">'.$info['title'].'</h3>';
					$cache .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments"><img width="880" height="440" src="'.$info['thumbnail'].'" class="attachment-showcase size-showcase wp-post-image" alt="" title="Recurring Payments"></a>';
					$cache .= '<p>'.$excerpt.'</p>';
					$cache .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments" class="button-secondary">'.$get_extention.'</a>';
					$cache .= '</div>';
				} //end foreach
				
				if ( $cache ) {
					set_transient( 'schema_wp_extensions_feed_' . $tab, $cache, HOUR_IN_SECONDS );
				} 
			} //end if ( $extensions )
		
		} else { //end if ( ! is_wp_error( $api_response ) ) 
		
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'schema-wp' ) . '</div>';
		}
	} //end if ( false === $cache )  

	return $cache;
}

/**
 * Outputs navigation tabs markup in core screens.
 *
 * @since 1.6.9.8
 *
 * @param array  $tabs       Navigation tabs.
 * @param string $active_tab Active tab slug.
 * @param array  $query_args Optional. Query arguments used to build the tab URLs. Default empty array.
 */
function schema_wp_admin_navigation_tabs( $tabs, $active_tab, $query_args = array() ) {
	$tabs = (array) $tabs;

	if ( empty( $tabs ) ) {
		return;
	}

	/**
	 * Filters the navigation tabs immediately prior to output.
	 *
	 * @since 1.6.9.8
	 *
	 * @param array  $tabs Tabs array.
	 * @param string $active_tab Active tab slug.
	 * @param array  $query_args Query arguments used to build the tab URLs.
	 */
	$tabs = apply_filters( 'schema_wp_admin_navigation_tabs', $tabs, $active_tab, $query_args );

	foreach ( $tabs as $tab_id => $tab_name ) {
		$query_args = array_merge( $query_args, array( 'tab' => $tab_id ) );
		$tab_url    = add_query_arg( $query_args );

		printf( '<a href="%1$s" alt="%2$s" class="%3$s">%4$s</a>',
			esc_url( $tab_url ),
			esc_attr( $tab_name ),
			$active_tab == $tab_id ? 'nav-tab nav-tab-active' : 'nav-tab',
			esc_html( $tab_name )
		);
	}

	/**
	 * Fires immediately after the navigation tabs output.
	 *
	 * @since 1.6.9.8
	 *
	 * @param array  $tabs Tabs array.
	 * @param string $active_tab Active tab slug.
	 * @param array  $query_args Query arguments used to build the tab URLs.
	 */
	do_action( 'schema_wp_admin_after_navigation_tabs', $tabs, $active_tab, $query_args );
}
