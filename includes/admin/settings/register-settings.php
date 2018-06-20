<?php
/**
 * Register Settings
 *
 * @package     Schema
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0
 * @global $schema_wp_options Array of all the Schema Options
 * @return mixed
 */
function schema_wp_get_option( $key = '', $default = false ) {
	global $schema_wp_options;
	$value = ! empty( $schema_wp_options[ $key ] ) ? $schema_wp_options[ $key ] : $default;
	$value = apply_filters( 'schema_wp_get_option', $value, $key, $default );
	return apply_filters( 'schema_wp_get_option_' . $key, $value, $key, $default );
}

/**
 * Update an option
 *
 * Updates an edd setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the schema_wp_options array.
 *
 * @since 1.0
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 * @global $schema_wp_options Array of all the Schema Options
 * @return boolean True if updated, false if not.
 */
function schema_wp_update_option( $key = '', $value = false ) {

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = schema_wp_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings
	$options = get_option( 'schema_wp_settings' );

	// Let's let devs alter that value coming in
	$value = apply_filters( 'schema_wp_update_option', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update = update_option( 'schema_wp_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $schema_wp_options;
		$schema_wp_options[ $key ] = $value;
	}

	return $did_update;
}

/**
 * Remove an option
 *
 * Removes an edd setting value in both the db and the global variable.
 *
 * @since 1.0
 * @param string $key The Key to delete
 * @global $schema_wp_options Array of all the Schema Options
 * @return boolean True if removed, false if not.
 */
function schema_wp_delete_option( $key = '' ) {

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'schema_wp_settings' );

	// Next let's try to update the value
	if( isset( $options[ $key ] ) ) {

		unset( $options[ $key ] );

	}

	$did_update = update_option( 'schema_wp_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $schema_wp_options;
		$schema_wp_options = $options;
	}

	return $did_update;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array Schema settings
 */
function schema_wp_get_settings() {

	$settings = get_option( 'schema_wp_settings' );

	if( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings = is_array( get_option( 'schema_wp_settings_general' ) )    ? get_option( 'schema_wp_settings_general' )    : array();
		$knowledge_graph_settings = is_array( get_option( 'schema_wp_settings_knowledge_graph' ) )    ? get_option( 'schema_wp_settings_knowledge_graph' )    : array();
		$search_results_settings = is_array( get_option( 'schema_wp_settings_search_results' ) )    ? get_option( 'schema_wp_settings_search_results' )    : array();
		$ext_settings     = is_array( get_option( 'schema_wp_settings_extensions' ) ) ? get_option( 'schema_wp_settings_extensions' ) : array();
		$license_settings = is_array( get_option( 'schema_wp_settings_licenses' ) )   ? get_option( 'schema_wp_settings_licenses' )   : array();
		$advanced_settings    = is_array( get_option( 'schema_wp_settings_advanced' ) )       ? get_option( 'schema_wp_settings_advanced' )	: array();

		$settings = array_merge( $general_settings, $knowledge_graph_settings, $search_results_settings, $ext_settings, $license_settings, $advanced_settings );

		update_option( 'schema_wp_settings', $settings );

	}
	return apply_filters( 'schema_wp_get_settings', $settings );
}

add_action( 'admin_init', 'schema_wp_register_settings' );
/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function schema_wp_register_settings() {

	if ( false == get_option( 'schema_wp_settings' ) ) {
		add_option( 'schema_wp_settings' );
	}

	foreach ( schema_wp_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings) {

			// Check for backwards compatibility
			$section_tabs = schema_wp_get_settings_tab_sections( $tab );
			if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
				$section = 'main';
				$settings = $sections;
			}

			add_settings_section(
				'schema_wp_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'schema_wp_settings_' . $tab . '_' . $section
			);

			foreach ( $settings as $option ) {
				// For backwards compatibility
				if ( empty( $option['id'] ) ) {
					continue;
				}

				$name = isset( $option['name'] ) ? $option['name'] : '';

				add_settings_field(
					'schema_wp_settings[' . $option['id'] . ']',
					$name . apply_filters( 'schema_wp_after_setting_name', '', $option ),
					function_exists( 'schema_wp_' . $option['type'] . '_callback' ) ? 'schema_wp_' . $option['type'] . '_callback' : 'schema_wp_missing_callback',
					'schema_wp_settings_' . $tab . '_' . $section,
					'schema_wp_settings_' . $tab . '_' . $section,
					array(
						'section'       => $section,
						'id'            => isset( $option['id'] )            ? $option['id']            : null,
						'class'         => isset( $option['class'] )         ? $option['class']         : null,
						'class_field'   => isset( $option['class_field'] )   ? $option['class_field']   : null,
						'desc'          => ! empty( $option['desc'] )        ? $option['desc']          : '',
						'name'          => isset( $option['name'] )          ? $option['name']          : null,
						'size'          => isset( $option['size'] )          ? $option['size']          : null,
						'options'       => isset( $option['options'] )       ? $option['options']       : '',
						'std'           => isset( $option['std'] )           ? $option['std']           : '',
						'min'           => isset( $option['min'] )           ? $option['min']           : null,
						'max'           => isset( $option['max'] )           ? $option['max']           : null,
						'step'          => isset( $option['step'] )          ? $option['step']          : null,
						'chosen'        => isset( $option['chosen'] )        ? $option['chosen']        : null,
						'placeholder'   => isset( $option['placeholder'] )   ? $option['placeholder']   : null,
						'allow_blank'   => isset( $option['allow_blank'] )   ? $option['allow_blank']   : true,
						'readonly'      => isset( $option['readonly'] )      ? $option['readonly']      : false,
						'faux'          => isset( $option['faux'] )          ? $option['faux']          : false,
						'tooltip_title' => isset( $option['tooltip_title'] ) ? $option['tooltip_title'] : false,
						'tooltip_desc'  => isset( $option['tooltip_desc'] )  ? $option['tooltip_desc']  : false,
						'post_type'		=> isset( $option['post_type'] )  	 ? $option['post_type']  	: false,
					)
				);
			}
		}

	}

	// Creates our settings in the options table
	register_setting( 'schema_wp_settings', 'schema_wp_settings', 'schema_wp_settings_sanitize' );

}

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.0
 * @return array
