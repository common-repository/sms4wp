
<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2><?php echo esc_html( '가져오기/내보내기'); ?></h2>


<form name="frmBookUp" id="frmBookUp" method="post" action="admin.php?page=sms4wp-book-file" enctype="multipart/form-data">
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-book-file">
<input type="hidden" name="_wpnonce_upload" value="<?php echo $nonce_up; ?>">

	<h3><?php echo esc_html( '가져오기' ); ?></h3>
	<p><?php echo esc_html( '엑셀에 저장된 휴대폰 목록을 가져옵니다.' ); ?></p>

	<table class="form-table-updown">
	<tr>
	<td colspan="2">
		<ul>
			<li><?php echo esc_html( '엑셀에는 이름, 핸드폰번호 2개 항목만 저장해주세요. 첫번째 라인부터 저장됩니다.' ); ?></li>
			<li><?php echo esc_html( '엑셀파일은 "파일 > 다른 이름으로 저장 > 파일형식 : CSV (쉼표로 분리) (*.CSV)" 로 저장한 후 업로드 해주세요.' ); ?></li>
		</ul>
	</td>
	</tr>

	<tr>
	<th scope="row" width="94"><label for="book_file"><?php _e('파일선택') ?> </label></th>
	<td>
		<input name="book_file" type="file" id="book_file" class="regular-text sms4wp_group" /> 
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="sms4wp_group"><?php _e('그룹선택') ?></label></th>
	<td>
		<select name="gr_parent" id="upload_parent">
			<option value="0"><?php _e('All') ?></option>
			<?php echo $parents; ?>
		</select>
		<input type="button" name="button_upload" id="button-upload" class="button button-primary" value="<?php _e('Upload') ?>">
		<div class="upload-progress"></div>
	</td>
	</tr>

	</table>

</form>

<p></p>

<?php /* ?>
<form name="frmBookDown" id="frmBookDown" method="post" action="admin.php?page=sms4wp-book-file">
<input type="hidden" name="action" value="download" />
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-book-file">
<input type="hidden" name="_wpnonce_download" value="<?php echo $nonce_down; ?>">

	<h3><?php echo esc_html( '내보내기' ); ?></h3>
	<p><?php echo esc_html( '저장된 휴대폰 목록을 엑셀파일로 다운로드할 수 있습니다. 다운로드 할 핸드폰 그룹을 선택해주세요.' ); ?></p>

	<table class="form-table-updown">
	<tr>
	<td colspan="2">
		<ul>
			<li><?php echo esc_html( '엑셀에는 이름, 핸드폰번호, 그룹, 수신정보가 저장됩니다.' ); ?></li>
		</ul>
	</td>
	</tr>

	<tr>
	<th scope="row" width="94"><label for="sms4wp_group"><?php _e('그룹선택') ?></label></th>
	<td>
		<select name="gr_parent2" id="download_parent">
			<option value="all"><?php _e('All') ?></option>
			<?php echo $parents; ?>
		</select>
		<input type="button" name="button_download" id="button-download" class="button button-primary" value="<?php _e('Download') ?>">
		<div class="download-progress"></div>
	</td>
	</tr>

	<tr>
		<th scope="row">&nbsp;</th>
		<td>&nbsp;</td>
	</tr>

	</table>

</form>
<?php */ ?>

</div>

<script>
	var inc_view_img_url = "<?php echo SMS4WP_INC_VIEW_IMG_URL; ?>";
	var ajaxurl = "<?php echo $book_link; ?>";
</script>

<script type="text/javascript" src="<?php echo SMS4WP_INC_VIEW_JS_URL; ?>/jquery.book_file.js"></script>
