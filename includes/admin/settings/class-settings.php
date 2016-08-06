<?php

class Schema_WP_Settings {

	private $options;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	*/
	public function __construct() {

		$this->options = get_option( 'schema_wp_settings', array() );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_filter( 'schema_wp_settings_sanitize_text', array( $this, 'sanitize_text_fields' ), 10, 2 );
		add_filter( 'schema_wp_settings_sanitize_url', array( $this, 'sanitize_url_fields' ), 10, 2 );
		add_filter( 'schema_wp_settings_sanitize_checkbox', array( $this, 'sanitize_cb_fields' ), 10, 2 );
		add_filter( 'schema_wp_settings_sanitize_number', array( $this, 'sanitize_number_fields' ), 10, 2 );
		add_filter( 'schema_wp_settings_sanitize_rich_editor', array( $this, 'sanitize_rich_editor_fields' ), 10, 2 );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * Note: By default, zero values are not allowed. If you have a custom
	 * setting that needs to allow 0 as a valid value, but sure to add its
	 * key to the filtered array seen in this method.
	 *
	 * @since  1.0
	 * @param  string  $key
	 * @param  mixed   $default (optional)
	 * @return mixed
	 */
	public function get( $key, $default = false ) {

		// Only allow non-empty values, otherwise fallback to the default
		$value = ! empty( $this->options[ $key ] ) ? $this->options[ $key ] : $default;

		/**
		 * Allow certain settings to accept 0 as a valid value without
		 * falling back to the default.
		 *
		 * @since  1.0
		 * @param  array
		 */
		$zero_values_allowed = (array) apply_filters( 'schema_wp_settings_zero_values_allowed', array() );

		// Allow 0 values for specified keys only
		if ( in_array( $key, $zero_values_allowed ) ) {

			$value = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
			$value = ( ! is_null( $value ) && '' !== $value ) ? $value : $default;

		}

		return $value;

	}

	/**
	 * Get all settings
	 *
	 * @since 1.0
	 * @return array
	*/
	public function get_all() {
		return $this->options;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0
	 * @return void
	*/
	function register_settings() {

		if ( false == get_option( 'schema_wp_settings' ) ) {
			add_option( 'schema_wp_settings' );
		}

		foreach( $this->get_registered_settings() as $tab => $settings ) {

			add_settings_section(
				'schema_wp_settings_' . $tab,
				__return_null(),
				'__return_false',
				'schema_wp_settings_' . $tab
			);

			foreach ( $settings as $key => $option ) {

				if( $option['type'] == 'checkbox' || $option['type'] == 'multicheck' || $option['type'] == 'radio' ) {
					$name = isset( $option['name'] ) ? $option['name'] : '';
				} else {
					$name = isset( $option['name'] ) ? '<label for="schema_wp_settings[' . $key . ']">' . $option['name'] . '</label>' : '';
				}

				$callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );

				add_settings_field(
					'schema_wp_settings[' . $key . ']',
					$name,
					is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
					'schema_wp_settings_' . $tab,
					'schema_wp_settings_' . $tab,
					array(
						'id'      => $key,
						'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'    => isset( $option['name'] ) ? $option['name'] : null,
						'section' => $tab,
						'size'    => isset( $option['size'] ) ? $option['size'] : null,
						'max'     => isset( $option['max'] ) ? $option['max'] : null,
						'min'     => isset( $option['min'] ) ? $option['min'] : null,
						'step'    => isset( $option['step'] ) ? $option['step'] : null,
						'options' => isset( $option['options'] ) ? $option['options'] : '',
						'post_type' => isset( $option['post_type'] ) ? $option['post_type'] : '',
						'std'     => isset( $option['std'] ) ? $option['std'] : '',
					)
				);
			}

		}

		// Creates our settings in the options table
		register_setting( 'schema_wp_settings', 'schema_wp_settings', array( $this, 'sanitize_settings' ) );

	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function sanitize_settings( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved    = get_option( 'schema_wp_settings', array() );
		if( ! is_array( $saved ) ) {
			$saved = array();
		}
		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

		$input = $input ? $input : array();
		$input = apply_filters( 'schema_wp_settings_' . $tab . '_sanitize', $input );

		// Ensure a value is always passed for every checkbox
		if( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $setting ) {

				// Single checkbox
				if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'checkbox' == $settings[ $tab ][ $key ][ 'type' ] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'multicheck' == $settings[ $tab ][ $key ][ 'type' ] ) {
					if( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type              = isset( $settings[ $tab ][ $key ][ 'type' ] ) ? $settings[ $tab ][ $key ][ 'type' ] : false;
			$sanitize_callback = isset( $settings[ $tab ][ $key ][ 'sanitize_callback' ] ) ? $settings[ $tab ][ $key ][ 'sanitize_callback' ] : false;
			$input[ $key ]     = $value;

			if ( $type ) {

				if( $sanitize_callback && is_callable( $sanitize_callback ) ) {

					add_filter( 'schema_wp_settings_sanitize_' . $type, $sanitize_callback, 10, 2 );

				}

				// Field type specific filter
				$input[ $key ] = apply_filters( 'schema_wp_settings_sanitize_' . $type, $input[ $key ], $key );
			}

			// General filter
			$input[ $key ] = apply_filters( 'schema_wp_settings_sanitize', $input[ $key ], $key );

			// Now remove the filter
			if( $sanitize_callback && is_callable( $sanitize_callback ) ) {

				remove_filter( 'schema_wp_settings_sanitize_' . $type, $sanitize_callback, 10 );

			}
		}

		add_settings_error( 'schema-wp-notices', '', __( 'Settings updated.', 'schema-wp' ), 'updated' );

		return array_merge( $saved, $input );

	}


	/**
	 * Sanitize text fields
	 *
	 * @since 1.0
	 * @return string
	*/
	public function sanitize_text_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize URL fields
	 *
	 * @since 1.0
	 * @return string
	*/
	public function sanitize_url_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize checkbox fields
	 *
	 * @since 1.0
	 * @return int
	*/
	public function sanitize_cb_fields( $value = '', $key = '' ) {
		return absint( $value );
	}

	/**
	 * Sanitize number fields
	 *
	 * @since 1.0
	 * @return int
	*/
	public function sanitize_number_fields( $value = '', $key = '' ) {
		return floatval( $value );
	}

	/**
	 * Sanitize rich editor fields
	 *
	 * @since 1.0
	 * @return int
	*/
	public function sanitize_rich_editor_fields( $value = '', $key = '' ) {
		return wp_kses_post( $value );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function get_registered_settings() {

		// get currently logged in username
		$user_info = get_userdata( get_current_user_id() );
		$username  = $user_info ? esc_html( $user_info->user_login ) : '';

		$settings = array(
			/** General Settings */
			'general' => apply_filters( 'schema_wp_settings_general',
			
				array(
					'about_page' => array(
						'name' => __( 'About Page', 'schema-wp' ),
						'desc' => __( 'Select the about page', 'schema-wp' ),
						'type' => 'post_select',
						'post_type' => 'page'
					),
					'contact_page' => array(
						'name' => __( 'Contact Page', 'schema-wp' ),
						'desc' => __( 'Select the contact page', 'schema-wp' ),
						'type' => 'post_select',
						'post_type' => 'page'
					),
					'auto_featured_img' => array(
						'name' => __( 'Set Featured image automatically?', 'schema-wp' ),
						'desc' => __( 'Check this box if you would like Schema to try setting Featured image while you create or edit the post.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					/*
					'currency_settings' => array(
						'name' => '<strong>' . __( 'Currency Settings', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Configure the currency options', 'schema-wp' ),
						'type' => 'header'
					),
					'currency' => array(
						'name' => __( 'Currency', 'schema-wp' ),
						'desc' => __( 'Choose your currency.', 'schema-wp' ),
						'type' => 'select',
						'options' => schema_wp_get_currencies()
					),
					
					'currency_position' => array(
						'name' => __( 'Currency Position', 'schema-wp' ),
						'desc' => __( 'Choose the location of the currency sign.', 'schema-wp' ),
						'type' => 'select',
						'options' => array(
							'before' => __( 'Before - $10', 'schema-wp' ),
							'after' => __( 'After - 10$', 'schema-wp' )
						)
					),
					'thousands_separator' => array(
						'name' => __( 'Thousands Separator', 'schema-wp' ),
						'desc' => __( 'The symbol (usually , or .) to separate thousands', 'schema-wp' ),
						'type' => 'text',
						'size' => 'small',
						'std' => ','
					),
					'decimal_separator' => array(
						'name' => __( 'Decimal Separator', 'schema-wp' ),
						'desc' => __( 'The symbol (usually , or .) to separate decimal points', 'schema-wp' ),
						'type' => 'text',
						'size' => 'small',
						'std' => '.'
					)*/
				)
			),
		
			
			/** Knowledge Graph Settings */
			'knowledge_graph' => apply_filters( 'schema_wp_settings_knowledge_graph',
				array(
					'knowledge_graph_settings' => array(
						'name' => '<strong>' . __( 'Organization Logo', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Indicates to Google that this image is designated as the organization’s logo and, where possible, may be used in Google search results. Markup like this is a strong signal to our algorithms to show this image in Knowledge Graph displays.', 'schema-wp' ),
						'type' => 'header'
					),
					'name' => array(
						'name' => __( 'Name', 'schema-wp' ),
						'desc' => __( 'Your Organization name, or your name.', 'schema-wp' ),
						'type' => 'text',
						'std' => ''
					),
					'url' => array(
						'name' => __( 'Organization Website', 'schema-wp' ),
						'desc' => __( 'Your Organization website URL.', 'schema-wp' ),
						'type' => 'text',
						'std' => ''
					),
					'logo' => array(
						'name' => __( 'Logo', 'schema-wp' ),
						'desc' => __( 'Logos should have a wide aspect ratio, not a square icon, it should be no wider than 600px, and no taller than 60px.', 'schema-wp' ) . ' <a href="https://developers.google.com/search/docs/data-types/articles#amp-logo-guidelines" target="_blank">'.__('Logo guidelines', 'schema-wp').'</a>',
						'type' => 'image_upload',
						'std' => ''
					),
					
					// Corporate Contacts
					'corporate_contacts' => array(
						'name' => '<strong>' . __( 'Corporate Contacts', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Use structured data markup embedded in your public website to specify your preferred social profiles.', 'schema-wp' ),
						'type' => 'header'
					),
					'corporate_contacts_telephone' => array(
						'name' => __( 'Telephone', 'schema-wp' ),
						'desc' => 'Required. An internationalized version of the phone number, starting with the "+" symbol and country code (+1 in the US and Canada).',
						'type' => 'text',
						'std' => ''
					),
					'corporate_contacts_contact_type' => array(
						'name' => __( 'Contact Type', 'schema-wp' ),
						'desc' => '',
						'type' => 'select',
						'options' => schema_wp_get_corporate_contacts_types()
					),
					
					// Social Profiles
					'social_profiles_settings' => array(
						'name' => '<strong>' . __( 'Social Profiles', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Use structured data markup embedded in your public website to specify your preferred social profiles.', 'schema-wp' ),
						'type' => 'header'
					),
					'facebook' => array(
						'name' => __( 'Facebook', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'twitter' => array(
						'name' => __( 'Twitter', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'google' => array(
						'name' => __( 'Google+', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'instagram' => array(
						'name' => __( 'Instagram', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'youtube' => array(
						'name' => __( 'YouTube', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'linkedin' => array(
						'name' => __( 'LinkedIn', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'myspace' => array(
						'name' => __( 'Myspace', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'pinterest' => array(
						'name' => __( 'Pinterest', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'soundcloud' => array(
						'name' => __( 'SoundCloud', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					),
					'tumblr' => array(
						'name' => __( 'Tumblr', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					)
				)
			),
			
			/** Search Restuls Settings */
			'search_results' => apply_filters( 'schema_wp_settings_search_results',
				array(
					'sitelinks_search_box_settings' => array(
						'name' => '<strong>' . __( 'Sitelinks Search Box', 'schema-wp' ) . '</strong>',
						'desc' => __( 'To make content searches easier for your users, add just one markup declaration on your home page, telling the Google crawlers that users can search your site directly from Search results.<br><br>Once Google scan your home page and after users perform relevant navigational queries for your property, the search box appears in results, which can take a few weeks depending on the site and other factors.<br><br>After you enable this feature, <b>wait for Google Search algorithms</b> to identify your site or app as a candidate for the new Sitelinks search box.', 'schema-wp' ),
						'type' => 'header'
					),
					'sitelinks_search_box' => array(
						'name' => __( 'Enable Sitelinks Search Box?', 'schema-wp' ),
						'desc' => __( 'Tell Google to show a Sitelinks search box.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					/*
					'sitelinks_search_box_disable' => array(
						'name' => __( 'Disable Sitelinks Search Box?', 'schema-wp' ),
						'desc' => __( 'Tell Google not to show a Sitelinks search box when your site appears in the search results.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					*/
					'site_name_settings' => array(
						'name' => '<strong>' . __( 'Include Your Site Name in Search Results', 'schema-wp' ) . '</strong>',
						'desc' => __( 'Indicate the preferred name you want Google to display in Search results.', 'schema-wp' ),
						'type' => 'header'
					),
					'site_name_enable' => array(
						'name' => __( 'Enable Site Name?', 'schema-wp' ),
						'desc' => __( 'Tell Google to show your site name in search results.', 'schema-wp' ),
						'type' => 'checkbox'
					),
					'site_name' => array(
						'name' => __( 'Site Name', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => get_bloginfo ('name'),
					),
					'site_alternate_name' => array(
						'name' => __( 'Site Alternate Name', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => ''
					)
				)
			),
			
			
			/** Misc Settings */
			'misc' => apply_filters( 'schema_wp_settings_misc',
				array(
					/*'debug_mode' => array(
						'name' => __( 'Enable Debug Mode?', 'schema-wp' ),
						'desc' => __( 'Check this box to enable debug mode. This will turn on error logging for the referral process to help identify problems.', 'schema-wp' ),
						'type' => 'checkbox'
					),*/
					'uninstall_on_delete' => array(
						'name' => __( 'Delete Data on Uninstall?', 'schema-wp' ),
						'desc' => __( 'Check this box if you would like Schema to completely remove all of its data when uninstalling via Plugins > Delete.', 'schema-wp' ),
						'type' => 'checkbox'
					)
				)
			)
		);

		return apply_filters( 'schema_wp_settings', $settings );
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
	function header_callback( $args ) {
		echo '<hr/>';
		echo '<p style="color:#999;">'.$args['desc'].'</p>';
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function checkbox_callback( $args ) {

		$checked = isset($this->options[$args['id']]) ? checked(1, $this->options[$args['id']], false) : '';
		$html = '<label for="schema_wp_settings[' . $args['id'] . ']">';
		$html .= '<input type="checkbox" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>&nbsp;';
		$html .= $args['desc'];
		$html .= '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function multicheck_callback( $args ) {

		if ( ! empty( $args['options'] ) ) {
			foreach( $args['options'] as $key => $option ) {
				if( isset( $this->options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
				echo '<label for="schema_wp_settings[' . $args['id'] . '][' . $key . ']">';
				echo '<input name="schema_wp_settings[' . $args['id'] . '][' . $key . ']" id="schema_wp_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
				echo $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function radio_callback( $args ) {

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key )
				$checked = true;
			elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) )
				$checked = true;

			echo '<label for="schema_wp_settings[' . $args['id'] . '][' . $key . ']">';
			echo '<input name="schema_wp_settings[' . $args['id'] . ']"" id="schema_wp_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
			echo $option . '</label><br/>';
		endforeach;

		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function text_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * URL Callback
	 *
	 * Renders URL fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function url_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="url" class="' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function number_callback( $args ) {

		// Get value, with special consideration for 0 values, and never allowing negative values
		$value = isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = schema_wp_abs_number_round( $value );

		// Other attributes and their defaults
		$max  = isset( $args['max'] )  ? $args['max']  : 999999;
		$min  = isset( $args['min'] )  ? $args['min']  : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;
		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

		$html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="large-text" cols="50" rows="5" id="schema_wp_settings_' . $args['id'] . '" name="schema_wp_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since 1.3
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function password_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="password" class="' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.3.1
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function missing_callback($args) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'schema-wp' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @return void
	 */
	function select_callback($args) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<select id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the Schema Options
	 * @global $wp_version WordPress Version
	 */
	function rich_editor_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		ob_start();
		wp_editor( stripslashes( $value ), 'schema_wp_settings_' . $args['id'], array( 'textarea_name' => 'schema_wp_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @since 1.0
	 * @param array $args Arguements passed by the setting
	 */
	function upload_callback( $args ) {
		if( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<span>&nbsp;<input type="button" class="upload_image_button button-secondary" value="' . __( 'Upload File', 'schema-wp' ) . '"/></span>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';

		echo $html;
	}
	
	/**
	 * Image Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @since 1.0
	 * @param array $args Arguements passed by the setting
	 */
	function image_upload_callback( $args ) {
		if( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="upload_field ' . $size . '-text" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<span>&nbsp;<input type="button" class="upload_image_button button-secondary" value="' . __( 'Choose Image', 'schema-wp' ) . '"/></span>';
		$html .= '<p class="description"> '  . $args['desc'] . '</p>';
		
		if ( ! empty( $value ) ) {
			$html .= '<div id="preview_image">';
			$html .= '<img src="'.esc_attr( stripslashes( $value ) ).'" />';
			$html .= '</div>';
		} else {
			$html .= '<div id="preview_image" style="display: none;"></div>';
		}
		
		echo $html;
	}
	
		
	/**
	 * Image Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @since 1.5.2
	 * @param array $args Arguements passed by the setting
	 */
	function post_select_callback( $args ) {
		
		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';
		
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
	
}
