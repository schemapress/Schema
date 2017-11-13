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
 * @return void
 */
function schema_add_ons_page() {
	
	$add_ons_tabs = apply_filters( 'schema_extensions_tabs', array( 'popular' => __( 'Popular', 'schema-wp' ), 'new' => __( 'New', 'schema-wp' ), 'all' => __( 'View all Integrations', 'schema-wp' ) ) );
	$active_tab   = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $add_ons_tabs ) ? $_GET['tab'] : 'popular';

	ob_start(); ?>
	<div class="wrap" id="edd-add-ons">
		<h1 class="wp-heading-inline"><?php echo __('Extentions for Schema', 'schema-wp' ); ?></h1>
		<a href="<?php echo admin_url( 'post-new.php?post_type=schema' ); ?>" class="page-title-action">Add New</a>
		<hr class="wp-header-end">
		<?php schema_display_product_tabs(); ?>
		<h2>
			<?php _e( 'Extensions and Integrations for Schema', 'schema-wp' ); ?>
			<span>
				&nbsp;&nbsp;<a href="https://schema.press/downloads/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=All%20Extensions" class="button-primary" target="_blank"><?php _e( 'Browse All Integrations', 'schema-wp' ); ?></a>
			</span>
		</h2>
		<p><?php _e( 'These <em><strong>add functionality</strong></em> to the Schema plugin.', 'schema-wp' ); ?></p>
		<div class="edd-add-ons-view-wrapper">
			<ul class="subsubsub">
				<?php
				$total_tabs = count( $add_ons_tabs );
				$i = 1;
				foreach( $add_ons_tabs as $tab_id => $tab_name ) {

					$tab_url = add_query_arg( array(
						'settings-updated' => false,
						'tab' => $tab_id
					) );

					if ( 'all' === $tab_id ) {
						$tab_url = 'https://schema.press/downloads/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=SchemaExtensionsPage&utm_content=All%20Extensions';
					}

					$active = $active_tab == $tab_id ? 'current' : '';

					echo '<li class="' . $tab_id . '">';
					echo '<a href="' . esc_url( $tab_url ) . '" class="' . $active . '">';
					echo esc_html( $tab_name );
					echo '</a>';

					if ( 'all' === $tab_id ) {
						$count = '99+';
					} else {
						$count = '29';
					}

					echo ' <span class="count">(' . $count . ')</span>';
					echo '</li>';

					if ( $i !== $total_tabs ) {
						echo ' | ';
					}

					$i++;
				}
				?>
			</ul>
		</div>
		<div id="tab_container">
			<?php echo edd_add_ons_get_feed( $active_tab ); ?>
			<?php //echo schema_display_news_page(); ?>
            <div class="clear"></div>
			<div class="edd-add-ons-footer">
				<a href="https://schema.press/downloads/?utm_source=plugin-extensions-page&utm_medium=plugin&utm_campaign=Schema%20Extensions%20Page&utm_content=All%20Extensions" class="button-primary" target="_blank"><?php _e( 'Browse All Extensions', 'schema-wp' ); ?></a>
			</div>
		</div><!-- #tab_container-->
	</div>
	<?php
	
	
	
	echo ob_get_clean();
}

/**
 * Extensions Get Feed
 *
 * Gets the extensions page feed.
 *
 * @since 1.6.9.8
 * @return void
 */
