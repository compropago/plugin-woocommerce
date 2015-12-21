<?php
/*
 Plugin Name: Compropago 
 Plugin URI: https://www.compropago.com/documentacion/plugins
 Description: Recibe pagos en efectivo en tu tienda a traves de la red más grande de puntos de cobro para que tus clientes paguen por tus productos o servicios.
 Version: 1.0.0-dev
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
 */


add_action('plugins_loaded', 'woocommerce_compropago_init', 0);

//load Compropago SDK & dependecies
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ){
	require __DIR__ . '/vendor/autoload.php';
}
	

function woocommerce_compropago_init() {
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
	/**
	 * Localisation
	 */
	load_plugin_textdomain('wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

	/**
	 * Gateway class
	 */
	class WC_Compropago extends WC_Payment_Gateway {

		// Go wild in here
	}

	/**
	 * Add the Gateway to WooCommerce
	 **/
	function woocommerce_add_compropago_gateway($methods) {
		$methods[] = 'WC_Compropago';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'woocommerce_add_compropago_gateway' );
}	
