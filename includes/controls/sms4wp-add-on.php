<?php
if ( !defined('ABSPATH') ) exit;

if ( ! class_exists('sms4wpAddOn') ):
	/**
	* 외부플러그인에 SMS 발송 위한 기본 클래스
	*/
	class sms4wpAddOn {
		private static $plugin_path = SMS4WP_PLUG_PATH; // 플러그인 절대 경로
		private static $admin_page = 'sms4wp-configure'; // 설정메뉴 페이지 이름
		// private $wc_file_name = ''; // 문자메시지 첨부파일 이름 
		
		public function __construct() {
			$this->init_hooks();
			$this->include_plugin_messages();
		}

		public function init_hooks() {
			// add_action( 'init', array( $this, 'include_plugin_messages') );

			if ( is_admin() ) {
				add_action( 'init', array( $this, 'update_sms_template') );
			}
		}

		/**
		 * sms4wp 어드민 설정주소 
		 * @return [string] [sms4wp url]
		 */
		public function get_sms4wp_admin_url() {
			return admin_url( 'admin.php' ) .'?page='. self::$admin_page;
		}

		/**
		 * sms4wp에서 지원하게될 외부 플러그인 한개의 SMS 메시지 정보 출력하기
		 * @param  [string] $dir_name  [플러그인 폴더명]
		 * @return [string]            [SMS 메시지 폼]
		 */
		public static function get_plugin_options( $dir_name ) {
			global $sms4wp_config;

			$plugin_data = array();
			$plugin_file = self::$plugin_path .'/'. $dir_name . '/' . $dir_name . '.php';
			if ( file_exists($plugin_file) ) {
				$plugin_data = get_plugin_data( $plugin_file ); // 플러그인 정보

				$plugin_data['sms4wp_plugin_checked'] = '';
				if ( in_array( $dir_name, $sms4wp_config['sms4wp_plugin'] ) )
					$plugin_data['sms4wp_plugin_checked'] = 'checked';
			}

			return $plugin_data;
		}

		/**
		 * sms4wp에서 지원하게될 외부 플러그인의 기본 정보
		 * @return [array] [애드온 플러그인 정보]
		 */
		public static function get_plugins() {
			global $sms4wp_config;

			$plugin_data = array();
			if ( $current_dir = @opendir(self::$plugin_path) ) :
				while ( false !== ($dir_name = readdir($current_dir)) ) {
					if ( ! is_dir($dir_name) ) {
						$plugin_data[$dir_name] = self::get_plugin_options( $dir_name );
					}
				}

				@closedir($current_dir);
			endif;

			return $plugin_data;
		}

		/**
		 * sms4wp에서 지원하게될 외부 플러그인에서 사용할 SMS 폴더 열기
		 * @return [boolean]
		 */
		public function include_plugin_messages() {
			$require_files = array();

			if ( $current_dir = @opendir( self::$plugin_path ) ) :
				while ( false !== ($dir_name = readdir($current_dir)) ) {
					$sms_dir = self::$plugin_path .'/'. $dir_name .'/sms';

					$require_files = $this->get_plugin_files( $sms_dir );
					self::include_plugin_files( $require_files );
				}

				@closedir($current_dir);
			endif;

			return true;
		}

		/**
		 * 특정 폴더의 파일 불러오기
		 * @param  [array] $require_files  [외부 플러그인 문자메시지 파일]
		 * @return [boolean]
		 */
		public static function include_plugin_files( $require_files ) {
			if ( ! empty($require_files) && is_array($require_files) ) :
				foreach ( $require_files as $key => $file_path ) {
					$file_path = self::get_root_path($file_path);
					@include_once( $file_path );
				}
			elseif ( ! is_array($require_files) && ! empty($require_files) ) :
				$require_files = self::get_root_path($require_files);
				@include_once( $require_files );
			endif;

			return true;
		}

		/**
		 * 특정 폴더의 파일 불러올때 절대경로 지정하기
		 * @param  [array] $require_files  [외부 플러그인 문자메시지 파일]
		 * @return [string]
		 */
		private static function get_root_path( $require_files ) {
			$require_files = self::$plugin_path . $require_files;
			if ( ! file_exists($require_files) )
				$require_files = '';

			return $require_files;
		}

		/**
		 * sms4wp에서 지원하게될 외부 플러그인에서 사용할 기본설정 파일 불러오기
		 * @param  [string] $sms_dir  [외부 플러그인 문자메시지 폴더]
		 * @return [array]            [플러그인 메시지폴더 파일 목록]
		 */
		public function get_plugin_files( $sms_dir ) {
			$require_files = array();

			if ( is_dir($sms_dir) ) :
				if ( $sms_dir_list = @opendir( $sms_dir ) ) {
					while ( $file_name = @readdir($sms_dir_list) ) {
						$sms_file = $sms_dir .'/'. $file_name;
						if ( is_file($sms_file) ) {
							$sms_file = str_replace( self::$plugin_path, '', $sms_file );
							$require_files[] = $sms_file;
						}
					}

					@closedir($sms_dir_list);
				}
			endif;

			return $require_files;
		}

		/**
		 * sms4wp 보낼 문자메시지 폼 출력하기
		 * @param  [string]   $group     [문자발송 그룹]
		 * @param  [string]   $receiver  [문자구분 키값]
		 * @return [array]               [플러그인 메시지폴더 파일 목록]
		 */
		public function sms_form( $group, $receiver ) {
			global $sms4wp_config;

			$rows = $this->sms4wp_get_wc_templates( $group, $receiver );

			$data = array();
			foreach ( $rows as $key => $row ) {
				$data[$key]['message_use'] = isset($row['te_use']) && $row['te_use'] ? 'checked' : '';
				$data[$key]['message_subject'] = isset($row['te_subject']) ? esc_attr($row['te_subject']) : '';
				$data[$key]['message_body'] = isset($row['te_message']) ? esc_attr($row['te_message']) : '';
				$data[$key]['message_sender_number'] = isset($row['te_sender_number']) ? esc_attr($row['te_sender_number']) : '';
			}

			$sms_form = '
<input type="hidden" name="action" value="addon_template" />
<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_use">활성화/비활성화</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>활성화/비활성화</span></legend>
				<label for="message_use">
				<input class="" type="checkbox" name="message_use[1]" style="" value="1" '. $data[1]['message_use'] .' /> 사용자에게 문자메시지를 발송합니다.</label><br>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_sender_number">발신번호</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>발신번호</span></legend>';
			$sms_form .= sms4wp_get_select_reply_number( 'message_sender_number[1]', $data[1]['message_sender_number'] );
			$sms_form .= '
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_subject">제목</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>제목</span></legend>
				<input class="input-text regular-input " type="text" name="message_subject[1]" style="min-width:300px;" value="'. $data[1]['message_subject'] .'" placeholder="">
				<p class="description">단문메시지(SMS) 발송 시에는 제목이 포함되지 않습니다.</p>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_body">메시지 본문</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>메시지 본문</span></legend>
				<textarea rows="3" cols="20" class="input-text wide-input " type="textarea" name="message_body[1]" style="min-width:300px;" placeholder="">'. $data[1]['message_body'] .'</textarea>
				<p class="description">80자가 넘을 경우에는 장문메시지(LMS)로 발송됩니다.</p>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2" style="height: 20px;"><hr></td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_use">활성화/비활성화</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>활성화/비활성화</span></legend>
				<label for="message_use">
				<input class="" type="checkbox" name="message_use[2]" style="" value="1" '. $data[2]['message_use'] .' /> 관리자에게 문자메시지를 발송합니다.</label><br>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_sender_number">발신번호/수신번호</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>발신번호</span></legend>';
			$sms_form .= sms4wp_get_select_reply_number( 'message_sender_number[2]', $data[2]['message_sender_number'] );
			$sms_form .= '
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_subject">제목</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>제목</span></legend>
				<input class="input-text regular-input " type="text" name="message_subject[2]" style="min-width:300px;" value="'. $data[2]['message_subject'] .'" placeholder="">
				<p class="description">단문메시지(SMS) 발송 시에는 제목이 포함되지 않습니다.</p>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="message_body">메시지 본문</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span>메시지 본문</span></legend>
				<textarea rows="3" cols="20" class="input-text wide-input " type="textarea" name="message_body[2]" style="min-width:300px;" placeholder="">'. $data[2]['message_body'] .'</textarea>
				<p class="description">80자가 넘을 경우에는 장문메시지(LMS)로 발송됩니다.</p>
			</fieldset>
		</td>
	</tr>
	</tbody>
</table>
			';

			return $sms_form;
		}



		/**
		 * 우커머스 서브스크립션 구독만료/다음결제일 알림 문자발송위해 발송데이터 생성
		 * @param  [string] $group    [문자발송 그룹]
		 * @param  [string] $receiver [문자구분 키값]
		 * @param  [int]    $order_id [주문번호]
		 * @return [boolean]          
		 */
		public function sms4wp_get_wc_subs_sms_data( $group, $receiver, $subs_id ) {
			$data = array();
			$pattern = array();
			
			$rows = $this->sms4wp_get_wc_templates( $group, $receiver );
			$subscription = new WC_Subscription( $subs_id );

			foreach ($rows as $key => $row) :
				if ( ! $row['te_use'] ) {
					continue;
				}

				$product_name = '';
				$product_cnt = 0;
				foreach( $subscription->get_items() as $item_id => $item ){
					if ( ! $product_name )
					    $product_name = $item['name'];
					else {
						$product_cnt++;
					}					
				}				

				if ( $product_cnt > 0 )
					$product_name .= ' 외 '. $product_cnt .'건';

				$subs_name = '';
				if ( isset($subscription->billing_first_name) && ! empty($subscription->billing_first_name) ) {
					$subs_name = $subscription->billing_first_name;
				}
				if ( isset($subscription->billing_last_name) && ! empty($subscription->billing_last_name) ) {
					$subs_name .= ' '. $$subscription->billing_last_name;
				}

				$pattern['blogname']      		= get_bloginfo( 'name' ); // 사이트명
				$pattern['subs_name']     		= $subs_name; // 주문자명
				$pattern['subs_content']  		= $product_name; // {상품명} | {상품명 외 O건}
				$pattern['subs_date']     		= $subscription->get_date('start', 'site'); // 구독 시작날짜
				$pattern['subs_id']       		= $subscription->ID; // 구독ID
				$pattern['subs_total']   		= $subscription->get_total(); // 결제 금액
				$pattern['subs_status']  		= $subscription->status; // 주문 상태
				$pattern['subs_expire_date'] 	= $subscription->get_date('end', 'site'); //구독 만료날짜
				$pattern['subs_next_payment']   = $subscription->get_date('next_payment', 'site'); //다음 결제일

				$receiver_phone = '';
				$receiver_name = '';
				switch ( $row['te_type'] ) {
					case '1': # 고객에게 발송
						$receiver_phone = $subscription->billing_phone;
						$receiver_name = $pattern['subs_name'];
						break;
					case '2': # 관리자에게 발송
						$receiver_phone = $row['te_sender_number'];
						$receiver_name = $row['te_sender_name'];
						break;
				}

				$data['nonce']           = self::get_create_nonce(); // nonce 데이터
				$data['sender_phone']    = $row['te_sender_number']; // 보내는 사람 전화번호
				$data['sender_name']     = $row['te_sender_name']; // 보내는 사람 이름
				$data['message_type']    = 'SMS'; // 메시지 종류(SMS, LMS, MMS)
				$data['message_subject'] = $row['te_subject']; // 보내는 메시지 제목
				$data['message_body']    = $row['te_message']; // 보내는 메시지
				$data['receiver_phone']  = $receiver_phone; // 받는 사람 전화번호
				$data['receiver_name']   = $receiver_name; // 받는 사람 이름
				$data['re_id']           = ''; // 수신자 관리 아이디 번호
				$data['pattern']         = $pattern; // 보내는 메시지 치환값 array( 'pattern'=>'replacement' )

				sms4wp_send_message( $data ); // 문자보내기
			endforeach;
		}			

		/**
		 * 우커머스에서 문자발송위해 발송데이터 생성
		 * @param  [string] $group    [문자발송 그룹]
		 * @param  [string] $receiver [문자구분 키값]
		 * @param  [int]    $order_id [주문번호]
		 * @return [boolean]          
		 */
		public function sms4wp_get_wc_sms_data( $group, $receiver, $order_id ) {
			$data = array();
			$pattern = array();
			
			$rows = $this->sms4wp_get_wc_templates( $group, $receiver );
			$order = wc_get_order( $order_id );


			foreach ($rows as $key => $row) :
				if ( ! $row['te_use'] ) {
					continue;
				}

				$product_name = '';
				$product_cnt = 0;
				foreach ( $order->get_items() as $item ) {
					if ( ! $product_name )
					    $product_name = $item['name'];
					else {
						$product_cnt++;
					}
				}

				if ( $product_cnt > 0 )
					$product_name .= ' 외 '. $product_cnt .'건';

				$order_name = '';
				if ( isset($order->billing_first_name) && ! empty($order->billing_first_name) ) {
					$order_name = $order->billing_first_name;
				}
				if ( isset($order->billing_last_name) && ! empty($order->billing_last_name) ) {
					$order_name .= ' '. $order->billing_last_name;
				}

				$pattern['blogname']      = get_bloginfo( 'name' ); // 사이트명
				$pattern['order_name']    = $order_name; // 주문자명
				$pattern['order_content'] = $product_name; // {상품명} | {상품명 외 O건}
				$pattern['order_date']    = $order->order_date; // 주문 날짜
				$pattern['order_id']      = $order->id; // 주문ID
				$pattern['order_number']  = $order->id; // 주문 번호
				$pattern['order_total']   = $order->get_total(); // 결제 금액
				$pattern['order_status']  = $order->status; // 주문 상태

				$receiver_phone = '';
				$receiver_name = '';
				switch ( $row['te_type'] ) {
					case '1': # 고객에게 발송
						$receiver_phone = $order->billing_phone;
						$receiver_name = $pattern['order_name'];
						break;
					case '2': # 관리자에게 발송
						$receiver_phone = $row['te_sender_number'];
						$receiver_name = $row['te_sender_name'];
						break;
				}

				$data['nonce']           = self::get_create_nonce(); // nonce 데이터
				$data['sender_phone']    = $row['te_sender_number']; // 보내는 사람 전화번호
				$data['sender_name']     = $row['te_sender_name']; // 보내는 사람 이름
				$data['message_type']    = 'SMS'; // 메시지 종류(SMS, LMS, MMS)
				$data['message_subject'] = $row['te_subject']; // 보내는 메시지 제목
				$data['message_body']    = $row['te_message']; // 보내는 메시지
				$data['receiver_phone']  = $receiver_phone; // 받는 사람 전화번호
				$data['receiver_name']   = $receiver_name; // 받는 사람 이름
				$data['re_id']           = ''; // 수신자 관리 아이디 번호
				$data['pattern']         = $pattern; // 보내는 메시지 치환값 array( 'pattern'=>'replacement' )

				sms4wp_send_message( $data ); // 문자보내기
			endforeach;
		}

		/**
		 * 우커머스 주문상태별 보낼 문자메시지 정보 저장하기
		 * @param  [array] $post  [문자메시지 저장 정보]
		 * @param  [files] $files [첨부파일]
		 * @return [null]        
		 */
		public function update_sms_template() {
			$admin_page = isset($_GET['page']) ? $_GET['page'] : '';
			$action = isset($_POST['action']) ? $_POST['action'] : '';

			if ( $action == 'addon_template' && $admin_page == self::$admin_page ) {
			    $this->sms4wp_update_wc_templates( $_POST, $_FILES );
			}
		}

		/**
		 * 템플릿 테이블에 저장된 주문상태별 보낼 문자메시지 정보 불러오기
		 * @param  [string] $te_group     [템플릿 테이블의 그룹명]
		 * @param  [string] $te_receiver  [템플릿 테이블의 수신자 구분 값]
		 * @param  [int]    $te_type      [문자 수신자 구분: 2-관리자에게 발송, 1-고객에게 발송]
		 * @return [array]
		 */
		public function sms4wp_get_wc_templates( $te_group, $te_receiver = '', $te_type = '' ) {
			global $wpdb;

			$list = array();

			if ( ! $te_group )
				return $list; // woocommerce

			$query = /** @lang text */
				" SELECT * FROM " . SMS4WP_TEMPLATE_TABLE . " WHERE te_group = %s ";
			$query = $wpdb->prepare( $query, $te_group );
			$gubun = 'te_receiver';

			if ( $te_receiver ) {
				$query .= " AND te_receiver = %s ";
				$query = $wpdb->prepare( $query, $te_receiver );
				$gubun = 'te_type';
			}

			if ( $te_type ) {
				$query .= " AND te_type = %s ";
				$query = $wpdb->prepare( $query, $te_type );
			}

			$query .= " GROUP BY te_group, te_receiver, te_type ";

			$rows = $wpdb->get_results( $query, ARRAY_A );

			foreach ($rows as $key => $row) {
				$list[$row[$gubun]] = $row;
			}

			if ( $te_type )
				return $list[$te_type];
			else 
				return $list;
		}

		/**
		 * 템플릿 테이블에 보낼 문자메시지 정보 저장하기
		 * @param  [array] $post  [문자메시지 저장 정보]
		 * @param  [file]  $files [첨부파일]
		 * @return [null]        
		 */
		public function sms4wp_update_wc_templates( $posts, $files ) {
			global $wpdb;

			if ( ! ( is_admin() && current_user_can('administrator') ) )
				return false;

			$message_cnt      = 2;
			$message_receiver = isset($posts['message_receiver']) ? $posts['message_receiver'] : '';
			$message_group    = isset($posts['message_group']) ? $posts['message_group'] : ''; // 템플릿 테이블 그룹정보

			for ( $c = 1; $c <= $message_cnt; $c++ ) :
				$message_use           = isset($posts['message_use'][$c]) ? $posts['message_use'][$c] : '';
				$message_sender_number = isset($posts['message_sender_number'][$c]) ? $posts['message_sender_number'][$c] : '';
				$message_sender_name   = isset($posts['message_sender_name'][$c]) ? $posts['message_sender_name'][$c] : '';
				$message_type          = $c;
				$message_subject       = isset($posts['message_subject'][$c]) ? $posts['message_subject'][$c] : '';
				$message_body          = isset($posts['message_body'][$c]) ? $posts['message_body'][$c] : '';


				if ( ! $message_receiver || ! $message_group )
					return false;

				$wc_file_name = $message_group;

				$row = $this->sms4wp_get_wc_templates( $message_group, $message_receiver, $message_type );

				$tmp_file = isset($files['add_file1']['tmp_name']) ? $files['add_file1']['tmp_name'] : '';
			    $filesize = isset($files['add_file1']['size']) ? $files['add_file1']['size'] : '';
			    $filename = isset($files['add_file1']['name']) ? $files['add_file1']['name'] : '';

			    $message_file1 = $row['te_file1'];
			    $message_file2 = $row['te_file2'];

				//if ( $add_file1_delete && $row['te_file1'] ) {
		        	//@unlink( SMS4WP_DATA_PATH . '/' . $row['te_file1'] );
		        	//$message_file1 = '';
				//}

			    if ( is_uploaded_file($tmp_file) ) {
			    	if ( $filesize > SMS4WP_FILE_MMS_LIMIT ) {
			            echo '<div class="file-upload-error">"'.$filename.'" 파일의 용량('.number_format( $filesize / 1024 ).' Kb)이 ('.number_format( SMS4WP_FILE_MMS_LIMIT / 1024 ).' Kb) 값보다 크므로 업로드 하지 않습니다.</div>';
			        } else {
			        	$message_file1 = $wc_file_name . '1_' . $c . '.jpg';
			        	$dest_file = SMS4WP_DATA_PATH . '/' . $message_file1;
			        	@unlink( SMS4WP_DATA_PATH . '/' . $row['te_file1'] );
			        	move_uploaded_file( $tmp_file, $dest_file );
			        }
			    }

			    $commonFields = " te_use         = %d,
				                te_sender_number = %s,
				                te_sender_name   = %s,
				                te_receiver      = %s,
				                te_type          = %s,
				                te_group         = %s,
				                te_subject       = %s,
				                te_message       = %s,
				                te_file1         = %s,
				                te_file2         = %s ";

			    if ( $row['ID'] ) {
			    	$query = "UPDATE ".SMS4WP_TEMPLATE_TABLE." SET " . $commonFields . " WHERE ID = %d";
			    	$query = $wpdb->prepare($query, 
								$message_use,
								$message_sender_number,
								$message_sender_name,
								$message_receiver,
								$message_type,
								$message_group,
								$message_subject,
								$message_body,
								$message_file1,
								$message_file2,
								$row['ID'] );
			    }
			    else {
			    	$query = /** @lang text */
					    "INSERT INTO " . SMS4WP_TEMPLATE_TABLE . " SET " . $commonFields . ", te_date = %s ";
			    	$query = $wpdb->prepare($query, 
								$message_use,
								$message_sender_number,
								$message_sender_name,
								$message_receiver,
								$message_type,
								$message_group,
								$message_subject,
								$message_body,
								$message_file1,
								$message_file2,
								date('Y-m-d H:i:s') );
			    }

		        $wpdb->query( $query );
		    endfor;

		    do_action('sms4wp_update_wc_templates_after', $posts, $files);

			return true;
		}

		public static function get_create_nonce( $nonce = 'sms4wp_ajax_message_nonce' ) {
			$_nonce = wp_create_nonce( $nonce );
			return $_nonce;
		}

	}

	return new sms4wpAddOn();
endif;
