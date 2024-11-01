<?php
/**
Plugin Name: SMS for sms4wp
Plugin URI: https://sms4wp.com/
Description: Send SMS messages from WordPress!
Version: 1.1.8
Author: sms4wp.com
Author URI: https://ivynet.co.kr/
*/
if ( !defined( 'ABSPATH' ) ) exit;

require_once( 'defines.php' );

require_once( SMS4WP_INC_CORE_PATH . '/sms4wp.init.php' );

if( is_admin() ) {
	require_once( SMS4WP_INC_MODEL_PATH . '/sms4wp.lib.php' );
    //-- registers a plugin function to be run when the sms4wp is activated. --//
    register_activation_hook( __FILE__, 'sms4wp_install' );
    // sms4wp_install_update();
}
require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp-add-on.php' );
