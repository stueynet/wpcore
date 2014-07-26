<?php
/**
 * @package		WPCore
 * @author		stueynet
 * @link		http://stuey.net
 */
if ( ! class_exists( 'WPCore' ) ) {
	class WPCore {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @var     string
		 */
		const VERSION = '1.0.0';

		/**
		 *
		 * The variable name is used as the text domain when internationalizing strings
		 * of text. Its value should match the Text Domain file header in the main
		 * plugin file.
		 *
		 * @since    1.0.0
		 *
		 * @var      string
		 */
		protected $plugin_slug = 'wpcore';

		/**
		 * @var string
		 */
		protected $plugin_basename = 'wpcore';

		/**
		 * @var string
		 */
		protected $transient_key = 'wpcore_payload';

		/**
		 * @var int
		 */
		protected $transient_timeout = null;

		/**
		 * Instance of this class.
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 */

		protected $plugin_screen_hook_suffix = null;

		/**
		 *
		 */
		function __construct() {
			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Activate plugin when new blog is added
			add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

			// Main functions
			add_action( 'tgmpa_register', array( $this, 'wpcore_register_required_plugins') );

			// Admin stuff
			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

			// Register the settings
			add_action( 'admin_init', array( $this, 'register_settings' ) );


			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		}

		/**
		 * Return the plugin slug.
		 *
		 * @return    Plugin slug variable.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public static function activate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide  ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_activate();

						restore_current_blog();
					}

				} else {
					self::single_activate();
				}

			} else {
				self::single_activate();
			}

		}

		/**
		 * Fired when the plugin is deactivated.
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Deactivate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       deactivated on an individual blog.
		 */
		public static function deactivate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_deactivate();

						restore_current_blog();

					}

				} else {
					self::single_deactivate();
				}

			} else {
				self::single_deactivate();
			}

		}


		/**
		 * Fired when a new site is activated with a WPMU environment.
		 * @param    int    $blog_id    ID of the new blog.
		 */
		public function activate_new_site( $blog_id ) {

			if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
				return;
			}

			switch_to_blog( $blog_id );
			self::single_activate();
			restore_current_blog();

		}

		/**
		 * Get all blog ids of blogs in the current network that are:
		 * - not archived
		 * - not spam
		 * - not deleted
		 *
		 * @return   array|false    The blog ids, false if no matches.
		 */
		private static function get_blog_ids() {

			global $wpdb;

			// get an array of blog ids
			$sql = "SELECT blog_id FROM $wpdb->blogs
				WHERE archived = '0' AND spam = '0'
				AND deleted = '0'";

			return $wpdb->get_col( $sql );

		}

		/**
		 * Fired for each blog when the plugin is activated.
		 *
		 * @since    1.0.0
		 */
		private static function single_activate() {
			//
		}

		/**
		 * Fired for each blog when the plugin is deactivated.
		 *
		 * @since    1.0.0
		 */
		private static function single_deactivate() {
			//
		}
		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			$domain = $this->plugin_slug;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

		/**
		 * The main logic to set the recommended plugins
		 */
		function wpcore_register_required_plugins() {

			// Get plugins from payload and pass to TGM
			$plugins = $this->get_plugins_from_payload();

			// convert to object
			$theme_text_domain = 'tgmpa';

			/**
			 * Array of configuration settings. Amend each line as needed.
			 * If you want the default strings to be available under your own theme domain,
			 * leave the strings uncommented.
			 * Some of the strings are added into a sprintf, so see the comments at the
			 * end of each line for what each argument will be.
			 */
			$config = array(
				'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
				'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
				'parent_menu_slug' 	=> 'plugins.php', 				// Default parent menu slug
				'parent_url_slug' 	=> 'plugins.php', 				// Default parent URL slug
				'menu'         		=> 'wpcore-install-plugins', 	// Menu slug
				'has_notices'      	=> true,                       	// Show admin notices or not
				'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
				'message' 			=> '',							// Message to output right before the plugins table
				'dismissable'		=> true,						// Message to output right before the plugins table
				'strings'      		=> array(
					'page_title'                       			=> __( 'WPCore Plugin list', $theme_text_domain ),
					'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
					'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
					'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
					'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
					'notice_can_install_recommended'			=> _n_noop( 'WPCore Plugin: %1$s.', 'WPCore Plugins: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
					'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
					'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
					'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
					'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
					'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
					'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
					'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
					'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
					'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
				)
			);
			if(isset($plugins)){
				tgmpa( $plugins, $config );
			}
		}

		/**
		 * Add the menus
		 */
		function add_plugin_admin_menu() {

			add_menu_page( 'WPCore Settings', 'WPCore', 'manage_options', 'wpcore', array ( $this, 'wpcore_options_page' ), plugins_url('assets/img/icon.png', dirname(__FILE__)), 69.324 );
			add_submenu_page('wpcore', 'Manage Keys', 'Manage Keys', 'manage_options', 'wpcore', array( $this, 'wpcore_options_page' ) );

			/**
			 * Enqueue wpcore.js with jQuery dependency
			 */
		}

		/**
		 * Register the setting for storing keys
		 */
		function register_settings() {
			add_option( 'wpcore_keys', '');
			register_setting( 'default', 'wpcore_keys', array( $this, 'save_keys' ) );
		}

		/**
		 * This is the callback for register_setting above
		 * @param $input
		 * @return mixed
		 */
		function save_keys($input){

			// every time we save keys we need to generate the payload
			$this->generate_payload($input);
			return $input;
		}

		/**
		 * Transport for payload. Give back the transient cache contents
		 * or build a new payload and send that back
		 * @return array|null
		 */
		function get_payload(){
			if( get_transient( $this->transient_key )){
	//			echo 'from cache!!';
				return get_transient( $this->transient_key );
			}
			return $this->generate_payload( get_option( 'wpcore_keys' ) );
		}

		/**
		 * If necessary generate a new payload. This is heavy on
		 * the server so we only do this when we have to.
		 * @param $input
		 * @return array|null
		 */
		function generate_payload($input){
			if($input){
				foreach($input as $key){
					// grad the contents of each collection
					$response =  wp_remote_get('http://wpcore.com/collections/'.$key.'/json', array('timeout' => 1));
					$json =  wp_remote_retrieve_body($response);

					// decode to array
					$payload[] = json_decode($json,true);
					if($payload['data']['plugins']){
						// Go through all the plugins and add the, to the array also
						foreach($payload['collection']['data']['plugins'] as $plugin){
							if($payload['collection']['success']){
								$payload['collection']['data']['plugins'][] = $plugin;
							}
						}
					}
				}
			} else {
				$payload = null;
			}
			set_transient($this->transient_key, $payload, $this->transient_timeout);
			return $payload;
		}

		/**
		 * Helper to grab all the plugins from the payload.
		 * This is because the plugin list is flat.
		 * @return array|null
		 */
		function get_plugins_from_payload(){
			$payload = $this->get_payload();
			if($payload){
				foreach( $payload as $collection ){
					if( $collection['success'] ){
						foreach( $collection['data']['plugins'] as $plugin){
							$plugins[] = $plugin;
						}
					}
				}
			} else {
				$plugins = null;
			}
			return $plugins;
		}

		/**
		 * Render the settings page
		 */
		function wpcore_options_page() {

			wp_enqueue_script(
				'wpcore-js-script',
				plugins_url('assets/js/wpcore.js', dirname(__FILE__) ),
				array( 'jquery' )
			);

			wp_enqueue_style(
				'wpcore-css',
				plugins_url('assets/css/wpcore.css', dirname(__FILE__) )
			);
			wp_enqueue_style(
				'wpcore-gridism',
				plugins_url('assets/css/gridism.css', dirname(__FILE__) )
			);


			include( dirname(__FILE__).'/../views/settings.php' );
		}

		/**
		 * Add settings action link to the plugins page.
		 */
		public function add_action_links( $links ) {

			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
				),
				$links
			);
		}
	}

}