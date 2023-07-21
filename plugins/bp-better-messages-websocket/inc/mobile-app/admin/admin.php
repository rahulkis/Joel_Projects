<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Admin' ) ):

    class Better_Messages_Mobile_App_Admin
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Admin();
            }

            return $instance;
        }

        public function __construct(){
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'settings_page' ), 20 );
        }

        public function enqueue_scripts(){
            if( ! defined('BM_DEV') ) return;
            //$filepath = Better_Messages()->path . 'assets/admin/admin.js';

            //$version = Better_Messages()->version;
            //$version .= filemtime( $filepath );

            //wp_register_script('better-messages-admin', Better_Messages()->url . 'assets/admin.js', [], $version, true );
            //wp_enqueue_script( 'better-messages-admin' );
        }

        public function settings_page(){
            add_submenu_page(
                'bp-better-messages',
                _x('Mobile App', 'Admin Menu', 'bp-better-messages'),
                _x('Mobile App', 'Admin Menu', 'bp-better-messages'),
                'manage_options',
                'better-messages-mobile-app',
                array($this, 'settings_page_html'),
                5
            );
        }
        public function settings_page_html(){
        ?>
            <div class="wrap">
                <h1><?php _ex( 'Mobile App', 'WP Admin','bp-better-messages' ); ?></h1>

                <div id="bm-mobile-app-settings"></div>
            </div>
            <?php
        }
    }

endif;

function Better_Messages_Mobile_App_Admin()
{
    return Better_Messages_Mobile_App_Admin::instance();
}
