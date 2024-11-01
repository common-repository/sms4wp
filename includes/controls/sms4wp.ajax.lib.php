<?php
if (!defined('ABSPATH')) exit;

require_once( SMS4WP_INC_CONTROL_PATH . '/JSON.php' );

/**
 * sms4wp ajax
 */

//-- json encode --//
if ( ! function_exists('sms4wp_json_encode') ) {
    function sms4wp_json_encode( $data )
    {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }
}

//-- go to url --//
if ( ! function_exists('sms4wp_goto_url') ) {
    function sms4wp_goto_url( $url )
    {
        echo '<script type="text/javascript">
            window.location = "'.$url.'";
        </script>';
    }
}

//-- 발신번호 핀타입 --//
if ( ! function_exists('sms4wpCheckPinType') ) {
	function sms4wpCheckPinType( $sendNumber )
	{
		$sendNumber = sms4wpOnlyNumber( $sendNumber );
		$hpList = array( '010', '011', '016', '017', '018', '019' );
		$pnc = substr( $sendNumber, 0, 3 );

		if ( in_array($pnc, $hpList) ) {
			return 'SMS';
		}

		return 'VMS';
	}
}

//-- 발신번호 확인 --//
if ( ! function_exists('sms4wpCheckPhone') ) {
	function sms4wpCheckPhone( $sendNumber )
	{
		$sendNumber = sms4wpOnlyNumber( $sendNumber );
		if ( strlen($sendNumber) < 8 ) {
			return '발신번호를 정확하게 입력하세요';
		}

		return '';
	}
}

//-- 숫자만 출력 --//
if ( ! function_exists('sms4wpOnlyNumber') ) {
	function sms4wpOnlyNumber( $number )
	{
		return preg_replace( "/[^0-9]/i", "", $number );
	}
}

//-- 발신번호 인증 보내기   --//
if ( ! function_exists('sms4wpSendNumberRest') ) {
	function sms4wpSendNumberRest( $args )
	{
		$sms4wp_config = sms4wp_get_configure();
		$SMS = new SMS4WP( $sms4wp_config['sms4wp_auth_email'], $sms4wp_config['sms4wp_auth_token'], $sms4wp_config['sms4wp_auth_signature'] );
		$SMS->Init();
		$res = $SMS->sendnumber( $args ); //{"result_code":"200","sendnumber":"0232894122"}
		$obj = json_decode($res);

		$result_code = $res;
		if ( isset($obj->result_code) ) {
			$result_code = $obj->result_code;
		}

		return $result_code;
	}
}


//-- 발신번호 인증번호 요청  --//
if ( ! function_exists('sms4wp_pincode_request') ) {
	function sms4wp_pincode_request()
	{
		$sendNumber = isset($_POST['sendnumber']) ? $_POST['sendnumber'] : '';
		$comment    = isset($_POST['comment']) ? $_POST['comment'] : 'null';
		$nonce      = isset($_POST['nonce']) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'pintype-pincode-nonce' ) ) {
			echo 'Security check';
			die();
		}

		$sendNumber = sms4wpOnlyNumber( $sendNumber );

		$pinType     = sms4wpCheckPinType( $sendNumber );
		$checkNumber = sms4wpCheckPhone( $sendNumber );

		if ( ! empty($checkNumber) ) {
			echo $checkNumber;
			die();
		}

		$args = array(
			'sendnumber' => $sendNumber,
			'comment'    => urlencode( $comment ),
			'pintype'    => $pinType,
		);
		$res = sms4wpSendNumberRest( $args );

		echo $res;
		die();
	}
	add_action( 'wp_ajax_sms4wp_pincode_request', 'sms4wp_pincode_request', 1 );
	add_action( 'wp_ajax_nopriv_sms4wp_pincode_request', 'sms4wp_pincode_request', 1 );
}