function edd_add_ons_get_feed( $tab = 'popular' ) {
	
	$cache = get_transient( 'schema_extensions_feed_' . $tab );
	
	$cache = false;
	
	if ( false === $cache ) {
		
		$url 			= 'https://wprichsnippets.com/add-ons/';
		$api_request	= 'https://wprichsnippets.com/edd-api/v2/products/';
		$api_response	= wp_remote_get( $api_request );
		$extensions		= json_decode( wp_remote_retrieve_body( $api_response ), true );
		
		if ($extensions) {
		
			foreach ( $extensions['products'] as $key => $extension ) {
			
				$info 		= $extension['info'];
			
				if ($info['slug'] === 'wprs' ) continue;
			
				$ext_url 	= $url.$info['slug'].'/';
				$sumarry	= wp_trim_words( $info['content'], 16, '...' );
			
				$cache = '<div class="edd-extension">';
				$cache .= '<h3 class="edd-extension-title">'.$info['title'].'</h3>';
				$cache .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments"><img width="880" height="440" src="'.$info['thumbnail'].'" class="attachment-showcase size-showcase wp-post-image" alt="" title="Recurring Payments"></a>';
				$cache .= '<p>'.$sumarry.'</p>';
				$cache .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments" class="button-secondary">'.__('Get this Extension', 'schema-wp').'</a>';
				$cache .= '</div>';
			}
		
		} else {
		
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'schema-wp' ) . '</div>';
		}
	} //end if

	if ( isset( $_GET['view'] ) && 'integrations' === $_GET['view'] ) {
		// Set a new campaign for tracking purposes
		//$cache = str_replace( 'EDDAddonsPage', 'EDDIntegrationsPage', $cache );
	}

	return $cache;
}


/**
 * Displays the product tabs for 'Products' and 'Apps and Integrations'
 *
 * @since 2.8.9
 */
function schema_display_product_tabs() {
	?>
	<h2 class="nav-tab-wrapper">
		<?php
		$tabs = array(
			'products' => array(
				'name' => 'Types',//edd_get_label_plural(),
				'url'  => admin_url( 'edit.php?post_type=schema' ),
			),
			'integrations' => array(
				'name' => __( 'Extensions and Integrations', 'schema-wp' ),
				'url'  => admin_url( 'edit.php?post_type=schema&page=schema-extensions&view=integrations' ),
			),
		);

		$tabs       = apply_filters( 'edd_add_ons_tabs', $tabs );
		$active_tab = isset( $_GET['page'] ) && $_GET['page'] === 'schema-extensions' ? 'integrations' : 'products';
		foreach( $tabs as $tab_id => $tab ) {

			$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

			echo '<a href="' . esc_url( $tab['url'] ) . '" class="nav-tab' . $active . '">';
			echo esc_html( $tab['name'] );
			echo '</a>';
		}
		?>
	</h2>
	<br />
	<?php
}


// include the news section in admin page
function schema_display_news_page(){
		
	$url 			= 'https://wprichsnippets.com/add-ons/';
	$api_request	= 'https://wprichsnippets.com/edd-api/v2/products/';
	$api_response	= wp_remote_get( $api_request );
	$extensions		= json_decode( wp_remote_retrieve_body( $api_response ), true );
		
	if ($extensions) {
		
		$out = '';
		
		foreach ( $extensions['products'] as $key => $extension ) {
			
			$info 		= $extension['info'];
			
			if ($info['slug'] === 'wprs' ) continue;
			
			$ext_url 	= $url.$info['slug'].'/';
			$sumarry	= wp_trim_words( $info['content'], 16, '...' );
			
			$out .= '<div class="edd-extension">';
			$out .= '<h3 class="edd-extension-title">'.$info['title'].'</h3>';
			$out .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments"><img width="880" height="440" src="'.$info['thumbnail'].'" class="attachment-showcase size-showcase wp-post-image" alt="" title="Recurring Payments"></a>';
			$out .= '<p>'.$sumarry.'</p>';
			$out .= '<a href="'.$ext_url.'?utm_source=plugin-extensions-page&amp;utm_medium=plugin&amp;utm_campaign=SchemaExtentionsPage&amp;utm_content='.$info['title'].'" title="Recurring Payments" class="button-secondary">'.__('Get this Extension', 'schema-wp').'</a>';
			$out .= '</div>';
		}
		
	} else {
		
		$out .= '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'schema-wp' ) . '</div>';
	}
	
	return $out;
}
	