<?php
/*
Plugin Name: Allow Additional Filetypes to be uploaded (.pub)
Description: This plugin allows you to upload additional filetypes that are not allowed by default (.pub)
Version:     1.0
Author:      Ron Holt
Author URI:  http://ronholt.info/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function af_mime_types($mime_types){
    $mime_types['pub'] = 'application/x-mspublisher'; // Adding publisher files
    $mime_types['svg'] = 'image/svg+xml'; // Adding SVG files

    return $mime_types;
}
add_filter('upload_mimes', 'af_mime_types', 1, 1);




//
// function af_test_mime_types() {
//
// }
// add_action( 'wp_loaded', 'af_test_mime_types' );
