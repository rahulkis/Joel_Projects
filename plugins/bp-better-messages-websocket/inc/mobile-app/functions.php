<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Better_Messages_Mobile_App_Functions' ) ):

    class Better_Messages_Mobile_App_Functions
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Functions();
            }

            return $instance;
        }

        public function __construct(){
        }

        public function get_user_devices( int $user_id ){
            global $wpdb;

            $cache_group = Better_Messages_Mobile_App()->cache_group;

            $cache_key   = 'user_devices_' . $user_id;

            $cached = wp_cache_get( $cache_key, $cache_group );

            if( $cached !== false ){
                return $cached;
            }

            $table = Better_Messages_Mobile_App()->devices_table;

            $user_devices = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$table} WHERE user_id = %d", $user_id ), ARRAY_A );

            wp_cache_set($cache_key, $user_devices, $cache_group);

            return $user_devices;
        }

        public function update_user_device( int $user_id, string $device_id, array $device ){
            global $wpdb;

            $table = Better_Messages_Mobile_App()->devices_table;

            $fields = [
                'user_id'   => $user_id,
                'device_id' => $device_id,
            ];

            if( isset($device['info'] ) ) {
                $fields['device_info'] = json_encode( $device['info'] );

                if( isset( $device['info']['platform'] ) ) $fields['platform'] = $device['info']['platform'];
            }
            if( isset($device['lang'] ) ) $fields['lang'] = $device['lang'];
            if( isset($device['push_token'] ) ) $fields['push_token'] = $device['push_token'];

            $selectors = [];
            $values = [];
            $data = [];
            $updateValues = [];

            foreach( array_keys( $fields ) as $key ){
                $selectors[] = $key;
                $updateValues[] = $key . ' = VALUES (' . $key . ')';
            }

            foreach( array_values( $fields ) as $key ){
                $values[]     = '%s';
                $data[]       = $key;
            }

            $sql = $wpdb->prepare(
                "INSERT INTO " . $table . " 
                (" . implode( ',', $selectors ) . ") 
                VALUES (" . implode( ',', $values ) . ")
                ON DUPLICATE KEY 
                UPDATE " . implode( ', ', $updateValues ) . "",
                $data
            );

            $wpdb->query( $sql );

            $cache_group = Better_Messages_Mobile_App()->cache_group;
            $cache_key   = 'user_devices_' . $user_id;
            wp_cache_delete( $cache_key, $cache_group );
        }
    }

endif;

function Better_Messages_Mobile_App_Functions()
{
    return Better_Messages_Mobile_App_Functions::instance();
}
