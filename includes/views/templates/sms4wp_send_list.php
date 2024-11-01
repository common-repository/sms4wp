<link rel="stylesheet" href="<?php echo SMS4WP_INC_VIEW_CSS_URL; ?>/sms4wp.css" />

<div class="wrap">
<h2>
<?php _e('전송내역') ?>	<a href="admin.php?page=sms4wp-send" class="add-new-h2">New Message</a>
</h2>

<ul class="subsubsub">
	<li class="all"><a href="admin.php?page=sms4wp-send-list" class="current"><?php _e('모두') ?> <span class="count">(<?php echo $list['total']; ?>)</span></a> |</li>
	<li class="administrator"><a href="admin.php?page=sms4wp-send-list&amp;sf=se_result_code&amp;ss=200"><?php _e('성공') ?> <span class="count">(<?php echo $list['successCnt']; ?>)</span></a> | </li>
	<li class="administrator"><a href="admin.php?page=sms4wp-send-list&amp;sf=se_result_code&amp;ss="><?php _e('실패') ?> <span class="count">(<?php echo $list['failedCnt']; ?>)</span></a></li>
</ul>
<form action="admin.php" method="get">

<p class="search-box">
	<label class="screen-reader-text" for="user-search-input"><?php _e('검색') ?>:</label>
	<input type="search" id="user-search-input" name="s" value="">
	<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged']; ?>">
	<input type="hidden" name="sf" value="<?php echo $_REQUEST['sf']; ?>">
	<input type="hidden" name="ss" value="<?php echo $_REQUEST['ss']; ?>">
	<input type="submit" name="" id="search-submit" class="button" value="<?php _e('검색') ?>"></p>

	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $delete_nonce; ?>">
	<input type="hidden" name="_wp_http_referer" value="admin.php?page=sms4wp-send-list">	
	<input type="hidden" name="page" value="sms4wp-send-list">

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
		<th scope="col" id="username" class="manage-column column-role sortable" style=""><span><?php _e('이름') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="name" class="manage-column column-num num" style=""><span><?php _e('수신자번호') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="name" class="manage-column column-num num" style=""><span><?php _e('발신번호') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="email" class="manage-column column-visible" style=""><span><?php _e('그룹') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" id="posts" class="manage-column column-date" style=""><?php _e('전송일시') ?></th>
		<th scope="col" id="role" class="manage-column column-rating" style=""><?php _e('예약') ?></th>
		<th scope="col" id="role" class="manage-column column-rating" style=""><?php _e('전송결과') ?></th>
		<th scope="col" id="role" class="manage-column column-role" style=""><?php _e('메시지') ?></th>
		<th scope="col" class="manage-column column-rating" style=""><?php _e('종류') ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></th>
		<th scope="col" class="manage-column column-role sortable" style=""><span><?php _e('이름') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-num num" style=""><span><?php _e('수신자번호') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-num num" style=""><span><?php _e('발신번호') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-visible" style=""><span><?php _e('그룹') ?></span><span class="sorting-indicator"></span></th>
		<th scope="col" class="manage-column column-date" style=""><?php _e('전송일시') ?></th>	
		<th scope="col" class="manage-column column-rating" style=""><?php _e('예약') ?></th>
		<th scope="col" class="manage-column column-rating" style=""><?php _e('전송결과') ?></th>
		<th scope="col" class="manage-column column-role" style=""><?php _e('메시지') ?></th>
		<th scope="col" class="manage-column column-rating" style=""><?php _e('종류') ?></th>
	</tr>
	</tfoot>

	<tbody id="the-list" data-wp-lists="list:user">
	<?php
	$send = $list['send'];
	$offset = get_option('gmt_offset');
	// print_r( $send );
	 date_default_timezone_set("Asia/Seoul");

	for ( $c = 0; $c < count($send); $c++ ) { 
		$class = 'alternate';
		if ( !($c%2) )
			$class = '';

		$group_name  = ''; // 그룹명
		$reservation = $send[$c]['se_reservation_use'] ? $send[$c]['se_reservation_date'] : 'no';
		$send_result = '진행';
		switch ( $send[$c]['se_result_code'] ) {
			case '200': $send_result = '성공';
				break;
			case '400': $send_result = '파라미터 에러';
				break;
			case '401': case '403': $send_result = '인증 토큰 에러';
				break;
			case '404': $send_result = '서버 자원 없음';
				break;
			case '405': $send_result = 'METHOD 오류';
				break;
			case '406': $send_result = '포인트가 부족';
				break;
			case '415': $send_result = '첨부파일 미지원';
				break;
			case '500': $send_result = '프로그램 에러';
				break;
			case '501': $send_result = 'API 에러';
				break;
		}

		$receiver = array();
		if ( $send[$c]['re_id'] ) {
			$receiver   = sms4wp_get_receiver( $send[$c]['re_id'] );
			$group_name = $groups[$receiver['gr_id']]['gr_name'];
		}

		$se_time = strtotime( $send[$c]['se_date'] ) + $offset * 3600;
		//$sendDateTime = date( 'Y-m-d H:i:s', $se_time );
		$sendDateTime = $send[$c]['se_date'];
	?>
	<tr id="user-1" class="<?php echo esc_attr($class); ?>">
		<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-1">Select <?php echo esc_html($send[$c]['ID']); ?></label><input type="checkbox" name="id[]" id="send_<?php echo esc_attr($send[$c]['ID']); ?>" class="checkbox-list" value="<?php echo esc_attr($send[$c]['ID']); ?><"></th>
		<td class="username column-role"> <strong><a href="admin.php?page=sms4wp-receivers&amp;action=edit&amp;id=<?php echo $receiver['ID']; ?>"><?php echo esc_html($receiver['re_name']); ?></a></strong></td>
		<td class="role column-num num"><?php echo esc_html($send[$c]['se_receiver_number']); ?></td>
		<td class="role column-num num"><?php echo esc_html($send[$c]['se_send_number']); ?></td>
		<td class="role column-visible"><?php echo esc_html($group_name); ?></td>
		<td class="role column-date"><?php echo esc_html($sendDateTime); ?></td>
		<td class="role column-rating"><?php echo esc_html($reservation); ?></td>
		<td class="role column-rating"><?php echo esc_html($send_result); ?></td>
		<td class="role column-role"><?php echo esc_html($send[$c]['se_message']); ?></td>
		<td class="role column-rating"><?php echo esc_html($send[$c]['se_type']); ?></td>
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
