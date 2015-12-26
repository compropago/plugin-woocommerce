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
use Compropago\Client;
use Compropago\Service;
use Compropago\Controllers\Views;

class WC_Gateway_Compropago extends WC_Payment_Gateway {
	
	private $compropagoConfig;
	private $compropagoClient;
	private $compropagoService;
	
	private $orderProvider;
	
	public function __construct(){
		$this->id='compropago';
		$this->has_fields=true;
		$this->method_title='ComproPago';
		$this->method_description='Con ComproPago puedes recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México';
		
		$this->init_form_fields();
		$this->init_settings();
		
		// Get setting values
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		$this->showlogo 	= $this->settings['showlogo'];
		$this->instrucciones 	= $this->settings['instrucciones'];
		
		$this->publickey 	= $this->settings['publickey'];
		$this->privatekey 	= $this->settings['privatekey'];
		
		$this->modopruebas 	= $this->settings['modopruebas'];
		
		//paso despues de selccion de gateway
		$this->has_fields	= true;
		
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		
		
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
			'showlogo' => array(
					'title' => __( 'Estilo', 'woocommerce' ),
					'label' => __( 'Activar Logos', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Activa o desactiva los logos de las empresas en donde realizar el pago ', 'woocommerce' ),
					'default' => 'yes'
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
			'instrucciones' => array(
					'title' => __( 'Texto Selección' ),
					'type' => 'text',
					'description' => __( 'El texto que se muestra para invitar a seleccionar una tienda para realizar el pago', 'woocommerce' ),
					'default' => 'Selecciona una tienda',
					
			),
			
			
			'modopruebas' => array(
					'title' => __( 'Modo de Pruebas', 'woocommerce' ),
					'label' => __( 'Activar modo pruebas', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Al activar el Modo de pruebas <b>es necesario que <span style="color:red;">cambie sus llaves por las de Modo Prueba</span></b>', 'woocommerce' ),
					'default' => 'no'
			)
		);
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
	 * handling payment and processing the order
	 * @param unknown $order_id
	 * @return array
	 * @throws Compropago\Exception
	 * https://docs.woothemes.com/document/payment-gateway-api/
	 */
	public function process_payment( $order_id ) {
		
		global $woocommerce;
		$order = new WC_Order( $order_id );
		
		
		 $data = array(
		 'order_id'    		 => $order_id,
		 'order_price'       => $order->get_total(),
		 'order_name'        => get_bloginfo('name').' orden:'.$order_id,
		 'customer_name'     => $order->billing_first_name . ' ' . $order->billing_last_name,
		 'customer_email'    => $order->billing_email,
		 'payment_type'     => $this->orderProvider,
		 'app_client_name'	=>	'woocommerce',
		 'app_client_version' => $woocommerce->version
		 
		 );
		 
		
		 
		
			
		   
			try{
				$this->compropagoConfig = array('publickey'=>$this->publickey,'privatekey'=>$this->privatekey,'live'=>$this->isLive());
				$this->compropagoClient = new Client($this->compropagoConfig);
				$this->compropagoService = new Service($this->compropagoClient);
			
				
				$res=$this->compropagoService->placeOrder($data) ;
				
				
				$response=(string)(Views::loadView('raw', $res,'ob'));		
				
				wc_add_notice($response, 'success' );
				
			} catch (Exception $e) {
				wc_add_notice( __('Compropago error:', 'woothemes') . $e->getMessage(), 'error' );
				return;
			
			}
		
		
		
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
	
		
		try{
			$this->compropagoConfig = array('publickey'=>$this->publickey,'privatekey'=>$this->privatekey,'live'=>$this->isLive());
			$this->compropagoClient = new Client($this->compropagoConfig);
			$this->compropagoService = new Service($this->compropagoClient);
			
			
		} catch (Exception $e) {
			wc_add_notice( __('Compropago error:', 'woothemes') . $e->getMessage(), 'error' );
			return;
		    
		}
		$data['providers']=$this->compropagoService->getProviders();
		$data['showlogo']=$this->showlogo;
		$data['description']=$this->description;
		$data['instrucciones']=$this->instrucciones;

		Views::loadView('providers',$data);		
	}
	
	
	/**
	 * @return true success
	 * @return null on ErrorException
	 * @throws WP exception
	 */
	public function validate_fields() {
		if(!isset($_POST['compropagoProvider']) || empty($_POST['compropagoProvider'])){
		
			$this->orderProvider='OXXO';
		}else{
			$this->orderProvider=$_POST['compropagoProvider'];
		}
		return true;
	}
	/**
	 * Compropago Valid Use Validation
	 * @return boolean
	 */
	public function is_valid_for_use() {
		//solo acepta total en Pesos Mexicanos
		if(get_option('woocommerce_currency')=='MXN'){
			return true;
		}else{
			wc_add_notice( 'ComproPago solo esta disponible para pagos en Pesos Mexicanos (MXN)', 'error' );
			return ;
		}
		
	}
}