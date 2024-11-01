<?php
/**
Plugin Name: woocommerce
Description: Send SMS messages from woocommerce!
Version: 0.5
Author: sms4wp.com
Author URI: https://sms4wp.com/
Text Domain: woocommerce/woocommerce.php
*/
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! function_exists('get_wc_sms_options') ) :
	/**
	 * 우커머스에서 사용할 SMS문자발송시 옵션들
	 * @return [string] [옵션사용법]
	 */
	function get_wc_sms_options() {
		ob_start();
?>
<div class="local_desc01 local_desc">
    <p>
        <dl>
            <dt><?php _e('메시지작성'); ?></dt>
            <dd>
				{<?php _e('blogname'); ?>}: <?php _e('사이트명'); ?><br />
				{<?php _e('order_name'); ?>}: <?php _e('주문자명'); ?><br />
				{<?php _e('order_content'); ?>}: {<?php _e('상품명'); ?>} | {<?php _e('상품명 외 O건'); ?>}<br />
				{<?php _e('order_date'); ?>}: <?php _e('주문 날짜'); ?><br />
				{<?php _e('order_id'); ?>}: <?php _e('주문ID'); ?><br />
				{<?php _e('order_number'); ?>}: <?php _e('주문 번호'); ?><br />
				{<?php _e('order_total'); ?>}: <?php _e('결제 금액'); ?><br />
				{<?php _e('order_status'); ?>}: <?php _e('주문 상태'); ?><br />
            </dd>
        </dl>
        <p><span class="frm_info">
        	<?php _e('주의! (영문 한글자 : 1byte , 한글 한글자 : 2bytes , 특수문자의 경우 1 또는 2 bytes 임)'); ?><br />
        	<?php _e('SMS 발송시 80 bytes 까지만 전송됩니다.'); ?> <br />
        	<?php _e('SMS 발송시에는 제목이 포함되지 않습니다.'); ?> <br />
        	<?php _e('LMS 발송시 내용 2000byte 까지만 전송됩니다.'); ?>
        	<?php //_e('MMS 발송시 첨부파일은 TXT 2000byte, JPG 20kbyte 이하 파일 전송가능 합니다.'); ?>
        </span></p>
    </p>
</div>
<?php 
		$options = ob_get_contents();
		ob_clean();

		echo $options;
	}

	add_action( 'sms4wp_plugin_woocommerce_information', 'get_wc_sms_options' );
endif;