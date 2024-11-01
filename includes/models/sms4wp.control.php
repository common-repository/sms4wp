<?php
if (!defined('ABSPATH')) exit;

/*************************************************************************
**
**  sms4wp 회원의 문자정보 함수 모음
**
*************************************************************************/

function sms4wp_get_authentication_header(& $auth_token) {
    return sprintf("Authorization: token %s", $auth_token);
}

function sms4wp_prepare_data() {
    $data = array(
        'today_sent'    => 0,
        'point'         => 0,
        'sms_cost'      => 0,
        'lms_cost'      => 0,
        'mms_cost'      => 0,
        'sms_error_msg' => 0,
    );

    // current user point
    $point = sms4wp_admin_user_point_get();
    if ( !is_array( $point ) ) {
        $data['sms_error_msg'] = '';
        return $data;
    }

    if ( isset($point['value']) )
        $data['point'] = $point['value'];
    else 
        $data['point'] = 0;

    if ( isset( $point['detail'] ) ) {
        $data['sms_error_msg'] = $point['detail'];
        return $data;
    }

    // current user message count
    $counter = sms4wp_admin_message_counter_get();
    if ( is_array( $counter ) ) {
        $today_flag = $counter['today_flag'];

        if ( gmdate("Y-m-d") == $today_flag ) {
            $data['today_sent'] =
                $counter['today_sms_sent'] +
                $counter['today_lms_sent'] +
                $counter['today_mms_sent'];
        }
    }

    // point cost query
    $costs = sms4wp_point_cost();
    if ( is_array( $costs ) || is_object( $costs ) ) {
	    foreach( $costs['data'] as $cost ) {
            if ( is_array($cost) ) {
    	        switch( $cost['type'] ) {
    	            case 'SMS':
    	                $data['sms_cost'] = $cost['point_cost'];
    	                break;
    	            case 'LMS':
    	                $data['lms_cost'] = $cost['point_cost'];
    	                break;
    	            case 'MMS':
    	                $data['mms_cost'] = $cost['point_cost'];
    	                break;
    	        }
            }
            else {
                $data['sms_error_msg'] = $cost;
            }
	    }
	}

    return $data;
}

function sms4wp_admin_user_point_get() {
	global $sms4wp_config;

	$auth_token_header = sms4wp_get_authentication_header( $sms4wp_config['sms4wp_auth_token'] );
    $header = array($auth_token_header);
    $data = array();

    $result = sms4wp_wrapper_call(
        sms4wp_admin_user_point(),
        'GET',
        $header,
        $data
    );

    if ( is_array( $result ) ) 
        return $result['data'];
}

function sms4wp_admin_message_counter_get() {
	global $sms4wp_config;

	$auth_token_header = sms4wp_get_authentication_header( $sms4wp_config['sms4wp_auth_token'] );
    $header = array($auth_token_header);
    $data = array();

    $result = sms4wp_wrapper_call(
        sms4wp_admin_message_counter(),
        'GET',
        $header,
        $data
    );

    if ( is_array( $result ) ) 
        return $result['data'];
}

function sms4wp_point_cost() {
	global $sms4wp_config;

	$auth_token_header = sms4wp_get_authentication_header( $sms4wp_config['sms4wp_auth_token'] );
    $header = array($auth_token_header);

    $result = sms4wp_wrapper_call(
        sms4wp_admin_point_cost(),
        'GET',
        $header
    );

    return $result;
}

function sms4wp_wrapper_call($url, $method, & $header = array(), & $data = array()) {

    $result =
        sms4wp_access(
            $url,
            $method,
            $header,
            $data
        );

    return sms4wp_handle_result($result);

}

function sms4wp_access( $url, $method, & $header = array(), & $data = array() ) {
	$ch = curl_init();

	if ( false !== $ch ) {
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_COOKIESESSION, TRUE );
		curl_setopt( $ch, CURLOPT_FORBID_REUSE, TRUE );
		curl_setopt( $ch, CURLOPT_FRESH_CONNECT, TRUE );
        //curl_setopt( $ch, CURLOPT_TIMEOUT, SMS4WP_CURLOPT_TIMEOUT ); // 프로세스 종료 추가

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		switch ( $method ) {
			case 'GET':
				curl_setopt( $ch, CURLOPT_HTTPGET, TRUE );
				break;

			case 'PUT':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );
				break;

			case 'DELETE':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
				break;

			case 'POST':
				curl_setopt( $ch, CURLOPT_POST, TRUE );
				break;

			case 'HEAD':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "HEAD" );
				break;

			case 'OPTIONS':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "OPTIONS" );
				break;

			default:
				return( "undefined method" );
				break;
		}

		if ( $method == 'GET' ) {
			$url .= '?' . http_build_query( $data );
			curl_setopt( $ch, CURLOPT_URL,  $url );
		} 
		else {
			curl_setopt( $ch, CURLOPT_URL,  $url );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		}

		$result = curl_exec( $ch );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	}

    return array( 'result'=>$result, 'http_code'=>$http_code );

}

function sms4wp_handle_result( $data ) {
	$response  = $data['result'];
	$http_code = $data['http_code'];

    // Decode only as associative array ( 2nd param = TRUE )
    $response_object = json_decode($response, TRUE);

    if ( !is_array($response_object) ) {
        return( "response is not decoded properly. response as text: '" . $response . "'" );
    }

    return array(
        'code' => $http_code,
        'data' => $response_object,
    );
}

function sms4wp_admin_point_cost() {
    return SMS4WP_BACKEND_URL . '/point_cost/';
}

function sms4wp_admin_user_point() {
    return SMS4WP_BACKEND_URL . '/user_point/';
}

function sms4wp_admin_message_counter() {
    return SMS4WP_BACKEND_URL . '/message_counter/';
}

function sms4wp_admin_user() {
	return SMS4WP_BACKEND_URL . '/user/';
}
