<?php
if ( !defined('ABSPATH') )
	exit;
// sms4wp에서 제공하는 함수

///////////////////////////////////////////////////////////////////////////////////////////
// 이 부분은 건드릴 필요가 없습니다.

/**
 * 휴대폰 번호의 유효성 체크
 * @param  [string] $sender_phone [전화번호]
 * @return [number]               [유효성 체크후 숫자만 출력]
 */
function sms4wp_CheckPhoneNumber( $sender_phone ) {
	//$sender_phone=eregi_replace("[^0-9]","",$sender_phone);
	$sender_phone = preg_replace( "/[^0-9]/i", "", $sender_phone );
	if ( strlen($sender_phone) < 10 || strlen($sender_phone) > 11 )
		return "휴대폰 번호가 틀렸습니다";

	$pnc = substr( $sender_phone, 0, 3 );
	if ( preg_match("/[^0-9]/i", $pnc) || ( $pnc!='010' && $pnc!='011' && $pnc!='016' && $pnc!='017' && $pnc!='018' && $pnc!='019' ) )
		return "휴대폰 앞자리 번호가 잘못되었습니다";
}

/**
 * 메시지의 내용 치환하기
 * @param  [string] $message [보내는 메시지]
 * @param  [array]  $pattern [메시지의 치환 내용]
 * @return [string]          [메시지의 치환된 결과]
 */
function sms4wp_content_replace( $message, $pattern = array() ) {
	if ( is_array($pattern) && ! empty($pattern) ) {
		foreach ( $pattern as $key => $value ) {
			$message = str_replace('{'. $key .'}', $value, $message);
		}
	}
	return $message;
}

class SMS4WP {
	private $backend_url_root;
    private $is_test;
    private $email;
    private $auth_token;
    private $signature_value;

    private $url;
    private $method;
    private $header = array();
    private $Sends  = array();
    private $Result = '';

	private $countgap = 1000; // 몇건씩 보낼지 설정
	private $sleepsec = 5;  // 천분의 몇초간 쉴지 설정

	public function __construct( $email, $auth_token, $signature ) {
		$this->backend_url_root = SMS4WP_SMS_URL;
		// $this->send_date        = date('c');
		$this->url              = $this->backend_url_root;
		$this->method           = 'POST';
		$this->is_test          = 'no';
		$this->email            = $email;
		$this->auth_token       = $auth_token;
		$this->signature_value  = $signature;
		$this->header           = array( sprintf( "Authorization: token %s", $this->auth_token ) );
	}

	public function Init() {
		$this->Sends = array();
		$this->Result = array();
	}

	/**
	 * 문자메시지 추가하기
	 * @param [array] $args [보낼 문자메시지 정보]
	 */
	public function Add( $args = array() ) {
        $default = array(
        	'send_date'    => date( 'c' ),
        	'send_name'    => '',
        	'message_type' => 'SMS',
        	'bulk_file'    => '',
        	're_id'        => '',
        	'file'         => ''
        );

        $add_file1 = '';
        $add_file2 = '';

		if(function_exists('curl_file_create')) {
			if(isset($args['file']['name']) && $args['file']['name'] ) {
				$add_file1 = curl_file_create($args['file']['tmp_name']);
			}
		} else {
			if ( isset($args['file']['name']) && $args['file']['name'] ) {
				$original_name = dirname( $args['file']['tmp_name'] ) . '/' . $args['file']['name'];
				@rename( $args['file']['tmp_name'], $original_name );
				$add_file1 = '@' . $original_name; // MMS 첨부이미지
			}
			else if ( isset($args['file']) && $args['file'] ){
				$add_file1 = '@' . $args['file']; // MMS 첨부이미지
			}
		}

        $args = array_merge( $default, $args );
        // $args = array_map( 'sms4wp_trim', $args );
        extract( $args );

		$receiver_phone = preg_replace( "/[^0-9]/i", "", $receiver_phone );
		$sender_phone   = preg_replace( "/[^0-9]/i", "", $sender_phone );
		$send_timestamp = isset($send_timestamp) ? $send_timestamp : '';

		// 받는 번호 검사 1
		$Error = sms4wp_CheckPhoneNumber( $receiver_phone );
		if ( $Error )
			return $Error;

		// 보내는 번호 검사 2
		if ( preg_match( "/[^0-9]/i", $sender_phone ) )
			return "회신 전화번호가 잘못되었습니다";

        $message_body = sms4wp_content_replace( $message_body, $pattern ); // 내용 치환

        switch ( $message_type ) {
        	case 'SMS':
        		$this->backend_url_root = SMS4WP_SMS_URL;
				$this->url              = $this->backend_url_root;
				if ( $this->strlen_utf8($message_body) > 80 ) {
					$this->backend_url_root = SMS4WP_LMS_URL;
					$this->url              = $this->backend_url_root;
					$message_type           = 'LMS';
				}
        		break;
        	case 'LMS':
        		$this->backend_url_root = SMS4WP_LMS_URL;
				$this->url              = $this->backend_url_root;
        		break;
        	case 'MMS':
        		$this->backend_url_root = SMS4WP_MMS_URL;
				$this->url              = $this->backend_url_root;
        		break;
        	default:
        		$message_type           = 'SMS';
        		$this->backend_url_root = SMS4WP_SMS_URL;
				$this->url              = $this->backend_url_root;
				if ( $this->strlen_utf8($message_body) > 80 ) {
					$this->backend_url_root = SMS4WP_LMS_URL;
					$this->url              = $this->backend_url_root;
					$message_type           = 'LMS';
				}
        		break;
        }

		$data = array(
	        'signature_value' => $this->signature_value,
	        'sender_phone'    => $sender_phone,
	        'sender_name'     => $send_name,
	        'body'            => $message_body,
	        'subject'         => $message_subject,
	        'message_type'    => $message_type,
	        'receiver_phone'  => $receiver_phone,
	        'receiver_name'   => $receiver_name,
	        'send_date'       => $send_date,
	        'send_timestamp'  => $send_timestamp, /* 예약일 */
	        /*'is_test'         => $is_test,*/
	        'file'            => $add_file1,
	        'bulk_file'       => $add_file2,
	        're_id'           => $re_id,
	    );

		array_push( $this->Sends, $data );
	}

