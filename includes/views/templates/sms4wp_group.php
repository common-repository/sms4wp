<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap nosubsub">
<h2>수신자 그룹</h2>

<div id="ajax-response"></div>

<form class="search-form" action="" method="get">
<input type="hidden" name="page" value="sms4wp-group">
<input type="hidden" name="paged" value="<?php echo $paged; ?>">

<p class="search-box">
	<label class="screen-reader-text" for="group-search-input">Search 수신자 그룹:</label>
	<input type="search" id="receiver-input" name="s" value="">
	<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
	<input type="submit" name="" id="search-submit" class="button" value="Search 수신자 그룹"></p>

</form>
<br class="clear">

<div id="col-container">

<div id="col-right">
<div class="col-wrap">
<form id="frmGroup" action="" method="post">
<input type="hidden" name="page" value="sms4wp-group">
<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $delete_nonce; ?>">
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-group">
	
	<div class="tablenav top">

		<div class="alignleft actions bulkactions">
			<select name="action">
			<option value="-1" selected="selected"><?php _e('일괄작업') ?></option>
			<option value="delete"><?php _e('삭제') ?></option>
			</select>

			<input type="submit" name="" id="doaction" class="button action button-submitaction" value="Apply">
		</div>
		<div class="tablenav-pages"><span class="displaying-num"><?php echo number_format( $list['total'] ); ?> items</span>
			<?php echo $links; ?>
		</div>
		<br class="clear">
	</div>

	<table class="wp-list-table widefat fixed tags">
		<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox">
			</th>
			<th scope="col" id="name" class="manage-column column-name sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_name&amp;order=asc"><span>Name</span><span class="sorting-indicator"></span></a>
			</th>
			<th scope="col" id="description" class="manage-column column-description sortable" style="">
				<span>Description</span><span class="sorting-indicator"></span>
			</th>
			<!-- <th scope="col" id="slug" class="manage-column column-slug sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_use&amp;order=asc"><span>Use</span><span class="sorting-indicator"></span></a>
			</th> -->
			<th scope="col" id="posts" class="manage-column column-posts num sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_count&amp;order=asc"><span>수신자</span><span class="sorting-indicator"></span></a>
			</th>	
		</tr>
		</thead>

		<tfoot>
		<tr>
			<th scope="col" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox">
			</th>
			<th scope="col" class="manage-column column-name sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_name&amp;order=desc"><span>Name</span><span class="sorting-indicator"></span></a>
			</th>
			<th scope="col" class="manage-column column-description sortable" style="">
				<span>Description</span><span class="sorting-indicator"></span>
			</th>
			<!-- <th scope="col" class="manage-column column-slug sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_use&amp;order=desc"><span>Use</span><span class="sorting-indicator"></span></a>
			</th> -->
			<th scope="col" class="manage-column column-posts num sortable desc" style="">
				<a href="admin.php?page=sms4wp-group&amp;orderby=gr_count&amp;order=desc"><span>수신자</span><span class="sorting-indicator"></span></a>
			</th>	
		</tr>
		</tfoot>

		<tbody id="the-list" data-wp-lists="list:tag">
		
			<?php
			$group = $list['group'];
			// print_r( $group );

			for ( $c = 0; $c < count($group); $c++ ) { 
				// if ( $group[$c]['gr_use'] == 1 ) {
				// 	$group_use = '사용';
				// }
				// else {
				// 	$group_use = '<span class="file-error">미사용</span>';
				// }

				$class = 'alternate';
				if ( !($c%2) )
					$class = '';

				$depth      = 0;
				$depth_line = '';
				if ( $group[$c]['gr_depth'] > 1 ) {
					$depth = 15 * (intval( $group[$c]['gr_depth'] ) - 1);
					$depth_line = '- ';
				}
			?>
			<tr id="group-1" class="<?php echo $class; ?>">
				<th scope="row" class="check-column">
					<label class="screen-reader-text" for="cb-select-3">Select <?php echo $group[$c]['gr_name']; ?></label><input type="checkbox" name="id[]" class="checkbox-list" value="<?php echo $group[$c]['ID']; ?>">
				</th>
				<td class="name column-name">
					<strong><span style="display:inline-block;width:<?php echo $depth; ?>px;"></span><?php echo $depth_line; ?><a class="row-title" href="admin.php?action=edit&amp;page=sms4wp-group&amp;id=<?php echo $group[$c]['ID']; ?>&amp;action=edit" title="Edit <?php echo $group[$c]['gr_name']; ?>"><?php echo $group[$c]['gr_name']; ?></a></strong>
					<br>
					<div class="row-actions">
						<span class="edit"><a href="admin.php?page=sms4wp-group&amp;action=edit&amp;id=<?php echo $group[$c]['ID']; ?>&amp;action=edit">Edit</a> | </span>
						<span class="delete"><a class="button-submitdelete" href="admin.php?page=sms4wp-group&amp;action=delete&amp;id=<?php echo $group[$c]['ID']; ?>&amp;_wpnonce=<?php echo $delete_nonce; ?>">Delete</a> | </span>
					</div>
					<div class="hidden" id="inline_1">
						<div class="name"><?php echo $group[$c]['gr_name']; ?></div>
						<!-- <div class="use"><?php // echo $group_use; ?></div> -->
						<div class="parent">0</div>
					</div>
				</td>
				<td class="description column-description"><?php echo $group[$c]['gr_memo']; ?></td>
				<!-- <td class="use column-use"><?php // echo $group_use; ?></td> -->
				<td class="posts column-posts"><?php echo number_format( $group[$c]['receivers'] ); ?></td>
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

			<input type="submit" name="" id="doaction" class="button action button-submitaction" value="Apply">
		</div>
		<div class="tablenav-pages"><span class="displaying-num"><?php echo number_format( $list['total'] ); ?> items</span>
			<?php echo $links; ?>
		</div>
		<br class="clear">
	</div>

<br class="clear">
</form>

</div>
</div><!-- /col-right -->

<div id="col-left">
<div class="col-wrap">


<div class="form-wrap">
<h3>Add New 수신자 그룹</h3>
<form id="addGroup" method="post" action="admin.php?page=sms4wp-group" class="validate">
<input type="hidden" name="action" value="add_new">
<input type="hidden" name="page" value="sms4wp-group">
<input type="hidden" name="paged" value="<?php echo $paged; ?>">
<input type="hidden"  name="_wpnonce" value="<?php echo $update_nonce; ?>">
<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-group">
	<div class="form-field form-required">
		<label for="group-name">Name</label>
		<input name="gr_name" id="gr_name" type="text" value="" size="40" aria-required="true">
	</div>
	<div class="form-field">
		<label for="parent">Parent</label>
		<select name="gr_parent" id="gr_parent" class="postform">
			<option value="0"><?php _e('None') ?></option>
			<?php echo $parents; ?>
		</select>
	</div>
	<div class="form-field">
		<label for="group-description">Description</label>
		<textarea name="gr_memo" id="gr_memo" rows="5" cols="40"></textarea>
	</div>

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Group"></p></form>
</div>

</div>
</div><!-- /col-left -->

</div><!-- /col-container -->
</div>

<script type="text/javascript" src="<?php echo SMS4WP_INC_VIEW_JS_URL; ?>/jquery.common.js"></script>