//-- 발신번호 인증번호 체크  --//
if ( ! function_exists('sms4wp_sendnumber_register') ) {
	function sms4wp_sendnumber_register()
	{
		$sendNumber = isset($_POST['sendnumber']) ? $_POST['sendnumber'] : '';
		$comment    = isset($_POST['comment']) ? $_POST['comment'] : 'null';
		$pinCode    = isset($_POST['pincode']) ? $_POST['pincode'] : '';
		$nonce      = isset($_POST['nonce']) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'pintype-pincode-nonce' ) ) {
			echo 'Security check'. $nonce;
			die();
		}

		$sendNumber = sms4wpOnlyNumber( $sendNumber );
		$pinType    = sms4wpCheckPinType( $sendNumber );

		$checkNumber = sms4wpCheckPhone( $sendNumber );
		if ( ! empty($checkNumber) ) {
			echo $checkNumber;
			die();
		}

		$args = array(
			'sendnumber' => $sendNumber,
			'comment'    => urlencode( $comment ),
			'pintype'    => $pinType,
			'pincode'    => $pinCode,
		);
		$res = sms4wpSendNumberRest( $args );

		echo $res;
		die();
	}
	add_action( 'wp_ajax_sms4wp_sendnumber_register', 'sms4wp_sendnumber_register', 1 );
	add_action( 'wp_ajax_nopriv_sms4wp_sendnumber_registert', 'sms4wp_sendnumber_register', 1 );
}

//-- 메시지 보내기 수신자 검색목록 --//
if ( ! function_exists('sms4wp_ajax_receivers') ) {
    function sms4wp_ajax_receivers() {
        global $wpdb;

	    $groups    = sms4wp_get_groups_all(); // 그룹정보
        $page_rows = isset($_POST['page_rows']) ? intval( $_POST['page_rows'] ) : '';
	    $paged     = isset($_POST['paged']) ? intval( $_POST['paged'] ) : '';
	    $s         = isset($_POST['s']) ? sanitize_text_field( $_POST['s'] ) : '';
	    $result    = '';

	    $orderby = isset($_POST['orderby']) ? sanitize_text_field( $_POST['orderby'] ) : '';
	    $order   = isset($_POST['order']) ? sanitize_text_field( $_POST['order'] ) : '';

	    $query_where = " WHERE re_use = '1' ";

	    if ( $paged < 1 ) 
	        $paged = 1;
	    if ( !( isset($page_rows) && is_int($page_rows) && $page_rows > 0 ) ) // 한페이지에서 보여지는 아이템 숫자
	        $page_rows = 15;

	    if ( $s ) { // 검색어
	        $query_where .= " AND ( re_name like '%{$s}%' ";
	        $query_where .= " OR re_phone_number like '%{$s}%' ";
	        $query_where .= " OR re_memo like '%{$s}%' ) ";
	    }

	    $query_order = " ORDER BY ID desc ";
	    if ( $orderby && $order ) { 
	        $query_order = " ORDER BY {$orderby} {$order} ";
	    }

	    $sql = "SELECT count(ID) AS cnt FROM ".SMS4WP_RECEIVERS_TABLE." " . $query_where;
	    $total = $wpdb->get_row( $sql ); // 전체 수신자

	    $list['total'] = $total->cnt;

	    $total_page  = ceil($total->cnt / $page_rows);  // 전체 페이지 계산
	    $from_record = ($paged - 1) * $page_rows; // 시작 열을 구함

	    $sql = "SELECT * FROM ".SMS4WP_RECEIVERS_TABLE." " . $query_where . $query_order . " limit " . $from_record . ", " . $page_rows;
	    // $sql = esc_sql( $sql );
	    $rows = $wpdb->get_results( $sql );

	    if ( ! ( is_array($rows) || is_object($rows) ) )
	        return;

	    foreach ( $rows as $row ) { 
	        $group_name = isset($groups[$row->gr_id]['gr_name']) ? $groups[$row->gr_id]['gr_name'] : '';
	        if ( $group_name == '' )
	        	$group_name = 'None';

	        $result .= '<li re_id="'.$row->ID.'" class="add-receiver">'.$group_name.', '.$row->re_name.', '.$row->re_phone_number.'</li>';
	    }

	    //die( json_encode( $sql ) );
	    echo $result;
	    die();
    }
    add_action( 'wp_ajax_sms4wp_ajax_receivers', 'sms4wp_ajax_receivers', 1 );
    add_action( 'wp_ajax_nopriv_sms4wp_ajax_receivers', 'sms4wp_ajax_receivers', 1 );
}

