<?php
/**
 * Plugin Name: Schema
 * Plugin URI: https://schema.press
 * Description: The next generation of Structured Data.
 * Author: Hesham
 * Author URI: http://zebida.com
 * Version: 1.7
 * Text Domain: schema-wp
 * Domain Path: languages
 *
 * Schema is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Schema is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Schema. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Schema
 * @category Core
 * @author Hesham Zebida
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Schema_WP' ) ) :

/**
 * Main Schema_WP Class
 *
 * @since 1.0
 */
final class Schema_WP {
	/** Singleton *************************************************************/

	/**
	 * @var Schema_WP The one true Schema_WP
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * The version number of Schema
	 *
	 * @since 1.0
	 */
	private $version = '1.7';

	/**
	 * The settings instance variable
	 *
	 * @var Schema_WP_Settings
	 * @since 1.0
	 */
	public $settings;

	/**
	 * The rewrite class instance variable
	 *
	 * @var Schema_WP_Rewrites
	 * @since 1.0
	 */
	public $rewrites;

	/**
	 * Main Schema_WP Instance
	 *
	 * Insures that only one instance of Schema_WP exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses Schema_WP::setup_globals() Setup the globals needed
	 * @uses Schema_WP::includes() Include the required files
	 * @return Schema_WP
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SCHEMA_WP ) ) {
			self::$instance = new SCHEMA_WP;

			if( version_compare( PHP_VERSION, '5.4', '<' ) ) {

				add_action( 'admin_notices', array( 'SCHEMA_WP', 'below_php_version_notice' ) );

				return self::$instance;
			}

			self::$instance->setup_constants();
			self::$instance->includes();

			add_action( 'plugins_loaded', array( self::$instance, 'setup_objects' ), -1 );
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			
			// initialize the classes
        	add_action( 'plugins_loaded', array( self::$instance, 'init_classes' ), 5 );
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'schema-wp' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'schema-wp' ), '1.0' );
	}

	/**
	 * Show a warning to sites running PHP < 5.3
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function below_php_version_notice() {
		echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by Schema plugin. Please contact your host and request that your version be upgraded to 5.4 or later.', 'schema-wp' ) . '</p></div>';
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version
		if ( ! defined( 'SCHEMAWP_VERSION' ) ) {
			define( 'SCHEMAWP_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'SCHEMAWP_PLUGIN_DIR' ) ) {
			define( 'SCHEMAWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'SCHEMAWP_PLUGIN_URL' ) ) {
			define( 'SCHEMAWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'SCHEMAWP_PLUGIN_FILE' ) ) {
			define( 'SCHEMAWP_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
		
		global $schema_wp_options;
		
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
		
		// get settings
		$schema_wp_options = schema_wp_get_settings();
		
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/class-capabilities.php';
		
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/post-type/schema-post-type.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/post-type/schema-wp-submit.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/post-type/schema-wp-ajax.php';
		
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/admin-functions.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/ref.php';
		
		if( is_admin() ) {
		
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/meta/class-meta.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/meta.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/meta-tax/class-meta-tax.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/meta-tax.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/meta-exclude.php';
			
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/settings/contextual-help.php';
			
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/admin-pages.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/extensions.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/scripts.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/class-menu.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/class-notices.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/class-welcome.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/class-setup-wizard.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/class-feedback.php';
			
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/post-type/class-columns.php';
			require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/post-type/schema-columns.php';
		}

		require_once SCHEMAWP_PLUGIN_DIR . 'includes/misc-functions.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/scripts.php';
		
		// Schema outputs
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/web-page-element.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/knowledge-graph.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/search-results.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/blog.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/category.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/tag.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/post-type-archive.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/taxonomy.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/author.php';
		
		// Schema main output
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/json/schema-output.php';
		
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/admin-bar-menu.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/admin/updater/class-license-handler.php';
		
		// Plugin Integrations
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/yoast-seo.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/amp.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/wp-rich-snippets.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/seo-framework.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/visual-composer.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/thirstyaffiliates.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/woocommerce.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/edd.php';
		
		// Theme Integrations
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/genesis.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/thesis.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/integrations/divi.php';
		
		// Core Extensions
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/post-meta-generator.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/breadcrumbs.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/author.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/page-about.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/page-contact.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/video-object.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/audio-object.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/sameAs.php';
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/extensions/comment.php';
		
		// Install
		require_once SCHEMAWP_PLUGIN_DIR . 'includes/install.php';
	}
	
	/**
     * Init all the classes
     *
     * @return void
     */
    function init_classes() {
        if ( is_admin() ) {
            new Schema_WP_Setup_Wizard();
        }
    }
	
	/**
	 * Setup all objects
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function setup_objects() {

		//self::$instance->settings       = new Schema_WP_Settings;
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( SCHEMAWP_PLUGIN_FILE ) ) . '/languages/';
		$lang_dir = apply_filters( 'schema_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'schema-wp' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'schema-wp', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/schema-wp/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/schema/ folder
			load_textdomain( 'schema-wp', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/schema/languages/ folder
			load_textdomain( 'schema-wp', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'schema-wp', false, $lang_dir );
		}
	}
}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Schema_WP
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $schema_wp = schema_wp(); ?>
 *
 * @since 1.0
 * @return Schema_WP The one true Schema_WP Instance
 */
function schema_wp() {
	return Schema_WP::instance();
}
schema_wp();
