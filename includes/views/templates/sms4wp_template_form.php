<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
	<h2><?php echo esc_html( '템플릿 관리' ); ?></h2>


	<form method="post" action="admin.php?page=sms4wp-template">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-template">
	<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>">
	<input type="hidden" name="s" value="<?php echo $_REQUEST['s']; ?>">
	<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">
	<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
	<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
	<input type="hidden" name="id" value="<?php echo $template['ID']; ?>">

	<table class="form-table">
	<!-- <tr>
		<th scope="row"><label for="message_type"><?php _e('메시지종류') ?></label></th>
		<td>
			<input name="te_type" type="radio" class="message_type" value="SMS" <?php echo ( $template['checked_sms'] || $template['checked_sms'] == '' ? 'checked' : '' ); ?> /><span class="msg_type_sms  <?php echo ( $template['checked_sms'] ? 'sms_selected' : '' ); ?>">SMS</span> &nbsp; 
			<input name="te_type" type="radio" class="message_type" value="LMS" <?php echo ( $template['checked_lms'] ? 'checked' : '' ); ?> /><span class="msg_type_lms <?php echo ( $template['checked_lms'] ? 'sms_selected' : '' ); ?>">LMS</span> &nbsp; 
			<input name="te_type" type="radio" class="message_type" value="MMS" <?php echo ( $template['checked_mms'] ? 'checked' : '' ); ?> /><span class="msg_type_mms <?php echo ( $template['checked_mms'] ? 'sms_selected' : '' ); ?>">MMS</span>
		</td>
	</tr> -->

	<!-- <tr>
		<th scope="row"><label for="message_group"><?php _e('그룹') ?></label></th>
		<td><input name="message_group" type="text" id="message_group" value="<?php echo $template['te_message_group']; ?>" class="regular-text" /></td>
	</tr> -->

	<tr>
		<th scope="row"><label for="te_subject"><?php _e('제목') ?></label></th>
		<td><input name="te_subject" type="text" id="te_subject" value="<?php echo $template['te_subject']; ?>" class="regular-text" /></td>
	</tr>

	<tr>
		<th scope="row"><label for="te_message"><?php _e('내용') ?></label></th>
		<td><textarea name="te_message" id="te_message" class="large-text code" rows="3"><?php echo esc_textarea( htmlspecialchars_decode( $template['te_message'] ) ); ?></textarea></td>
	</tr>

	<tr>
		<td colspan="2" class="box_msg_addfile">
			<div class="msg_addfile" style="display:none;">
				<table class="form-table">
				<tr>
				<th scope="row"><label for="te_file1"><?php _e('첨부파일1') ?> </label></th>
				<td><input name="te_file1" type="file" id="te_file1" class="regular-text" /></td>
				</tr>

				<tr>
				<th scope="row"><label for="te_file2"><?php _e('첨부파일2') ?> </label></th>
				<td><input name="te_file2" type="file" id="te_file2" class="regular-text" /></td>
				</tr>
				</table>
			</div>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="sms4wp_charge"><?php _e('등록일') ?></label></th>
		<td><?php echo $template['te_date']; ?></td>
	</tr>
	</table>

	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes') ?>">
		&nbsp;&nbsp;
		<input type="button" name="list" id="list" class="button action" value="<?php _e('LIST') ?>" onclick="location.href='admin.php?page=sms4wp-template<?php echo $qs; ?>'">
	</p>

	</form>

</div>

<script>
	jQuery(document).ready(function() {	
		// MMS 첨푸파일
		jQuery(".message_type").click(function (e) {
			var msg_type = jQuery(this).val();

			if ( msg_type == "MMS" ) {
				jQuery('.msg_addfile').slideDown( function() {
					jQuery( this ).css( 'height', 'auto' ); // so that the .accordion-section-content won't overflow
				} );
				jQuery('.msg_addfile').show();

				jQuery('.msg_type_mms').addClass('sms_selected');
				jQuery('.msg_type_sms').removeClass('sms_selected');
				jQuery('.msg_type_lms').removeClass('sms_selected');
			}
			else if ( msg_type == "LMS" ) {
				jQuery('.msg_addfile').slideUp( function() {
					jQuery('.msg_addfile').hide();
				} );

				jQuery('.msg_type_mms').removeClass('sms_selected');
				jQuery('.msg_type_sms').removeClass('sms_selected');
				jQuery('.msg_type_lms').addClass('sms_selected');
			}
			else {
				jQuery('.msg_addfile').slideUp( function() {
					jQuery('.msg_addfile').hide();
				} );

				jQuery('.msg_type_mms').removeClass('sms_selected');
				jQuery('.msg_type_sms').addClass('sms_selected');
				jQuery('.msg_type_lms').removeClass('sms_selected');
			}
		});	
	});
</script>
