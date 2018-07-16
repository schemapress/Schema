<?php
/**
 * Setup wizard class
 *
 * Walkthrough to the basic setup
 *
 * @since 1.7
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Schema_WP_Setup_Wizard' ) ) :

/**
 * The class
 */
class Schema_WP_Setup_Wizard {
    /** @var string Currenct Step */
    protected $step   = '';

    /** @var array Steps for the setup wizard */
    protected $steps  = array();

    /**
     * Hook in tabs.
     */
    public function __construct() {
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_head', array( $this, 'hide_admin_menus' ) );
            add_action( 'admin_init', array( $this, 'setup_wizard' ), 99 );
		}
    }

    /**
     * Enqueue scripts & styles from woocommerce plugin.
     *
     * @return void
     */
    public function enqueue_scripts() {
		
        $suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix     = '';
		$url 		= SCHEMAWP_PLUGIN_URL . 'assets/vendors/woo-setup-wiz/';
		
        wp_register_script( 'jquery-blockui', $url . 'js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
        wp_register_script( 'selectWoo', $url . 'js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.1' );
        wp_register_script( 'wc-enhanced-select', $url . 'js/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), SCHEMAWP_VERSION );
        wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
            'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'schema-wp' ),
            'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'schema-wp' ),
            'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'schema-wp' ),
            'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'schema-wp' ),
            'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'schema-wp' ),
            'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'schema-wp' ),
            'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'schema-wp' ),
            'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'schema-wp' ),
            'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'schema-wp' ),
            'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'schema-wp' ),
            'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'schema-wp' ),
            'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'schema-wp' ),
            'ajax_url'                  => admin_url( 'admin-ajax.php' ),
        ) );
		
		wp_enqueue_style( 'schema-wp-admin', SCHEMAWP_PLUGIN_URL . 'assets/css/admin' . $suffix . '.css', SCHEMAWP_VERSION );
        wp_enqueue_style( 'woocommerce_admin_styles', $url . 'css/admin.css', array(), SCHEMAWP_VERSION );
        wp_enqueue_style( 'wc-setup', $url . 'css/wc-setup.css', array( 'dashicons', 'install' ), SCHEMAWP_VERSION );
		
		wp_enqueue_style( 'schema-wp-setup', $url . 'css/schema-setup.css', SCHEMAWP_VERSION );

        wp_register_script( 'wc-setup', $url . 'js/wc-setup.js', array( 'jquery', 'wc-enhanced-select', 'jquery-blockui' ), SCHEMAWP_VERSION );
        wp_localize_script( 'wc-setup', 'wc_setup_params', array() );
		
		wp_register_script( 'schema-wp-setup', $url . 'js/schema-setup.js', array( 'jquery' ), SCHEMAWP_VERSION );
		wp_localize_script( 'schema-wp-setup', 'schema_wp_vars', array(
			//'post_id'                     => isset( $post->ID ) ? $post->ID : null,
			'post_id'                     => null,
			'schema_wp_version'           => SCHEMAWP_VERSION,
			'use_this_file'               => __( 'Use This File', 'schema-wp' ),
			'remove_text'                 => __( 'Remove', 'schema-wp' ),
			'new_media_ui'                => apply_filters( 'schema_wp_use_35_media_ui', 1 ),
			'unsupported_browser'         => __( 'We are sorry but your browser is not compatible with this kind of file upload. Please upgrade your browser.', 'schema-wp' ),
		));
	
		//wp_enqueue_style( 'wp-color-picker' );
		//wp_enqueue_script( 'wp-color-picker' );

		// For media uploader
		wp_enqueue_media();
	
		//wp_enqueue_script( 'jquery-ui-datepicker' );
		//wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
	
		wp_enqueue_script( 'media-upload' );
		//wp_enqueue_script( 'thickbox' );
		//wp_enqueue_style( 'thickbox' );
    }

    /**
     * Add admin menus/screens.
     */
    public function admin_menus() {
        add_dashboard_page( __('Schema Setup', 'schema-wp'), __('Schema Setup Wizard', 'schema-wp'), 'manage_options', 'schema-setup', '' );
    }
		
	/**
     * Hide admin menus/screens.
	 *
	 * @since 1.7.1
     */
    public function hide_admin_menus() {
        remove_submenu_page( 'index.php', 'schema-setup' );
    }

    /**
     * Show the setup wizard.
     */
    public function setup_wizard() {
        if ( empty( $_GET['page'] ) || 'schema-setup' !== $_GET['page'] ) {
            return;
        }
        $this->steps = array(
            'introduction' => array(
                'name'    =>  __( 'Introduction', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_introduction' ),
                'handler' => ''
            ),
			'site_type' => array(
                'name'    =>  __( 'Site Type', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_site_type' ),
                'handler' => array( $this, 'schema_setup_save' ),
            ),
            'general_schema' => array(
                'name'    =>  __( 'General', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_general' ),
                'handler' => array( $this, 'schema_setup_save' ),
            ),
			'social_profiles' => array(
                'name'    =>  __( 'Social Profiles', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_social_profiles' ),
                'handler' => array( $this, 'schema_setup_save' ),
            ),
            'schemas' => array(
                'name'    =>  __( 'Schemas', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_schemas' ),
                'handler' => array( $this, 'schema_setup_save' ),
            ),
            'next_steps' => array(
                'name'    =>  __( 'Ready!', 'schema-wp' ),
                'view'    => array( $this, 'schema_setup_ready' ),
                'handler' => ''
            )
        );
        $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

        $this->enqueue_scripts();

        if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) { // WPCS: CSRF ok.
            call_user_func( $this->steps[ $this->step ]['handler'] );
        }

        ob_start();
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        exit;
    }

    public function get_next_step_link() {
        $keys = array_keys( $this->steps );

        return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ] );
    }

    /**
     * Setup Wizard Header.
     */
    public function setup_wizard_header() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php _e( 'Schema &rsaquo; Setup Wizard', 'schema-wp' ); ?></title>
			<?php wp_print_scripts( 'wc-setup' ); ?>
            <?php wp_print_scripts( 'schema-wp-setup' ); ?>
            <?php do_action( 'admin_print_styles' ); ?>
            <?php do_action( 'admin_head' ); ?>
			<style type="text/css">
                .wc-setup-steps {
                    justify-content: center;
                }
                .wc-setup-content a {
                    color: #c90000;
                }
                .wc-setup-steps li.active:before {
                    border-color: #c90000;
                }
                .wc-setup-steps li.active {
                    border-color: #c90000;
                    color: #c90000;
                }
                .wc-setup-steps li.done:before {
                    border-color: #c90000;
					background: #c90000;
                }
                .wc-setup-steps li.done {
                    border-color: #c90000;
                    color: #c90000;
                }
                .wc-setup .wc-setup-actions .button-primary, .wc-setup .wc-setup-actions .button-primary, .wc-setup .wc-setup-actions .button-primary {
                    background: #c90000 !important;
                }
                .wc-setup .wc-setup-actions .button-primary:active, .wc-setup .wc-setup-actions .button-primary:focus, .wc-setup .wc-setup-actions .button-primary:hover {
                    background: #660000 !important;
                    border-color: #660000 !important;
                }
                .wc-setup-content .wc-setup-next-steps ul .setup-product a, .wc-setup-content .wc-setup-next-steps ul .setup-product a, .wc-setup-content .wc-setup-next-steps ul .setup-product a {
                    background: #c90000 !important;
                    box-shadow: inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #c90000;
                }
                .wc-setup-content .wc-setup-next-steps ul .setup-product a:active, .wc-setup-content .wc-setup-next-steps ul .setup-product a:focus, .wc-setup-content .wc-setup-next-steps ul .setup-product a:hover {
                    background: #660000 !important;
                    border-color: #660000 !important;
                    box-shadow: inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #660000;
                }
                .wc-setup .wc-setup-actions .button-primary {
                    border-color: #c90000 !important;
                }
                .wc-setup-content .wc-setup-next-steps ul .setup-product a {
                    border-color: #c90000 !important;
				}
            </style>
        </head>
        <body class="wc-setup wp-core-ui">
            <?php
                $badge_url = SCHEMAWP_PLUGIN_URL . 'assets/images/schema-badge.png';
				//$badge_url = SCHEMAWP_PLUGIN_URL . 'assets/images/icon-128x128.png';
				
            ?>
            <h1 id="wc-logo"><a href="https://schema.press"><img src="<?php echo $badge_url; ?>" alt="Schema" /></a></h1>
        <?php
    }

    /**
     * Setup Wizard Footer.
     */
    public function setup_wizard_footer() {
        ?>
            <?php if ( 'next_steps' === $this->step ) : ?>
                <a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress Dashboard', 'schema-wp' ); ?></a>
            <?php endif; ?>
            </body>
            <?php do_action( 'admin_footer' ); ?>
			<?php do_action( 'admin_print_footer_scripts' ); ?>
            <?php //wp_footer(); ?>
        </html>
        <?php
    }

    /**
     * Output the steps.
     */
    public function setup_wizard_steps() {
        $ouput_steps = $this->steps;
        array_shift( $ouput_steps );
        ?>
        <ol class="wc-setup-steps">
            <?php foreach ( $ouput_steps as $step_key => $step ) : ?>
                <li class="<?php
                    if ( $step_key === $this->step ) {
                        echo 'active';
                    } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                        echo 'done';
                    }
                ?>"><?php echo esc_html( $step['name'] ); ?></li>
            <?php endforeach; ?>
        </ol>
        <?php
    }

    /**
     * Output the content for the current step.
     */
    public function setup_wizard_content() {
        echo '<div class="wc-setup-content schema-setup-content">';
        call_user_func( $this->steps[ $this->step ]['view'] );
        echo '</div>';
    }
	
	 /**
     * Save options.
     */
    public function schema_setup_save() {
        check_admin_referer( 'schema-setup' );
	
		$options 			= get_option( 'schema_wp_settings', array() );
		$sanitize_settings 	= array_map("strip_tags", $_POST['schema_wp_settings']); // sanitize
		$new_settings		= array_replace( $options, $sanitize_settings); // merge
		
		update_option( 'schema_wp_settings', $new_settings );
		
        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }
	
    /**
     * Introduction step.
     */
    public function schema_setup_introduction() {
        ?>
        <h1><?php _e( 'Welcome to Schema!', 'schema-wp' ); ?></h1>
        <p><?php _e( 'Thank you for choosing Schema to power your website! This quick setup wizard will help you configure the basic settings. <strong>It’s completely optional and shouldn’t take longer than two minutes.</strong>', 'schema-wp' ); ?></p>
        <p><?php _e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'schema-wp' ); ?></p>
        <p class="wc-setup-actions step">
            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php _e( 'Let\'s Go!', 'schema-wp' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=schema' ) ); ?>" class="button button-large"><?php _e( 'Not right now', 'schema-wp' ); ?></a>
        </p>
        <?php
    }
	
	/**
     * Person or Organization step.
     */
    public function schema_setup_site_type() {
        ?>
        <h1><?php _e( 'Site Type', 'schema-wp' ); ?></h1>
        
        <p><?php _e( 'What is your site about?', 'schema-wp' ); ?></p>
        
        <p class="description"><?php _e( 'This information can help us to prioritize future additions to our plugin for specific types of sites', 'schema-wp' ); ?>.</p>

        <form method="post">
            <table class="form-table">
                <tr>
                	<th scope="row"><label for="site_type"><?php //_e( 'This Website Represent', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_radio_callback( array(
						'id' => 'site_type',
						'name' => __( 'Site Type', 'schema-wp' ),
						'desc' => '',
						'type' => 'radio',
						'options' => array(
							'blog'					=> 'Blog or Personal Website',
							'online_shop' 			=> 'Online Shop',
							'news_chanel' 			=> 'News and Magazine',
							'offline_business' 		=> 'Small Offline Business',
							'corporation' 			=> 'Corporation',
							'portfolio' 			=> 'Portfolio',
							//'photography'			=> 'Photography',
							//'music'					=> 'Music',
							//'niche_affiliate'		=> 'Niche Affiliate / Reviews',
							//'business_directory' 	=> 'Online Business Directory',
							//'job_board'				=> 'Online Job Board',
							//'knowledgebase'			=> 'Knowledgebase / Wiki',
							//'question_answer'		=> 'Question & Answer',
							//'school'				=> 'School or College',
							'else' 					=> 'Something else'
						),
						'std' => '',
					)); ?></td>
                </tr>
            </table>
            <p class="wc-setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'schema-wp' ); ?>" name="save_step" />
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php _e( 'Skip this step', 'schema-wp' ); ?></a>
                <?php wp_nonce_field( 'schema-setup' ); ?>
            </p>
        </form>
        <?php
    }
	
    /**
     * General step.
     */
    public function schema_setup_general() {
        ?>
        <h1><?php _e( 'Person or Organization', 'schema-wp' ); ?></h1>
        
        <p><?php _e( 'Does your site represent a Person or Organization?', 'schema-wp' ); ?></p>
        
        <p class="description"><?php _e( 'This information will be used in Google\'s Knowledge Graph Card, the big block of information you see on the right side of the search results.', 'schema-wp' ); ?></p>

        <form method="post">
            <table class="form-table">
                <tr>
                	<th scope="row"><label for="organization_or_person"><?php _e( 'This Website Represent', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_radio_callback( array(
						'id' => 'organization_or_person',
						'name' => __( 'Organization or Person?', 'schema-wp' ),
						'desc' => '',
						'type' => 'radio',
						'options' => array(
							'organization'	=> 'Organization',
							'person' 		=> 'Person'
						),
						'std' => '',
					)); ?></td>
                </tr>
                <tr class="organization_or_person">
                	<th scope="row"><label for="name"><?php _e( 'Name', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'name',
						'name' => __( 'Name', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => get_bloginfo( 'name' ),
						'readonly' => false
					)); ?></td>
                </tr>
                <tr class="organization-logo">
                    <th scope="row"><label for="logo"><?php _e( 'Organization Logo', 'schema-wp' ); ?></label></th>
                    <td> 
                    <?php
                         schema_wp_wiz_image_upload_callback( array(
							'id' => 'logo',
							'name' => __( 'Logo', 'schema-wp' ),
							'desc' => __( 'Specify the image of your organization\'s logo to be used in Google Search results and in the Knowledge Graph.<br />Learn more about', 'schema-wp') . ' <a href="https://developers.google.com/search/docs/data-types/logo" target="_blank">'.__('Logo guidelines', 'schema-wp').'</a>',
							'type' => 'image_upload',
							'std' => ''
						));
                    ?>
                    </td>
                </tr>
            </table>
            <p class="wc-setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'schema-wp' ); ?>" name="save_step" />
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php _e( 'Skip this step', 'schema-wp' ); ?></a>
                <?php wp_nonce_field( 'schema-setup' ); ?>
            </p>
        </form>
        <?php
    }

   /**
     * Social Profiles step.
     */
    public function schema_setup_social_profiles() {
       
        ?>
        <h1><?php _e( 'Social Profiles', 'schema-wp' ); ?></h1>
        
        <p><?php _e( 'Provide your social profile information to a Google Knowledge panel.', 'schema-wp' ); ?></p>
        
        <p class="description"><?php _e( 'Knowledge panels prominently display your social profile information in some Google Search results.', 'schema-wp' ); ?></p>

        <form method="post">
            <table class="form-table">
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Facebook', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'facebook',
						'name' => __( 'Facebook', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Twitter', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'twitter',
						'name' => __( 'Twitter', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Google+', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'google',
						'name' => __( 'Google+', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Instagram', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'instagram',
						'name' => __( 'Instagram', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'YouTube', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'youtube',
						'name' => __( 'YouTube', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'LinkedIn', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'linkedin',
						'name' => __( 'Linkedin', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Myspace', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'myspace',
						'name' => __( 'Myspace', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Pinterest', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'pinterest',
						'name' => __( 'Pinterest', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'SoundCloud', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'soundcloud',
						'name' => __( 'SoundCloud', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="name"><?php _e( 'Tumblr', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_text_callback( array(
						'id' => 'tumblr',
						'name' => __( 'Tumblr', 'schema-wp' ),
						'desc' => '',
						'type' => 'text',
						'std' => '',
						'placeholder' => 'https://',
						'readonly' => false
					)); ?></td>
                </tr>
            </table>
            <p class="wc-setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'schema-wp' ); ?>" name="save_step" />
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php _e( 'Skip this step', 'schema-wp' ); ?></a>
                <?php wp_nonce_field( 'schema-setup' ); ?>
            </p>
        </form>
        <?php
    }

    /**
     * General step.
     */
    public function schema_setup_schemas() {
        ?>
        <h1><?php _e( 'Mark Up Your Content', 'schema-wp' ); ?></h1>
        
        <p><?php _e( 'Automatically, add additional schema.org markups to your website content.', 'schema-wp' ); ?></p>
        
        <form method="post">
            <table class="form-table">
                <tr>
                	<th scope="row"><label for="about_page"><?php _e( 'About Page', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_post_select_callback( array(
							'id' => 'about_page',
							'name' => __( 'About Page', 'schema-wp' ),
							'desc' => __( '', 'schema-wp' ),
							'type' => 'post_select',
							'post_type' => 'page'
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="contact_page"><?php _e( 'Contact Page', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_wiz_post_select_callback( array(
							'id' => 'contact_page',
							'name' => __( 'Contact Page', 'schema-wp' ),
							'desc' => __( '', 'schema-wp' ),
							'type' => 'post_select',
							'post_type' => 'page'
					)); ?></td>
                </tr>
                <tr class="organization-logo">
                    <th scope="row"><label for="logo"><?php _e( 'Publisher Logo', 'schema-wp' ); ?></label></th>
                    <td> 
                    <?php
                         schema_wp_wiz_image_upload_callback( array(
							'id' => 'publisher_logo',
							'name' => __( 'Publisher Logo', 'schema-wp' ),
							'desc' => __( 'Publisher Logo should have a wide aspect ratio, not a square icon, it should be no wider than 600px, and no taller than 60px.', 'schema-wp' ) . ' <a href="https://developers.google.com/search/docs/data-types/articles#logo-guidelines" target="_blank">'.__('Logo guidelines', 'schema-wp').'</a>',
							'type' => 'image_upload',
							'std' => ''
						));
                    ?>
                    </td>
                </tr>
                <tr>
                	<th scope="row"><label for="web_page_element_enable"><?php _e( 'WPHeader and WPFooter', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_checkbox_callback( array(
							'id' => 'web_page_element_enable',
							'name' => __( 'WPHeader and WPFooter', 'schema-wp' ),
							'desc' => __( 'enable?', 'schema-wp' ),
							'type' => 'post_select'
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="breadcrumbs_enable"><?php _e( 'Breadcrumbs', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_checkbox_callback( array(
							'id' => 'breadcrumbs_enable',
							'name' => __( 'Breadcrumbs', 'schema-wp' ),
							'desc' => __( 'enable?', 'schema-wp' ),
							'type' => 'checkbox'
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="comments_enable"><?php _e( 'Comments', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_checkbox_callback( array(
							'id' => 'comments_enable',
							'name' => __( 'Comments', 'schema-wp' ),
							'desc' => __( 'enable?', 'schema-wp' ),
							'type' => 'checkbox'
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="video_object_enable"><?php _e( 'VideoObject', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_checkbox_callback( array(
							'id' => 'video_object_enable',
							'name' => __( 'VideoObject', 'schema-wp' ),
							'desc' => __( 'enable?', 'schema-wp' ),
							'type' => 'checkbox'
					)); ?></td>
                </tr>
                <tr>
                	<th scope="row"><label for="audio_object_enable"><?php _e( 'AudioObject', 'schema-wp' ); ?></label></th>
                    <td><?php schema_wp_checkbox_callback( array(
							'id' => 'audio_object_enable',
							'name' => __( 'AudioObject', 'schema-wp' ),
							'desc' => __( 'enable?', 'schema-wp' ),
							'type' => 'checkbox'
					)); ?></td>
                </tr>
            </table>
            <p class="wc-setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'schema-wp' ); ?>" name="save_step" />
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php _e( 'Skip this step', 'schema-wp' ); ?></a>
                <?php wp_nonce_field( 'schema-setup' ); ?>
            </p>
        </form>
        <?php
    }

    /**
     * Final step.
     */
    public function schema_setup_ready() {
        ?>
        <h1><?php _e( 'You\'ve done it!', 'schema-wp' ); ?></h1>
		
        <p><?php _e( 'Schema will now take care of all the needed technical optimization of your site.', 'schema-wp' ); ?></b>
        
         <p><?php _e( 'You can change settings from', 'schema-wp' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=schema' ) ); ?>"><?php _e( 'here', 'schema-wp' ); ?></a>, or check plugin <a href="https://schema.press/docs/" target="_blank">documentation</a>.</b>
        
        <div class="wc-setup-next-steps">
            <div class="wc-setup-next-steps-first">
                <h2><?php _e( 'Next Step', 'schema-wp' ); ?></h2>
                <ul>
                    <li class="setup-product"><a class="button button-primary button-large" href="<?php echo esc_url( admin_url( 'edit.php?post_type=schema' ) ); ?>"><?php _e( 'Configure Your Schema Types!', 'schema-wp' ); ?></a></li>
                </ul>
            </div>
            <!--
            <div class="wc-setup-next-steps-last">
                <h2><a href="<?php echo esc_url( admin_url( 'admin.php?page=schema-wp-what-is-new' ) ); ?>"><?php _e( 'Learn More', 'schema-wp' ); ?></a></h2>
            </div>
            -->
        </div>
        <?php
    }
}

endif;

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
function schema_wp_wiz_text_callback( $args ) {
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

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '" placeholder="' . $args['placeholder'] . '" ' . $readonly . '/>';
	//$html    .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'schema_wp_after_setting_output', $html, $args );
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
function schema_wp_wiz_select_callback($args) {
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

	$html = '<select class="wc-enhanced-select" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" ' . $chosen . 'data-placeholder="' . esc_html( $placeholder ) . '" />';
	
	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

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
function schema_wp_wiz_post_select_callback( $args ) {
		
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( $schema_wp_option ) {
		$value = $schema_wp_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
		
	$html = '<select class="wc-enhanced-select" id="schema_wp_settings[' . $args['id'] . ']" name="schema_wp_settings[' . $args['id'] . ']"/>';
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
 * Image Upload Callback
 *
 * Renders file upload fields.
 *
 * @since 1.0
 * @param array $args Arguements passed by the setting
 */
function schema_wp_wiz_image_upload_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );
	
	if( $schema_wp_option )
		$value = $schema_wp_option;
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" style="width:initial" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" name="schema_wp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="schema_wp_settings_upload_button button-secondary" style="width:initial" value="' . __( 'Select Image', 'wp-schema' ) . '"/></span>';
	
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		
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
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function schema_wp_wiz_checkbox_callback( $args ) {
	$schema_wp_option = schema_wp_get_option( $args['id'] );

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"';
	}
	
	if ( isset( $args['class_field'] ) ) {
		$class_field = 'class="' . schema_wp_sanitize_key( $args['class_field'] ) . ' wc-wizard-shipping-method-enable"';
	} else {
		$class_field = 'class="wc-wizard-shipping-method-enable"';
	}
	
	$checked  = ! empty( $schema_wp_option ) ? checked( 1, $schema_wp_option, false ) : '';
	$html     = '<input type="hidden"' . $name . ' value="-1" />';
	//$html    .= '<input ' . $class_field . 'type="checkbox" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . '/>';
	$html    .= '<input ' . $class_field . 'id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" type="checkbox"' . $name . ' value="1" ' . $checked . '/>';
										
	$html    .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

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
function schema_wp_wiz_radio_callback( $args ) {
	$schema_wp_options = schema_wp_get_option( $args['id'] );

	$html = '<fieldset class="">';

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( $schema_wp_options && $schema_wp_options == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! $schema_wp_options )
			$checked = true;

		$html .= '<input class="schema-wizard-radio" name="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . ']" id="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']" type="radio" value="' . schema_wp_sanitize_key( $key ) . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		$html .= '<label for="schema_wp_settings[' . schema_wp_sanitize_key( $args['id'] ) . '][' . schema_wp_sanitize_key( $key ) . ']">' . esc_html( $option ) . '</label><br/>';
	endforeach;
	
	$html .= '</fieldset>';
	
	$html .= '<p class="description">' . apply_filters( 'schema_wp_after_setting_output', wp_kses_post( $args['desc'] ), $args ) . '</p>';

	echo $html;
}
