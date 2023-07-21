<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App' ) ):

    class Better_Messages_Mobile_App
    {
        /* @Better_Messages_Mobile_App_Options */
        public $settings;

        /* @Better_Messages_Mobile_App_Admin */
        public $admin;

        /* @Better_Messages_Mobile_App_Hooks */
        public $hooks;

        /* @Better_Messages_Mobile_App_Functions */
        public $functions;

        /* @Better_Messages_Mobile_App_Auth */
        public $auth;

        /* @Better_Messages_Mobile_App_JWT */
        public $jwt;

        /* @Better_Messages_Mobile_App_IOS */
        public $ios;

        /* @Better_Messages_Mobile_App_Pushs */
        public $pushs;

        public $path;

        public $devices_table;
        public $cache_group = 'bm_messages_mobile';

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App();
            }

            return $instance;
        }

        public function __construct(){
            global $wpdb;

            $bp_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

            $this->path = trailingslashit(dirname(__FILE__)) ;

            $this->devices_table = $wpdb->prefix . 'bm_mobile_devices';

            require_once( $this->path . 'options.php');
            $this->settings = Better_Messages_Mobile_App_Options();

            require_once($this->path . 'hooks.php');
            $this->hooks = Better_Messages_Mobile_App_Hooks();

            require_once($this->path . 'admin/admin.php');
            $this->admin = Better_Messages_Mobile_App_Admin();

            require_once($this->path . 'jwt.php');
            $this->jwt = Better_Messages_Mobile_App_JWT();

            require_once($this->path . 'functions.php');
            $this->functions = Better_Messages_Mobile_App_Functions();

            require_once ($this->path . 'ios.php');
            $this->ios = Better_Messages_Mobile_App_IOS();

            require_once ($this->path . 'api/scripts.php');
            Better_Messages_Mobile_App_Scripts();

            require_once ($this->path . 'api/auth.php');
            $this->auth = Better_Messages_Mobile_App_Auth();

            require_once ($this->path . 'api/pushs.php');
            $this->pushs = Better_Messages_Mobile_App_Pushs();
        }
    }

endif;

function Better_Messages_Mobile_App()
{
    return Better_Messages_Mobile_App::instance();
}
