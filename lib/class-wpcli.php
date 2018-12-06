<?php
/**
 * wp-cli wpcore
 *
 * @version 0.0.1
 * @author Stuart Starr <stuart@stuey.net>
 */
if ( true === class_exists( 'WP_CLI_Command' ) ){
	/**
	 * Do something.
	 */
	class WP_CLI_WPCore_Command extends WP_CLI_Command{
		
		private $version = '0.1.3';
		
        /**
         * Import WPCore.com collections via the command line!
         *
         * <command>
         * : Import a wpcore collection.
         * --key=<key>
         * : The collection key you want to install.
         * 
        * [--activate=<no>]
        * : Whether or not to activate all the plugins.
        * ---
        * default: no
        * options:
        *   - yes
        *   - no
        * ---
         *
         * @when before_wp_load
         */
		public function __invoke( $args = null, $assoc_args = null ){
			// print_r( $args );
            // print_r( $assoc_args );

            $key = $assoc_args['key'];

            $response =  wp_remote_get('https://wpcore.com/api/'.$key, array('timeout' => 5, 'sslverify' => false));

            $json =  wp_remote_retrieve_body($response);
 
            $payload = json_decode($json,true);

            if(! $payload[0]['success']) {
                return WP_CLI::error("Collection {$key} not found");
            }
            
            if ( isset( $payload['data']['plugins'] ) ) {
                // Go through all the plugins and add the, to the array also
                foreach($payload['data']['plugins'] as $plugin){
                    if($payload['success']){
                        $payload['data']['plugins'][] = $plugin;
                    }
                }
            }

            if($payload[0]['success'] == 1 && count($payload[0]['data']['plugins']) > 0) {
                WP_CLI::success( "Collection {$key} found..." );
                $plugins = $payload[0]['data']['plugins'];
                foreach($plugins as $plugin){

                    if(isset($plugin['source'])) {
                        WP_CLI::line("Installing " . $plugin['name'] . " from " . $plugin['source']);
                    } else {
                        WP_CLI::line("Installing " . $plugin['name'] . " from https://wordpress.org/plugins/" . $plugin['source']);
                    }

                    $source = isset($plugin['source']) ? $plugin['source'] : $plugin['slug'];
                    
                    $command = 'plugin install --activate ' . $source;
                    WP_CLI::runcommand( $command, $options = array() );
                }
            } else {
                WP_CLI::error("No plugins found");
            }
            
            // WP_CLI::runcommand( $command, $options = array() );
            WP_CLI::line( 'Done some stuff' );
		}
		
		/**
		 * Return command version
		 *
		 * @since 0.0.1
		 * @when before_wp_load
		 */
		public function version(){
			WP_CLI::line( 'wp-cli wpcore importing collection ' . $this->version );
        }
	}
	WP_CLI::add_command( 'wpcore', 'WP_CLI_WPCore_Command' );
}