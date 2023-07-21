<?php
/**
 * Plugin Name: GravityMath
 * Plugin URI:  https://www.gravitykit.com/extensions/math/
 * Description: Perform calculations inside or outside GravityView using the <code>[gravitymath]</code> shortcode. Requires PHP 7.1.
 * Version:     2.3.3
 * Text Domain: gk-gravitymath
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Author:      GravityKit
 * Author URI:  https://www.gravitykit.com
 * Requires at least: 5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! GravityKit\GravityMath\Foundation\should_load( __FILE__ ) ) {
	return;
}

define( 'GRAVITYMATH_VERSION', '2.3.3' );

/** @define "GRAVITYMATH_FILE" "./gravityview-math.php" Full path to the gravityview-math.php file */
define( 'GRAVITYMATH_FILE', __FILE__ );

/** @define "GRAVITYMATH_DIR" "./" The absolute path to the plugin directory, with trailing slash */
define( 'GRAVITYMATH_DIR', plugin_dir_path( __FILE__ ) );

require_once GRAVITYMATH_DIR . 'vendor/autoload.php';
require_once GRAVITYMATH_DIR . 'vendor_prefixed/autoload.php';

GravityKit\GravityMath\Foundation\Core::register( __FILE__ );

add_action( 'plugins_loaded', 'gv_extension_math_load', 20 );

/**
 * Wrapper function to make sure GravityView_Extension has loaded
 * @return void
 */
function gv_extension_math_load() {

	class GravityView_Math {

		/**
		 * @var GravityView_Math
		 */
		public static $instance;

		/**
		 * @var GravityMath_Engine
		 */
		public $calculator;

		/**
		 * @var GravityMath_Report
		 */
		public $reporter;

		/**
		 * @var GravityMath_Shortcode
		 */
		public $shortcode;

		function __construct() {
			$this->setup();
		}

		/**
		 * Load the constants, include files, populate the variables
		 * @return void
		 */
		private function setup() {

			$this->include_files();

			$this->calculator = GravityMath_Engine::get_instance();
			$this->reporter   = GravityMath_Report::get_instance();
			$this->shortcode  = GravityMath_Shortcode::get_instance( $this->calculator, $this->reporter );
		}

		/**
		 * Include files required by the plugin
		 * @return void
		 */
		private function include_files() {
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-report.php';
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-engine.php';
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-gfaddon.php';
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-shortcode.php';
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-gravityforms.php';
			require_once GRAVITYMATH_DIR . 'includes/class-gravitymath-table-footer-calculation.php';
		}

		/**
		 * @return GravityView_Math
		 */
		public static function get_instance() {

			if ( empty( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Easy access to the GravityMath_Report object
		 *
		 * @return GravityMath_Report
		 */
		public static function reporter() {
			return self::get_instance()->reporter;
		}

	}

	GravityView_Math::get_instance();
}