//-- 보내는 메시지 --//
if( !function_exists('sms4wp_ajax_msg_sends') ) {
	function sms4wp_ajax_msg_sends() {
	    global $wpdb;

	    $countgap = 1000; // 몇건씩 보낼지 설정
		$sleepsec = 5;  // 천분의 몇초간 쉴지 설정
		$success  = 0; // 성공숫자
		$failed   = 0; // 실패숫자

		if ( !wp_verify_nonce( $_REQUEST['nonce'], "sms4wp_ajax_message_nonce")) {
			die( json_encode(array('error'=>"No naughty business please")) );
		}  

	    require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.sms.class.php' );
	    require_once( SMS4WP_INC_CONTROL_PATH . '/sms4wp.lib.php' );

	    $sms4wp_config = sms4wp_get_configure();

	    $groups    = array();
	    $receivers = array();

		$data = array_map( 'sms4wp_trim', $_REQUEST );
	    // $data = array_map( 'sms4wp_htmlspecialchars', $data );
	    $list = array(); // 수신번호, 수신자, 그룹 수신자

	    $send_timestamp = '';
	    if ( $data['reservation_date'] != '' && $data['reservation_time'] != '' ) {
		    // UTC 기준 WP 설정시간 추가
		    $offset = get_option('gmt_offset');
		    $send_timestamp = date( 'c', strtotime($data['reservation_date'] . ' ' . $data['reservation_time']) );
	    }

	    // 받는사람 여러명인 경우 확인
    	$receivers_phone = $data['receiver_phone'];
    	$receivers_phone = explode( ',', $receivers_phone );
	    // 받는사람 입력
	    if ( is_array( $receivers_phone ) ) {
		    foreach ( $receivers_phone as $number ) { // 수신번호
		    	$number = preg_replace( "/[^0-9]/i", "", $number );
		    	$list[$number] = array( 're_id'=>'', 'gr_id'=>'' );
		    }
	    }
	    else {
	    	$number = preg_replace( "/[^0-9]/i", "", $receivers_phone );
	    	$list[$number] = array( 're_id'=>'', 'gr_id'=>'' );
		}

		// 수신자
		$receivers = explode( ',', $data['receivers'] );
		if ( is_array( $receivers ) && $data['receivers'] ) {
		    foreach ( $receivers as $re_id ) { // 수신자
		    	$re = sms4wp_get_receiver( intval( trim($re_id) ) );

		    	$number = preg_replace( "/[^0-9]/i", "", $re['re_phone_number'] );

		    	if ( ! isset( $list[$number] ) ) // 중복 핸드폰이 없는 경우
			    	$list[$number] = array( 're_id'=>$re['ID'], 'gr_id'=>'' );
		    }
		}

		// 그룹
		$groups = explode( ',', $data['groups'] );
		if ( is_array( $groups ) && $data['groups'] ) {
		    foreach ( $groups as $gr_id ) { // 그룹소속 수신자
		    	$gr_receiver = sms4wp_get_group_receivers( intval( trim($gr_id) ) );

		    	for ( $c = 0; $c < count($gr_receiver); $c++ ) {
			    	$number = preg_replace( "/[^0-9]/i", "", $gr_receiver[$c]['re_phone_number'] );

		    		if ( ! isset( $list[$number] ) ) // 중복 핸드폰이 없는 경우
				    	$list[$number] = array( 're_id'=>$gr_receiver[$c]['ID'], 'gr_id'=>$gr_receiver[$c]['gr_id'] );
		    	}
		    }
		}

	    $SMS = new SMS4WP( $sms4wp_config['sms4wp_auth_email'], $sms4wp_config['sms4wp_auth_token'], $sms4wp_config['sms4wp_auth_signature'] );

	    $c = 0;
	    foreach ( $list as $number => $receiver ) {
	    	if ( $countgap == $c ) {
	    		$c = 0;
	    	}

	    	if ( !$number )
	    		continue;

		    $SMS->Init();

		    $pattern         = array(); // 메시지 치환정보 array('patterns'=>'replacements');
		    $message_body    = isset($data['message_body']) ? $data['message_body'] : '';
		    $message_subject = isset($data['message_subject']) ? $data['message_subject'] : '';;

		    $args = array(
		        'sender_phone'    => isset($data['sender_phone']) ? $data['sender_phone'] : '', 
		        'sender_name'     => isset($data['sender_name']) ? $data['sender_name'] : '', 
		        'message_type'    => isset($data['message_type']) ? $data['message_type'] : '', 
		        'message_subject' => isset($data['message_subject']) ? $data['message_subject'] : '', 
		        'message_body'    => $message_body, 
		        'message_subject' => $message_subject, 
		        'pattern'    	  => $pattern, 
		        'receiver_phone'  => $number, 
		        'send_timestamp'  => $send_timestamp, 
		        'receiver_name'   => isset($data['receiver_name']) ? $data['receiver_name'] : '', 
		        're_id'   		  => isset($receiver['re_id']) ? $receiver['re_id'] : '', 
		        'gr_id'   		  => isset($receiver['gr_id']) ? $receiver['gr_id'] : '', 
		        'file'            => isset($_FILES['add_file1']) ? $_FILES['add_file1'] : '', 
		        'bulk_file'       => isset($_FILES['add_file2']) ? $_FILES['add_file2'] : '', 
		    );
		    $SMS->Add( $args );

		
		    $res_code = $SMS->Send();
		    $c++;

		    if ( $res_code == '200' || $res_code == '100' || $res_code == '0' )
		    	$success++;
		    else
		    	$failed++;

	        usleep( $sleepsec );
	    }
	    $SMS->Init();

	    @unlink( $_FILES['add_file1']['tmp_name'] );
	    @unlink( $_FILES['add_file2']['tmp_name'] );

	    // die( json_encode("success") );
	    die( json_encode(array('success'=>$success, 'failed'=>$failed)) );
	}
	add_action( 'wp_ajax_sms4wp_ajax_msg_sends', 'sms4wp_ajax_msg_sends', 1 );
    add_action( 'wp_ajax_nopriv_sms4wp_ajax_msg_sends', 'sms4wp_ajax_msg_sends', 1 );
}


