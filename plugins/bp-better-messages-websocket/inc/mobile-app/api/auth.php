<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Auth' ) ):

    class Better_Messages_Mobile_App_Auth
    {
        public $is_mobile = false;
        public $current_device_id = false;
        public $current_device    = false;

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Auth();
            }

            return $instance;
        }

        public function __construct(){
            add_filter('better_messages_rest_is_user_authorized', array( $this, 'check_app_access' ), 10, 2 );
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );
        }

        public function rest_api_init(){
            register_rest_route( 'better-messages/v1/app', '/login', array(
                'methods' => 'POST',
                'callback' => array( $this, 'login' ),
                'permission_callback' => '__return_true'
            ) );
        }

        public function is_mobile_app(){
            return $this->is_mobile;
        }

        public function login( WP_REST_Request $request ){
            $secret_key = defined( 'BM_JWT_AUTH_SECRET_KEY' ) ? BM_JWT_AUTH_SECRET_KEY : false;

            if ( ! $secret_key ) {
                return new WP_Error(
                    'jwt_auth_bad_config',
                    __( 'JWT is not configured properly, please contact the admin', 'wp-api-jwt-auth' ),
                    [
                        'status' => 403,
                    ]
                );
            }

            $login    = $request->get_param('username');
            $password = $request->get_param('password');
            $device   = $request->get_param("device");

            $user = wp_authenticate( $login, $password );

            if( ! is_wp_error( $user ) ) {
                //$device_id = $device['id'];
                $user_id = $user->ID;
                wp_set_current_user($user_id);

                //$new_access_token   = Better_Messages()->functions->generateRandomString( 50 );

                $issuedAt  = time();
                $notBefore = apply_filters( 'better_messages_mobile_jwt_auth_not_before', $issuedAt, $issuedAt );
                $expire    = apply_filters( 'better_messages_mobile_jwt_auth_expire', $issuedAt + ( DAY_IN_SECONDS * 31 ), $issuedAt );

                $token = [
                    'iss'  => get_bloginfo( 'url' ),
                    'iat'  => $issuedAt,
                    'nbf'  => $notBefore,
                    'exp'  => $expire,
                    'data' => [
                        'user_id'   => $user->data->ID,
                        'device_id' => $device['id']
                    ],
                ];

                $token = \BetterMessages\Firebase\JWT\JWT::encode(
                    apply_filters( 'better_messages_mobile_jwt_auth_token_before_sign', $token, $user ),
                    $secret_key,
                    'HS256'
                );

                Better_Messages_Mobile_App()->functions->update_user_device( $user_id, $device['id'], $device );

                return [
                    'auth' => [
                        'token'       => $token,
                        'user_id'     => $user->ID,
                        'message'     => _x('Authenticated successfully', 'App Authorization', 'better-messages-mobile-app')
                    ],
                    'settings' => Better_Messages()->get_script_variables(),
                ];
            } else {
                return new WP_Error(
                    'rest_forbidden',
                    _x('Authentication error', 'App Authorization', 'better-messages-mobile-app'),
                    array( 'status' => 403 )
                );
            }
        }

        public function check_app_access( $allowed, WP_REST_Request $request ){
            $secret_key = defined( 'BM_JWT_AUTH_SECRET_KEY' ) ? BM_JWT_AUTH_SECRET_KEY : false;
            $authorization = $request->get_header('authorization');
            if(strpos($authorization, 'BMAuth ', 0 ) === 0){
                $auth_token  = substr( $authorization, 7 );

                try {
                    $token = \BetterMessages\Firebase\JWT\JWT::decode($auth_token, new \BetterMessages\Firebase\JWT\Key($secret_key, 'HS256'));

                    /** The Token is decoded now validate the iss */
                    if ( $token->iss !== get_bloginfo( 'url' ) ) {
                        /** The iss do not match, return error */
                        return new WP_Error(
                            'rest_forbidden',
                            'The iss do not match with this server',
                            [
                                'status' => 403,
                            ]
                        );
                    }

                    /** So far so good, validate the user id in the token */
                    if ( ! isset( $token->data->user_id ) ) {
                        /** No user id in the token, abort!! */
                        return new WP_Error(
                            'rest_forbidden',
                            'User ID not found in the token',
                            [
                                'status' => 403,
                            ]
                        );
                    }

                    wp_set_current_user( $token->data->user_id );
                    $this->current_device_id = $token->data->device_id;

                    $this->is_mobile = true;

                    $allowed = true;
                } catch ( Exception $exception ){
                    return new WP_Error(
                        'rest_forbidden',
                        $exception->getMessage(),
                        array( 'status' => 403 )
                    );
                }
            }

            return $allowed;
        }
    }

endif;

function Better_Messages_Mobile_App_Auth()
{
    return Better_Messages_Mobile_App_Auth::instance();
}
