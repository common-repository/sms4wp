<?php
if ( !defined('ABSPATH') ) exit;

/**
 * sms4wp 패널 메뉴생성 및 플러그인 설치시 초기 테이블생성
 */


//-- admin panel menu structure --//
add_action('admin_menu', 'sms4wp_menu');
function sms4wp_menu() {
    add_menu_page('SMS4WP title', '문자메시지', '', 'sms4wp-service', 'sms4wp_view_configure', '', 100);

    add_submenu_page('sms4wp-service', 'SMS4WP SET-UP',    '설정',          'manage_options', 'sms4wp-configure', 'sms4wp_view_configure');
    add_submenu_page('sms4wp-service', 'SMS4WP SEND',      '메시지 보내기',   'manage_options', 'sms4wp-send',      'sms4wp_view_send');
    add_submenu_page('sms4wp-service', 'SMS4WP RECEIVERS', '수신자 관리',     'manage_options', 'sms4wp-receivers', 'sms4wp_view_receivers');
    add_submenu_page('sms4wp-service', 'SMS4WP GROUP',     '수신자 그룹관리',  'manage_options', 'sms4wp-group',     'sms4wp_view_group');
    add_submenu_page('sms4wp-service', 'SMS4WP SEND LIST', '전송 내역',       'manage_options', 'sms4wp-send-list', 'sms4wp_view_send_list');
    add_submenu_page('sms4wp-service', 'SMS4WP TEMPLATE',  '템플릿 관리',     'manage_options', 'sms4wp-template',   'sms4wp_view_template');
    add_submenu_page('sms4wp-service', 'SMS4WP BOOK FILE', '가져오기',       'manage_options', 'sms4wp-book-file',  'sms4wp_view_book_file');
    // add_submenu_page('sms4wp-service', 'SMS4WP BOOK FILE', '가져오기/내보내기', 'manage_options', 'sms4wp-book-file',  'sms4wp_view_book_file');
    // add_submenu_page('sms4wp-service', 'SMS4WP Plugin Options', '수신자 그룹관리', 'manage_options', 'edit-tags.php?taxonomy=sms4wp');
}

//-- 문자 잔여건수 설정값 이하로 떨어지면 지정된 번호로 문자전송
add_action( 'init', 'sms4wp_charge_notice');
function sms4wp_charge_notice() {
    $sms4wp_config = sms4wp_get_configure();
    $checker            = $sms4wp_config['sms4wp_charge_notice_checker'];
    if($checker == 'yes'){
        $charge_notice_from     =  (isset($sms4wp_config['sms4wp_charge_notice_from'])) ? $sms4wp_config['sms4wp_charge_notice_from'] : '';
        $charge_notice_to       =  (isset($sms4wp_config['sms4wp_charge_notice_to'])) ? $sms4wp_config['sms4wp_charge_notice_to'] : '';
        $charge_notice_count    = (isset($sms4wp_config['sms4wp_charge_notice_count'])) ? $sms4wp_config['sms4wp_charge_notice_count'] : '';

        global $sms4wp_config;

        $sms4wp_config = sms4wp_get_configure();

        require_once( SMS4WP_INC_MODEL_PATH . '/sms4wp.control.php' );
        $sms4wp_data = sms4wp_prepare_data();
        $sms4wp_data['sms_point'] = @floor( $sms4wp_data['point'] / $sms4wp_data['sms_cost'] );   
        $sms_point = $sms4wp_data['sms_point'];

        if(is_numeric($sms_point)){
            date_default_timezone_set("Asia/Seoul");
            if($charge_notice_count >= $sms_point){
                $already_send_status = get_option('sms4wp_charge_notice_send', true);

                $date       = (isset($already_send_status['date'])) ? $already_send_status['date'] : '';
                $status     = (isset($already_send_status['status'])) ? $already_send_status['status'] : 'no';
                $date_ms    = strtotime($date);
                $now_ms     = strtotime(date('Y-m-d H:i:s'));
                $diff       = floor( ($now_ms - $date_ms) / 3600 ); 

                if( !is_array($already_send_status) || ($diff >= 24 && $status != 'yes') ){
                    $data['nonce']           = wp_create_nonce( 'sms4wp_ajax_message_nonce' ); // nonce 데이터
                    $data['sender_phone']    = $charge_notice_from; // 보내는 사람 전화번호
                    $data['sender_name']     = '시스템'; // 보내는 사람 이름
                    $data['message_type']    = 'SMS'; // 메시지 종류(SMS, LMS, MMS)
                    $data['message_subject'] = '전송 가능 건수가 얼마 남지 않았습니다.'; // 보내는 메시지 제목
                    $data['message_body']    = '['.get_bloginfo('name').']의 SMS 잔액이 ['.number_format($sms_point).']건입니다. 모두 소진되기 전에 sms4wp.com에 접속하여 충전하여 주십시오.'; // 보내는 메시지
                    $data['receiver_phone']  = $charge_notice_to; // 받는 사람 전화번호
                    $data['receiver_name']   = '담당자'; // 받는 사람 이름
                    $data['re_id']           = ''; // 수신자 관리 아이디 번호
                    $data['pattern']         = array();
                    sms4wp_send_message( $data ); // 문자보내기
                    $send_status = array( 'date' => (date('Y-m-d')." 10:00:00"), 'status' => 'yes' );
                    update_option('sms4wp_charge_notice_send', $send_status);
                }
            }
        }

    }
}