//-- 템플릿 목록 --//
if( !function_exists('sms4wp_ajax_templates') ) {
    function sms4wp_ajax_templates() {
        global $wpdb;

        $page_rows = intval( $_POST['page_rows'] );
	    $paged     = intval( $_POST['paged'] );
	    $result    = '';

	    $query_where = " WHERE te_group = '' ";

	    if ( $paged < 1 ) 
	        $paged = 1;
	    if ( !( isset($page_rows) && is_int($page_rows) && $page_rows > 0 ) ) // 한페이지에서 보여지는 아이템 숫자
	        $page_rows = 15;

	    $query_order = " ORDER BY ID desc ";
	    if ( isset($orderby) && isset($order) ) { 
	    	$orderby = sanitize_post_field( $orderby );
	    	$order = sanitize_post_field( $order );
	        $query_order = " ORDER BY {$orderby} {$order} ";
	    }

	    $sql = "SELECT count(ID) AS cnt FROM ".SMS4WP_TEMPLATE_TABLE." " . $query_where;
	    // $sql = esc_sql( $sql );
	    $total = $wpdb->get_row( $sql ); // 전체 템플릿

	    $list['total'] = $total->cnt;

	    $total_page  = ceil($total->cnt / $page_rows);  // 전체 페이지 계산
	    $from_record = ($paged - 1) * $page_rows; // 시작 열을 구함

	    $sql = "SELECT * FROM ".SMS4WP_TEMPLATE_TABLE." " . $query_where . $query_order . " limit " . $from_record . ", " . $page_rows ;
	    // $sql = esc_sql( $sql );
	    $rows = $wpdb->get_results( $sql );
	    // print_r( $rows );
	    if ( !( is_array($rows) || is_object($rows) ) )
	        return;

	    foreach ( $rows as $row ) { 
	        $result .= '
				<li class="add-template">
					<span>' . $row->te_subject . '</span>
					<span class="message">' . $row->te_message . '</span>
				</li>
	        ';
	    }

	    die( $result );
    }
    add_action( 'wp_ajax_sms4wp_ajax_templates', 'sms4wp_ajax_templates', 1 );
    add_action( 'wp_ajax_nopriv_sms4wp_ajax_templates', 'sms4wp_ajax_templates', 1 );
}



//-- 템플릿 저장하기 --//
if( !function_exists('sms4wp_ajax_save_template') ) {
    function sms4wp_ajax_save_template() {
        global $wpdb;

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "sms4wp_ajax_message_nonce")) {
			die( json_encode("No naughty business please") );
		}  

        $message = htmlspecialchars( trim( $_POST['message'] ) );
        $subject = htmlspecialchars( trim( $_POST['subject'] ) );

        if ( $subject == '' )
        	$subject = 'none';

        $query = /** @lang text */
	        "INSERT INTO " . SMS4WP_TEMPLATE_TABLE . " SET te_subject = %s, te_message = %s, te_date = now()";
	    $wpdb->get_results( $wpdb->prepare($query, $subject, $message) );
	    die( '100' );
    }
    add_action( 'wp_ajax_sms4wp_ajax_save_template', 'sms4wp_ajax_save_template', 1 );
    add_action( 'wp_ajax_nopriv_sms4wp_ajax_save_template', 'sms4wp_ajax_save_template', 1 );
}


