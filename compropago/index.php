<?php
/*
Plugin Name: WC Compropago Gateway
Description: The Compropago payment gateway plugin for WooCommerce, Therefore an SSL certificate is required to ensure your customer credit card details are safe.
Version: 0.1.0
Author: Rodrigo Ayala <rodrigo@compropago.com>
*/

add_action('plugins_loaded', 'woocommerce_compropago_init', 0);

function woocommerce_compropago_init() {

	if ( ! class_exists( 'Woocommerce' ) ) { return; }
	
	/**
 	 * Localication
	 */
	load_textdomain( 'woocommerce', 'langs/simplepay4u-'.get_locale().'.mo' );
	
	if(!defined('STRIPE_SDK')) {
		define('STRIPE_SDK', 1);
		require_once('wc-gateway-compropago.php');
	}
	
	require_once('includes/gateway-request.php');
	require_once('includes/gateway-response.php');
	
	/**
 	* Add the Gateway to WooCommerce
 	**/
	function add_compropago_gateway($methods) {
		$methods[] = 'WC_Gateway_Compropago';
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'add_compropago_gateway' );
}

function compropago_status_function ( $order_id, $status = 'processing' ) { 
	$order = new WC_Order($order_id);
	

	
	if( $status == 'processing' ){
		$order->payment_complete( $status );		
	}else{
		$order->update_status( $status );		
	}
	

	return true;
}

function request_data_compropago() {
	$body = @file_get_contents('php://input'); 
	$event_json = json_decode($body);

    // Almacenando los valores del JSON 
	$id = $event_json->data->object->{'id'};
    $status = $event_json->{'type'};
	if ( $status == 'charge.pending' ) {
		$status = 'pending';
	} elseif ( $status == 'charge.success' ) {
		$status = 'processing';
	}
    $product_id = $event_json->data->object->payment_details->{'product_id'};
	compropago_status_function( $product_id, $status );
	
	echo $body;
}
add_action('admin_post_nopriv_compropago', 'request_data_compropago' );
add_action('admin_post_compropago', 'request_data_compropago' );
