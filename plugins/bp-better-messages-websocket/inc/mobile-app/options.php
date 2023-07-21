<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Options' ) ):

    class Better_Messages_Mobile_App_Options
    {
        public $settings;
        public $defaults;

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Options();
            }

            return $instance;
        }

        public function __construct(){
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );
        }

        public function rest_api_init(){
            register_rest_route( 'better-messages/v1/app', '/getMobileSettings', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_mobile_settings' ),
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                }
            ) );

            register_rest_route( 'better-messages/v1/app', '/saveMobileSettings', array(
                'methods' => 'POST',
                'callback' => array( $this, 'save_mobile_settings' ),
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                }
            ) );

            register_rest_route( 'better-messages/v1/app', '/uploadFile', array(
                'methods' => 'POST',
                'callback' => array( $this, 'upload_file' ),
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                }
            ) );
        }

        public function init(){

            $this->defaults = array(
                'applicationName'   => 'My Messenger',
                'appIcon'           => '',
                'appSplash'         => '',
                'iosApi'            => get_option('better-messages-app-ios-auth', []),
                'iosBundleDev'      => '',
                'iosCertificateDev' => '',
                'iosCertificateServiceDev' => '',
                'iosDevices'        => [],
                'iosProfileDev'     => '',
                'iosProfileServiceDev' => '',
                'iosBundleProd'     => '',
            );

            $args = get_option( 'better-messages-app-settings', array() );

            $files = [
                'appIcon',
                'appSplash',
                'iosAPIKey'
            ];

            foreach ( $files as $file ){
                $args[$file] = get_option('better-messages-app-settings-file-' . $file, '');
            }

            $devCert = get_option('better-messages-app-ios-certificate-DEVELOPMENT');

            if( $devCert ){
                $args['iosCertificateDev'] = $devCert['certificate']['id'];
            }

            $iosProfileDev = get_option('better-messages-app-ios-profile-dev-application');
            if( $iosProfileDev ){
                $args['iosProfileDev'] = $iosProfileDev['id'];
            }

            $iosProfileServiceDev = get_option('better-messages-app-ios-profile-dev-service');
            if( $iosProfileServiceDev ){
                $args['iosProfileServiceDev'] = $iosProfileServiceDev['id'];
            }

            $this->settings = wp_parse_args( $args, $this->defaults );
        }

        public function get_mobile_settings( WP_REST_Request $request = null ){
            $this->init();
            return $this->settings;
        }

        public function save_mobile_settings( WP_REST_Request $request ){
            $settings = (array) $request->get_param('settings');

            if( count( $settings ) > 0 ){
                $_settings = get_option('better-messages-app-settings', []);

                foreach( $settings as $key => $value ){
                    $_settings[$key] = $value;
                }

                $this->update_mobile_settings( $_settings );
            }
        }

        public function update_mobile_settings( $_settings ){
            update_option('better-messages-app-settings', $_settings, false);
        }

        public function upload_file( WP_REST_Request $request ){
            $key  = $request->get_param('key');
            $files = $request->get_file_params();

            if( ! isset( $files['file'] ) ) {
                return new WP_Error(
                    'rest_error',
                    _x( 'Sorry, you are not allowed to do that', 'Rest API Error', 'bp-better-messages' ),
                    array( 'status' => 406 )
                );
            }

            $file = $files['file'];

            $path = $file['tmp_name'];

            switch ( $key ){
                case 'appIcon';
                    $size = getimagesize( $path );
                    if( ! $size ){
                        return new WP_Error(
                            'rest_error',
                            _x( 'Not possible to determine image size', 'Rest API Error', 'bp-better-messages' ),
                            array( 'status' => 406 )
                        );
                    }

                    $width  = $size[0];
                    $height = $size[1];

                    if( $width < 1024 || $height < 1024 || ( $width !== $height ) ){
                        return new WP_Error(
                            'rest_error',
                            sprintf(_x( 'Image must be equal height and at least %s', 'Rest API Error', 'bp-better-messages' ), '1024x1024px'),
                            array( 'status' => 406 )
                        );
                    }
                    break;
                case 'appSplash';
                    $size = getimagesize( $path );
                    if( ! $size ){
                        return new WP_Error(
                            'rest_error',
                            _x( 'Not possible to determine image size', 'Rest API Error', 'bp-better-messages' ),
                            array( 'status' => 406 )
                        );
                    }

                    $width  = $size[0];
                    $height = $size[1];

                    if( $width < 2732 || $height < 2732 || ( $width !== $height ) ){
                        return new WP_Error(
                            'rest_error',
                            sprintf(_x( 'Image must be equal height and at least %s', 'Rest API Error', 'bp-better-messages' ), '2732x2732px'),
                            array( 'status' => 406 )
                        );
                    }
                    break;
            }

            $file_content = file_get_contents($path);

            update_option('better-messages-app-settings-file-' . $key, base64_encode($file_content), false );

            return true;
        }
    }

endif;

function Better_Messages_Mobile_App_Options()
{
    return Better_Messages_Mobile_App_Options::instance();
}
