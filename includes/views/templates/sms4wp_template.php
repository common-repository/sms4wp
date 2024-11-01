<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2>
<?php _e('템플릿 관리') ?>	<a href="admin.php?page=sms4wp-template&amp;action=add_new" class="add-new-h2">Add New</a>
</h2>

<ul class="subsubsub">
	<li class="all"><a href="admin.php?sms4wp-template" class="current"><?php _e('모두') ?> <span class="count">(<?php echo number_format( $list['total'] ); ?>)</span></a></li>
</ul>
<form id="frmTemplate" action="" method="post">
<input type="hidden" name="page" value="sms4wp-template">
<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">

 <p class="search-box">
	<label class="screen-reader-text" for="user-search-input"><?php _e('검색') ?>:</label>
	<input type="search" id="user-search-input" name="s" value="">
	<input type="submit" name="" id="search-submit" class="button" value="<?php _e('검색') ?>"></p>

	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $delete_nonce; ?>">
	<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-template">	

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

<table class="wp-list-table widefat fixed users">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
		<th scope="col" id="username" class="manage-column column-role sortable " style=""><span><?php _e('제목') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="name" class="manage-column column-role sortable " style=""><span><?php _e('내용') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="posts" class="manage-column column-date num" style=""><?php _e('업데이트') ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></th>
		<th scope="col" class="manage-column column-role sortable " style=""><span><?php _e('제목') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-role sortable " style=""><span><?php _e('내용') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-date num" style=""><?php _e('업데이트') ?></th>	
	</tr>
	</tfoot>

	<tbody id="the-list" data-wp-lists="list:user">
	<?php
	$template = $list['template'];
	// print_r( $template );

	for ( $c = 0; $c < count($template); $c++ ) { 
		$class = 'alternate';
		if ( !($c%2) )
			$class = '';
	?>
	<tr id="user-2" class="<?php echo $class; ?>">
		<th scope="row" class="check-column">
			<label class="screen-reader-text" for="cb-select-2">Select <?php echo $template[$c]['te_subject']; ?></label><input type="checkbox" name="id[]" id="template_<?php echo $template[$c]['ID']; ?>" class="checkbox-list" value="<?php echo $template[$c]['ID']; ?>">
		</th>
		<td class="username column-role"> 
			<strong><a href="admin.php?page=sms4wp-template&amp;action=edit&amp;id=<?php echo $template[$c]['ID']; ?>"><?php echo $template[$c]['te_subject']; ?></a></strong><br>
			<div class="row-actions">
				<span class="edit"><a href="admin.php?page=sms4wp-template&amp;action=edit&amp;id=<?php echo $template[$c]['ID']; ?>">Edit</a> | </span>
				<span class="delete"><a class="submitdelete" href="admin.php?page=sms4wp-template&amp;action=delete&amp;id=<?php echo $template[$c]['ID']; ?>&amp;_wpnonce=<?php echo $delete_nonce; ?>">Delete</a></span>
			</div>
		</td>
		<td class="role column-role"><?php echo $template[$c]['te_message']; ?></td>
		<td class="date column-date num"><?php echo substr( $template[$c]['te_date'], 0, 10 ); ?></td>
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
