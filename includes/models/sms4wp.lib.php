<?php
if (!defined('ABSPATH')) exit;

require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.ajax.lib.php' );
require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.sms.class.php' );
require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.lib.php' );
require_once( SMS4WP_INC_MODEL_PATH . '/sms4wp.common.php' );

/**
 * sms4wp functions
 */

//-- Add top level menu page --//
function sms4wp_view_configure() {
    global $sms4wp_config;

    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action'] ) {
        case 'update':
            sms4wp_update_configure( $_REQUEST );
        break;
    }
    $sms4wp_config = sms4wp_get_configure();

    require_once( SMS4WP_INC_MODEL_PATH . '/sms4wp.control.php' );
    $sms4wp_data = sms4wp_prepare_data();

    $sms4wp_data['sms_point'] = @floor( $sms4wp_data['point'] / $sms4wp_data['sms_cost'] );
    $sms4wp_data['lms_point'] = @floor( $sms4wp_data['point'] / $sms4wp_data['lms_cost'] );
    $sms4wp_data['mms_point'] = @floor( $sms4wp_data['point'] / $sms4wp_data['mms_cost'] );

    if ( $sms4wp_data['sms_error_msg'] ) 
        $sms_charge = $sms4wp_data['sms_error_msg'];
    else 
        $sms_charge = 'SMS: ' . number_format( $sms4wp_data['sms_point']) . '건';
    
    @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_configure.php' );
}

//-- 메시지 보내기 --//
function sms4wp_view_send() {

    $sms4wp_config = sms4wp_get_configure();

    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action'] ) {
        case 'sms_sends':
            sms4wp_send_message( $_REQUEST );
        break;

        default:
            add_action( 'wp_ajax_sms4wp_ajax_sms_sends', 'sms4wp_ajax_sms_sends' );
            $groups = sms4wp_get_groups_select();
            $nonce  = wp_create_nonce( 'sms4wp_ajax_sms_sends_nonce' );

            $message_nonce   = wp_create_nonce( 'sms4wp_ajax_message_nonce' );
            $template_nonce  = wp_create_nonce( 'sms4wp_ajax_template_nonce' );
            $receivers_nonce = wp_create_nonce( 'sms4wp_ajax_receivers_nonce' );
            $receivers_link  = admin_url('admin-ajax.php');

            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_sms_form.php' );
        break;
    }
} 

