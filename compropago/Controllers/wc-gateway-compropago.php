<?php
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
class WC_Gateway_Compropago extends WC_Payment_Gateway {
	// Go wild in here
	public function __construct(){
		$this->id='compropago';
		$this->has_fields=true;
		$this->method_title='ComproPago';
		$this->method_description='Recibe pagos en efectivo en tu tienda a traves de la red más grande de puntos de cobro para que tus clientes paguen por tus productos o servicios.';
		
		
		
		
		$this->init_form_fields();
		$this->init_settings();
		
		// Get setting values
		$this->title 				= $this->settings['title'];
		$this->description 			= $this->settings['description'];
		
		$this->publickey 			= $this->settings['publickey'];
		$this->privatekey 			= $this->settings['privatekey'];
		
		$this->modopruebas 			= $this->settings['modopruebas'];
		
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
	
	public function init_form_fields(){
		$this->form_fields=array(
			'enabled' => array(
					'title'			=> __( 'Enable/Disable', 'woocommerce' ),
					'label' 		=> __( 'Enable Compropago', 'woocommerce' ),
					'type' 			=> 'checkbox',
					'description'	=> 'Activar ComproPago como método de pago. (<a href="https://compropago.com/" target="_new">Registrarse en Compropago</a>)',
					'default' 		=> 'no'
			),
			'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					'default' => __( 'ComproPago', 'woocommerce' ),
			),
				
			'description' => array(
					'title' => __( 'Description', 'woocommerce' ),
					'type' => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
					'default' => "Con ComproPago puedes realizar tu pago en OXXO, 7Eleven y muchas tiendas más",
			),
			'publickey' => array(
					'title' => __( 'Llave Pública' ),
					'type' => 'text',
					'description' => __( 'Obten tu llave pública: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'woocommerce' ),
					'default' => '',
					'css' => "width: 300px;"
			),
			'private' => array(
					'title' => __( 'Llave Privada' ),
					'type' => 'text',
					'description' => __( 'Obten tu llave privada: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'woocommerce' ),
					'default' => '',
					'css' => "width: 300px;"
			),
			'modopruebas' => array(
					'title' => __( 'Modo de Pruebas', 'woocommerce' ),
					'label' => __( 'Activar modo pruebas', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Al activar el Modo de pruebas, <b>es necesario que cambie sus llaves por las de <span style="color:red;">Modo Prueba</span></b>, Obten tus llaves en: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'woocommerce' ),
					'default' => 'no'
			),
		);
	}
}