//-- api key 인증 확인 --//
function sms4wp_ajax_api_certification() {
	global $wpdb;
}


//-- 핸드폰 인증 확인 --//
function sms4wp_ajax_cellphone_certification() {
	global $wpdb;
}


//-- 템플릿 가져오기 --//
function sms4wp_ajax_import_template() {
	global $wpdb;
}


//-- 받는사람 추가 (수신자, 그룹) --//
function sms4wp_ajax_import_addressee_group() {
	global $wpdb;
}


//-- 수신자 파일 다운로드 --//
if( !function_exists('sms4wp_ajax_book_file_download') ) {
	function sms4wp_ajax_book_file_download() {
		global $wpdb;

		if ( !wp_verify_nonce( $_REQUEST['nonce'], "sms4wp_book-file-download")) {
			die( json_encode("No naughty business please") );
		} 

		$data = array_map( 'sms4wp_trim', $_REQUEST );
	    $data = array_map( 'sms4wp_htmlspecialchars', $data );
	    extract( $data );

		if ( isset($gr_parent) && $gr_parent != 'all' && $gr_parent < 1 )
		    die( '801' ); // 다운로드 할 휴대폰번호 그룹을 선택해주세요.

		$sql_group = ""; 
		if ( isset($gr_parent) && $gr_parent != 'all' ) {
			$gr_parent = (int)$gr_parent;
			$sql_group = " and gr_id = '{$gr_parent}' ";
		}

		$sql_hp = " and re_phone_number <> '' ";

		$sql = " SELECT COUNT(*) AS cnt FROM " . SMS4WP_RECEIVERS_TABLE . " WHERE (1) {$sql_group} {$sql_hp} ORDER BY re_name ";
		// $sql = esc_sql( $sql );
		$total = $wpdb->get_row( $sql );

		if ( !$total->cnt ) 
			die( '802' ); // 데이터가 없습니다.

		$sql = " SELECT * FROM " . SMS4WP_RECEIVERS_TABLE . " WHERE (1) {$sql_group} {$sql_hp} ORDER BY re_name ";
		// $sql = esc_sql( $sql );
		$rows = $wpdb->get_results( $sql );

		/*================================================================================
		php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
		=================================================================================*/

		include_once( SMS4WP_INC_MODEL_PATH . '/Excel/php_writeexcel/class.writeexcel_workbook.inc.php' );
		include_once( SMS4WP_INC_MODEL_PATH . '/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php' );

		$upload_dir = wp_upload_dir();
		$fname      = tempnam( $upload_dir['basedir'], "/receivers.xls" );

		$workbook  = new writeexcel_workbook( $fname );
		$worksheet = $workbook->addworksheet();

		$num2_format =& $workbook->addformat( array(num_format => '\0#') );

		// Put Excel data
		$data = array('이름', '전화번호');
		$data = array_map('sms4wp_iconv_euckr', $data);

		$col = 0;
		foreach( $data as $cell ) {
		    $worksheet->write( 0, $col++, $cell );
		}

		$c = 0;
		foreach ( $rows as $key=>$row ) {
			$c++;

		    $cellPhone = sms4wp_get_hp( sms4wp_iconv_euckr( $row->re_phone_number ) );
		    $re_name   = sms4wp_iconv_euckr( $row->re_name );
		    if ( !$cellPhone ) 
		    	continue;

		    $worksheet->write( $c, 0, $re_name );
		    $worksheet->write( $c, 1, $cellPhone, $num2_format );
		}


		$workbook->close();

		$filename = "수신자번호목록-" . date( "ymd", time() ) . ".xls";
		if( sms4wp_is_ie() ) 
			$filename = sms4wp_utf2euc( $filename );


		header( "Content-Type: application/x-msexcel; name=" . $filename );
		header( "Content-Disposition: inline; filename=" . $filename );

		flush();
		$fp = @fopen( $fname, "rb" );
		
		if ( !fpassthru( $fp ) ) {
		   fclose( $fp );
		}
		flush();
		@unlink( $fname );

		die( json_encode('success') );
		exit;
	}
	add_action( 'wp_ajax_sms4wp_ajax_book_file_download', 'sms4wp_ajax_book_file_download', 1 );
	add_action( 'wp_ajax_nopriv_sms4wp_ajax_book_file_download', 'sms4wp_ajax_book_file_download', 1 );
}


