<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2><?php echo esc_html( '수신자 그룹' ); ?></h2>


<form method="post" action="admin.php?page=sms4wp-group">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-group">
<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>">
<input type="hidden" name="s" value="<?php echo $_REQUEST['s']; ?>">
<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">
<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
<input type="hidden" name="id" value="<?php echo $group['ID']; ?>">

<table class="form-table">
<!-- <tr>
<th scope="row"><label for="gr_use"><?php _e('use') ?></label></th>
<td><input name="gr_use" type="radio" id="gr_use" value="1" <?php echo ($group['gr_use'] == 1 ? 'checked': ''); ?> /><?php _e('use') ?> &nbsp; <input name="gr_use" type="radio" id="gr_use" value="0" <?php echo ($group['gr_use'] != 1 ? 'checked': ''); ?> /><?php _e('not use') ?>
</td>
</tr> -->

<tr>
<th scope="row"><label for="gr_name"><?php _e('Name') ?></label></th>
<td><input name="gr_name" type="text" id="gr_name" value="<?php echo $group['gr_name']; ?>" class="regular-text" />
</td>
</tr>

<tr>
<th scope="row"><label for="gr_parent"><?php _e('Parent') ?></label></th>
<td>
	<select name="gr_parent" class="gr_parent">
		<option value="0"><?php _e('None') ?></option>
		<?php echo $parents; ?>
	</select>
</td>
</tr>

<tr>
<th scope="row"><label for="gr_memo"><?php _e('Description') ?></label></th>
<td><textarea name="gr_memo" id="gr_memo" class="large-text code" rows="3"><?php echo esc_textarea( htmlspecialchars_decode( $group['gr_memo'] ) ); ?></textarea></td>
</tr>

</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('SAVE') ?>">
	&nbsp;&nbsp;
	<input type="button" name="list" id="list" class="button action" value="<?php _e('LIST') ?>" onclick="location.href='admin.php?page=sms4wp-group<?php echo $qs; ?>'">
</p>

</form>

</div>

<script>
	jQuery(document).ready(function(){
		jQuery("select[name=gr_parent] > option[value=<?php echo ($group['gr_parent'] ? intval($group['gr_parent']): '0'); ?>]").attr("selected", "true");
		jQuery("select[name=gr_order] > option[value=<?php echo ($group['gr_order'] ? intval($group['gr_order']): '0'); ?>]").attr("selected", "true");
	});
</script>