	/**
	 * 문자메시지 보내기
	 */
	public function Send () {
		$ch = curl_init();

		if ( false !== $ch ) {
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		    curl_setopt( $ch, CURLOPT_COOKIESESSION, TRUE );
		    curl_setopt( $ch, CURLOPT_FORBID_REUSE, TRUE );
		    curl_setopt( $ch, CURLOPT_FRESH_CONNECT, TRUE );
		    //curl_setopt( $ch, CURLOPT_TIMEOUT, SMS4WP_CURLOPT_TIMEOUT ); // 프로세스 종료 추가
		    // curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

		    curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->header );

		    switch ( $this->method ) {
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
		            return;
		        break;
		    }

		    // die( json_encode( $this->Sends[1]['subject'] . ' - test') );

		    curl_setopt( $ch, CURLOPT_URL, $this->url );

		    foreach ( $this->Sends as $key => $data ) {
			    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			    $result    = curl_exec( $ch );
			    $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			    $this->Save_sms( $data, $result, $http_code );
			    array_push( $this->Result, $result );
			    error_log( 'result: ' . print_r($result, true));
			    error_log( 'errno: ' . curl_errno($ch));
			    error_log( 'error: ' . curl_error($ch));
		    }

		}

		curl_close( $ch );

		return $http_code;
	}

	/**
	 * 문자메시지 발송후 결과 저장하기
	 * @param [array] $data    [보낼 문자메시지 정보]
	 * @param [int] $result    [문자메시지 전송결과]
	 * @param [int] $http_code [문자전송 curl_init()가 반환한 cURL 핸들]
	 */
	public function Save_sms( $data, $result, $http_code ) {
		global $wpdb;

		$default = array(
        	'se_sms_file1'    => '',
        	'se_sms_file2'    => ''
        );

        $data = array_map(
        	function( $value ) {
        	    return is_object( $value ) ? sanitize_text_field( $value ) : $value;
            },
            array_merge( $default, $data )
		);
        $result = sanitize_text_field( $result );
        $http_code = sanitize_text_field( $http_code );

		extract( $data );

		$se_reservation_use = 0;
		if ( $send_timestamp )
			$se_reservation_use = 1;

		$fields = " re_id               = '{$re_id}',
					se_type             = '{$message_type}',
					se_send_number      = '{$sender_phone}',
					se_receiver_number  = '{$receiver_phone}',
					se_subject          = '{$subject}',
					se_message          = '{$body}',
					se_sms_file1        = '{$se_sms_file1}',
					se_sms_file2        = '{$se_sms_file2}',
					se_reservation_date = '{$send_timestamp}',
					se_reservation_use  = '{$se_reservation_use}',
					se_result           = '{$result}',
					se_result_code      = '{$http_code}',
					se_date             = '{$send_date}'
		";
		$sql = /** @lang text */
			"INSERT INTO " . SMS4WP_SEND_LIST_TABLE . " SET $fields";
		// $sql = esc_sql( $sql );
		$result = $wpdb->query( $sql );
	}

	/**
	 * 보내는 문자번호 등록하기
	 * @param  [string] $sendnumber [발송인 전화번호]
	 * @param  [string] $comment    [설명]
	 * @return [int]                [등록 전송 결과]
	 */
    public function sendnumber( $args ) {
        //$params    = http_build_query( $args );
	    $data = '';

        $ch = curl_init();
        if ( false !== $ch ) {
	        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	        curl_setopt( $ch, CURLOPT_COOKIESESSION, TRUE );
	        curl_setopt( $ch, CURLOPT_FORBID_REUSE, TRUE );
	        curl_setopt( $ch, CURLOPT_FRESH_CONNECT, TRUE );
	        curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->header );
	        curl_setopt( $ch, CURLOPT_POST, TRUE );
	        curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
	        curl_setopt( $ch, CURLOPT_URL, SMS4WP_SEND_NUMBER_URL );
	        //curl_setopt( $ch, CURLOPT_TIMEOUT, SMS4WP_CURLOPT_TIMEOUT ); // 프로세스 종료 추가

            $data = curl_exec( $ch );
            //$http_code = curl_getinfo( $ch );
        }
        curl_close( $ch );
        
        return $data;
    }

    /**
     * 문자길이 체크하기
     * @param  [string]  $str     [문자내용]
     * @param  [boolean] $checkmb [한글체크]
     * @return [int]              [문자길이]
     */
    public function strlen_utf8( $str, $checkmb = true ) {
	    preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match); // target for BMP
	 
	    $m = $match[0];
	    $mlen = count($m); // length of matched characters
	 
	    if ( ! $checkmb ) 
	    	return $mlen;
	 
	    $count = 0;
	    for ( $i = 0; $i < $mlen; $i++ ) {
	        $count += ( $checkmb && strlen($m[$i]) > 1 ) ? 2 : 1;
	    }
	 
	    return $count;
	}
}