//-- plugin auto update --//
add_action('init', 'sms4wp_activate_au');
function sms4wp_activate_au() {
    require_once ( SMS4WP_INC_CORE_PATH . '/sms4wp.update.php' );

    $sms4wp_plugin_current_version = get_option( 'sms4wp-plugin-version' );
    // $sms4wp_plugin_remote_path     = admin_url() . '/update.php';
    $sms4wp_plugin_remote_path     = 'https://downloads.wordpress.org/plugin/sms4wp.zip';
    $sms4wp_plugin_slug            = plugin_basename(__FILE__);

    if ( !$sms4wp_plugin_current_version ) {
        add_option( 'sms4wp-plugin-version', '1.0', '', 'no' );
        $sms4wp_plugin_current_version = '1.0';
    }

    new sms4wp_auto_update ( $sms4wp_plugin_current_version, $sms4wp_plugin_remote_path, $sms4wp_plugin_slug );

    // 추가 sms 플러그인
    /*$sms4wp_config = sms4wp_get_configure();
    if ( !isset( $sms4wp_config['sms4wp_plugin'] ) || !is_array( $sms4wp_config['sms4wp_plugin'] ) )
        return;*/

    /*$plugin_path = SMS4WP_PLUG_PATH;
    $current_dir = @opendir($plugin_path);
    while ( $filename = @readdir($current_dir) ) {
        if ( $filename != "." and $filename != ".." ) {
            $plugin_file = $plugin_path . '/' . $filename . '/' . $filename . '.action.php';

            if ( in_array( $filename, $sms4wp_config['sms4wp_plugin'] ) && file_exists( $plugin_file ) ) {
                @require_once( $plugin_file );
            }
        }
    }*/
}


/***
 * 메시지 보내기 *
 ****/

/**
 * 보내는 메시지
 * @param  [array]  $data [메시지 정보]
 *         $data['nonce']           nonce 데이터
 *         $data['sender_phone']    보내는 사람 전화번호
 *         $data['sender_name']     보내는 사람 이름
 *         $data['message_type']    메시지 종류(SMS, LMS, MMS)
 *         $data['message_subject'] 보내는 메시지 제목
 *         $data['message_body']    보내는 메시지
 *         $data['receiver_phone']  받는 사람 전화번호
 *         $data['receiver_name']   받는 사람 이름
 *         $data['re_id']           수신자 관리 아이디 번호
 *         $data['pattern']         보내는 메시지 치환값 array( 'pattern'=>'replacement' )
 */
