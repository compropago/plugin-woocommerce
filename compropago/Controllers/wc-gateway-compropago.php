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
	
	private $compropagoConfig;
	private $compropagoClient;
	private $compropagoService;
	
	public function __construct(){
		$this->id='compropago';
		$this->has_fields=true;
		$this->method_title='ComproPago';
		$this->method_description='Recibe pagos en efectivo en tu tienda a traves de la red más grande de puntos de cobro para que tus clientes paguen por tus productos o servicios.';
		
		$this->init_form_fields();
		$this->init_settings();
		
		// Get setting values
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		$this->showlogo 	= $this->settings['showlogo'];
		
		$this->publickey 	= $this->settings['publickey'];
		$this->privatekey 	= $this->settings['privatekey'];
		
		$this->modopruebas 	= $this->settings['modopruebas'];
		
		//paso despues de selccion de gateway
		$this->has_fields	= true;
		
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		
		
	}
	/**
	 * @return boolean
	 */
	private function isLive(){
		if($this->modopruebas=='no'){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * setup admin page
	 */
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
			'showlogo' => array(
					'title' => __( 'Estilo', 'woocommerce' ),
					'label' => __( 'Activar Logos', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Activa o desactiva los logos de las empresas en donde realizar el pago ', 'woocommerce' ),
					'default' => 'no'
			),
			'publickey' => array(
					'title' => __( 'Llave Pública' ),
					'type' => 'text',
					'description' => __( 'Obten tu llave pública: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'woocommerce' ),
					'default' => '',
					'css' => "width: 300px;"
			),
			'privatekey' => array(
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
					'description' => __( 'Al activar el Modo de pruebas <b>es necesario que <span style="color:red;">cambie sus llaves por las de Modo Prueba</span></b>, Obten tus llaves en: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'woocommerce' ),
					'default' => 'no'
			)
		);
	}
	
	/**
	 * handling payment and processing the order
	 * @param unknown $order_id
	 * @return array
	 * @throws Compropago\Exception
	 * https://docs.woothemes.com/document/payment-gateway-api/
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );
		
		// estatus en de la orden onhold, webhook actualizara a confirmacion de pago
		$order->update_status('on-hold', __( 'Esperando pago por ComproPago', 'woocommerce' ));
		
		// Reduce stock levels
		$order->reduce_order_stock();
		
		// Remove cart
		$woocommerce->cart->empty_cart();
		
		// Return thankyou redirect
		return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
		);
	}
	
	public function payment_fields(){
		if(!class_exists('CP_Views')){
			require __DIR__ . '/CP_Views.php';
		}
		
		try{
			$this->compropagoConfig = array('publickey'=>$this->publickey,'privatekey'=>$this->privatekey,'live'=>$this->isLive());
			$this->compropagoClient = new Compropago\Client($this->compropagoConfig);
			$this->compropagoService = new Compropago\Service($this->compropagoClient);
			
			
		} catch (Exception $e) {
			wc_add_notice( __('Compropago error:', 'woothemes') . $e->getMessage(), 'error' );
			return;
		    
		}
		$data['providers']=$this->compropagoService->getProviders();
		$data['showlogo']=$this->showlogo;
		
		CP_Views::loadView('proveedores', $data);
	}
	
	public function validate_fields() {
		
	}
}