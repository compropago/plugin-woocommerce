<?php
/*
 Plugin Name: ComproPago 
 Plugin URI: https://www.compropago.com/documentacion/plugins
 Description: Con ComproPago puedes recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México.
 Version: 3.0.0
 Author: Compropago <contacto@compropago.com>
 Author URI: https://www.compropago.com/
 */
/*
* Copyright 2015 Compropago. 
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @since 3.0.0
 */

//iniciamos el plugin
add_action('plugins_loaded', 'woocommerce_compropago_init', 0);

//load Compropago SDK & dependecies
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ){
	require __DIR__ . '/vendor/autoload.php';
}



/*
 * Do something after WooCommerce sets an order on completed
 *//*
add_action( 'woocommerce_order_status_completed', 'compropago_complete' );

function compropago_complete($order_id) {

	//compropago set to hold

}*/


//hook css
add_action( 'wp_enqueue_scripts', 'compropago_css' );
/**
 * compropago css file
 * @since 3.0.0
 */
function compropago_css() {
	wp_register_style( 'prefix-style', plugins_url('vendor/compropago/php-sdk/assets/css/compropago.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}
/**
 * compropago init plugin
 * @since 3.0.0
 */
function woocommerce_compropago_init() {
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
	/**
	 * Localisation
	 */
	load_plugin_textdomain('wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

	/**
	 * Gateway class
	*/
	require_once 'includes/wc-gateway-compropago.php';

	/**
	 * Add the Gateway to WooCommerce
	 **/
	function add_compropago_gateway($methods) {
		$methods[] = 'WC_Gateway_Compropago';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'add_compropago_gateway' );
}	
