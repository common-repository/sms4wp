
<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

<div class="wrap">
<h2><?php esc_html_e( '메시지 보내기'); ?></h2>

<!--div class="local_desc01 local_desc">
	<p><?php esc_html_e( 'Here is where the form would go if I actually had options.'); ?></p>
</div-->


<form id="frmSMS" method="post" action="/wp-admin/admin.php?page=sms4wp-send" enctype="multipart/form-data">
<input type="hidden" name="action" value="sms_sends" />
<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=sms4wp-send">
<input type="hidden" name="nonce" value="<?php echo $message_nonce; ?>">

<table class="form-table">
<tr>
<th scope="row"><label for="message_type"><?php _e('메시지 종류') ?></label></th>
<td>
	<input name="message_type" type="radio" class="message_type" value="SMS" checked /><span class="msg_type_sms sms_selected">SMS</span> &nbsp; 
	<input name="message_type" type="radio" class="message_type" value="LMS" /><span class="msg_type_lms">LMS</span> &nbsp; 
	<input name="message_type" type="radio" class="message_type" value="MMS" /><span class="msg_type_mms">MMS</span>
</td>
</tr>

<tr>
<th scope="row"><label for="sender_phone"><?php _e('발신 번호') ?></label></th>
<td>
	<select name="sender_phone" id="sender_phone">
		<?php
		for ( $c = 0; $c < count( $sms4wp_config['sms4wp_reply_number'] ); $c++ ) { 
			if ( $sms4wp_config['sms4wp_reply_use'][$c] == 200 || $sms4wp_config['sms4wp_reply_use'][$c] == 500 ) {
				echo '<option value="'.$sms4wp_config['sms4wp_reply_number'][$c].'">'.$sms4wp_config['sms4wp_reply_number'][$c].'</value>' . PHP_EOL;
			}
		}
		?>
	</select>
</td>
</tr>

<tr>
<th scope="row"><label for="receiver_phone"><?php _e('수신 번호') ?></label></th>
<td><textarea name="receiver_phone" id="receiver_phone" class="large-text code" rows="2"><?php echo esc_textarea( get_option('receiver_phone') ); ?></textarea> 
	<span class="exp"><?php _e('입력예: 010-1212-2323 (여러명을 입력하실 때는 콤마(,)로 구분해주세요.)') ?></span>

	<a href="javascript:;" class="sms4wp-btn-receiver-group button"><?php _e('수신자, 수신자그룹 추가') ?></a><!-- <a href="#dialog" name="modal"><?php _e('받는 사람 추가') ?></a> -->

	<div class="sms4wp" style="">	
		<!-- <div class="sms4wp-btn-receiver-group">
		<div class="sms4wp-title"><h4><?php _e('받는 사람 추가') ?></h4></div>
		</div> -->

		<div class="sms4wp-msg-inside" style="display: none;">

			<div class="sms4wp-msg">
				<p>
					<label for="receiver-search">
						<span><?php _e('검색') ?>:</span>
						<input type="text" name="s" id="receiver-search" value=""><input type="button" name="savewidget" class="button button-receiver-search" value="<?php _e('수신자 검색') ?>"> <span class="exp"><?php _e('수신자관리 이름, 전화번호 검색') ?></span>
					</label>
				</p>

				<span class="exp receiver-exp"><?php _e('검색목록에서 수신자를 클릭하시면 받는 사람에 추가됩니다.') ?></span>
				<div class="receiver-list">
					<ul class="add-sms4wp-receiver">
					</ul>
				</div>
			</div>

			<div class="sms4wp-control-actions">
			
					<span><?php _e('수신자 그룹') ?>:</span>
						<select name="sms4wp_groups" class="sms4wp-groups">
							<option value="0"><?php _e('None') ?></option>
							<?php echo $groups; ?>
						</select>
					<input type="button" name="savewidget" class="button add-sms4wp-group" value="<?php _e('수신자 그룹 추가') ?>">
				
				<br class="clear">
			</div>

		</div>

	</div>

	<dl class="receiver-group">
	</dl>
</td>
</tr>

<tr>
<td colspan="2" class="box_msg_subject">
	<div class="msg_mms" style="display:none;">
		<table class="form-table">
		<tr>
		<th scope="row"><label for="message_subject"><?php _e('메시지 제목') ?></th>
		<td><input name="message_subject" type="text" id="message_subject" value="" class="regular-text" /></td>
		</tr>
		</table>
	</div>
</td>
</tr>

<tr>
<th scope="row"><label for="message_body"><?php _e('메시지 내용') ?></label></th>
<td><textarea name="message_body" id="message_body" class="large-text code" rows="5"></textarea>
	<p>
		<a href="#" class="button sms4wp-btn-template"><?php _e('템플릿 선택') ?></a>
		<a href="#" class="button sms4wp-save-template"><?php _e('템플릿 저장') ?></a>
	</p>

		<div class="sms4wp-template-inside" style="display: none;">

			<div class="sms4wp-template">
				<div class="template-list">
					<ul class="add-sms4wp-template">
					</ul>
				</div>
			</div>

		</div>
</td>
</tr>

<tr>
<td colspan="2" class="box_msg_addfile">
	<div class="msg_mms" style="display:none;">
		<table class="form-table">
		<tr>
		<th scope="row"><label for="add_file1"><?php _e('첨부이미지') ?> </label></th>
		<td><input name="add_file1" type="file" id="add_file1" class="regular-text" /><span class="exp receiver-exp"><?php _e('JPG 20Kb 이하') ?></span></td>
		</tr>

		<!-- <tr>
		<th scope="row"><label for="add_file2"><?php _e('첨부이미지') ?> </label></th>
		<td><input name="add_file2" type="file" id="add_file2" class="regular-text" /></td>
		</tr> -->
		</table>
	</div>
</td>
</tr>

<tr>
<th scope="row"><label for="reservation_date"><?php _e('예약전송') ?> </label></th>
<td>
	<input name="reservation_date" type="text" id="reservation_date" value="" class="date-text" />
	<input name="reservation_date_current" type="hidden" id="reservation_date_current" value="<?php echo date("Y-m-d");   ;?>" class="date-text" />
	<input name="reservation_time" type="text" id="reservation_time" value="" class="time-text" />
	<input name="reservation_time_current" type="hidden" id="reservation_time_current" value="<?php echo date("H:i:s");?>" class="time-text" />
</td>
</tr>

</table>

<p class="submit"><input type="button" name="submit" id="message-submit" class="button button-primary" value="<?php _e('Send Message') ?>"></p>
<!-- <p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php _e('Send Message') ?>"></p> -->

</form>

</div>

<div id="prog" style="width:500px; height:30px; border:0px solid #ccc;"></div>

<div id="boxes">
	<div id="dialog" class="window">
		Simple Modal Window | 
		<a href="#"class="close"/>Close it</a>
		<div id="dialog-content"></div>
	</div>

	<!-- Mask to cover the whole screen -->
	<div id="mask"></div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/jquery.timepicker.css">

<script>
	var inc_view_img_url = "<?php echo SMS4WP_INC_VIEW_IMG_URL; ?>";
	var receivers_nonce  = "<?php echo $receivers_nonce; ?>";
	var message_nonce    = "<?php echo $message_nonce; ?>";
	var template_nonce   = "<?php echo $template_nonce; ?>";
	var ajaxurl          = "<?php echo $receivers_link; ?>";
</script>

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.pack.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo SMS4WP_INC_VIEW_JS_URL; ?>/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?php echo SMS4WP_INC_VIEW_JS_URL; ?>/jquery.sms_form.js"></script>
