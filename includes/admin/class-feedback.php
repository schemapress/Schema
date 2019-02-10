<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @version   1.0
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Schema_DotOrg_Plugin_Feedback' ) ) :
class Schema_DotOrg_Plugin_Feedback {

        /**
         * Private variables.
         *
         * These should be customised for each project.
         */
        private $slug;        // The plugin slug
        private $name;        // The plugin name
        private $time_limit;  // The time limit at which notice is shown

        /**
         * Variables.
         */
        public $nobug_option;

        /**
         * Fire the constructor up :)
         */
        public function __construct( $args ) {

                $this->slug        = $args['slug'];
                $this->name        = $args['name'];
                if ( isset( $args['time_limit'] ) ) {
                        $this->time_limit  = $args['time_limit'];
                } else {
                        $this->time_limit = WEEK_IN_SECONDS;
                }

                $this->nobug_option = $this->slug . '-no-bug';

                // Loading main functionality
                add_action( 'admin_init', array( $this, 'check_installation_date' ) );
                add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
        }

        /**
         * Seconds to words.
         */
        public function seconds_to_words( $seconds ) {

                // Get the years
                $years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
                if ( $years > 1 ) {
                        return sprintf( __( '%s years', $this->slug ), $years );
                } elseif ( $years > 0) {
                        return __( 'a year', $this->slug );
                }

                // Get the weeks
                $weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
                if ( $weeks > 1 ) {
                        return sprintf( __( '%s weeks', $this->slug ), $weeks );
                } elseif ( $weeks > 0) {
                        return __( 'a week', $this->slug );
                }

                // Get the days
                $days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
                if ( $days > 1 ) {
                        return sprintf( __( '%s days', $this->slug ), $days );
                } elseif ( $days > 0) {
                        return __( 'a day', $this->slug );
                }

                // Get the hours
                $hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
                if ( $hours > 1 ) {
                        return sprintf( __( '%s hours', $this->slug ), $hours );
                } elseif ( $hours > 0) {
                        return __( 'an hour', $this->slug );
                }

                // Get the minutes
                $minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
                if ( $minutes > 1 ) {
                        return sprintf( __( '%s minutes', $this->slug ), $minutes );
                } elseif ( $minutes > 0) {
                        return __( 'a minute', $this->slug );
                }

                // Get the seconds
                $seconds = intval( $seconds ) % 60;
                if ( $seconds > 1 ) {
                        return sprintf( __( '%s seconds', $this->slug ), $seconds );
                } elseif ( $seconds > 0) {
                        return __( 'a second', $this->slug );
                }

                return;
        }

        /**
         * Check date on admin initiation and add to admin notice if it was more than the time limit.
         */
        public function check_installation_date() {

                if ( true != get_site_option( $this->nobug_option ) ) {

                        // If not installation date set, then add it
                        $install_date = get_site_option( $this->slug . '-activation-date' );
                        if ( '' == $install_date ) {
                                add_site_option( $this->slug . '-activation-date', time() );
                        }

                        // If difference between install date and now is greater than time limit, then display notice
                        if ( ( time() - $install_date ) >  $this->time_limit  ) {
                                add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
                        }

                }

        }