//-- 수신자 파일 업로드 --//
if( !function_exists('sms4wp_ajax_book_file_upload') ) {
	function sms4wp_ajax_book_file_upload() {
		global $wpdb;

	    if ( !wp_verify_nonce( $_REQUEST['nonce'], "sms4wp_book-file-upload")) {
			die( json_encode("No naughty business please") );
		} 

		$data = array_map( 'sms4wp_trim', $_REQUEST );
	    $data = array_map( 'sms4wp_htmlspecialchars', $data );
	    extract( $data );

		if ( !$_FILES['book_file']['size'] ) 
		    die( '801' ); // 파일을 선택해주세요.

		$file     = $_FILES['book_file']['tmp_name'];
		$filename = $_FILES['book_file']['name'];

		$info = pathinfo( $filename );
		$ext  = $info['extension'];

		$gr_parent = isset($gr_parent) ? $gr_parent : '';
		$gr_id     = isset($gr_id) ? $gr_id : '';

		switch ( $ext ) {
		    case 'csv' :
		        $ext_file = file( $file );
		        $num_rows = count( $ext_file ) + 1;
		        $csv      = array();

		        foreach ( $ext_file as $item ) {
		            $item = explode( ',', $item );
		            array_push( $csv, $item );

		            if ( count($item) < 2 ) 
		                die( '802' ); // 올바른 파일이 아닙니다.
		        }
		        break;
		    default :
		        die( '803' ); // xls파일과 csv파일만 허용합니다.
		}

		$success = 0;
		$arr_hp  = array();

		for ( $c = 0; $c <= $num_rows; $c++ ) {
		    switch ($ext) {
		        case 'csv' :
		            $name = $csv[$c][0];
		            if ( mb_detect_encoding( $name, 'UTF-8', true ) === false ) { 
					    $name = utf8_encode( $name ); 
				    }

		            $name      = str_replace( array('"', '\''), '', trim($name) );
		            $name      = addslashes( trim($name) );
		            $cellPhone = sms4wp_get_hp( $csv[$c][1], 0 );
	            break;
		    }

	        if ( !$cellPhone ) // 전화번호 없는 경우
	        	continue;

		    if ( strlen($name) && $cellPhone ) {
		        if ( !in_array( $cellPhone, $arr_hp ) ) { // 중복번호 없는 경우
		            array_push( $arr_hp, $cellPhone );

		            // 수신자 중복번호 확인
		            $qry = /** @lang text */
			            " SELECT COUNT(*) AS cnt FROM " . SMS4WP_RECEIVERS_TABLE . " WHERE re_phone_number = %s ";
		            $res = $wpdb->get_row( $wpdb->prepare($qry, $cellPhone) );
		            if ( !$res->cnt && $cellPhone ) {
		            	$qry = /** @lang text */
		            	    " INSERT INTO " . SMS4WP_RECEIVERS_TABLE . " 
		            					SET gr_id           = %d, 
		            						re_name         = %s, 
		            						re_phone_number = %s, 
		            						re_update       = now() ";
		                $wpdb->query( $wpdb->prepare($qry, $gr_parent, $name, $cellPhone) );
		                $success++;
		            }
		        }
		    }
		}

		unlink( $_FILES['book_file']['tmp_name'] );

		if ( $success ) {
		    $qry = /** @lang text */
			    " SELECT COUNT(*) AS cnt FROM " . SMS4WP_RECEIVERS_TABLE . " WHERE gr_id = %d ";
		    $total = $wpdb->get_row( $wpdb->prepare($qry, $gr_id) );

		    $qry = " UPDATE " . SMS4WP_GROUP_TABLE . " SET bg_count = %d WHERE gr_id = %d ";
		    $wpdb->query( $wpdb->prepare($qry, $total->cnt, $gr_id) );
		}

		die( json_encode('success') );
	}
	add_action( 'wp_ajax_sms4wp_ajax_book_file_upload', 'sms4wp_ajax_book_file_upload', 1 );
	add_action( 'wp_ajax_nopriv_sms4wp_ajax_book_file_upload', 'sms4wp_ajax_book_file_upload', 1 );
}
