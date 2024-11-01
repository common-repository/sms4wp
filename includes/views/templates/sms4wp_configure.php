<?php
$tab = 'general';
if ( isset( $_REQUEST['tab'] ) )
	$tab = esc_html( $_REQUEST['tab'] );

$section = isset($_REQUEST['section']) ? esc_html( $_REQUEST['section'] ) : '';
$nonce = wp_create_nonce('pintype-pincode-nonce');

global $sms4wpAddOn;
?>

<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css?ver=1111" />

<div class="wrap">
<h2><?php esc_html_e( 'SMS4WP Config'); ?></h2>

<div class="local_desc01 local_desc">
    <p>
        <?php esc_html_e( 'SMS 기능을 사용하시려면 먼저 SMS4WP에 서비스 신청을 하셔야 합니다.'); ?><br>
        <a href="https://sms4wp.com/register/" target="_blank" class="button"><?php esc_html_e( 'SMS4WP 서비스 신청하기'); ?></a>
        <a href="https://sms4wp.com/price/" class="button" target="_blank"><?php _e('충전하기') ?></a>
    </p>
</div>

<form id="frmConfig" method="post" enctype="multipart/form-data">
<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-configure&amp;tab=<?php echo $tab; ?>">

<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
	<a href="admin.php?page=sms4wp-configure&amp;tab=general" class="nav-tab <?php echo ($tab == 'general' ? 'nav-tab-active' : ''); ?>">기본설정</a>
	<a href="admin.php?page=sms4wp-configure&amp;tab=enabled" class="nav-tab <?php echo ($tab == 'enabled' ? 'nav-tab-active' : ''); ?>">고급설정</a>
	<?php
	foreach ( $sms4wp_config['sms4wp_plugin'] as $key => $filename ) {
		// $plugin_file = SMS4WP_PLUG_PATH . '/' . $filename . '/' . $filename . '.php';
		// $plugin_data = get_plugin_data( $plugin_file );
	?>
	<a href="admin.php?page=sms4wp-configure&amp;tab=<?php echo $filename; ?>" class="nav-tab <?php echo ($tab == $filename ? 'nav-tab-active' : ''); ?>"><?php echo $filename; ?></a>
	<?php } ?>
</h2>

