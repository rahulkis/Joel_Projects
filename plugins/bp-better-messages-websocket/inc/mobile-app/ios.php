<?php
defined('ABSPATH') || exit;

if (!class_exists('Better_Messages_Mobile_App_IOS')):

    class Better_Messages_Mobile_App_IOS
    {

        public $push_endpoint = 'https://api.development.push.apple.com';

        public $app_bundle_id = 'com.wordplus.messenger';

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_IOS();
            }

            return $instance;
        }

        public function __construct()
        {
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );
        }

        public function rest_api_init(){
            register_rest_route( 'better-messages/v1/app', '/ios/connectToAppStore', array(
                'methods' => 'POST',
                'callback' => array( $this, 'connect_to_app_store' ),
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                }
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/getBundles', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_bundles' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/getCertificates', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_certificates' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/getProfiles', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_profiles' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/getMatchedProfiles', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_matched_profiles' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/createCertificate', array(
                'methods' => 'POST',
                'callback' => array( $this, 'create_certificate' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/createProfile', array(
                'methods' => 'POST',
                'callback' => array( $this, 'create_profile' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/getDevices', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_devices' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );

            register_rest_route( 'better-messages/v1/app', '/ios/addDevice', array(
                'methods' => 'POST',
                'callback' => array( $this, 'add_device' ),
                'permission_callback' => array( $this, 'ensure_api_access' )
            ) );
        }

        public function ensure_api_access(){
            $access = get_option('better-messages-app-ios-auth', false);

            if( ! $access ){
                return new WP_Error(
                    'rest_error',
                    _x( 'Sorry, you are not allowed to do that', 'Rest API Error', 'bp-better-messages' ),
                    array( 'status' => 406 )
                );
            }

            return current_user_can( 'manage_options' );
        }

        public function get_settings(){
            $defaults = [
                'bundleDev'  => '',
                'bundleProd' => '',
            ];

            $settings = get_option('better-messages-app-ios-settings', $defaults);

            return wp_parse_args( $settings, $defaults );
        }

        public function generate_jwt_token( string $issuerID, string $keyID, string $apiKey, int $expiration = 15 ) : string{
            $claims = [
                'iss' => $issuerID,
                'iat' => time(),
                'exp' => time() + 60 * $expiration,
                'aud' => 'appstoreconnect-v1',
            ];

            $head = [
                "alg" => "ES256",
                "kid" => $keyID,
                "typ" => "JWT"
            ];

            return \BetterMessages\Firebase\JWT\JWT::encode($claims, $apiKey, 'ES256', null, $head);
        }

        public function get_jwt(){
            $access = get_option('better-messages-app-ios-auth', false);
            if( ! $access ) return "";

            return $this->generate_jwt_token( $access['issuerID'], $access['keyID'], $access['apiKey'] );
        }

        public function get_push_jwt(){
            $access = get_option('better-messages-app-ios-auth', false);
            if( ! $access ) return "";

            $key = '';
            $team_id = '';
            $app_bundle_id = $this->app_bundle_id;
            $key_id = '';

            $claims = array(
                'iss' => $team_id,
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $app_bundle_id,
            );

            return \BetterMessages\Firebase\JWT\JWT::encode($claims, $key, 'ES256', $key_id);
        }

        public function get_bundles( WP_REST_Request $request ){
            $jwt = $this->get_jwt();

            $response = wp_remote_get('https://api.appstoreconnect.apple.com/v1/bundleIds?limit=200', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                ),
            ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ) {
                foreach ($data as $item) {
                    $item_id = $item->id;
                    $name = $item->attributes->name;
                    $identifier = $item->attributes->identifier;

                    $result[] = [ 'id' => $item_id, 'identifier' => $identifier, 'name' => $name ];
                }
            }

            return $result;
        }

        public function get_certificates( WP_REST_Request $request ){
            $jwt = $this->get_jwt();

            $response = wp_remote_get('https://api.appstoreconnect.apple.com/v1/certificates?limit=200', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                ),
            ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ) {
                foreach ($data as $item) {
                    $id = $item->id;
                    $serialNumber = $item->attributes->serialNumber;
                    $certificateContent = $item->attributes->certificateContent;
                    $displayName = $item->attributes->displayName;
                    $name = $item->attributes->name;
                    $expirationDate = $item->attributes->expirationDate;
                    $certificateType = $item->attributes->certificateType;

                    $result[] = [
                        'id' => $id,
                        'serialNumber' => $serialNumber,
                        'certificateContent' => $certificateContent,
                        'displayName' => $displayName,
                        'name' => $name,
                        'expirationDate' => $expirationDate,
                        'certificateType' => $certificateType,
                    ];
                }
            }

            return $result;
        }

        public function get_bundle_capabilities(){
            $jwt = Better_Messages_Mobile_App_IOS()->get_jwt();

            $response = wp_remote_get('https://api.appstoreconnect.apple.com/v1/bundleIds/54YSTNSYN4/bundleIdCapabilities', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/json'
                )
            ]);

            $response = json_decode($response['body']);

            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ) {
                foreach ($data as $item) {
                    $result[] =  $item->attributes->capabilityType;
                }
            }

            return $result;
        }

        public function get_matched_profiles( WP_REST_Request $request ){
            $type          = 'IOS_APP_DEVELOPMENT';
            $bundleId      = $request->get_param('bundleId');
            $certificateId = $request->get_param( 'certificateId' );

            $jwt = $this->get_jwt();

            $url = add_query_arg([
                'include' => 'bundleId,certificates',
                'filter' => [
                    'profileState' => 'ACTIVE',
                    'profileType' => $type
                ],
                'limit' => 200
            ], 'https://api.appstoreconnect.apple.com/v1/profiles');

            $response = wp_remote_get($url, [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/json'
                )
            ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ){
                foreach ( $data as $item ){
                    $itemBundleId = $item->relationships->bundleId->data->id;
                    if( $itemBundleId !== $bundleId ) continue;
                    $certificates = $item->relationships->certificates->data;

                    $has_certificate = false;
                    foreach ( $certificates as $certificate ){
                        if( $certificate->id === $certificateId ) $has_certificate = true;
                    }

                    if( $has_certificate ){
                        $result[] = [
                            'id' => $item->id,
                            'profileState' => $item->attributes->profileState,
                            'createdDate' => $item->attributes->createdDate,
                            'profileType' => $item->attributes->profileType,
                            'name' => $item->attributes->name,
                            'profileContent' => $item->attributes->profileContent,
                            'uuid' => $item->attributes->uuid,
                            'platform' => $item->attributes->IOS,
                            'expirationDate' => $item->attributes->expirationDate,
                        ];
                    }
                }
            }

            return $result;
        }

        public function add_device( WP_REST_Request $request ){
            $deviceId = $request->get_param('deviceId');
            $settings = Better_Messages_Mobile_App_Options()->get_mobile_settings();

            $iosDevices = $settings['iosDevices'];
            $iosDevices[] = $deviceId;

            $settings['iosDevices'] = array_unique($iosDevices);

            Better_Messages_Mobile_App_Options()->update_mobile_settings( $settings );

            return true;
        }

        public function get_devices( WP_REST_Request $request ){
            $jwt = $this->get_jwt();

            $url = add_query_arg([
                'limit' => 200
            ], 'https://api.appstoreconnect.apple.com/v1/devices');

            $response = wp_remote_get($url, [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/json'
                )
            ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ) {
                foreach ($data as $item) {
                    $id = $item->id;

                    $result[] = [
                        'id'          => $id,
                        'addedDate'   => $item->attributes->addedDate,
                        'name'        => $item->attributes->name,
                        'deviceClass' => $item->attributes->deviceClass,
                        'model'       => $item->attributes->model,
                        'udid'        => $item->attributes->udid,
                        'platform'    => $item->attributes->platform,
                        'status'      => $item->attributes->status
                    ];
                }
            }

            return $result;
        }

        public function get_profiles( WP_REST_Request $request ){
            $jwt = $this->get_jwt();

            $response = wp_remote_get('https://api.appstoreconnect.apple.com/v1/profiles?limit=200', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/json'
                )
            ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $result = [];

            if( count( $data ) > 0 ) {
                foreach ($data as $item) {
                    $id = $item->id;

                    $result[] = [
                        'id' => $id,
                        'profileState' => $item->attributes->profileState,
                        'createdDate' => $item->attributes->createdDate,
                        'profileType' => $item->attributes->profileType,
                        'name' => $item->attributes->name,
                        'profileContent' => $item->attributes->profileContent,
                        'uuid' => $item->attributes->uuid,
                        'platform' => $item->attributes->IOS,
                        'expirationDate' => $item->attributes->expirationDate,
                    ];
                }
            }

            return $result;
        }

        public function generate_csr(){
            $dn = array(
                "countryName" => "UA",
                "stateOrProvinceName" => "Odesska",
                "localityName" => "Odessa",
                "organizationName" => "WordPlus",
                "organizationalUnitName" => "Company",
                "commonName" => "WordPlus",
                "emailAddress" => "csr@wordplus.org"
            );

            $password = Better_Messages()->functions->generateRandomString(10);

            $privkey = openssl_pkey_new(array(
                "private_key_bits" => 2048,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ));

            openssl_pkey_export( $privkey, $pk, $password );

            $csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
            openssl_csr_export($csr, $csrString);

            return [
                'pass'  => $password,
                'pkcs8' => trim( $pk ),
                'csr'   => trim( $csrString )
            ];
        }

        public function create_certificate( WP_REST_Request $request ){
            $jwt  = $this->get_jwt();
            $type = $request->get_param('type');

            $certificateType = 'DEVELOPMENT';
            if( $type === 'DISTRIBUTION' ){
                $certificateType = 'DISTRIBUTION';
            }

            $cert = Better_Messages_Mobile_App_IOS()->generate_csr();

            $response = wp_remote_post('https://api.appstoreconnect.apple.com/v1/certificates', [
               'headers'     => array(
                   'Authorization' => 'Bearer ' . $jwt,
                   'Content-Type' => 'application/json'
               ),
               'body'        => json_encode([
                   'data' => [
                       'attributes' => [
                           'certificateType' => 'DEVELOPMENT', # IOS_DEVELOPMENT | IOS_DISTRIBUTION
                           'csrContent' => $cert['csr']
                       ],
                       'type' => 'certificates'
                   ]
               ]),
           ]);

            $response = json_decode($response['body']);
            $data = $response->data;

            $certificate = [
                'id' => $data->id,
                'serialNumber' => $data->attributes->serialNumber,
                'certificateContent' => $data->attributes->certificateContent,
                'displayName' => $data->attributes->displayName,
                'name' => $data->attributes->name
            ];

            $cert['certificate'] = $certificate;

            $developmentCer = base64_decode($cert['certificate']['certificateContent']);

            $x509_cert = openssl_x509_read( $this->makeX509Cert( $developmentCer ) );
            $pk = $cert['pkcs8'];
            $password = $cert['pass'];

            $private_key = openssl_get_privatekey( $pk, $password );
            openssl_pkcs12_export( $x509_cert, $p12, $private_key, $password );

            $cert['p12'] = base64_encode($p12);

            $key = 'better-messages-app-ios-certificate-' . $certificateType;
            update_option( $key, $cert, false );
        }

        public function makeX509Cert($bindata) {
            $beginpem = "-----BEGIN CERTIFICATE-----\n";
            $endpem = "-----END CERTIFICATE-----\n";

            $pem = $beginpem;
            $cbenc = base64_encode($bindata);
            for($i = 0; $i < strlen($cbenc); $i++) {
                $pem .= $cbenc[$i];
                if (($i + 1) % 64 == 0)
                    $pem .= "\n";
            }
            $pem .= "\n".$endpem;

            return $pem;
        }

        public function create_profile( WP_REST_Request $request ){
            $settings = Better_Messages_Mobile_App_Options()->get_mobile_settings();

            $jwt = $this->get_jwt();

            $type = 'IOS_APP_DEVELOPMENT'; // https://developer.apple.com/documentation/appstoreconnectapi/profilecreaterequest/data/attributes

            $bundleId      = $request->get_param('bundleId');
            $certificateId = $request->get_param('certificateId');
            $key           = $request->get_param('key');

            switch ($key){
                case 'iosProfileDev':
                    $profileFor = 'dev-application';
                    break;
                case 'iosProfileServiceDev':
                    $profileFor = 'dev-service';
                    break;

                default:
                    return new WP_Error(
                        'rest_forbidden',
                        _x( 'Sorry, you are not allowed to do that', 'Rest API Error', 'bp-better-messages' ),
                        array( 'status' => 401 )
                    );
            }

            $devices = (array) $settings['iosDevices'];

            if( count( $devices ) === 0 ) {
                return new WP_Error(
                    'rest_forbidden',
                    'Development profile cant be created without selected devices',
                    array( 'status' => 401 )
                );
            }

            $devices_args = [];
            foreach ( $devices as $device ){
                $devices_args[] = [
                    'id'   => $device,
                    'type' => 'devices'
                ];
            }

            $args = [
                'data' => [
                    'attributes' => [
                        'name'        => 'Better Messages Dev Profile (BundleId ' . $bundleId . ')',
                        'profileType' => $type
                    ],
                    'relationships' => [
                        'bundleId' => [
                            'data' => [
                                'id' => $bundleId,
                                'type' => 'bundleIds'
                            ]
                        ],
                        'certificates' => [
                            'data' => [
                                [
                                    'id' => $certificateId,
                                    'type' => 'certificates'
                                ]
                            ]
                        ],
                        'devices' => [
                            'data' => $devices_args
                        ]
                    ],
                    'type' => 'profiles'
                ]
            ];

            $response = wp_remote_post('https://api.appstoreconnect.apple.com/v1/profiles', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                    'Content-Type' => 'application/json'
                ),
                'body'        => json_encode($args),
            ]);

            $response = json_decode($response['body']);
            $item = $response->data;
            $profile = [
                'id' => $item->id,
                'profileState' => $item->attributes->profileState,
                'createdDate' => $item->attributes->createdDate,
                'profileType' => $item->attributes->profileType,
                'name' => $item->attributes->name,
                'profileContent' => $item->attributes->profileContent,
                'uuid' => $item->attributes->uuid,
                'platform' => $item->attributes->IOS,
                'expirationDate' => $item->attributes->expirationDate,
            ];

            update_option( 'better-messages-app-ios-profile-' . $profileFor, $profile, false );
        }

        public function connect_to_app_store( WP_REST_Request $request ){
            $keyID    = $request->get_param('keyID');
            $issuerID = $request->get_param('issuerID');
            $files    = $request->get_file_params();

            if( ! isset( $files['apiKey'] ) ) {
                return new WP_Error(
                    'rest_error',
                    _x( 'Sorry, you are not allowed to do that', 'Rest API Error', 'bp-better-messages' ),
                    array( 'status' => 406 )
                );
            }

            $apiKey = file_get_contents($files['apiKey']['tmp_name']);

            $jwt = $this->generate_jwt_token($issuerID, $keyID, $apiKey);

            $response = wp_remote_get('https://api.appstoreconnect.apple.com/v1/apps', [
                'headers'     => array(
                    'Authorization' => 'Bearer ' . $jwt,
                ),
            ]);

            if( $response['response'] ){
                $code = $response['response']['code'];
                if( $code === 200 ){
                    update_option( 'better-messages-app-ios-auth', [
                        'keyID'    => $keyID,
                        'issuerID' => $issuerID,
                        'apiKey'   => $apiKey
                    ], false );

                    return [
                        'result'  => 'success'
                    ];
                } else {
                    return new WP_Error(
                        'rest_error',
                        _x( 'Connection to Apple Developer Account was not successful', 'WP Admin (Mobile App)', 'bp-better-messages' ),
                        array( 'status' => $code )
                    );
                }
            }
        }


    }

endif;

function Better_Messages_Mobile_App_IOS()
{
    return Better_Messages_Mobile_App_IOS::instance();
}
