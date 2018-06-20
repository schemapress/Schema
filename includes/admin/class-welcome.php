<?php
/**
 * Welcome Page Class
 *
 * @package     Schema
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Schema_WP_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0
 */
class Schema_WP_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head'  ) );
		add_action( 'admin_init', array( $this, 'welcome'     ), 9999 );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menus() {

		// What's New
		add_dashboard_page(
			__( 'What\'s new in Schema', 'schema-wp' ),
			__( 'What\'s new in Schema', 'schema-wp' ),
			$this->minimum_capability,
			'schema-wp-what-is-new',
			array( $this, 'whats_new_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with Schema', 'schema-wp' ),
			__( 'Getting started with Schema', 'schema-wp' ),
			$this->minimum_capability,
			'schema-wp-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Credits Page
		add_dashboard_page(
			__( 'The people that build Schema', 'schema-wp' ),
			__( 'The people that build Schema', 'schema-wp' ),
			$this->minimum_capability,
			'schema-wp-credits',
			array( $this, 'credits_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'schema-wp-what-is-new' );
		remove_submenu_page( 'index.php', 'schema-wp-getting-started' );
		remove_submenu_page( 'index.php', 'schema-wp-credits' );

		$page = isset( $_GET['page'] ) ? $_GET['page'] : false;

		if ( 'schema-wp-what-is-new' != $page  && 'schema-wp-getting-started' != $page && 'schema-wp-credits' != $page ) {
			return;
		}

		// Badge for welcome page
		$badge_url = SCHEMAWP_PLUGIN_URL . 'assets/images/schema-badge.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.schema-wp-badge {
			height: 128px;
			width: 128px;
			position: relative;
			color: #777777;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			background: url('<?php echo esc_url( $badge_url ); ?>') no-repeat;
		}
		.schema-wp-badge span {
			position: absolute;
			bottom: -30px;
			left: 0;
			width: 100%;
		}
		.about-wrap .schema-wp-badge {
			position: absolute;
			top: 0;
			right: 0;
		}
		.schema-wp-welcome-screenshots {
			float: right;
			margin-left: 60px !important;
		}
		.schema-wp-info-notice {
			border-left: 4px solid #5b9dd9;
			display: block;
		}
		.schema-wp-info-notice h3 {
			font-size: 1.6em !important;
		}
		.schema-wp-info-notice i {
			color: #5b9dd9;
		}
		@media (max-width: 800px) {
    		.schema-wp-welcome-screenshots {
				float: none;
				margin-left: 0px !important;
			}
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'schema-wp-getting-started';
		?>
		<h2 class="nav-tab-wrapper">
			
			<a class="nav-tab <?php echo $selected == 'schema-wp-what-is-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'schema-wp-what-is-new' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'schema-wp' ); ?>
            
            <a class="nav-tab <?php echo $selected == 'schema-wp-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'schema-wp-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'schema-wp' ); ?>
			</a>
            
			<a class="nav-tab <?php echo $selected == 'schema-wp-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'schema-wp-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'schema-wp' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function whats_new_screen() {
		list( $display_version ) = explode( '-', SCHEMAWP_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Schema v%s', 'schema-wp' ), esc_html( $display_version ) ); ?></h1>
			<div class="about-text"><?php echo __( 'Thank you for installing Schema. The best Schema.org plugin for WordPress.', 'schema-wp'); ?></div>
			
            <div class="schema-wp-badge">
            	<span><?php printf( __( 'Version %s', 'schema-wp' ), esc_html( $display_version ) ); ?></span>
            </div>
			   
			<?php $this->tabs(); ?>
			
			<div class="changelog">
				
                <div class="update-nag schema-wp-info-notice">
                 <h3><?php _e( 'First-time Schema configuration!', 'schema-wp' );?></h3>
					<p><?php _e( 'Get started quickly with the Schema configuration wizard!', 'schema-wp' );?></p>
                    <p>
                    	<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=schema' ) ); ?>"><?php _e( 'Plugin Settings', 'schema-wp' ); ?></a>
                        <a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=schema-setup' ) ); ?>"><?php _e( 'Quick Configuration Wizard', 'schema-wp' ); ?></a>
                    </p> 
					
                </div>
                 
				<div class="feature-section sub-section">
                    
                    <h2><?php _e( 'Support More Schema Types', 'schema-wp' );?></h2>
					<p><?php _e( 'Now, Schema plugin supports more schema.org types.', 'schema-wp' );?></p>
                    
                    - <a href="https://schema.org/Article" target="_blank"></a><?php _e( 'Article', 'schema-wp' );?>
                    <ul>
                        	<li><?php _e( 'General', 'schema-wp' );?></li>
                        	<li><?php _e( 'BlogPosting', 'schema-wp' );?></li>
                        	<li><?php _e( 'NewsArticle', 'schema-wp' );?></li>
                            <li><?php _e( 'Report', 'schema-wp' );?></li>
                        	<li><?php _e( 'ScholarlyArticle', 'schema-wp' );?></li>
                        	<li><?php _e( 'TechArticle', 'schema-wp' );?></li>
             		</ul>
                    
                    <br>
                    
                    - <?php _e( 'Blog', 'schema-wp' );?> (<?php _e( 'for Blog posts list page', 'schema-wp' );?>)
                    
                    <br>
                    
                    - <?php _e( 'WPHeader', 'schema-wp' );?> (<?php _e( 'for Web Page Header', 'schema-wp' );?>)
                    
                    <br>
                    
                    - <?php _e( 'WPFooter', 'schema-wp' );?> (<?php _e( 'for Web Page Footer', 'schema-wp' );?>)
                    
                     <br>
                    
                    - <?php _e( 'BreadcrumbList', 'schema-wp' );?> (<?php _e( 'for Breadcrumbs', 'schema-wp' );?>)
                    
                     <br>
                    
                    - <?php _e( 'CollectionPage', 'schema-wp' );?> (<?php _e( 'for Categories', 'schema-wp' );?>)
                    
                     <br>
                    
                    - <?php _e( 'CollectionPage', 'schema-wp' );?> (<?php _e( 'for Tags', 'schema-wp' );?>)
                    
                    <br>
                    
                    - <?php _e( 'AboutPage', 'schema-wp' );?> (<?php _e( 'for the about page', 'schema-wp' );?>)
                    
                    <br>
                    
                    - <?php _e( 'ContactPage', 'schema-wp' );?> (<?php _e( 'for the contact page', 'schema-wp' );?>)
                    
                    <br>
                    
                    - <?php _e( 'Person', 'schema-wp' );?> (<?php _e( 'author archive', 'schema-wp' );?>)
                    
                    <br><br>
                    
                    - <?php _e( 'New Schema Type?', 'schema-wp' );?>
                    <ul>
                        	<li><?php _e( 'Maybe coming soon!', 'schema-wp' );?></li>
                    </ul>
            		
                    <div class="return-to-dashboard">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=schema' ) ); ?>"><i class="dashicons dashicons-admin-generic"></i><?php _e( 'Go To Schema Settings', 'schema-wp' ); ?></a>
					</div>
            		
                   
                    <h2><?php _e( 'Integration with Themes and other Plugins', 'schema-wp' );?></h2>
                    <p><?php _e( 'Schema plays nicely  and support themes mentioned below.', 'schema-wp' );?></p>
                    
                    <h3><?php _e( 'Play nicely with Yoast SEO', 'schema-wp' );?></h3>
					<p><?php _e( 'Now Schema plugin plays nicely with Yoast SEO plugin, you can have both plugins active with no conflicts.', 'schema-wp' );?></p>
                    
                    <h3><?php _e( 'Hello AMP!', 'schema-wp' );?></h3>
					<p><?php _e( 'If you are using the AMP plugin, Schema got you covered!', 'schema-wp' );?></p>
                    
                    <h3><?php _e( 'WPRichSnippets plugin', 'schema-wp' );?></h3>
					<p><?php _e( 'If you are using the WPRichSnippets plugin, Schema will behave!', 'schema-wp' );?></p>
                    
                    <h3><?php _e( 'Correct Genesis Schema Markup', 'schema-wp' );?></h3>
					<p><?php _e( 'Using Genesis Framework? The Schema plugin will automatically indicate that and correct Genesis Schema output.', 'schema-wp' );?></p>
                    <h3><?php _e( 'Uses Thesis Theme 2.x Post Image', 'schema-wp' );?></h3>
					<p><?php _e( 'Using Thesis? The Schema plugin will automatically indicate and use Thesis Post Image is are presented.', 'schema-wp' );?></p>
                    
                    <h3><?php _e( 'The SEO Framework plugin is active?', 'schema-wp' );?></h3>
					<p><?php _e( 'No problem! The Schema plugin will automatically indicate that and show respect for SEO Framework.', 'schema-wp' );?></p>
					
                    <h3><?php _e( 'Is Divi your Theme?', 'schema-wp' );?></h3>
					<p><?php _e( 'If Divi theme is active, Schema plugin will clear shortcodes to be able to output the content description.', 'schema-wp' );?></p>
                
                </div>
             	
               	<div class="schema-types-section sub-section">
                
                	<h2><?php _e( 'Schema Post Type', 'schema-wp' );?></h2>
					<p><?php _e( 'Now, you can create new schema.org markup types and enable them on post type bases.', 'schema-wp' );?></p>
                    <p><?php _e( 'Also, you can set Schema to work on specific post categories.', 'schema-wp' );?></p>
                    
                    <img src="<?php echo esc_url( SCHEMAWP_PLUGIN_URL . 'assets/images/screenshot-2.png' ); ?>" class="schema-wp-welcome-screenshots"/>
                    
                    <h2><?php _e( 'Automatically add VideoObject to oEmbed', 'schema-wp' );?></h2>
					<p><?php _e( 'Schema allow you to enable VideoObject markup automatically whenever oEmbed is called on your page.', 'schema-wp' );?></p>
                    <p><?php _e( 'Supported oEmbed videos: Dailymotion, TED, Vimeo, VideoPress, Vine, YouTube.', 'schema-wp' );?></p>
                    
                    <h2><?php _e( 'Automatically add AudioObject to oEmbed', 'schema-wp' );?></h2>
					<p><?php _e( 'Schema allow you to enable AudioObject markup automatically whenever oEmbed is called on your page.', 'schema-wp' );?></p>
                    <p><?php _e( 'Supported oEmbed audios: SoundCloud, and Mixcloud.', 'schema-wp' );?></p>
                    
                    
                    
                </div>    
                
			</div>

			
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function getting_started_screen() {
		list( $display_version ) = explode( '-', SCHEMAWP_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Schema v%s', 'schema-wp' ), esc_html( $display_version ) ); ?></h1>
			<div class="about-text"><?php echo __( 'Thank you for installing Schema. The best schema.org plugin for WordPress.', 'schema-wp' ); ?></div>
            <div class="schema-wp-badge"><span><?php printf( __( 'Version %s', 'schema-wp' ), esc_html( $display_version ) ); ?></span></div>
			        
			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Hang on! We are going to add more schema integration and cool features to Schema plugin.', 'schema-wp' ); ?></p>

			<div class="changelog">
				<h3><?php _e( 'Overview', 'schema-wp' );?></h3>

				<div class="feature-section">
					<img src="<?php echo esc_url( SCHEMAWP_PLUGIN_URL . 'assets/images/serps.png' ); ?>" class="schema-wp-welcome-screenshots"/>
					
                    <h4><?php _e( 'What is Schema markup?', 'schema-wp' );?></h4>
					<p><?php _e( 'Schema markup is code (semantic vocabulary) that you put on your website to help the search engines return more informative results for users. So, Schema is not just for SEO reasons, it’s also for the benefit of the searcher.' ,'schema-wp' ); ?></p>
                    
					<h4><?php _e( 'Why is Structured Data so Important?', 'schema-wp' );?></h4>
					<p><?php _e( 'Structured Data can help you to send the right signals to search engines about your business and content.' ,'schema-wp' ); ?></p>
                    <p><?php _e('Structured Data helps search engines to understand what the content is specifically about. Moreover, structured data will allow users to see the value of a website before they visit, via rich snippets, which are rich data that are displayed in the SERP’s.', 'schema-wp') ?></p>
				
                	<div class="return-to-dashboard">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=schema' ) ); ?>"><i class="dashicons dashicons-admin-generic"></i><?php _e( 'Go To Schema Settings', 'schema-wp' ); ?></a>
					</div>
                
                </div>
				
			</div>

			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'schema-wp' );?></h3>
				
                <div class="feature-section">
					<h4><?php _e( 'Documentation','schema-wp' );?></h4>
					<p><?php _e( 'Docs are on its way! We will update <a href="http://schema.press/">schema.press</a> site with plugin documentation soon.', 'schema-wp' );?></p>
				</div>
                
				<div class="feature-section">
					<h4><?php _e( 'Support','schema-wp' );?></h4>
					<p><?php _e( 'We do our best to provide support we can. If you encounter a problem, report it to <a href="https://wordpress.org/support/plugin/schema">support</a>.', 'schema-wp' );?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Credits Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function credits_screen() {
		list( $display_version ) = explode( '-', SCHEMAWP_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Schema v%s', 'schema-wp' ), esc_html( $display_version ) ); ?></h1>
			<div class="about-text"><?php _e( 'Thank you for updating to the latest version!', 'schema-wp' ); ?></div>
			<div class="schema-wp-badge"><span><?php printf( __( 'Version %s', 'schema-wp' ), esc_html( $display_version ) ); ?></span></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Here, we will be listing some of the faces that have helped build Schema.', 'schema-wp' ); ?></p>

			<?php echo $this->contributors(); ?>
		</div>
		<?php
	}

	/**
	 * Render Contributors List
	 *
	 * @since 1.0
	 * @uses Schema_WP_Welcome::get_contributors()
	 * @return string $contributor_list HTML formatted list of all the contributors for Schema
	 */
	public function contributors() {
		$contributors = $this->get_contributors();

		if ( empty( $contributors ) ) {
			return '';
		}

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s" title="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'schema-wp' ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retreive list of contributors from GitHub.
	 *
	 * @access public
	 * @since 1.0
	 * @return array $contributors List of contributors
	 */
	public function get_contributors() {
		
		//@ todo
		return;
	}

	/**
	 * Sends user to the Welcome page on first activation of affwp as well as each
	 * time affwp is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function welcome() {

		// Bail if no activation redirect
		if ( ! get_transient( '_schema_wp_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_schema_wp_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		$upgrade = get_option( 'schema_wp_version_upgraded_from' );

		if ( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=schema-wp-getting-started' ) );
			exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=schema-wp-what-is-new' ) );
			exit;
		}
	}
}

new Schema_WP_Welcome;
