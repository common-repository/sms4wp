<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SMS_Processing_Order' ) ) :

/**
 * 새로운 주문에 SMS 보내기
 */
class WC_SMS_Processing_Order extends sms4wpAddOn {
	private $section;
	/**
	 * Constructor
	 */
	function __construct() {
		$this->section = 'wc_processing_order';

		// Triggers for this sms
		add_action( 'woocommerce_order_status_processing', array( $this, 'trigger' ) );
		// add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		// add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ) );

		// 설정정보
		add_action( 'sms4wp_plugin_woocommerce_menu', array( $this, 'get_navi_link' ) );
		add_action( 'sms4wp_plugin_woocommerce_menu_'. $this->section, array( $this, 'init_form_fields' ) );
	}

	/**
	 * Trigger.
	 */
	function trigger( $order_id ) {

		if ( ! $order_id ) {
			return;
		}

		$this->sms4wp_get_wc_sms_data( 'woocommerce', $this->section, $order_id );
	}

	/**
	 * sms settings form fields
	 */
	function init_form_fields() {
		$title = $this->get_form_title();
		$description = $this->get_form_description();

		$html = '<div class="sms_option_form">';
		$html .= '<h3>'. $title .'</h3>';
		$html .= '<input type="hidden" name="message_group" value="woocommerce" />';
		$html .= '<input type="hidden" name="message_receiver" value="'. $this->section .'" />';
		$html .= '<p>'. $description .'</p>';
		$html .= $this->sms_form( 'woocommerce', $this->section );
		$html .= '</div>';

		echo $html;
	}

	/**
	 * sms settings navi title a link
	 */
	function get_navi_link() {
		$title = $this->get_form_title();
		$adminUrl = $this->get_sms4wp_admin_url() .'&amp;tab=woocommerce&amp;section=';

		$cls_section = '';
		if ( isset($_GET['section']) && $_GET['section'] == $this->section )
			$cls_section = 'current';

		$link = '<li><a href="'. $adminUrl. $this->section .'" class="'. $cls_section .'">'. $title .'</a> | </li>';


		echo $link;
	}

	/**
	 * sms settings form title
	 */
	function get_form_title() {
		$title = __( 'Processing order', 'woocommerce' );

		return $title;
	}

	/**
	 * sms settings form description
	 */
	function get_form_description() {
		$description = __( 'This is an order notification sent to customers containing their order details after payment.', 'woocommerce' );

		return $description;
	}
}

endif;

return new WC_SMS_Processing_Order();