//-- 수신자 관리 --//
function sms4wp_view_receivers() {
    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action'] ) {
        case 'add_new':
            $groups = sms4wp_get_groups_select(); // 수신자 그룹
            $nonce  = wp_create_nonce( 'sms4wp_update_receiver_nonce' );
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_receiver_form.php' );
        break;

        case 'edit':
            $qs       = sms4wp_query_string();
            $id       = intval( $_REQUEST['id'] );
            $receiver = sms4wp_get_receiver( $id ); // 수신자 정보
            $smssends = sms4wp_sms_sends( $id ); // SMS전송목록
            $nonce    = wp_create_nonce( 'sms4wp_update_receiver_nonce' );
            $groups   = sms4wp_get_groups_select(); // 수신자 그룹
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_receiver_form.php' );
        break;

        case 'update':
            $url = sms4wp_receiver_update( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        case 'delete':
            $url = sms4wp_receiver_delete( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        default:
            $page_rows = SMS4WP_PAGE_ROWS;
            $_REQUEST['page_rows'] = $page_rows;

            $groups    = sms4wp_get_groups_all();
            $groupList = sms4wp_get_groups_select(); // 수신자 그룹
            $list      = sms4wp_list_receivers( $_REQUEST );
            $qs        = sms4wp_query_string();

            $update_nonce = wp_create_nonce( 'sms4wp_update_receiver_nonce' );
            $delete_nonce = wp_create_nonce( 'sms4wp_delete_receiver_nonce' );
            $pagenum_link = './admin.php?page=sms4wp-receivers' . $qs;

            $args = array( 
                'page_rows'    => $page_rows, 
                'paged'        => ( isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 0 ), 
                's'            => ( isset($_REQUEST['s']) ? $_REQUEST['s'] : '' ), 
                'total'        => $list['total'], 
                'pagenum_link' => $pagenum_link 
            );
            $links = sms4wp_pagination( $args );
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_receivers.php' );
        break;
    }
} 

//-- 수신자 그룹관리 --//
function sms4wp_view_group() {
    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action'] ) {
        case 'add_new':
            $url = sms4wp_group_update( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;
        
        case 'edit':
            $qs      = sms4wp_query_string();
            $id      = intval( $_REQUEST['id'] );
            $group   = sms4wp_get_group( $id );
            $nonce   = wp_create_nonce( 'sms4wp_update_group_nonce' );
            $parents = sms4wp_get_groups_select();
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_group_form.php' );
        break;

        case 'update':
            $url = sms4wp_group_update( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        case 'delete':
            $url = sms4wp_group_delete( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        default:
            $page_rows = SMS4WP_PAGE_ROWS;
            $_REQUEST['page_rows'] = $page_rows;

            $qs      = sms4wp_query_string();
            $list    = sms4wp_list_groups( $_REQUEST );
            $parents = sms4wp_get_groups_select();

            $delete_nonce = wp_create_nonce( 'sms4wp_delete_group_nonce' );
            $update_nonce = wp_create_nonce( 'sms4wp_update_group_nonce' );
            $pagenum_link = './admin.php?page=sms4wp-group' . $qs;

            $args = array( 
                'page_rows'    => $page_rows, 
                'paged'        => ( isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 0 ), 
                's'            => ( isset($_REQUEST['s']) ? $_REQUEST['s'] : '' ), 
                'total'        => $list['total'], 
                'pagenum_link' => $pagenum_link 
            );
            $links = sms4wp_pagination( $args );

            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_group.php' );
        break;
    }
} 

//-- 전송 내역 --//
function sms4wp_view_send_list() {
    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action']) {
        case 'delete':
            $url = sms4wp_list_sends_delete( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;
        
        default:
            $page_rows = SMS4WP_PAGE_ROWS;
            $_REQUEST['page_rows'] = $page_rows;

            $list   = sms4wp_list_sends( $_REQUEST );
            $qs     = sms4wp_query_string();
            $groups = sms4wp_get_groups_all();

            $delete_nonce = wp_create_nonce( 'sms4wp_delete_send_list_nonce' );
            $pagenum_link = './admin.php?page=sms4wp-send-list' . $qs;

            $args = array( 
                'page_rows'    => $page_rows, 
                'paged'        => ( isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 0 ), 
                's'            => ( isset($_REQUEST['s']) ? $_REQUEST['s'] : '' ), 
                'total'        => $list['total'], 
                'pagenum_link' => $pagenum_link 
            );
            $links = sms4wp_pagination( $args );

            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_send_list.php' );
        break;
    }
} 

//-- 템플릿 관리 --//
function sms4wp_view_template() {
    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';

    switch ( $_REQUEST['action'] ) {
        case 'add_new':
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_template_form.php' );
        break;

        case 'edit':
            $id       = intval( $_REQUEST['id'] );
            $qs       = sms4wp_query_string();
            $template = sms4wp_get_template( $id );
            $nonce    = wp_create_nonce( 'sms4wp_update_template_nonce' );

            if ( !isset( $template['te_message_type'] ) )
                $template['te_message_type'] = '';

            switch ( $template['te_message_type'] ) {
                case 'SMS':
                    $template['checked_sms'] = true;
                    break;

                case 'LMS':
                    $template['checked_lms'] = true;
                    break;

                case 'MMS':
                    $template['checked_mms'] = true;
                    break;
                
                default:
                    $template['checked_sms'] = true;
                    break;
            }

            $template_group = sms4wp_template_groups();

            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_template_form.php' );
        break;

        case 'update':
            $url = sms4wp_template_update( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        case 'delete':
            $url = sms4wp_template_delete( $_REQUEST );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        default:
            $page_rows = SMS4WP_PAGE_ROWS;
            $_REQUEST['page_rows'] = $page_rows;
            
            $list = sms4wp_list_templates( $_REQUEST );
            $qs   = sms4wp_query_string();

            $delete_nonce = wp_create_nonce( 'sms4wp_delete_template_nonce' );
            $pagenum_link = './admin.php?page=sms4wp-send-list' . $qs;

            $args = array( 
                'page_rows'    => $page_rows, 
                'paged'        => ( isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 0 ), 
                's'            => ( isset($_REQUEST['s']) ? $_REQUEST['s'] : '' ), 
                'total'        => $list['total'], 
                'pagenum_link' => $pagenum_link 
            );
            $links = sms4wp_pagination( $args );
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_template.php' );
        break;
    }
} 

//-- 가져오기/내보내기 --//
function sms4wp_view_book_file() {
    if ( !current_user_can('manage_options') )
        wp_die( __('You do not have sufficient permissions to access this page.') );

    if ( !isset( $_REQUEST['action'] ) )
        $_REQUEST['action'] = '';
    
    switch ( $_REQUEST['action'] ) {
        case 'download':
            $url = sms4wp_book_file_download( $gr_id );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        case 'upload':
            $url = sms4wp_book_file_upload( $_REQUEST, $_FILIES );
            if ( $url )
                sms4wp_goto_url( $url );
        break;

        default:
            $nonce_down = wp_create_nonce( 'sms4wp_book-file-download' );
            $nonce_up   = wp_create_nonce( 'sms4wp_book-file-upload' );
            $parents    = sms4wp_get_groups_select();
            $book_link  = admin_url('admin-ajax.php');
            @include_once( SMS4WP_INC_VIEW_TEMPLATE_PATH . '/sms4wp_book_file.php' );
        break;
    }
}