function sms4wp_send_message( $data = array() ) {
    global $wpdb;
    require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.sms.class.php' );

    $default = array(
        'nonce'           => '',
        'sender_phone'    => '',
        'sender_name'     => '',
        'message_type'    => 'SMS',
        'message_subject' => '',
        'message_body'    => '',
        'receiver_phone'  => '',
        'receiver_name'   => '',
        're_id'           => '',
        'pattern'         => array()
    );
    $data = array_merge( $default, $data );

    if ( isset($data['nonce']) && ! wp_verify_nonce( $data['nonce'], 'sms4wp_ajax_message_nonce' ) )
        return;

    if ( ! ( isset($data['message_body']) && $data['message_body'] ) || ! ( isset($data['receiver_phone']) && $data['receiver_phone'] ) )
        return;

    $sms4wp_config = sms4wp_get_configure();

    $SMS = new SMS4WP( $sms4wp_config['sms4wp_auth_email'], $sms4wp_config['sms4wp_auth_token'], $sms4wp_config['sms4wp_auth_signature'] );
    $SMS->Init();

    $args = array(
        'message_type'    => $data['message_type'],
        'message_subject' => stripslashes( htmlspecialchars_decode( $data['message_subject'] ) ),
        'message_body'    => stripslashes( htmlspecialchars_decode( $data['message_body'] ) ),
        'sender_phone'    => $data['sender_phone'],
        'sender_name'     => $data['sender_name'],
        'receiver_phone'  => $data['receiver_phone'],
        'receiver_name'   => $data['receiver_name'],
        'pattern'         => $data['pattern'],
        're_id'           => $data['re_id'] /* 수신자 관리 번호 */
    );

    $SMS->Add( $args );

    $SMS->Send();
    $SMS->Init();

    if ( isset($data['_wp_http_referer']) && $data['_wp_http_referer'] ) {
        echo '<script type="text/javascript">
            window.location = "'.$data['_wp_http_referer'].'";
        </script>';
        exit;
    }
}
//-- 메시지 DB저장 --//
function sms4wp_add_send_message( $args ) {
    global $wpdb;

    return;
}

//-- 기본설정데이터 --//
function sms4wp_get_configure( $option_name = '' ) {
    global $wpdb;

    $options = array();
    $option_name = trim($option_name);
    $query_where = '';

    if ( $option_name ) { // 한개의 옵션값 요청
        $query_where = " AND op_name='{$option_name}' ";
    }

    $rows = $wpdb->get_results( "SELECT * FROM `".SMS4WP_OPTIONS_TABLE."` WHERE op_use='1'" . $query_where );
    if ( !( is_array($rows) || is_object($rows) ) )
        return;

    foreach ( $rows as $row ) {
        $options[$row->op_name] = maybe_unserialize( $row->op_value );
    }

    if ( ! isset($options['sms4wp_auth_email']) )
        $options['sms4wp_auth_email'] = '';

    if ( $option_name ) {
        return $options[$option_name];
    }
    else {
        return $options;
    }
}

//-- plugin update --//
function sms4wp_install_update() {
    /*global $wpdb;

    $row = $wpdb->get_row(" SELECT * FROM `".SMS4WP_TEMPLATE_TABLE."` LIMIT 1 ");

    if( ! isset($row->te_receiver) )
        $wpdb->query(" ALTER TABLE `".SMS4WP_TEMPLATE_TABLE."` ADD `te_receiver` varchar(255) NOT NULL default '' AFTER `ID` ");

    if( ! isset($row->te_use) )
        $wpdb->query(" ALTER TABLE `".SMS4WP_TEMPLATE_TABLE."` ADD `te_use` tinyint(4) NOT NULL default '1' AFTER `ID` ");

    if( ! isset($row->te_sender_number) )
        $wpdb->query(" ALTER TABLE `".SMS4WP_TEMPLATE_TABLE."` ADD `te_sender_number` varchar(255) NOT NULL default '' AFTER `ID` ");

    if( ! isset($row->te_sender_name) )
        $wpdb->query(" ALTER TABLE `".SMS4WP_TEMPLATE_TABLE."` ADD `te_sender_name` varchar(255) NOT NULL default '' AFTER `ID` ");*/
}

