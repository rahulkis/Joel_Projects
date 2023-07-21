<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Scripts' ) ):

    class Better_Messages_Mobile_App_Scripts
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Scripts();
            }

            return $instance;
        }

        public function __construct(){
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );
        }

        public function rest_api_init(){
            register_rest_route( 'better-messages/v1/app', '/syncScripts', array(
                'methods' => 'GET',
                'callback' => array( $this, 'sync_scripts' ),
                'permission_callback' => '__return_true'
            ) );

            register_rest_route( 'better-messages/v1/app', '/getSettings', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_settings' ),
                'permission_callback' => '__return_true'
            ) );
        }

        public function get_settings( WP_REST_Request $request ){
            Better_Messages_Rest_Api()->is_user_authorized($request);

            Better_Messages()->load_options();

            return Better_Messages()->get_script_variables();
        }

        public function sync_scripts(){
            $is_dev = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

            $dependencies = array(
                'jquery',
                'wp-i18n'
            );

            $version = Better_Messages()->version;

            $file_name = 'bp-messages-app.js';
            if( $is_dev ){
                $version .= filemtime( Better_Messages()->path . 'assets/js/' . $file_name );
            }

            wp_register_script(
                'better-messages-app',
                Better_Messages()->url . 'assets/js/' . $file_name,
                $dependencies,
                $version
            );

            $file_name = 'app.js';
            if( $is_dev ){
                $version .= filemtime( Better_Messages()->path . 'assets/js/' . $file_name );
            }

            wp_register_script(
                'better-messages-app-init',
                Better_Messages()->url . 'assets/js/' . $file_name,
                [],
                $version
            );

            wp_scripts()->all_deps(['better-messages-app-init', 'better-messages-app']);


            if( $is_dev ){
                $version .= filemtime( Better_Messages()->path . 'assets/css/mobile-app.css' );
            }

            wp_register_style('better-messages-app', Better_Messages()->url . 'assets/css/mobile-app.css',
                false,
                $version
            );

            Better_Messages()->enqueue_css();
            wp_styles()->all_deps(['better-messages', 'better-messages-app']);

            $base_url = site_url( '' );

            $scripts = [];
            $styles  = [];

            foreach( wp_scripts()->to_do as $handle ){
                $_script = wp_scripts()->registered[ $handle ];

                $src = $_script->src;

                if( empty( $src ) ) continue;

                if( strpos($src, 'http', 0) === false ){
                    $src = $base_url . $src;
                }

                $script = [
                    'handle' => $handle,
                    'src'    => $src,
                    'ver'    => $_script->ver,
                    'extra'  => []
                ];

                if( isset($_script->extra['after']) ){
                    $script['extra']['after'] = $_script->extra['after'];
                }

                if( isset($_script->extra['data']) ){
                    $script['extra']['data'] = $_script->extra['data'];
                }

                $scripts[] = $script;
            }


            foreach( wp_styles()->to_do as $handle ) {
                $_style = wp_styles()->registered[$handle];
                $src = $_style->src;

                if( strpos($src, 'http', 0) === false ){
                    $src = $base_url . $src;
                }

                $style = [
                    'handle' => $handle,
                    'src'    => $src,
                    'ver'    => $_style->ver
                ];

                $styles[] = $style;
            }

            //Better_Messages()->load_options();

            return [
                'scripts'   => $scripts,
                'styles'    => $styles
            ];
        }
    }

endif;

function Better_Messages_Mobile_App_Scripts()
{
    return Better_Messages_Mobile_App_Scripts::instance();
}