<?php if ( $tab == 'general' ) { // 기본설정 ?>
<input type="hidden" name="action" value="update" />
<table class="form-table">
<!-- <tr>
<th scope="row"><label for="sms4wp_mb_id"><?php //_e('SMS4WP member ID') ?></label></th>
<td><input name="sms4wp_mb_id" type="text" id="sms4wp_mb_id" value="<?php //echo $sms4wp_config['sms4wp_mb_id']; ?>" class="regular-text" /></td>
</tr> -->

<tr>
<th scope="row"><label for="sms4wp_auth_token"><?php _e('인증 토큰') ?></label></th>
<td>
	<input name="sms4wp_auth_token" type="password" id="sms4wp_auth_token" value="<?php echo esc_attr($sms4wp_config['sms4wp_auth_token']); ?>" class="regular-text" />
	<span class="sms4wp_auth_information"><a href="http://sms4wp.com/tools/authorization/" target="_blank"><?php _e('인증토큰은 어디에 있나요?'); ?></a></span>
</td>
</tr>

<!-- <tr>
<th scope="row"><label for="sms4wp_auth_email"><?php //_e('Authentication E-Amil ') ?></label></th>
<td><input name="sms4wp_auth_email" type="text" id="sms4wp_auth_email" value="<?php //echo $sms4wp_config['sms4wp_auth_email']; ?>" class="regular-text" /></td>
</tr> -->

<tr>
<th scope="row"><label for="sms4wp_auth_signature"><?php _e('인증 시그니쳐') ?></label></th>
<td>
	<input name="sms4wp_auth_signature" type="text" id="sms4wp_auth_signature" value="<?php echo esc_attr($sms4wp_config['sms4wp_auth_signature']); ?>" class="regular-text" />
	<span class="sms4wp_auth_information"><a href="http://sms4wp.com/tools/authorization/" target="_blank"><?php _e('인증 시그니쳐는 어디에 있나요?'); ?></a></span>
</td>
</tr>

<tr>
<th scope="row"><label for="sms4wp_charge"><?php _e('문자 잔여건수') ?></label></th>
<td>
	<input name="message_type" type="radio" class="message_type" value="SMS" checked /><span class="msg_type_sms sms_selected">SMS</span> &nbsp; 
	<input name="message_type" type="radio" class="message_type" value="LMS" /><span class="msg_type_lms">LMS</span> &nbsp; 
	<input name="message_type" type="radio" class="message_type" value="MMS" /><span class="msg_type_mms">MMS</span>
	<div style="display:block"><span class="message_count" style="display:inline-block"><?php echo esc_html($sms_charge); ?></span> <input type="checkbox" class="check-charge-notice" name="sms4wp_charge_notice_checker" value="yes" <?php if($sms4wp_config['sms4wp_charge_notice_checker'] == 'yes') : echo 'checked'; endif; ?>> 잔액 알림</div>
</td>
</tr>
<?php
	
?>
<tr class="sms-count-notice-tr <?php if($sms4wp_config['sms4wp_charge_notice_checker'] == 'yes') : echo 'active'; endif; ?>">
	<th scope="row"></th>
	<td>
		잔액이 <input type="number" name="sms4wp_charge_notice_count" style="width:80px" value="<?php echo esc_attr($sms4wp_config['sms4wp_charge_notice_count']);?>">건 이하로 떨어지면,<br>
		<input type="text" name="sms4wp_charge_notice_to" style="width:140px" value="<?php echo esc_attr($sms4wp_config['sms4wp_charge_notice_to']);?>"> 로 알림메세지를 전송합니다.<br>
		발신 전화번호는 <?php echo sms4wp_get_select_reply_number( 'sms4wp_charge_notice_from', $sms4wp_config['sms4wp_charge_notice_from'] );?> 입니다.
	</td>
</tr>
<tr>
<th scope="row"><label for="sms4wp_reply_number"><?php _e('발신번호 목록') ?> </label></th>
<td>
	<ul class="transmit_number_list">
		<?php
		for ( $c = 0; $c < count( $sms4wp_config['sms4wp_reply_number'] ); $c++ ) { 
			if ( $sms4wp_config['sms4wp_reply_use'][$c] == 200 || $sms4wp_config['sms4wp_reply_use'][$c] == 500 ) {
		?>
		<li>
			<input type="hidden" class="sms4wp_reply_use" name="sms4wp_reply_use[]" value="<?php echo esc_attr($sms4wp_config['sms4wp_reply_use'][$c]); ?>" />
			<input name="sms4wp_reply_number[]" type="text" class="sms4wp_reply_number" value="<?php echo esc_attr($sms4wp_config['sms4wp_reply_number'][$c]); ?>" readonly />
			<input name="sms4wp_reply_comment[]" type="text" class="sms4wp_reply_comment" value="<?php echo esc_attr($sms4wp_config['sms4wp_reply_comment'][$c]); ?>" readonly />
			<a href="#" class="transmit_num_delete"><?php _e('삭제') ?></a>
		</li>
		<?php
			}
		}
		?>
	</ul>
</td>
</tr>

<tr>
<th scope="row"><label for="sms4wp_sendnumber_register"><?php _e('발신번호 등록') ?> </label></th>
<td>
	<ul class="sms4wp_sendnumber_wrap">
		<li>
            <input name="sms4wp_sendnumber" type="text" class="sms4wp_sendnumber" value="" placeholder="발신번호" />
            <input name="sms4wp_comment" type="text" class="sms4wp_comment" value="" max="200" placeholder="발신번호 설명" />
			<!--<select name="sms4wp_pintype" class="sms4wp_pintype">
				<option value="">인증방식</option>
				<option value="SMS">SMS</option>
				<option value="VMS">ARS</option>
			</select>-->
			<a href="#" class="sms4wp_pincode_request button"><?php _e('인증번호 요청') ?></a>
            <div class="sms4wp_progress" style=""></div>
		</li>

		<li class="sms4wp_pincode_response">
			<input name="sms4wp_use" type="hidden" value="" />
			<input name="sms4wp_sendnumber" type="hidden" value="" />
			<input name="sms4wp_comment" type="hidden" value="" />
			<input name="sms4wp_pincode" type="text" class="sms4wp_pincode" value="" placeholder="인증번호" />
			<a href="#" class="sms4wp_sendnumber_register button"><?php _e('발신번호 등록') ?></a>
			<div class="sms4wp_wrap_timer">[<span id="sms4wp_timer">00:00</span>]</div>
            <div class="sms4wp_progress"></div>
			<br />
			<span class="sms4wp_description">3분 이후 인증번호 인증 시간이 만료됩니다. 만료시 인증번호를 재요청해야 합니다.</span>
			<br />
			<span class="sms4wp_sendnumber_information"></span>
		</li>

	</ul>
</td>
</tr>

</table>

<script>
(function($){

	// 잔액알림 체크시 하단 tr 노출
	jQuery("input.check-charge-notice").click(function(){
		if(jQuery(this).prop("checked") == true){
			jQuery("table.form-table").find("tr.sms-count-notice-tr").addClass("active");
		} else {
			jQuery("table.form-table").find("tr.sms-count-notice-tr").removeClass("active");
		}
	});

	$(".transmit_number_list").on("click", ".transmit_num_delete", function(event) {
		/* 발신번호 삭제 */
		var $el = $(this).closest("li");
		
		if ( confirm("<?php _e('Are you sure you want to delete?'); ?>") ) {
			$el.remove();
		}
		return false;
	});

	$(".message_type").click(function (e) {
		var msg_type = $(this).val();

		var sms_point = "<?php echo number_format( $sms4wp_data['sms_point'] ); ?>";
		var lms_point = "<?php echo number_format( $sms4wp_data['lms_point'] ); ?>";
		var mms_point = "<?php echo number_format( $sms4wp_data['mms_point'] ); ?>";

		if ( msg_type === "MMS" ) {
			if ( mms_point !== "0" ) {
				$('.msg_type_mms').addClass('sms_selected');
				$('.msg_type_sms').removeClass('sms_selected');
				$('.msg_type_lms').removeClass('sms_selected');

				$(".message_count").text( "MMS: " + mms_point + "건");
			}
			else {
				$(".message_count").text( "<?php echo $sms4wp_data['sms_error_msg']; ?>");
			}
		}
		else if ( msg_type === "LMS" ) {
			if ( lms_point !== "0" ) {
				$('.msg_type_mms').removeClass('sms_selected');
				$('.msg_type_sms').removeClass('sms_selected');
				$('.msg_type_lms').addClass('sms_selected');

				$(".message_count").text( "LMS: " + lms_point + "건" );
			}
			else {
				$(".message_count").text( "<?php echo $sms4wp_data['sms_error_msg']; ?>");
			}
		}
		else {
			if ( sms_point !== "0" ) {
				$('.msg_type_mms').removeClass('sms_selected');
				$('.msg_type_sms').addClass('sms_selected');
				$('.msg_type_lms').removeClass('sms_selected');

				$(".message_count").text( "SMS: " + sms_point + "건" );
			}
			else {
				$(".message_count").text( "<?php echo $sms4wp_data['sms_error_msg']; ?>");
			}
		}
	});	

	$(".sms4wp_pincode_request").click(function() {
        var els = $(this).closest("li");

        var sms4wp_sendnumber = els.find("input[name='sms4wp_sendnumber']").val();
        var sms4wp_comment    = els.find("input[name='sms4wp_comment']").val();
        var sms4wp_pintype    = els.find("select[name='sms4wp_pintype']").val();
        var sms4wp_nonce      = "<?php echo esc_html($nonce); ?>";

        if ( sms4wp_sendnumber === "" ) {
			alert("발신번호를 입력하세요.");
			return false;
        }

        if ( sms4wp_comment === "" ) {
			alert("발신번호 설명을 입력하세요.");
			return false;
        }

        /*if ( sms4wp_pintype === "" ) {
			alert("인증방식을 선택하세요.");
			return false;
        }*/

        pincodeForm( "", "", "", false );
        els.find(".sms4wp_progress").css("display", "inline-block");

        $.ajax({
            type    : "post",
            url     : "<?php echo admin_url('admin-ajax.php'); ?>",
            data    : {action: "sms4wp_pincode_request", sendnumber: sms4wp_sendnumber, comment: sms4wp_comment, nonce: sms4wp_nonce},
            success : function(response){
                switch( response ) {
                	case "200":
                	case "500":
	                	pincodeSuccess( response, sms4wp_sendnumber, sms4wp_comment );
	                	break;
                	case "300":
                	case "400":
                	case "600":
                	case "700":
	                	pincodeFailed( response );
	                	break;
                    default:
                        alert( response );
                        break;
                }
            },
            complete : function() {
                els.find(".sms4wp_progress").css("display", "none");
            }
        });

        return false;
	});

	$(".sms4wp_sendnumber_register").click(function() {
        var els = $(this).closest("li");

        var sms4wp_sendnumber = els.find("input[name='sms4wp_sendnumber']").val();
        var sms4wp_comment    = els.find("input[name='sms4wp_comment']").val();
        var sms4wp_use        = els.find("select[name='sms4wp_use']").val();
        var sms4wp_pincode    = els.find("input[name='sms4wp_pincode']").val();
        var sms4wp_nonce      = "<?php echo esc_html($nonce); ?>";

        if ( sms4wp_sendnumber === "" ) {
			alert("발신번호를 입력하세요.");
			return false;
        }

        if ( sms4wp_comment === "" ) {
			alert("발신번호 설명을 입력하세요.");
			return false;
        }

        if ( sms4wp_pincode === "" ) {
			alert("인증번호를 입력하세요.");
			return false;
        }

        els.find(".sms4wp_progress").css("display", "inline-block");

        $.ajax({
            type    : "post",
            url     : "<?php echo admin_url('admin-ajax.php'); ?>",
            data    : {action: "sms4wp_sendnumber_register", sendnumber: sms4wp_sendnumber, comment: sms4wp_comment, pincode: sms4wp_pincode, nonce: sms4wp_nonce},
            success : function(response){
                switch( response ) {
                	case "200":
                        addSendNumber( response, sms4wp_sendnumber, sms4wp_comment );
                        break;
                	case "500":
	                	pincodeSuccess( response, sms4wp_sendnumber, sms4wp_comment );
	                	break;
                	case "300":
                	case "400":
                	case "600":
                	case "700":
	                	pincodeFailed( response );
	                	break;
                    default:
                        alert( response );
                        break;
                }
            },
            complete : function() {
                els.find(".sms4wp_progress").css("display", "none");
            }
        });

        return false;
	});

	function pincodeSuccess( response, sendnumber, comment ) 
	{
		var message = "";

		switch( response ) {
        	case "200": 
        		pincodeForm( response, sendnumber, comment, true );
        		break; // 성공
        	case "500": 
        		message = "이미 등록된 번호입니다.\n발신번호 목록에 추가하시겠습니까?";
        		if ( confirm( message ) ) {
                    addSendNumber(response, sendnumber, comment);
                }
        		break;
        }
	}

	function pincodeFailed( response ) 
	{
		switch( response ) {
        	case "300": 
        		message = "파라메터 에러"; 
        		break;
        	case "400": 
        		message = "인증 업데이트 중 에러";  
        		break;
        	case "600": 
        		message = "일치 하지 않는 인증번호"; 
        		break;
        	case "700": 
        		message = "핀코드 인증 시간 만료(3분 이후 만료이며 재등록 요청해야 함.)"; 
        		break;
        }

        alert( message );
        pincodeForm( response, sendnumber, comment, false );
	}

	function pincodeForm( response, sendnumber, comment, certification ) 
	{
		var els = $(".sms4wp_pincode_response");

		if ( certification === false ) {
			els.slideUp("fast", function() {
                els.find("input").val("");
			});
			return false;
		} else {
            countdown("sms4wp_timer", 180);	 // second base
            els.find("input[name='sms4wp_use']").val(response);
            els.find("input[name='sms4wp_sendnumber']").val(sendnumber);
            els.find("input[name='sms4wp_comment']").val(comment);
            $(".sms4wp_sendnumber_information").text("발신번호: "+ sendnumber +", 발신번호 설명: "+ comment);
			els.slideDown("slow", function() {
			});
		}
	}

	function addSendNumber( response, sendnumber, comment ) 
	{
        pincodeForm( "", "", "", false );

		var $el = $(".transmit_number_list");
		var html = "";
		html += '<li>';
		html += '<input type="hidden" class="sms4wp_reply_use" name="sms4wp_reply_use[]" value="'+ response +'" />';
		html += '<input type="text" name="sms4wp_reply_number[]" class="sms4wp_reply_number" value="'+ sendnumber +'" readonly />';
		html += ' <input type="text" name="sms4wp_reply_comment[]" class="sms4wp_reply_comment" value="'+ comment +'" readonly />';
		html += ' <a href="#" class="transmit_num_delete"><?php _e('삭제') ?></a>';
		html += '</li>';

		$el.append( html );
	}
	
	/* Timer */
	var timeoutId;
	function countdown( elementId, seconds )
	{
		var element, endTime, hours, mins, msLeft, time;
		clearTimeout( timeoutId );

		function updateTimer(){
			msLeft = endTime - (+new Date);
			if ( msLeft < 0 ) {
				console.log('done');
			} else {
				time = new Date( msLeft );
				hours = time.getUTCHours();
				mins = time.getUTCMinutes();
				element.innerHTML = (hours ? hours + ':' + ('0' + mins).slice(-2) : mins) + ':' + ('0' + time.getUTCSeconds()).slice(-2);
                timeoutId = setTimeout( updateTimer, time.getUTCMilliseconds());
			}
		}

		element = document.getElementById( elementId );
		endTime = (+new Date) + 1000 * seconds;
		updateTimer();
	}

	// countdown("sms4wp_timer", 180);	 // second base
})(jQuery);
</script>

<?php 
} 
else if ( $tab == 'enabled' ) { // 고급설정
?>
<input type="hidden" name="action" value="update" />
<table class="form-enabled-table">

<?php
if ( class_exists('sms4wpAddOn') ) :
	$t = 0;
	// $sms4wpAddOn = new sms4wpAddOn();
	$sms4wp_plugins = sms4wpAddOn::get_plugins();

	foreach ( $sms4wp_plugins as $key => $plugin_data) {
		$sms4wp_plugin_checked = '';
		if ( in_array( $key, $sms4wp_config['sms4wp_plugin'] ) )
			$sms4wp_plugin_checked = 'checked';
?>
	<tr>
		<th scope="row"><label for="sms4wp_plugin<?php echo esc_attr($t); ?>"><?php _e('Activate') ?> <?php echo esc_html($plugin_data['Name']) ?> <?php _e('plugin Options') ?></label></th>
		<td><input name="sms4wp_plugin[]" type="checkbox" id="sms4wp_plugin<?php echo esc_attr($t); ?>" value="<?php echo esc_attr( dirname( $plugin_data['TextDomain'] ) ); ?>" <?php echo esc_attr($sms4wp_plugin_checked); ?>/> <?php _e('사용하기') ?></td>
	</tr>

<?php
		$t++;
	}
endif;
?>

</table>
<?php 
} 
else if ( $tab ) {
	if ( class_exists('sms4wpAddOn') ) :
		// 기타 추가 설정
		$sms4wp_plugin = sms4wpAddOn::get_plugin_options( $tab );
		sms4wpAddOn::include_plugin_files( '/'. $sms4wp_plugin['TextDomain'] );

		$sms4wp_url = sms4wpAddOn::get_sms4wp_admin_url(). '&amp;tab='. $tab;


		$cls_section = '';
		if ( ! $section )
			$cls_section = 'current';
?>
<div class="local_woocommerce_status">
	<ul class="subsubsub">
	<li><a href="<?php echo esc_attr($sms4wp_url); ?>" class="<?php echo esc_attr($cls_section); ?>"><?php echo esc_attr($sms4wp_plugin['Title']); ?></a> | </li>
<?php do_action( 'sms4wp_plugin_'. $tab .'_menu' ); // 탭 하위 섹션 메뉴 ?>
	</ul>
</div>

<br class="clear">

<p><?php //echo $sms4wp_plugins[$tab]['Description']; ?></p>
<?php
		do_action( 'sms4wp_plugin_'. $tab .'_menu_'. $section ); // 탭/섹션 출력
		do_action( 'sms4wp_plugin_'. $tab .'_information' ); // 탭메뉴 안내글

	endif;
}
?>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('변경사항 저장') ?>"></p>

</form>

</div>
