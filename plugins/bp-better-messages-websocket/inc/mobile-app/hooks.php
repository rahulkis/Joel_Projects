<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Hooks' ) ):

    class Better_Messages_Mobile_App_Hooks
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Hooks();
            }

            return $instance;
        }

        public function __construct(){
            add_filter( 'better_messages_user_config', array( $this, 'logout_button'), 12, 1 );
        }

        public function logout_button( $user_settings ){

            if( Better_Messages_Mobile_App()->auth->is_mobile_app() ){
                $user_settings[] = [
                    'id' => 'mobile_logout_button',
                    'title' => _x('Logout', 'Mobile App - User settings', 'bp-better-messages'),
                    'type' => 'mobile_logout'
                ];
            }

            return $user_settings;
        }
    }

endif;

function Better_Messages_Mobile_App_Hooks()
{
    return Better_Messages_Mobile_App_Hooks::instance();
}
