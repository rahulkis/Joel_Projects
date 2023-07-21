<?php
/*
Plugin Name:    GravityView - Social Sharing & SEO
Plugin URI:     https://www.gravitykit.com/extensions/sharing-seo/
Description:    Enable social sharing using Jetpack or WordPress 5.4 icons. Integrates with WordPress SEO for View and Entry SEO.
Version:        3.3.1
Text Domain:    gravityview-sharing-seo
License:        GPLv2 or later
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
Domain Path:    /languages
Author:         GravityKit
Author URI:     https://www.gravitykit.com
*/

add_action( 'plugins_loaded', 'gv_extension_sharing_load', 20 );

/**
 * Wrapper function to make sure GravityView_Extension has loaded
 * @return void
 */
function gv_extension_sharing_load() {

	if( !class_exists( 'GravityView_Extension' ) ) {

		if( class_exists('GravityView_Plugin') && is_callable(array('GravityView_Plugin', 'include_extension_framework')) ) {
			GravityView_Plugin::include_extension_framework();
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}


	class GravityView_Sharing extends GravityView_Extension {

		protected $_title = 'Social Sharing & SEO';

		protected $_version = '3.3.1';

		protected $_min_gravityview_version = '2.0.2';

		protected $_text_domain = 'gravityview-sharing-seo';

		protected $_path = __FILE__;

		protected $item_id = 274;

		protected $_dir_path;

		protected $_includes_dir_path;

		/**
		 * @var GravityView_Sharing_Service[]
		 */
		private $_gravityview_sharing_services = array();

		static $instance;

		public static function get_instance() {

			if( empty( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		function __construct() {

			$this->_dir_path = plugin_dir_path( $this->_path );
			$this->_includes_dir_path = trailingslashit( $this->_dir_path . 'includes' );

			parent::__construct();

		}

		function add_hooks() {

			include_once( $this->_dir_path . 'sharing-common-functions.php' );
			include_once( $this->_includes_dir_path . 'class-gravityview-social-register-field.php' );
			include_once( $this->_includes_dir_path . 'class-gravityview-social-meta.php' );

			$this->register_sharing_services();
		}

		public function get_path() {
			return $this->_path;
		}

		public function get_sharing_services() {
			return $this->_gravityview_sharing_services;
		}

		public function register_sharing_services() {

			require_once( $this->_includes_dir_path . '/class-sharing-service.php' );

			// Load Field files automatically
			foreach ( glob( $this->_includes_dir_path . '/services/*.php' ) as $service ) {
				require_once( $service );
			}

			/**
			 * Register your own sharing service.
			 * @param GravityView_Sharing_Service[]
			 */
			$this->_gravityview_sharing_services = apply_filters( 'gravityview_sharing_services', array() );

		}

	}

	GravityView_Sharing::get_instance();
}
