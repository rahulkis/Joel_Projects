<?php
/*
Plugin Name: Tars - Create Chatbots with no Programming Knowledge
Version: 0.0.2
Plugin URI: https://hellotars.com/
Description: Create Engaging Conversational Bots. We help you make Chatbots that can be used on your Website. No coding required.
Author: Vinit Agrawal
Author URI: https://profiles.wordpress.org/vinitagr/
License: GPL v2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_menu', 'tars_admin_menu' );
function tars_admin_menu() {
    add_options_page( __('TARS Bot Widget', 'tarsbot' ), __('TARS Bot Widget', 'tarsbot' ), 'manage_options', 'tarsbot-plugin', 'tarsbot_options_page' );
}


add_action( 'admin_init', 'tarsbot_admin_init' );

function tarsbot_admin_init() {

  	register_setting( 'tarsbot-settings-group', 'tarsbot-plugin-settings' ); 
  	add_settings_section( 'tarsbot-section-1', __( 'TARS Bot Widget Script', 'tarsbot' ), 'tarsbot_section_1_view', 'tarsbot-plugin' );
  	
  	// add_settings_section( 'tarsbot-section-2', __( 'Remove TARS Bot from', 'tarsbot' ), 'tarsbot_section_2_view', 'tarsbot-plugin' );
  	

  	add_settings_field( 'script', __( 'Script', 'tarsbot' ), 'tarsbot_script_callback', 'tarsbot-plugin', 'tarsbot-section-1' );
	
	// add_settings_field( 'home', __( 'Home', 'tarsbot' ), 'tarsbot_home_callback', 'tarsbot-plugin', 'tarsbot-section-2' );
	// Post types specific
	
	// add_settings_field( 'allpages', __( 'All Pages', 'tarsbot' ), 'tarsbot_allpages_callback', 'tarsbot-plugin', 'tarsbot-section-2' );
	// add_settings_field( 'allposts', __( 'All Posts', 'tarsbot' ), 'tarsbot_allposts_callback', 'tarsbot-plugin', 'tarsbot-section-2' );

	// // Woocommerce Support
	// if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	// 	add_settings_field( 'cart', __( 'Cart', 'tarsbot' ), 'tarsbot_cart_callback', 'tarsbot-plugin', 'tarsbot-section-2' );
	// 	add_settings_field( 'checkout', __( 'Checkout', 'tarsbot' ), 'tarsbot_checkout_callback', 'tarsbot-plugin', 'tarsbot-section-2' );
	// }
	
	
}
/* 
 * THE ACTUAL PAGE 
 * */
function tarsbot_options_page() {
?>
  <div class="wrap">
      <h2><?php _e('TARS Bot Widget Options', 'tarsbot'); ?></h2>
      <form action="options.php" method="POST">
        <?php settings_fields('tarsbot-settings-group'); ?>
        <?php do_settings_sections('tarsbot-plugin'); ?>
        <?php submit_button(); ?>
      </form>
  </div>
<?php }
/*
* THE SECTIONS
* */
function tarsbot_section_1_view() {
	_e( "Please copy the  code snippet from the Distribute section of your <a href='https://admin.hellotars.com' target='_blank'>TARS Bot</a> and paste it  here.", 'tarsbot' );
	echo "<p>Don't have a TARS account? <a href='https://admin.hellotars.com/#register' target='_blank'> Click here to register</a>. <p>";

}
function tarsbot_section_2_view() {
	_e( 'Tick against the pages where you want to hide the chatbot.', 'tarsbot' );
}
/*
* THE FIELDS
* */
function tarsbot_script_callback() {
	
	$settings = (array) get_option( 'tarsbot-plugin-settings' );
	$field = "script";
	if(isset($settings[$field])){
		$value =$settings[$field];
	}
		if(isset($value)){
			echo '<textarea rows="7" cols="50" name="tarsbot-plugin-settings[script]">'.$value.'</textarea>';
		}
		else{
			echo '<textarea rows="7" cols="50" name="tarsbot-plugin-settings[script]"></textarea>';
		}
	
}
// CHECKBOX - Name: plugin_options[chkbox1]
function tarsbot_home_callback() {
	$options =get_option('tarsbot-plugin-settings');
	if(isset($options['home']) && $options['home']==1 ){
		echo "<input type='checkbox' name='tarsbot-plugin-settings[home]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='tarsbot-plugin-settings[home]' value='1'/>";
	}	
	
}
function tarsbot_allpages_callback() {
	$options =get_option('tarsbot-plugin-settings');
	if(isset($options['allpages']) && $options['allpages']==1 ){
		echo "<input type='checkbox' name='tarsbot-plugin-settings[allpages]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='tarsbot-plugin-settings[allpages]' value='1'/>";
	}	
}

function tarsbot_allposts_callback() {
	$options =get_option('tarsbot-plugin-settings');
	if(isset($options['allposts']) && $options['allposts']==1 ){
		echo "<input type='checkbox' name='tarsbot-plugin-settings[allposts]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='tarsbot-plugin-settings[allposts]' value='1'/>";
	}	
}
function tarsbot_cart_callback() {
	$options =get_option('tarsbot-plugin-settings');
	if(isset($options['cart']) && $options['cart']==1 ){
		echo "<input type='checkbox' name='tarsbot-plugin-settings[cart]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='tarsbot-plugin-settings[cart]' value='1'/>";
	}	
}
function tarsbot_checkout_callback() {
	$options =get_option('tarsbot-plugin-settings');
	if(isset($options['checkout']) && $options['checkout']==1 ){
		echo "<input type='checkbox' name='tarsbot-plugin-settings[checkout]' value='1' checked/>";
	}					
	else{ 
		echo "<input type='checkbox' name='tarsbot-plugin-settings[checkout]' value='1'/>";
	}	
}

add_action("wp_footer","tarsbot_footer_script",100,1);
function tarsbot_footer_script($pos_id){

	$settings = (array) get_option( 'tarsbot-plugin-settings' );
	
	if(isset($settings['script'])){
		$script =$settings['script'];
	}
	if(isset($settings['home']) && $settings['home']==1){
		if((is_home() || is_front_page() )){
			return;
		}
	}
	if(isset($settings['allpages'])&& $settings['allpages']==1){
		if (is_page()) {
			return;
		}
	}
	if(isset($settings['allposts']) && $settings['allposts']==1){
		if ('post' == get_post_type() && !is_home()) {
			return ;
		}
		
	}
	if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
				if(isset($settings['cart'])){
						if(is_cart() && $settings['cart']==1) {
							return;
						}
				}		
				if(isset($settings['checkout'])){
					if(is_checkout() && $settings['checkout']==1) {
						return;
					}
				}	
	}			
	
	echo $script;

}


function plugin_add_settings_link_tarsbot( $links ) {
    $settings_link = '<a href="options-general.php?page=tarsbot-plugin">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link_tarsbot' );

?>