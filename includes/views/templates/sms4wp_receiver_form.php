<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2><?php echo esc_html( '수신자 추가하기' ); ?></h2>

<div class="local_desc01 local_desc">
	<p><?php echo esc_html( '새로운 수신자를 추가합니다. 엑셀파일을 이용한 대량 업로드는 가져오기를 이용해 주십시오.' ); ?></p>
</div>


<form method="post" action="admin.php?page=sms4wp-receivers">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=sms4wp-receivers">
<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>">
<input type="hidden" name="s" value="<?php echo $_REQUEST['s']; ?>">
<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">
<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
<input type="hidden" name="id" value="<?php echo $receiver['ID']; ?>">

<table class="form-table">
<tr>
<th scope="row"><label for="receiver_use"><?php _e('수신') ?></label></th>
<td><input name="receiver_use" type="radio" id="receiver_use" value="1" <?php echo ($receiver['re_use'] == 1 ? 'checked': ''); ?> /><?php _e('수신') ?> &nbsp; <input name="receiver_use" type="radio" id="receiver_use" value="0" <?php echo ($receiver['re_use'] != 1 ? 'checked': ''); ?> /><?php _e('거부') ?>
</td>
</tr>

<tr>
<th scope="row"><label for="receiver_name"><?php _e('이름') ?></label></th>
<td><input name="receiver_name" type="text" id="receiver_name" value="<?php echo $receiver['re_name']; ?>" class="regular-text" />
</td>
</tr>

<tr>
<th scope="row"><label for="receiver_phone_number"><?php _e('전화번호') ?></label></th>
<td><input name="receiver_phone_number" type="text" id="receiver_phone_number" value="<?php echo sms4wp_get_hp( $receiver['re_phone_number'] ); ?>" class="regular-text" /></td>
</tr>

<tr>
<th scope="row"><label for="receiver_group"><?php _e('그룹') ?></label></th>
<td>
	<select name="receiver_group" class="receiver_group">
		<option value="0"><?php _e('None') ?></option>
		<?php echo $groups; ?>
	</select>
</td>
</tr>

<tr>
<th scope="row"><label for="receiver_memo"><?php _e('메모') ?></label></th>
<td><textarea name="receiver_memo" id="receiver_memo" class="large-text code" rows="3"><?php echo esc_textarea( htmlspecialchars_decode( $receiver['re_memo'] ) ); ?></textarea></td>
</tr>

<tr>
<th scope="row"><label for="receiver_history"><?php _e('sms history') ?></label></th>
<td>
	<table class="widefat">
		<thead>
			<tr>
				<th class="num"><?php _e('날짜') ?></th>
				<th class="num"><?php _e('내용') ?></th>
				<th class="num"><?php _e('수신') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// print_r($smssends);
			foreach ( $smssends as $key => $send ) {
				$send_result = $send['se_result_code'] == 200 ? '성공' : '실패';
			?>
			<tr>
				<td class="num"><?php echo $send['se_date']; ?></td>
				<td><code><?php echo $send['se_message']; ?></code></td>
				<td class="num"><?php echo $send_result; ?></td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</td>
</tr>

</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('SAVE') ?>">
	&nbsp;&nbsp;
	<input type="button" name="list" id="list" class="button action" value="<?php _e('LIST') ?>" onclick="location.href='admin.php?page=sms4wp-receivers<?php echo $qs; ?>'">
</p>

</form>

</div>

<script>
	jQuery(document).ready(function(){
		//...receiver_group
		jQuery("select[name=receiver_group] > option[value=<?php echo ($receiver['gr_id'] ? intval($receiver['gr_id']): '0'); ?>]").attr("selected", "true");
	});
</script>
