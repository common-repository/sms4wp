<?php
if ( !defined('ABSPATH') ) exit;

global $wpdb;


define( 'SMS4WP_BACKEND', 'https://backend.sms4wp.com/api/' );
define( 'SMS4WP_BACKEND_URL', SMS4WP_BACKEND . 'v1' );
define( 'SMS4WP_SMS_URL',  SMS4WP_BACKEND_URL . '/message/sms/');
define( 'SMS4WP_LMS_URL',  SMS4WP_BACKEND_URL . '/message/lms/');
define( 'SMS4WP_MMS_URL',  SMS4WP_BACKEND_URL . '/message/mms/');
define( 'SMS4WP_SMS_TEST', SMS4WP_BACKEND_URL . '/message/test/sms/');
define( 'SMS4WP_SEND_NUMBER_URL', SMS4WP_BACKEND . 'v2/sendnumber/register/');

define( 'SMS4WP_FULL_NAME',    'SMS for WordPress Plugin');
define( 'SMS4WP_SHORT_NAME',   'sms4wp');
define( 'SMS4WP_LANG_CONTEXT', 'sms4wp');
define( 'SMS4WP_VERSION',      '0.1');
define( 'SMS4WP_CURLOPT_TIMEOUT', 1);

define( 'SMS4WP_SENDNUMBER_COMMENT', 'ivynet');

/* sms4wp upload */
$upload_dir = wp_upload_dir();
define( 'SMS4WP_DIR_PERMISSION',  0755 ); // 디렉토리 생성시 퍼미션
define( 'SMS4WP_FILE_PERMISSION', 0644 ); // 파일 생성시 퍼미션
define( 'SMS4WP_DATA_PATH', $upload_dir['basedir'] . '/' . SMS4WP_SHORT_NAME );
define( 'SMS4WP_DATA_URL',  $upload_dir['baseurl'] . '/' . SMS4WP_SHORT_NAME );
define( 'SMS4WP_FILE_MMS_LIMIT', 1024*20 ); // 파일 생성시 퍼미션
// sms4wp dir
if ( ! file_exists(SMS4WP_DATA_PATH) ) {
	@mkdir( SMS4WP_DATA_PATH, SMS4WP_DIR_PERMISSION );
	@chmod( SMS4WP_DATA_PATH, SMS4WP_DIR_PERMISSION );
}

/* sms4wp */
define( 'SMS4WP_PAGE_ROWS', 15 );

/* Absolute path of sms4wp */
define( 'SMS4WP_ABSPATH', dirname( __FILE__ ) );

/* main file */
define( 'SMS4WP_MAIN_FILE', SMS4WP_ABSPATH . '/sms4wp.php' );

/* first-depth directories */
define( 'SMS4WP_INC_PATH',  SMS4WP_ABSPATH . '/includes' );
define( 'SMS4WP_LANG_PATH', SMS4WP_ABSPATH . '/languages' );
define( 'SMS4WP_PLUG_PATH', SMS4WP_ABSPATH . '/plugins' );

/* second-depth directories */
// define( 'SMS4WP_INC_BOOTSTRAP_PATH', SMS4WP_INC_PATH . '/bootstraps' );
define( 'SMS4WP_INC_CONTROL_PATH', SMS4WP_INC_PATH . '/controls' );
define( 'SMS4WP_INC_CORE_PATH',    SMS4WP_INC_PATH . '/core' );
define( 'SMS4WP_INC_MODEL_PATH',   SMS4WP_INC_PATH . '/models' );
define( 'SMS4WP_INC_VIEW_PATH',    SMS4WP_INC_PATH . '/views' );

/* third-depth directories */
define( 'SMS4WP_INC_VIEW_TEMPLATE_PATH', SMS4WP_INC_VIEW_PATH . '/templates' );

/* sms providers */
// define( 'SMS4WP_INC_PROVIDER_KTH_PATH', SMS4WP_INC_PROVIDER_PATH . '/kth' );

/* view urls */
define( 'SMS4WP_URL',              plugin_dir_url( __FILE__ ) );
define( 'SMS4WP_INC_URL',          SMS4WP_URL . 'includes' );
define( 'SMS4WP_INC_CONTROL_URL',  SMS4WP_INC_URL . '/controls' );
define( 'SMS4WP_INC_CORE_URL',     SMS4WP_INC_URL . '/core' );
define( 'SMS4WP_INC_MODEL_URL',    SMS4WP_INC_URL . '/models' );
define( 'SMS4WP_INC_VIEW_URL',     SMS4WP_INC_URL . '/views' );
define( 'SMS4WP_INC_VIEW_CSS_URL', SMS4WP_INC_VIEW_URL . '/css' );
define( 'SMS4WP_INC_VIEW_JS_URL',  SMS4WP_INC_VIEW_URL . '/js' );
define( 'SMS4WP_INC_VIEW_IMG_URL', SMS4WP_INC_VIEW_URL . '/img' );


/* BACKEND URLs */
// define( 'SMS4WP_BACKEND_URL', 'http://54.92.100.248/backend/api/v0' );
// define( 'SMS4WP_BACKEND_URL', 'http://54.92.100.245/backend/api/v0' );
define( 'SMS4WP_BACKEND_URL_MESSAGING_SMS', SMS4WP_BACKEND_URL . '/messaging/sms/' );
define( 'SMS4WP_BACKEND_URL_MESSAGING_LMS', SMS4WP_BACKEND_URL . '/messaging/lms/' );
define( 'SMS4WP_BACKEND_URL_MESSAGING_MMS', SMS4WP_BACKEND_URL . '/messaging/mms/' );

/* sms4wp DB */
define( 'SMS4WP_OPTIONS_TABLE',   $wpdb->prefix . 'sms4wp_options' ); // 옵션정보
define( 'SMS4WP_SEND_LIST_TABLE', $wpdb->prefix . 'sms4wp_sends' ); // 전송내역
define( 'SMS4WP_RECEIVERS_TABLE', $wpdb->prefix . 'sms4wp_receivers' ); // 수신자 목록
define( 'SMS4WP_GROUP_TABLE',     $wpdb->prefix . 'sms4wp_groups' ); // 그룹 관리
define( 'SMS4WP_TEMPLATE_TABLE',  $wpdb->prefix . 'sms4wp_templates' ); // 템플릿 관리
