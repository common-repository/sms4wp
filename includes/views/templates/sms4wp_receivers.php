<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2>
<?php _e('수신자 관리') ?>	<a href="./admin.php?page=sms4wp-receivers&amp;action=add_new" class="add-new-h2">Add New</a>
</h2>

<ul class="subsubsub">
	<li class="all"><a href="admin.php?page=sms4wp-receivers" class="current"><?php _e('모두') ?> <span class="count">(<?php echo number_format( $list['total'] ); ?>)</span></a> |</li>
	<li class="administrator"><a href="admin.php?page=sms4wp-receivers&amp;sf=re_use&amp;ss=0"><?php _e('수신거부') ?> <span class="count">(<?php echo number_format( $list['total_not_call'] ); ?>)</span></a></li>
</ul>
<form action="admin.php" method="get">

 <p class="search-box">
	<label class="screen-reader-text" for="receiver-input"><?php _e('검색') ?>:</label>
	<select name="receiver_group" class="receiver_group">
		<option value="0"><?php _e('GROUP') ?></option>
		<?php echo $groupList; ?>
	</select>	
	<input type="search" id="receiver-input" name="s" value="<?php echo $_REQUEST['s']; ?>">
	<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
	<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
	<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">
	<input type="submit" name="" id="search-submit" class="button" value="<?php _e('검색') ?>">
</p>
<script type="text/javascript">
(function($){
	$(".receiver_group").val("<?php echo ( isset($_REQUEST['receiver_group']) ? $_REQUEST['receiver_group'] : '0' ) ; ?>").attr("selected", "selected");
})(jQuery);
</script>
	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $delete_nonce; ?>">
	<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-receivers">	
	<input type="hidden" name="page" value="sms4wp-receivers">
	<div class="tablenav top">

		<div class="alignleft actions bulkactions">
			<select name="action">
			<option value="-1" selected="selected"><?php _e('일괄작업') ?></option>
			<option value="delete"><?php _e('삭제') ?></option>
			</select>

			<input type="submit" name="" id="doaction" class="button action button-submitaction" value="Apply">
		</div>
		<div class="tablenav-pages">
			<?php echo $links; ?>
		</div>
		<br class="clear">
		
	</div>

<table class="wp-list-table widefat fixed users">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
		<th scope="col" id="username" class="manage-column column-role sortable desc" style=""><a href="admin.php?page=sms4wp-receivers&amp;orderby=re_name&amp;order=asc"><span><?php _e('이름') ?></span><span class="sorting-indicator"></span></a></th>
		<th scope="col" id="name" class="manage-column column-role sortable desc" style=""><a href="admin.php?page=sms4wp-receivers&amp;orderby=re_phone_number&amp;order=asc"><span><?php _e('전화번호') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="email" class="manage-column column-role sortable desc" style=""><?php _e('그룹') ?></th>
		<th scope="col" id="role" class="manage-column column-role" style=""><?php _e('수신') ?></th>
		<th scope="col" id="posts" class="manage-column column-role num" style=""><?php _e('등록일') ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></th>
		<th scope="col" class="manage-column column-role sortable desc" style=""><a href="admin.php?page=sms4wp-receivers&amp;orderby=re_name&amp;order=desc"><span><?php _e('이름') ?></span><span class="sorting-indicator"></span></a></th>
		<th scope="col" class="manage-column column-role sortable desc" style=""><a href="admin.php?page=sms4wp-receivers&amp;orderby=re_phone_number&amp;order=desc"><span><?php _e('전화번호') ?></span><span class="sorting-indicator"></span></a></th>
		<th scope="col" class="manage-column column-role sortable desc" style=""><?php _e('그룹') ?></th>
		<th scope="col" class="manage-column column-role" style=""><?php _e('수신') ?></th>
		<th scope="col" class="manage-column column-role num" style=""><?php _e('등록일') ?></th>	
	</tr>
	</tfoot>

	<tbody id="the-list" data-wp-lists="list:user">
		
	<?php
	$receivers = $list['receivers'];
	// print_r( $receivers );

	for ( $c = 0; $c < count($receivers); $c++ ) { 
		if ( $receivers[$c]['re_use'] == 1 ) {
			$receiver_use = '수신';
		}
		else {
			$receiver_use = '<span class="file-error">거부</span>';
		}
	?>
	<tr id="receiver-<?php echo $receivers[$c]['ID']; ?>" class="alternate">
		<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-1">Select <?php echo $receivers[$c]['re_name']; ?></label><input type="checkbox" name="id[]" id="receiver_<?php echo $receivers[$c]['ID']; ?>" class="checkbox-list" value="<?php echo $receivers[$c]['ID']; ?>"></th>
		<td class="receivername column-role"> 
			<strong><a href="admin.php?page=sms4wp-receivers&amp;action=edit&amp;id=<?php echo $receivers[$c]['ID']; ?>"><?php echo $receivers[$c]['re_name']; ?></a></strong>
			<br>
			<div class="row-actions">
				<span class="edit"><a href="admin.php?page=sms4wp-receivers&amp;action=edit&amp;id=<?php echo $receivers[$c]['ID']; ?>">Edit</a> | </span><span class="delete"><a class="button-submitdelete" href="admin.php?page=sms4wp-receivers&amp;action=delete&amp;id=<?php echo $receivers[$c]['ID']; ?>&amp;_wpnonce=<?php echo $delete_nonce; ?>">Delete</a></span>
			</div>
		</td>
		<td class="role column-role"><?php echo sms4wp_get_hp( $receivers[$c]['re_phone_number'] ); ?></td>
		<td class="role column-role"><a href="admin.php?page=sms4wp-receivers&amp;receiver_group=<?php echo $receivers[$c]['gr_id']; ?>"><?php echo $groups[$receivers[$c]['gr_id']]['gr_name']; ?></a></td>
		<td class="role column-role"><?php echo $receiver_use; ?></td>
		<td class="role column-role num"><?php echo $receivers[$c]['re_update']; ?></td>
	</tr>
	<?php
	}
	?>

</tbody>
</table>

	<div class="tablenav bottom">

		<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1" selected="selected"><?php _e('일괄작업') ?></option>
				<option value="delete"><?php _e('삭제') ?></option>
			</select>
			<input type="submit" name="" id="doaction2" class="button action button-submitaction" value="Apply">
		</div>
		
		<div class="tablenav-pages">
			<?php echo $links; ?>
		</div>
		<br class="clear">
	</div>
</form>

<br class="clear">
</div>

<script type="text/javascript" src="<?php echo SMS4WP_INC_VIEW_JS_URL; ?>/jquery.common.js"></script>