        /**
         * Display Admin Notice, asking for a review.
         */
        public function display_admin_notice() {

                $screen = get_current_screen(); 
                if ( isset( $screen->base ) && 'plugins' == $screen->base ) {

                        $no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'review-nonce' );
                        $time = $this->seconds_to_words( time() - get_site_option( $this->slug . '-activation-date' ) );
						
						
		?>
        <style>
			.notice.schema-notice {
				border-left-color: #990000 !important;
				padding: 20px;
			}
			.rtl .notice.schema-notice {
				border-right-color: #990000 !important;
			}
			.notice.notice.schema-notice .schema-notice-inner {
				display: table;
				width: 100%;
			}
			.notice.schema-notice .schema-notice-inner .schema-notice-icon,
			.notice.schema-notice .schema-notice-inner .schema-notice-content,
			.notice.schema-notice .schema-notice-inner .schema-install-now {
				display: table-cell;
				vertical-align: middle;
			}
			.notice.schema-notice .schema-notice-icon {
				color: #990000;
				font-size: 50px;
				width: 60px;
			}
			.notice.schema-notice .schema-notice-icon img {
				width: 60px;
			}
			.notice.schema-notice .schema-notice-content {
				padding: 0 20px;
			}
			.notice.schema-notice p {
				padding: 0;
				margin: 0;
			}
			.notice.schema-notice h3 {
				margin: 0 0 5px;
			}
			.notice.schema-notice .schema-install-now {
				text-align: center;
			}
			.notice.schema-notice .schema-install-now .schema-install-button {
				background-color: #990000;
				color: #fff;
				border-color: #660000;
				box-shadow: 0 1px 0 #660000;
				padding: 5px 30px;
				height: auto;
				line-height: 20px;
				text-transform: capitalize;
			}
			.notice.schema-notice .schema-install-now .schema-install-button span {
				padding-left: 20px;
			}
			.rtl .notice.schema-notice .schema-install-now .schema-install-button i {
				padding-right: 0;
				padding-left: 20px;
			}
			.notice.schema-notice .schema-install-now .schema-install-button:hover {
				background-color: #cc0000;
			}
			.notice.schema-notice .schema-install-now .schema-install-button:active {
				box-shadow: inset 0 1px 0 #cc0000;
				transform: translateY(1px);
			}
			@media (max-width: 767px) {
				.notice.schema-notice {
					padding: 10px;
				}
				.notice.schema-noticee .schema-notice-inner {
					display: block;
				}
				.notice.schema-notice .schema-notice-inner .schema-notice-content {
					display: block;
					padding: 0;
				}
				.notice.schema-notice .schema-notice-inner .schema-notice-icon,
				.notice.schema-notice .schema-notice-inner .schema-install-now {
					display: none;
				}
			}
		</style>
        <div class="notice updated schema-notice">
			<div class="schema-notice-inner">
				<div class="schema-notice-icon">
                    <img src="<?php echo esc_url( SCHEMAWP_PLUGIN_URL . 'assets/images/icon-128x128.png' ); ?>" alt="Schema Logo" />
				</div>
                
				<div class="schema-notice-content">
					<h3><?php _e( 'How do you like Schema?', 'schema-wp' ); ?></h3>
					
                    <p><?php echo sprintf( __( 'You have been using the %s plugin for %s now!', 'schema-wp' ), $this->name, $time ) ?>
                   
                    <?php echo ' '.  __( 'Let us know what you think about the plugin, what is missing, and how we can make it better. Leave us a review with', 'schema-wp' ) . ' <a onclick="location.href=\'' . esc_url( $no_bug_url ) . '\';" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform' ) . '" target="_blank">' . __( 'your feedback', 'schema-wp' ) . '</a>.'; ?></p>
                    
				</div>

				<div class="schema-install-now">
                <?php echo '
				<a onclick="location.href=\'' . esc_url( $no_bug_url ) . '\';" class="button schema-install-button" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform' ) . '" target="_blank">' . __( 'Leave Feedback', 'schema-wp' ) . ' <span class="dashicons dashicons-smiley"></span></a>
				'; ?>
                    <br /><br />
                    <a href="<?php echo esc_url( $no_bug_url ); ?>"><?php echo __( 'No thanks', 'schema-wp' ); ?></a>
				</div>
			</div>
		</div>
        <?php
   

                }

        }

        /**
         * Set the plugin to no longer bug users if user asks not to be.
         */
        public function set_no_bug() {

                // Bail out if not on correct page
                if (
                        ! isset( $_GET['_wpnonce'] )
                        ||
                        (
                                ! wp_verify_nonce( $_GET['_wpnonce'], 'review-nonce' )
                                ||
                                ! is_admin()
                                ||
                                ! isset( $_GET[$this->nobug_option] )
                                ||
                                ! current_user_can( 'manage_options' )
                        )
                ) {
                        return;
                }

                add_site_option( $this->nobug_option, true );

        }

}
endif;


/*
* Instantiate Schema_DotOrg_Plugin_Feedback class
*
* @since 1.5.4
*/
new Schema_DotOrg_Plugin_Feedback( array(
	'slug'        => 'schema',						// The plugin slug
	'name'        => __('Schema', 'schema-wp'), 	// The plugin name
	'time_limit'  => WEEK_IN_SECONDS,				// The time limit at which notice is shown
) );