//-- sms4wp registers a plugin function --//
function sms4wp_install() {
    global $wpdb;
    global $charset_collate;

    if ( !empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }

    if ( !empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE $wpdb->collate";
    }

    // sms options table
    if( $wpdb->get_var("show tables like '".SMS4WP_OPTIONS_TABLE."'") != SMS4WP_OPTIONS_TABLE ) {
        $sqls[] = "
            CREATE TABLE `".SMS4WP_OPTIONS_TABLE."` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `op_name` varchar(255) NOT NULL default '',
                `op_value` longtext NOT NULL,
                `op_date` datetime NOT NULL default '0000-00-00 00:00:00',
                `op_use` tinyint(4) NOT NULL default '1', /* 0:미사용, 1:사용, */
                PRIMARY KEY  (`ID`),
                UNIQUE KEY `op_name` (`op_name`)
            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;
        ";
    }

    // sms send list table
    if( $wpdb->get_var("show tables like '".SMS4WP_SEND_LIST_TABLE."'") != SMS4WP_SEND_LIST_TABLE ) {
        $sqls[] = "
            CREATE TABLE `".SMS4WP_SEND_LIST_TABLE."` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `re_id` bigint(11) NOT NULL default '0',
                `se_type` varchar(10) NOT NULL default '', /* SMS, LMS, MMS */
                `se_send_number` varchar(100) NOT NULL default '', /* 보내는 번호 */
                `se_receiver_number` varchar(100) NOT NULL default '', /* 받는 번호 */
                `se_receiver_name` varchar(100) NOT NULL default '', /* 받는 번호 */
                `se_subject` varchar(255) NOT NULL default '',
                `se_message` text NOT NULL,
                `se_sms_file1` varchar(255) NOT NULL default '',
                `se_sms_file2` varchar(255) NOT NULL default '', 
                `se_reservation_date` datetime NOT NULL default '0000-00-00 00:00:00', /* 예약일 */
                `se_reservation_use` tinyint(4) NOT NULL default '0', /* 0:즉시발송, 1:예약문자, */
                `se_result` tinyint(4) NOT NULL default '0', /* 0:실패, 1:성공, */
                `se_result_code` text NOT NULL,
                `se_date` datetime NOT NULL default '0000-00-00 00:00:00', /* 발송일 */
                PRIMARY KEY  (`ID`),
                KEY `re_id` (`re_id`)
            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;;
        ";
    }

    // sms receivers table
    if( $wpdb->get_var("show tables like '".SMS4WP_RECEIVERS_TABLE."'") != SMS4WP_RECEIVERS_TABLE ) {
        $sqls[] = "
            CREATE TABLE `".SMS4WP_RECEIVERS_TABLE."` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `gr_id` bigint(11) NOT NULL default '0',
                `re_user_id` varchar(255) NOT NULL default '',
                `re_name` varchar(255) NOT NULL default '',
                `re_phone_number` varchar(100) NOT NULL default '',
                `re_use` tinyint(4) NOT NULL default '1', /* 0:수신거부, 1:수신허용, */
                `re_memo` text NOT NULL,
                `re_update` datetime NOT NULL default '0000-00-00 00:00:00',
                PRIMARY KEY  (`ID`)
            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;;
        ";
    }

    // sms group table
    if( $wpdb->get_var("show tables like '".SMS4WP_GROUP_TABLE."'") != SMS4WP_GROUP_TABLE ) {
        $sqls[] = "
            CREATE TABLE `".SMS4WP_GROUP_TABLE."` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `gr_count` int(11) NOT NULL default '0',
                `gr_name` varchar(255) NOT NULL default '',
                `gr_parent` bigint(20) NOT NULL default '0',
                `gr_depth` tinyint(4) NOT NULL default '1',
                `gr_order` int(11) NOT NULL default '0',
                `gr_use` tinyint(4) NOT NULL default '1',
                `gr_memo` text NOT NULL,
                `gr_update` datetime NOT NULL default '0000-00-00 00:00:00',
                PRIMARY KEY  (`ID`)
            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;;
        ";
    }

    // sms template table
    if( $wpdb->get_var("show tables like '".SMS4WP_TEMPLATE_TABLE."'") != SMS4WP_TEMPLATE_TABLE ) {
        $sqls[] = "
            CREATE TABLE `".SMS4WP_TEMPLATE_TABLE."` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `te_sender_name` varchar(255) NOT NULL default '',
                `te_sender_number` varchar(255) NOT NULL default '',
                `te_use` tinyint(4) NOT NULL default '1',
                `te_receiver` varchar(255) NOT NULL default '',
                `te_type` varchar(10) NOT NULL default '',
                `te_group` varchar(255) NOT NULL default '',
                `te_subject` varchar(255) NOT NULL default '',
                `te_message` text NOT NULL,
                `te_file1` varchar(255) NOT NULL default '',
                `te_file2` varchar(255) NOT NULL default '',
                `te_date` datetime NOT NULL default '0000-00-00 00:00:00',
                PRIMARY KEY  (`ID`)
            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;;
        ";
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    foreach($sqls as $sql) {
        dbDelta($sql);
    }
}