*/
function schema_wp_get_registered_settings() {

	/**
	 * 'Whitelisted' Schema settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$schema_wp_settings = array(
		/** General Settings */
		'general' => apply_filters( 'schema_wp_settings_general',
			array(
				'main' => array(
					'site_type' => array(
						'id' => 'site_type',
						'name' => __( 'Site Type', 'schema-wp' ),
						'desc' => '',
						'type' => 'select',
						'options' => array(
							'' 						=> __('Select Site Type', 'schema-wp'),
							'blog'					=> 'Blog or Personal',
							'online_shop' 			=> 'Online Shop',
							'news_chanel' 			=> 'News and Magazine',
							'offline_business' 		=> 'Small Offline Business',
							'corporation' 			=> 'Corporation',
							'portfolio' 			=> 'Portfolio',
							'photography'			=> 'Photography',
							'music'					=> 'Music',
							'niche_affiliate'		=> 'Niche Affiliate / Reviews',
							'business_directory' 	=> 'Online Business Directory',
							'job_board'				=> 'Online Job Board',
							'knowledgebase'			=> 'Knowledgebase / Wiki',
							'question_answer'		=> 'Question & Answer',
							'school'				=> 'School or College',
							'else' 					=> 'Something else'
						),
						'std' => ''
					),
					'publisher_logo' => array(
						'id' => 'publisher_logo',
						'name' => __( 'Publisher Logo', 'schema-wp' ),
						'desc' => __( 'Publisher Logo should have a wide aspect ratio, not a square icon, it should be no wider than 600px, and no taller than 60px.', 'schema-wp' ) . ' <a href="https://developers.google.com/search/docs/data-types/articles#logo-guidelines" target="_blank">'.__('Logo guidelines', 'schema-wp').'</a>',
						'type' => 'image_upload',
						'std' => ''
					)
				)
			)
		),

		/** Knowledge Graph Settings */
		'knowledge_graph' => apply_filters('schema_wp_settings_knowledge_graph',
			array(
				'organization' => array( // section
					// Social Profiles
					'person_or_organization_settings' => array(
						'id' => 'social_profiles_settings',
						'name' => '<strong>' . __( 'Person or Organization', 'schema-wp' ) . '</strong>',
						'desc' => __( 'This information will be used in Google\'s Knowledge Graph Card, the big block of information you see on the right side of the search results.', 'schema-wp' ),
						'type' => 'header'
					),
					'organization_or_person' => array(
						'id' => 'organization_or_person',
						'class_field' => 'organization_or_person_radio',
						'name' => __( 'This Website Represent', 'schema-wp' ),
						'desc' => '',
						'type' => 'radio',
						'options' => array(
							//'' 				=> __('Select Type', 'schema-wp'),
							'organization'	=> 'Organization',
							'person' 		=> 'Person'
						),
						'std' => ''
					),
					'name' => array(
						'id' => 'name',
						'class_field' => 'input_name',
						'class' => 'tr_field_name',
						'name' => __( 'Name', 'schema-wp' ),
						'desc' => __( '', 'schema-wp' ),
						'type' => 'text',
						'placeholder' => get_bloginfo( 'name' ),
						'std' => ''
					),
					'url' => array(
						'id' => 'url',
						'class' => 'tr_field_url',
						'name' => __( 'Website URL', 'schema-wp' ),
						'desc' => __( '', 'schema-wp' ),
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'logo' => array(
						'id' => 'logo',
						'class' => 'tr_field_logo',
						'name' => __( 'Logo', 'schema-wp' ),
						'desc' => __('Specify the image of your organization\'s logo to be used in Google Search results and in the Knowledge Graph.<br />Learn more about', 'schema-wp') . ' <a href="https://developers.google.com/search/docs/data-types/logo" target="_blank">'.__('Logo guidelines', 'schema-wp').'</a>',
						'type' => 'image_upload',
						'std' => ''
					)
				),
				
				/** Social Profiles Settings */
				'social_profiles' => array( // section
				
					// Social Profiles
					'social_profiles_settings' => array(
						'id' => 'social_profiles_settings',
						'name' => '<strong>' . __( 'Social Profiles', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Provide your social profile information to a Google Knowledge panel.', 'schema-wp' ),
						'type' => 'header'
					),
					'facebook' => array(
						'id' => 'facebook',
						'name' => __( 'Facebook', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'twitter' => array(
						'id' => 'twitter',
						'name' => __( 'Twitter', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'google' => array(
						'id' => 'google',
						'name' => __( 'Google+', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'instagram' => array(
						'id' => 'instagram',
						'name' => __( 'Instagram', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'youtube' => array(
						'id' => 'youtube',
						'name' => __( 'YouTube', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'linkedin' => array(
						'id' => 'linkedin',
						'name' => __( 'LinkedIn', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'myspace' => array(
						'name' => __( 'Myspace', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'pinterest' => array(
						'id' => 'pinterest',
						'name' => __( 'Pinterest', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'soundcloud' => array(
						'id' => 'soundcloud',
						'name' => __( 'SoundCloud', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					),
					'tumblr' => array(
						'id' => 'tumblr',
						'name' => __( 'Tumblr', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					)
				),
				/** Corporate Contacts Settings */
				'corporate_contacts' => array( // section
				
					'corporate_contacts_contact_type' => array(
						'id' => 'corporate_contacts_contact_type',
						'name' => __( 'Contact Type', 'schema-wp' ),
						'desc' => '',
						'type' => 'select',
						'options' => schema_wp_get_corporate_contacts_types()
					),
					
					'corporate_contacts_telephone' => array(
						'id' => 'corporate_contacts_telephone',
						'name' => __( 'Telephone', 'schema-wp' ),
						'desc' => '<br>' . __('Recommended. An internationalized version of the phone number, starting with the "+" symbol and country code (+1 in the US and Canada).', 'schema-wp'),
						'type' => 'text',
						'std' => ''
					),
					
					'corporate_contacts_url' => array(
						'id' => 'corporate_contacts_url',
						'name' => __( 'URL', 'schema-wp' ),
						'desc' => '<br>' . __('Recommended. The URL of contact page.', 'schema-wp'),
						'type' => 'text',
						'placeholder' => 'https://',
						'std' => ''
					)
				), 
				
				/** Search Results Settings */
				'search_results' => array( // section
					
					// Sitelinks
					'sitelinks_search_box' => array(
						'id' => 'sitelinks_search_box',
						'name' => __( 'Enable Sitelinks Search Box?', 'schema-wp' ),
						'desc' => __( 'Tell Google to show a Sitelinks search box.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					
					// Site name
					'site_name_enable' => array(
						'id' => 'site_name_enable',
						'class_field' => 'site_name_enable_checkbox',
						'name' => __( 'Enable Site Name?', 'schema-wp' ),
						'desc' => __( 'Tell Google to show your site name in search results.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					'site_name' => array(
						'id' => 'site_name',
						'class' => 'tr_field_site_name',
						'name' => __( 'Site Name', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => get_bloginfo ('name'),
					),
					'site_alternate_name' => array(
						'id' => 'site_alternate_name',
						'class' => 'tr_field_site_alternate_name',
						'name' => __( 'Site Alternate Name', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					)
				), 
			)
		),
			
		/** Content Settings */
		'schemas' => apply_filters( 'schema_wp_settings_schemas',
			array(
				'about_page' => array(
					'id' => 'about_page',
					'name' => __( 'About Page', 'schema-wp' ),
					'desc' => __( '', 'schema-wp' ),
					'type' => 'post_select',
					'post_type' => 'page'
				),
				'contact_page' => array(
					'id' => 'contact_page',
					'name' => __( 'Contact Page', 'schema-wp' ),
					'desc' => __( '', 'schema-wp' ),
					'type' => 'post_select',
					'post_type' => 'page'
				),
				'web_page_element' => array(
					'id' => 'web_page_element_enable',
					'name' => __( 'WPHeader and WPFooter', 'schema-wp' ),
					'desc' => __( 'enable?', 'schema-wp' ),
					'type' => 'checkbox'
				),
				'breadcrumbs' => array(
					'id' => 'breadcrumbs_enable',
					'name' => __( 'Breadcrumbs', 'schema-wp' ),
					'desc' => __( 'enable?', 'schema-wp' ),
					'type' => 'checkbox'
				),
				'comments' => array(
					'id' => 'comments_enable',
					'name' => __( 'Comments', 'schema-wp' ),
					'desc' => __( 'enable?', 'schema-wp' ),
					'type' => 'checkbox'
				),
				'video' => array(
					'id' => 'video_object_enable',
					'name' => __( 'VideoObject', 'schema-wp' ),
					'desc' => __( 'enable?', 'schema-wp' ),
					'type' => 'checkbox'
				),
				'audio' => array(
					'id' => 'audio_object_enable',
					'name' => __( 'AudioObject', 'schema-wp' ),
					'desc' => __( 'enable?', 'schema-wp' ),
					'type' => 'checkbox'
				)
			)
		),
		
		/** Extension Settings */
		'extensions' => apply_filters('schema_wp_settings_extensions',
			array()
		),
		'licenses' => apply_filters('schema_wp_settings_licenses',
			array()
		),
		
		/** Advanced Settings */
		'advanced' => apply_filters('schema_wp_settings_advanced',
			array(
				'main' => array(
					'uninstall_on_delete' => array(
						'id'   => 'uninstall_on_delete',
						'name' => __( 'Delete Data on Uninstall?', 'schema-wp' ),
						'desc' => __( 'Check this box if you would like Schema to completely remove all of its data when uninstalling via Plugins > Delete.', 'schema-wp' ),
						'type' => 'checkbox'
					)
				)
			)
		)
	);
		
	return apply_filters( 'schema_wp_registered_settings', $schema_wp_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.8.2
 *
 * @param array $input The value inputted in the field
 * @global $schema_wp_options Array of all the Schema Options
 *
 * @return string $input Sanitizied value
 */
function schema_wp_settings_sanitize( $input = array() ) {
	global $schema_wp_options;

	$doing_section = false;
	if ( ! empty( $_POST['_wp_http_referer'] ) ) {
		$doing_section = true;
	}

	$setting_types = schema_wp_get_registered_settings_types();
	$input         = $input ? $input : array();

	if ( $doing_section ) {

		parse_str( $_POST['_wp_http_referer'], $referrer ); // Pull out the tab and section
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
		$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

		// Run a general sanitization for the tab for special fields (like taxes)
		$input = apply_filters( 'schema_wp_settings_' . $tab . '_sanitize', $input );

		// Run a general sanitization for the section so custom tabs with sub-sections can save special data
		$input = apply_filters( 'schema_wp_settings_' . $tab . '-' . $section . '_sanitize', $input );

	}

	// Merge our new settings with the existing
	$output = array_merge( $schema_wp_options, $input );

	foreach ( $setting_types as $key => $type ) {

		if ( empty( $type ) ) {
			continue;
		}

		// Some setting types are not actually settings, just keep moving along here
		$non_setting_types = apply_filters( 'schema_wp_non_setting_types', array(
			'header', 'descriptive_text', 'hook',
		) );

		if ( in_array( $type, $non_setting_types ) ) {
			continue;
		}

		if ( array_key_exists( $key, $output ) ) {
			$output[ $key ] = apply_filters( 'schema_wp_settings_sanitize_' . $type, $output[ $key ], $key );
			$output[ $key ] = apply_filters( 'schema_wp_settings_sanitize', $output[ $key ], $key );
		}

		if ( $doing_section ) {
			switch( $type ) {
				case 'checkbox':
					if ( array_key_exists( $key, $input ) && $output[ $key ] === '-1' ) {
						unset( $output[ $key ] );
					}
					break;
				default:
					if ( array_key_exists( $key, $input ) && empty( $input[ $key ] ) ) {
						unset( $output[ $key ] );
					}
					break;
			}
		} else {
			if ( empty( $input[ $key ] ) ) {
				unset( $output[ $key ] );
			}
		}

	}

	if ( $doing_section ) {
		add_settings_error( 'schema-wp-notices', '', __( 'Settings updated.', 'wp-schema' ), 'updated' );
	}

	return $output;
}

/**
 * Flattens the set of registered settings and their type so we can easily sanitize all the settings
 * in a much cleaner set of logic in schema_wp_settings_sanitize
 *
 * @since  2.6.5
 * @return array Key is the setting ID, value is the type of setting it is registered as
 */
function schema_wp_get_registered_settings_types() {
	$settings      = schema_wp_get_registered_settings();
	$setting_types = array();
	
	// debug
	//echo'<pre>';print_r($settings);echo'</pre>';exit;
	
	foreach ( $settings as $tab ) {

		foreach ( $tab as $section_or_setting ) {

			// See if we have a setting registered at the tab level for backwards compatibility
			if ( is_array( $section_or_setting ) && array_key_exists( 'type', $section_or_setting ) ) {
				$setting_types[ $section_or_setting['id'] ] = $section_or_setting['type'];
				continue;
			}

			foreach ( $section_or_setting as $section => $section_settings ) {
				if(isset($section_settings['id'])) $setting_types[ $section_settings['id'] ] = $section_settings['type'];
			}
		}

	}

	return $setting_types;
}

add_filter( 'schema_wp_settings_sanitize_text', 'schema_wp_sanitize_text_field' );
/**
 * Sanitize text fields
 *
 * @since 1.0
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function schema_wp_sanitize_text_field( $input ) {
	$tags = array(
		'p' => array(
			'class' => array(),
			'id'    => array(),
		),
		'span' => array(
			'class' => array(),
			'id'    => array(),
		),
		'a' => array(
			'href' => array(),
			'title' => array(),
			'class' => array(),
			'title' => array(),
			'id'    => array(),
		),
		'strong' => array(),
		'em' => array(),
		'br' => array(),
		'img' => array(
			'src'   => array(),
			'title' => array(),
			'alt'   => array(),
			'id'    => array(),
		),
		'div' => array(
			'class' => array(),
			'id'    => array(),
		),
		'ul' => array(
			'class' => array(),
			'id'    => array(),
		),
		'li' => array(
			'class' => array(),
			'id'    => array(),
		)
	);

	$allowed_tags = apply_filters( 'schema_wp_allowed_html_tags', $tags );

	return trim( wp_kses( $input, $allowed_tags ) );
}

/**
 * Retrieve settings tabs
 *
 * @since 1.0
 * @return array $tabs
 */
function schema_wp_get_settings_tabs() {

	$settings = schema_wp_get_registered_settings();

	$tabs						= array();
	$tabs['general']			= __( 'General',			'schema-wp' );
	$tabs['knowledge_graph']	= __( 'Knowledge Graph',	'schema-wp' );
	$tabs['schemas']			= __( 'Schemas',			'schema-wp' );
	
	if( ! empty( $settings['extensions'] ) ) {
		$tabs['extensions'] = __( 'Extensions', 'wp-schema' );
	}
	if( ! empty( $settings['licenses'] ) ) {
		$tabs['licenses'] = __( 'Licenses', 'wp-schema' );
	}

	$tabs['advanced']      = __( 'Advanced', 'wp-schema' );
	
	//if( schema_wp()->settings->get( 'debug_mode', false ) ) {	
	//	$tabs['schema_wp_debug']     = __( 'Debug Assistant', 'schema-wp' );
	//}
	
	return apply_filters( 'schema_wp_settings_tabs', $tabs );
}

/**
 * Retrieve settings tabs
 *
 * @since 1.0
 * @return array $section
 */
function schema_wp_get_settings_tab_sections( $tab = false ) {

	$tabs     = false;
	$sections = schema_wp_get_registered_settings_sections();

	if( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = false;
	}

	return $tabs;
}

/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @since 1.0
 * @return array Array of tabs and sections
 */
function schema_wp_get_registered_settings_sections() {

	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}

	$sections = array(
		'general'    => apply_filters( 'schema_wp_settings_sections_general', array(
			'main'		=> '',
		) ),
		'schemas'    => apply_filters( 'schema_wp_settings_sections_schemas', array(
			'main'		=> '',
		) ),
		'knowledge_graph'	=> apply_filters( 'schema_wp_settings_sections_knowledge_graph', array(
			'organization'			=> __( 'Organization Info', 'wp-schema' ),
			'search_results'			=> __( 'Search Results', 'wp-schema' ),
			'social_profiles'		=> __( 'Social Profiles', 'wp-schema' ),
			'corporate_contacts'	=> __( 'Corporate Contacts', 'wp-schema' ),
		) ),
		'extensions' => apply_filters( 'schema_wp_settings_sections_extensions', array(
			'main'		 => __( 'Main', 'wp-schema' ),
		) ),
		'licenses'	=> apply_filters( 'schema_wp_settings_sections_licenses', array() ),
		'advanced'	=> apply_filters( 'schema_wp_settings_sections_advanced', array(
			'main'		=> '',
		) ),
	);

	$sections = apply_filters( 'schema_wp_settings_sections', $sections );

	return $sections;
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

	$pages_options = array( '' => '' ); // Blank option

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

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function schema_wp_header_callback( $args ) {
	echo $args['desc'];
	echo apply_filters( 'schema_wp_after_setting_output', '', $args );
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_checkbox_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = 'class="' . schema_wp_sanitize_key( $args['class_field'] ) . '"';
	} else {
		$class_field = '';
	}
	
	$checked  = ! empty( $schema_wp_option ) ? checked( 1, $schema_wp_option, false ) : '';
	$html     = '<input type="hidden"' . $name . ' value="-1" />';
	$html    .= '<input ' . $class_field . 'type="checkbox" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . '/>';
	$html    .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_multicheck_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	$html = '';
	if ( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ):
			if( isset( $schema_wp_option[ $key ] ) ) { $enabled = $option; } else { $enabled = NULL; }
			$html .= '<input name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']" type="checkbox" value="' . esc_attr( $option ) . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
			$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']">' . wp_kses_post( $option ) . '</label><br/>';
		endforeach;
		$html .= '<p class="description">' . $args['desc'] . '</p>';
	}

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_radio_callback( $args ) {
	$schema_wp_options = schema_wp_get_option( $args['id'] );

	$html = '<fieldset class="">';
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = 'class="' . schema_wp_sanitize_key( $args['class_field'] ) . '"';
	} else {
		$class_field = '';
	}
	
	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( $schema_wp_options && $schema_wp_options == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! $schema_wp_options )
			$checked = true;

		$html .= '<input ' . $class_field .' name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']" type="radio" value="' . schema_wp_sanitize_key( $key ) . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']">' . esc_html( $option ) . '</label><br/>';
	endforeach;
	
	$html .= '</fieldset>';
	
	$html .= '<p class="description">' . apply_filters( 'schema_wp_after_setting_output', wp_kses_post( $args['desc'] ), $args ) . '</p>';

	echo $html;
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_text_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']"';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = schema_wp_sanitize_key( $args['class_field'] ) . ' ';
	} else {
		$class_field = '';
	}

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . $class_field . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '" placeholder="' . $args['placeholder'] . '" ' . $readonly . '/>';
	$html    .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_number_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']"';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = schema_wp_sanitize_key( $args['class_field'] ) . ' ';
	} else {
		$class_field = '';
	}
	
	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $class_field . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_textarea_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = schema_wp_sanitize_key( $args['class_field'] ) . ' ';
	} else {
		$class_field = '';
	}
	
	$html = '<textarea class="' . $class_field . 'large-text" cols="50" rows="5" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . ' placeholder="' . $args['placeholder'] .'"</textarea>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_password_callback( $args ) {
	$schema_wp_options = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_options ) {
		$value = $schema_wp_options;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function schema_wp_missing_callback($args) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'wp-schema' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_select_callback($args) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="schema-wp-chosen"';
	} else {
		$chosen = '';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = 'class="' . schema_wp_sanitize_key( $args['class_field'] ) . '"';
	} else {
		$class_field = '';
	}

	$html = '<select ' . $class_field .' id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" ' . $chosen . 'data-placeholder="' . esc_html( $placeholder ) . '" />';

	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_color_select_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$html = '<select id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']"/>';

	foreach ( $args['options'] as $option => $color ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $color['label'] ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 */
function schema_wp_rich_editor_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;

		if( empty( $args['allow_blank'] ) && empty( $value ) ) {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$rows = isset( $args['size'] ) ? $args['size'] : 20;


	ob_start();
	wp_editor( stripslashes( $value ), 'schema_wp_settings_' . esc_attr( $args['id'] ), array( 'textarea_name' => 'schema_wp_settings[' . esc_attr( $args['id'] ) . ']', 'textarea_rows' => absint( $rows ) ) );
	$html = ob_get_clean();

	$html .= '<br/><label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_upload_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = schema_wp_sanitize_key( $args['class_field'] ) . ' ';
	} else {
		$class_field = '';
	}

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset($args['std']) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $class_field . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="schema_wp_settings_upload_button button-secondary" value="' . __( 'Upload File', 'wp-schema' ) . '"/></span>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Image Upload Callback
 *
 * Renders file upload fields.
 *
 * @since 1.0
 * @param array $args Arguements passed by the setting
 */
function schema_wp_image_upload_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = schema_wp_sanitize_key( $args['class_field'] ) . ' ';
	} else {
		$class_field = '';
	}

	if( $schema_wp_option )
		$value = $schema_wp_option;
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $class_field . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="schema_wp_settings_upload_button button-secondary" value="' . __( 'Upload File', 'wp-schema' ) . '"/></span>';
	
	$html .= '<p>'  . wp_kses_post( $args['desc'] ) . '</p>';
		
	if ( ! empty( $value ) ) {
		$html .= '<div id="preview_image">';
		$html .= '<img src="'.esc_attr( stripslashes( $value ) ).'" />';
		$html .= '</div>';
	} else {
		$html .= '<div id="preview_image" style="display: none;"></div>';
	}
	
	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}

/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_color_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<input type="text" class="schema-wp-color-picker" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}	

/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function schema_wp_descriptive_text_callback( $args ) {
	$html = wp_kses_post( $args['desc'] );

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
}


/**
 * Post Select Callback
 *
 * Renders file upload fields.
 *
 * @since 1.0
 * @param array $args Arguements passed by the setting
 */
function schema_wp_post_select_callback( $args ) {
		
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
		
	$html = '<select id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']"/>';
	$html .= '<option value=""> - '.__('Select One', 'schema-wp').' - </option>'; // Select One
	$posts = get_posts( array( 'post_type' => $args['post_type'], 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );
	foreach ( $posts as $item ) :
	$selected = selected( $item->ID , $value, false );
		$html .= '<option value="' . $item->ID . '"' . $selected . '>' . $item->post_title . '</option>';
		$post_type_object = get_post_type_object( $args['post_type'] );
	endforeach;
	$html .= '</select>';
	$html .= '<p class="description">' . $args['desc'] . '</p>';
		
	echo $html;
}
	
/**
 * Registers the license field callback for Software Licensing
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
if ( ! function_exists( 'schema_wp_license_key_callback' ) ) {
	function schema_wp_license_key_callback( $args ) {
		$schema_wp_option = schema_wp_get_option( $args['id'] );

		$messages = array();
		$license  = get_option( $args['options']['is_valid_license_option'] );

		if ( $schema_wp_option ) {
			$value = $schema_wp_option;
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if( ! empty( $license ) && is_object( $license ) ) {

			// activate_license 'invalid' on anything other than valid, so if there was an error capture it
			if ( false === $license->success ) {

				switch( $license->error ) {

					case 'expired' :

						$class = 'expired';
						$messages[] = sprintf(
							__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'wp-schema' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
							'https://easydigitaldownloads.com/checkout/?schema_wp_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=expired'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'revoked' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'wp-schema' ),
							'https://easydigitaldownloads.com/support?utm_campaign=admin&utm_source=licenses&utm_medium=revoked'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'missing' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'wp-schema' ),
							'https://easydigitaldownloads.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'invalid' :
					case 'site_inactive' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'wp-schema' ),
							$args['name'],
							'https://easydigitaldownloads.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=invalid'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'item_name_mismatch' :

						$class = 'error';
						$messages[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-schema' ), $args['name'] );

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'no_activations_left':

						$class = 'error';
						$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'wp-schema' ), 'https://easydigitaldownloads.com/your-account/' );

						$license_status = 'license-' . $class . '-notice';

						break;

					default :

						$messages[] = print_r( $license, true );
						break;
				}

			} else {

				switch( $license->license ) {

					case 'valid' :
					default:

						$class = 'valid';

						$now        = current_time( 'timestamp' );
						$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

						if( 'lifetime' === $license->expires ) {

							$messages[] = __( 'License key never expires.', 'wp-schema' );

							$license_status = 'license-lifetime-notice';

						} elseif( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

							$messages[] = sprintf(
								__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank">Renew your license key</a>.', 'wp-schema' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
								'https://easydigitaldownloads.com/checkout/?schema_wp_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew'
							);

							$license_status = 'license-expires-soon-notice';

						} else {

							$messages[] = sprintf(
								__( 'Your license key expires on %s.', 'wp-schema' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
							);

							$license_status = 'license-expiration-date-notice';

						}

						break;

				}

			}

		} else {
			$class = 'empty';

			$messages[] = sprintf(
				__( 'To receive updates, please enter your valid %s license key.', 'wp-schema' ),
				$args['name']
			);

			$license_status = null;
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';

		if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'wp-schema' ) . '"/>';
		}

		$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

		if ( ! empty( $messages ) ) {
			foreach( $messages as $message ) {

				$html .= '<div class="schema-wp-license-data schema-wp-license-' . $class . ' ' . $license_status . '">';
					$html .= '<p>' . $message . '</p>';
				$html .= '</div>';

			}
		}

		wp_nonce_field( schema_wp_sanitize_key( $args['id'] ) . '-nonce', schema_wp_sanitize_key( $args['id'] ) . '-nonce' );

		echo $html;
	}
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function schema_wp_hook_callback( $args ) {
	do_action( 'schema_wp_' . $args['id'], $args );
}

add_filter( 'schema_wp_after_setting_name', 'schema_wp_add_setting_tooltip', 10, 2 );
/**
 * Set manage_schema_options as the cap required to save Schema settings pages
 *
 * @since 1.0
 * @return string capability required
 */
function schema_wp_set_settings_cap() {
	return 'manage_schema_options';
}
add_filter( 'option_page_capability_schema_wp_settings', 'schema_wp_set_settings_cap' );

function schema_wp_add_setting_tooltip( $html, $args ) {

	if ( ! empty( $args['tooltip_title'] ) && ! empty( $args['tooltip_desc'] ) ) {
		$tooltip = '<span alt="f223" class="schema-wp-help-tip dashicons dashicons-editor-help" title="<strong>' . $args['tooltip_title'] . '</strong>: ' . $args['tooltip_desc'] . '"></span>';
		$html .= $tooltip;
	}

	return $html;
}

add_action( 'admin_print_footer_scripts', 'schema_wp_admin_footer_scripts' );
/**
 * Footer scripts
 *
 * @since 1.7
 *
 * @return void
 */
function schema_wp_admin_footer_scripts() { 

	if( ! schema_wp_is_admin_page() ) {
		return;
	}

?>
<script>                
	jQuery( document ).ready(function($) {
        	   
   	// Hide/Show Organization or Person fields 
	$('.tr_field_name').hide();
	$('.tr_field_logo').hide();
	
	var inputValue = $(".organization_or_person_radio:checked").attr("value");
	
	if ( inputValue == 'person' ) {
		$('.tr_field_logo').hide();
		$('.tr_field_name th').text('Person Name');
    	$('.tr_field_name').show();
	} 
	else {
		$('.tr_field_logo').show();
		$('.tr_field_name').show();
		$('.tr_field_name th').text('Organization Name');
	}
	
	$(".organization_or_person_radio").change(function(){
        var inputValue = $(this).attr("value");
        if ($(this).val() == 'person') {
			$('.tr_field_name th').text('Person Name');
    		$('.tr_field_name').show();
			$('.tr_field_logo').hide();
    	}
   		else {
   			$('.tr_field_name').show();
			$('.tr_field_logo').show();
			$('.tr_field_name th').text('Organziation Name');
   		}
	});
	
	// Hide/Show Site Name fields 
	$('.tr_field_site_name').hide();
	$('.tr_field_site_alternate_name').hide();
	
	var inputValue = $(".site_name_enable_checkbox:checked").attr("value");
	
	if ( inputValue == 1 ) {
		$('.tr_field_site_name').show();
		$('.tr_field_site_alternate_name').show();
	} 
	
	$(".site_name_enable_checkbox").change(function(){
        var $this = $(this);
         if ($this.is(':checked')) {
			$('.tr_field_site_name').show();
			$('.tr_field_site_alternate_name').show();
    	}
   		else {
   			$('.tr_field_site_name').hide();
			$('.tr_field_site_alternate_name').hide();
   		}
	});
	
});
</script>
<?php
}
