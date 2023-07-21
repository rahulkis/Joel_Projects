<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Pushs' ) ):

    class Better_Messages_Mobile_App_Pushs
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Pushs();
            }

            return $instance;
        }

        public function __construct(){
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );
            add_filter( 'better_messages_realtime_server_send_data', array( $this, 'sendMessagePush' ), 10, 2 );
        }

        public function rest_api_init(){
            register_rest_route( 'better-messages/v1/app', '/savePushToken', array(
                'methods' => 'POST',
                'callback' => array( $this, 'save_push_token' ),
                'permission_callback' => array(Better_Messages_Rest_Api(), 'is_user_authorized')
            ) );
        }

        public function sendMessagePush( $payloadData, $message){
            global $wpdb;

            $message_id = (int) $message->id;
            $thread_id  = (int) $message->thread_id;
            $sender_id  = (int) $message->sender_id;

            $sql = $wpdb->prepare("SELECT *
            FROM wp_bm_mobile_devices
            WHERE user_id IN (SELECT user_id
                FROM wp_bm_message_recipients as recipients
            WHERE thread_id = %d
            AND user_id != %d AND is_deleted = 0 AND is_muted = 0 )
            AND push_token IS NOT NULL
            ", $message->thread_id, $message->sender_id );

            $mobileDevices = $wpdb->get_results( $sql );

            if( count( $mobileDevices ) === 0 ){
                return $payloadData;
            }

            $tokens = [];

            foreach( $mobileDevices as $mobileDevice ) {
                $user_id    = $mobileDevice->user_id;
                $platform   = $mobileDevice->platform;
                $push_token = $mobileDevice->push_token;

                if( ! isset( $tokens[$user_id][$platform] ) ){
                    $tokens[$user_id][$platform] = [];
                }

                $tokens[$user_id][$platform][] = $push_token;
            }


            if( count( $tokens ) === 0 ){
                return $payloadData;
            }


            $title    = BP_Better_Messages()->functions->get_thread_title($thread_id);
            $subtitle = "Subtitle";

            $sender_name   = BP_Better_Messages()->functions->get_name( $sender_id );
            $sender_avatar = BP_Better_Messages()->functions->get_avatar( $sender_id, 100, ['html' => false] );

            $sender_avatar = 'https://www.wordplus.org/wp-content/uploads/avatars/1/5820b7b52c22b-bpfull.png';

            $mobile_pushs = [];

            foreach( $tokens as $user_id => $token_array ) {
                $content = BP_Better_Messages()->functions->format_message($message->message, $message->id, 'mobile_app', $user_id);

                if( empty( trim( $content ) ) ){
                    continue;
                }

                $ios = [
                    'aps' => [
                        'alert' => [
                            "title"    => $title,
                            "subtitle" => $subtitle,
                            "body"     => $content
                        ],
                        'sound' => 'default',
                        'badge' => rand( 1, 99 ), //Total Messages Count
                        'content-available' => 1,
                        'mutable-content' => 1
                    ],
                    'type'      => 'new_message',
                    'user_id'   => (string) $sender_id,
                    'name'      => (string) $sender_name,
                    'image'     => $sender_avatar,
                    'thread_id' => (string) $thread_id
                ];


                $mobile_pushs[] = [
                    'tkns'    => $token_array,
                    "user_id" => (int) $user_id,
                    'ios'     => $ios
                    //'android' => $fcm_data
                ];
            }


            if( count( $mobile_pushs ) > 0 ){
                try {
                    $iosToken = Better_Messages_Mobile_App()->ios->get_push_jwt();

                    $payloadData['mobile_pushs'] = [
                        'iosConfig' => [
                            'jwt' => $iosToken,
                            'endpoint' => Better_Messages_Mobile_App()->ios->push_endpoint,
                            'bundle' => Better_Messages_Mobile_App()->ios->app_bundle_id,
                        ],
                        'pushs' => $mobile_pushs
                    ];
                } catch ( \Exception $exception ){}
            }

            return $payloadData;
        }


        public function save_push_token( WP_REST_Request $request ){
            $user_id = Better_Messages()->functions->get_current_user_id();
            $token   = sanitize_text_field($request->get_param('token'));

            Better_Messages_Mobile_App()->functions->update_user_device( $user_id, Better_Messages_Mobile_App()->auth->current_device_id, [
                'push_token' => $token
            ] );

            return true;
        }
    }

endif;

function Better_Messages_Mobile_App_Pushs()
{
    return Better_Messages_Mobile_App_Pushs::instance();
}
