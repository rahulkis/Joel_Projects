<?php
if ( !class_exists( 'Better_Messages_Rest_Api_Admin' ) ):

    class Better_Messages_Rest_Api_Admin
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Rest_Api_Admin();
            }

            return $instance;
        }

        public function __construct()
        {
            add_action('rest_api_init', array($this, 'rest_api_init'));
        }

        public function user_is_admin(){
            return current_user_can('manage_options');
        }

        public function rest_api_init(){
            register_rest_route('better-messages/v1/admin', '/getMessages', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_messages'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            /* register_rest_route('better-messages/v1/admin', '/getThreads', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_threads'),
                'permission_callback' => array($this, 'user_is_admin'),
            )); */

            register_rest_route('better-messages/v1/admin', '/searchSenders', array(
                'methods' => 'GET',
                'callback' => array($this, 'search_senders'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin', '/getUsers', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_users'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin', '/getGuests', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_guests'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin', '/deleteMessages', array(
                'methods'             => 'POST',
                'callback'            => array($this, 'delete_messages'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin', '/getThreadParticipants', array(
                'methods'             => 'POST',
                'callback'            => array($this, 'get_thread_participants'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));
        }

        public function get_thread_participants( WP_REST_Request $request ){
            global $wpdb;

            $page   = ( $request->has_param('page') ) ? intval( $request->get_param('page') ) : 1;

        }

        public function get_guests( WP_REST_Request $request ){
            global $wpdb;

            $page   = ( $request->has_param('page') ) ? intval( $request->get_param('page') ) : 1;

            $search = ( $request->has_param('search') ) ? sanitize_text_field( $request->get_param('search') ) : "";

            $search_sql = "";

            if( $search ){
                $search_sql = $wpdb->prepare("
                    AND( `guests`.`name` LIKE %s
                    OR `guests`.`email` LIKE %s
                    OR `guests`.`ip` LIKE %s )
                ", "%" . $search . "%", "%" . $search . "%", "%" . $search . "%" );
            }

            $per_page = 20;

            $offset = 0;

            if( $page > 1 ){
                $offset = ( $page - 1 ) * $per_page;
            }

            $count = (int) $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM `" . bm_get_table('guests') . "` `guests`
                WHERE 1=1
                $search_sql
            "));

            $user_ids = $wpdb->get_results( $wpdb->prepare("
                SELECT id, email, ip, created_at,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('messages') . "` 
                 WHERE `sender_id` = (-1 * `guests`.`id`) ) messages,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('recipients') . "` 
                 WHERE `user_id` = (-1 * `guests`.`id` )) participants
                FROM `" . bm_get_table('guests') . "` `guests`
                WHERE 1=1
                $search_sql
                ORDER BY id ASC
                LIMIT {$offset}, {$per_page}
            "), ARRAY_A );

            $return = [
                'total'    => $count,
                'page'     => $page,
                'perPage'  => $per_page,
                'pages'    => ceil( $count / $per_page ),
                'users' => []
            ];

            foreach( $user_ids as $user ){
                $user_item = Better_Messages()->functions->rest_user_item( -1 * abs($user['id']) );
                $user_item['id']            = abs( $user_item['id'] );
                $user_item['email']         = $user['email'];
                $user_item['ip']            = $user['ip'];
                $user_item['createdAt']      = $user['created_at'];
                $user_item['messages']      = $user['messages'];
                $user_item['conversations'] = $user['participants'];

                $return['users'][] = $user_item;
            }

            return $return;

        }

        public function get_users( WP_REST_Request $request ){
            global $wpdb;

            $page = ( $request->has_param('page') ) ? intval( $request->get_param('page') ) : 1;

            $search = ( $request->has_param('search') ) ? sanitize_text_field( $request->get_param('search') ) : "";

            $search_sql = "";

            if( $search ){
                $search_sql = $wpdb->prepare("
                    AND (
                        ID = %s
                        OR `user_nicename` LIKE %s
                        OR `display_name` LIKE %s 
                        OR `ID` IN (
                            SELECT user_id  
                            FROM `{$wpdb->usermeta}` 
                            WHERE `meta_key` IN ( 'nickname', 'first_name', 'last_name' )
                            AND `meta_value` LIKE %s
                        )
                    )
                ", "%" . $search . "%", "%" . $search . "%", "%" . $search . "%", "%" . $search . "%" );
            }

            $per_page = 20;

            $offset = 0;

            if( $page > 1 ){
                $offset = ( $page - 1 ) * $per_page;
            }

            $count = (int) $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM `{$wpdb->users}` `users`
                WHERE 1 = 1
                {$search_sql}
            "));

            $user_ids = $wpdb->get_results( $wpdb->prepare("
                SELECT ID,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('messages') . "` 
                 WHERE `sender_id` = `users`.`ID`) messages,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('recipients') . "` 
                 WHERE `user_id` = `users`.`ID`) participants
                FROM `{$wpdb->users}` `users`
                WHERE 1 = 1
                {$search_sql}
                ORDER BY ID ASC
                LIMIT {$offset}, {$per_page}
            "), ARRAY_A );

            $return = [
                'total'    => $count,
                'page'     => $page,
                'perPage'  => $per_page,
                'pages'    => ceil( $count / $per_page ),
                'users' => []
            ];

            foreach( $user_ids as $user ){
                $user_item = Better_Messages()->functions->rest_user_item( $user['ID'] );
                $user_item['messages']      = $user['messages'];
                $user_item['conversations'] = $user['participants'];

                $return['users'][] = $user_item;
            }

            return $return;

        }

        public function delete_messages( WP_REST_Request $request ){
            set_time_limit(0);

            $messageIds = $request->get_param('messageIds');

            if( ! is_array( $messageIds ) ) return false;

            $messageIds = array_map( 'intval', $messageIds );

            foreach ( $messageIds as $messageId ) {
                Better_Messages()->functions->delete_message( $messageId );
            }

            return true;
        }

        public function search_senders( WP_REST_Request $request ){
            global $wpdb;

            $search = $request->get_param('search');

            if( empty( $search ) ) {
                return [];
            }

            $sql = $wpdb->prepare("
            SELECT ID FROM `{$wpdb->users}`
            WHERE ID IN (SELECT sender_id FROM `" . bm_get_table('messages') . "` GROUP BY sender_id)
            AND (
                ID = %s
                OR `user_nicename` LIKE %s
                OR `display_name` LIKE %s 
                OR `ID` IN (
                    SELECT user_id  
                    FROM `{$wpdb->usermeta}` 
                    WHERE `meta_key` IN ( 'nickname', 'first_name', 'last_name' )
                    AND `meta_value` LIKE %s
                )
            )
            LIMIT 0, 10", $search, '%' . $search . '%', '%' . $search . '%', '%' . $search . '%');

            $search_results = $wpdb->get_col( $sql );

            $return = [];

            foreach( $search_results as $user_id ){
                $return[] = Better_Messages()->functions->rest_user_item( $user_id );
            }

            return $return;
        }

        public function get_messages( WP_REST_Request $request ){
            global $wpdb;

            $page = ( $request->has_param('page') ) ? intval( $request->get_param('page') ) : 1;

            $per_page = 20;

            $offset = 0;

            if( $page > 1 ){
                $offset = ( $page - 1 ) * $per_page;
            }

            $sender_id = $request->has_param('sender_id') ?  intval($request->get_param('sender_id' )) : false;
            $search = $request->has_param('search') ?  sanitize_text_field( $request->get_param('search' )) : false;
            $thread_id = $request->has_param('thread_id') ?  intval($request->get_param('thread_id' )) : false;

            $sender_sql = $search_sql = $thread_sql = '';

            if( $sender_id ) {
                $sender_sql = $wpdb->prepare('AND `sender_id` = %d', $sender_id);
            }

            if( $search ){
                $search_sql = $wpdb->prepare('AND `message` LIKE %s', '%'. $search . '%');
            }

            if( $thread_id ){
                $thread_sql = $wpdb->prepare('AND `thread_id` = %d', $thread_id);
            }

            $count = $wpdb->get_var( "
            SELECT COUNT(*) 
            FROM `" . bm_get_table('messages') . "`
            WHERE `date_sent` > '0000-00-00 00:00:00'
            AND `message` != '<!-- BBPM START THREAD -->'
            $sender_sql $search_sql $thread_sql");

            $messages = $wpdb->get_results( "
                SELECT *,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('recipients') . "` 
                 WHERE `thread_id` = `messages`.`thread_id`) participants
                FROM `" . bm_get_table('messages') . "` `messages`
                WHERE `date_sent` > '0000-00-00 00:00:00'
                AND `message` != '<!-- BBPM START THREAD -->'
                $sender_sql $search_sql $thread_sql
                ORDER BY `id` DESC
                LIMIT {$offset}, {$per_page}
            ", ARRAY_A );

            $return = [
                'total'    => $count,
                'page'     => $page,
                'perPage'  => $per_page,
                'pages'    => ceil( $count / $per_page ),
                'messages' => [],
            ];

            if( count( $messages ) > 0 ) {
                foreach ($messages as $i => $message) {
                    $view_link = Better_Messages()->functions->add_hash_arg('conversation/' . $message['thread_id'], [
                        'scrollToContainer' => ''
                    ], Better_Messages()->functions->get_link() );

                    $item = [
                        'id'           => $message['id'],
                        'user'         => Better_Messages()->functions->rest_user_item( $message['sender_id'] ),
                        'thread_id'    => $message['thread_id'],
                        'message'      => $message['message'],
                        'time'         => $message['date_sent'],
                        'view_link'    => $view_link,
                        'participants' => $message['participants']
                    ];

                    $return['messages'][] = $item;
                }
            }

            return $return;
        }

        public function get_threads( WP_REST_Request $request ){
            global $wpdb;
            $page = (isset($_GET['cpage'])) ? intval( $_GET['cpage'] ) : 1;

            $per_page = 20;
            $offset = 0;
            if( $page > 1 ){
                $offset = ( $page - 1 ) * $per_page;
            }

            $count = $wpdb->get_var("SELECT COUNT(*) FROM `" . bm_get_table('threads') . "`");

            $return = [
                'total' => $count,
                'threads' => []
            ];

            $threads = $wpdb->get_results( "
                SELECT *, 
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('recipients') . "` 
                 WHERE `thread_id` = `threads`.`id`) participants,
                (SELECT COUNT(*) 
                  FROM `" . bm_get_table('messages') . "` 
                 WHERE `thread_id` = `threads`.`id`) messages
                FROM `" . bm_get_table('threads') . "` `threads`
                ORDER BY `threads`.`id` DESC
                LIMIT {$offset}, {$per_page}
            ", ARRAY_A );

            if( count($threads) > 0 ){
                foreach( $threads as $thread ){
                    $item = [
                        'id'           => $thread['id'],
                        'subject'      => $thread['subject'],
                        'participants' => $thread['participants'],
                        'messages'     => $thread['messages']
                    ];

                    $return['threads'][] = $item;
                }
            }

            return $return;
        }
    }

    function Better_Messages_Rest_Api_Admin(){
        return Better_Messages_Rest_Api_Admin::instance();
    }

endif;
