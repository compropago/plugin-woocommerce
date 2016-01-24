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
 * @since 3.0.0
 */
use Compropago\Client;
use Compropago\Service;
use Compropago\Controllers\Views;

class WC_Gateway_Compropago extends WC_Payment_Gateway {
	
	const VERSION="3.0.0";
	
	private $compropagoConfig;
	private $compropagoClient;
	private $compropagoService;
	
	private $orderProvider;
	
	private $controlVision;
	private $modopruebas;
	
	public function __construct(){
		$this->id='compropago';
		$this->has_fields=true;
		$this->method_title='ComproPago';
		$this->method_description='Con ComproPago puedes recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México';
		
		$this->init_form_fields();
		
		$this->init_settings();
		$this->settings['webhook']=plugins_url( 'webhook.php', __FILE__ );
		// Get setting values
		//front end title and order method name on admin
		$this->title 		= $this->settings['title'];
		//$this->title 		= 'ComproPago';
		$this->description 	= $this->settings['description'];
		$this->showlogo 	= $this->settings['showlogo'];
		$this->instrucciones 	= $this->settings['instrucciones'];
		
		$this->publickey 	= $this->settings['COMPROPAGO_PUBLICKEY'];
		$this->privatekey 	= $this->settings['COMPROPAGO_PRIVATEKEY'];
		
		$this->modopruebas 	= $this->settings['COMPROPAGO_MODE'];
		
		//paso despues de selccion de gateway
		$this->has_fields	= true;
		$this->controlVision='no';
		
	
		
		
		$this->setCompropagoConfig();
		if($this->settings['enabled']=='yes'){
			if(isset($this->publickey) && isset($this->privatekey) &&
					!empty($this->publickey) && !empty($this->privatekey)  ){
				$this->controlVision='yes';
					if($this->settings['COMPROPAGO_MODE']=='yes'){
						$moduleLive=true;
					}else {
						$moduleLive=false;
					}
				
					try{
						$this->compropagoClient = new Client($this->compropagoConfig);
						$this->compropagoService = new Service($this->compropagoClient);
						//eval keys
						if(!$compropagoResponse = $this->compropagoService->evalAuth()){
							$this->settings['COMPROPAGO_ERRORS'] = __('Invalid Keys, The Public Key and Private Key must be valid before using this module.','compropago');
						}else{
							if($compropagoResponse->mode_key != $compropagoResponse->livemode){
								// compropagoKey vs compropago Mode
								$this->settings['COMPROPAGO_ERRORS'] = __('Your Keys and Your ComproPago account are set to different Modes.','compropago');
							}else{
								if($moduleLive != $compropagoResponse->livemode){
									// store Mode vs compropago Mode
									$this->settings['COMPROPAGO_ERRORS'] = __('Your Store and Your ComproPago account are set to different Modes.','compropago');
								}else{
									if($moduleLive != $compropagoResponse->mode_key){
										// store Mode vs compropago Keys
										$this->settings['COMPROPAGO_ERRORS'] = __('ComproPago ALERT:Your Keys are for a different Mode.','compropago');
									}else{
										if(!$compropagoResponse->mode_key && !$compropagoResponse->livemode){
											//can process orders but watch out, NOT live operations just testing
											$this->settings['COMPROPAGO_ERRORS'] = __('WARNING: ComproPago account is Running in TEST Mode','compropago');
										}else{
											$this->settings['COMPROPAGO_ERRORS'] = '';
										}
									}
								}
							}
						}
					}catch (Exception $e) {
						//something went wrong on the SDK side
						$this->settings['COMPROPAGO_ERRORS'] = $e->getMessage(); //may not be show or translated
					}
					
			}else{
				$this->settings['COMPROPAGO_ERRORS']=__('Ingrese sus Llaves para poder utilizar ComproPago','compropago');
				$this->controlVision='no';
			}
		}else{
			$this->settings['COMPROPAGO_ERRORS']=__('ComproPago No se encuentra Activo','compropago');
			$this->controlVision='no';
		}
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		
		
	}
	/**
	 * setup admin page
	 */
	public function init_form_fields(){
		$this->form_fields=array(
			
			'enabled' => array(
					'title'			=> __( 'Enable/Disable', 'compropago' ),
					'label' 		=> __( 'Enable Compropago', 'compropago' ),
					'type' 			=> 'checkbox',
					'description'	=> 'Activar ComproPago como método de pago. (<a href="https://compropago.com/" target="_new">Registrarse en Compropago</a>)',
					'default' 		=> 'no'
			),
			'COMPROPAGO_ERRORS' => array(
					'type' => 'textarea',
					'default' => '',
					'css' => 'color:#FF8C00;'
			),
			'COMPROPAGO_PUBLICKEY' => array(
					'title' => __( 'Llave Pública' ),
					'type' => 'text',
					'description' => __( 'Obten tu llave pública: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'compropago' ),
					'default' => '',
					'css' => "width: 300px;"
			),
			'COMPROPAGO_PRIVATEKEY' => array(
					'title' => __( 'Llave Privada' ),
					'type' => 'text',
					'description' => __( 'Obten tu llave privada: <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'compropago' ),
					'default' => '',
					'css' => "width: 300px;"
			),
			'COMPROPAGO_MODE' => array(
					'title' => __( 'Modo Activo', 'compropago' ),
					//'label' => __( 'Cambiar a modo Activo', 'compropago' ),
					'type' => 'checkbox',
					'description' => __( 'Modo Activo o de Pruebas?, cambie sus llaves de acuerdo al modo <a href="https://compropago.com/panel/configuracion" target="_new">Panel de Compropago</a>', 'compropago' ),
					'default' => 'no'
			),
			'webhook' => array(
					'title' => __('<b>WebHook</b>','compropago'),
					'css' => 'color:#0000FF',
					'type'	=> 'textarea',
					'desc_tip' =>__('Ingrese ésta dirección en su  panel de ComproPago para poder recibir la confirmación de pagos','compropago'),
					'description'=>__('Copie y Pegue la dirección en la sección de WebHooks de su panel en ComproPago para poder recibir la confirmación de pagos <a href="https://compropago.com/panel/webhooks" target="_new">Panel de Compropago:WebHooks</a>','compropago'),
					'default'=> plugins_url( 'webhook.php', __FILE__ ), 
			),
			'showlogo' => array(
					'title' => __( 'Estilo', 'compropago' ),
					'label' => __( 'Activar Logos', 'compropago' ),
					'type' => 'checkbox',
					'description' => __( 'Activa o desactiva los logos de las empresas en donde realizar el pago ', 'compropago' ),
					'default' => 'yes'
			),
			'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'compropago' ),
					'default' => __( 'ComproPago', 'compropago' ),
			),
				
			'description' => array(
					'title' => __( 'Description', 'compropago' ),
					'type' => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'compropago' ),
					'default' => "Con ComproPago puedes realizar tu pago en OXXO, 7Eleven y muchas tiendas más",
			),
			'instrucciones' => array(
					'title' => __( 'Texto Selección' ),
					'type' => 'text',
					'description' => __( 'El texto que se muestra para invitar a seleccionar una tienda para realizar el pago', 'compropago' ),
					'default' => 'Selecciona una tienda',
					
			),
			
		);
	
		
	}
	
	private function setCompropagoConfig(){
		global $wp_version;
		global $woocommerce;
		$this->compropagoConfig = array(
				'publickey'=>$this->publickey,
				'privatekey'=>$this->privatekey,
				'live'=>($this->modopruebas=='yes')? true:false,
				'contained'=>'plugin; cpwc '.self::VERSION.';woocommerce '.$woocommerce->version.'; wordpress '.$wp_version.';'
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
		if(!$this->is_valid_for_use()){
			wc_add_notice( __('Método de pago no disponible', 'compropago'),'error');
			return;
		}
		
		global $woocommerce;
		global $wpdb;
		$order = new WC_Order( $order_id );
	
		/*
		$orderItems = $order->get_items();
		$orderDetails=' ( ';
		foreach ($orderItems as $product){
			$product_name[] = $product['name'] .' x '. $product['qty'];
		}
		$product_list = implode( ' , ', $product_name );
		$orderDetails .= $product_list;
		$orderDetails .= ' ) ';
		*/
		
		 $compropagoOrderData = array(
		 'order_id'    		 => $order_id,
		 'order_price'       => $order->get_total(),
		 //'order_name'        => 'No. orden: '.$order_id.$orderDetails,
		 'order_name'        => 'No. orden: '.$order_id,
		 'customer_name'     => $order->billing_first_name . ' ' . $order->billing_last_name,
		 'customer_email'    => $order->billing_email,
		 'payment_type'     => $this->orderProvider,
		 'app_client_name'	=>	'woocommerce',
		 'app_client_version' => $woocommerce->version
		 
		 );
	   
		try{
			
			$this->compropagoClient = new Client($this->compropagoConfig);
			$this->compropagoService = new Service($this->compropagoClient);
		
			
			$compropagoResponse=$this->compropagoService->placeOrder($compropagoOrderData) ;
			
			$dbprefix=$wpdb->prefix;
			
			$recordTime=time();
			$ioIn=base64_encode(json_encode($compropagoResponse));
			$ioOut=base64_encode(json_encode($compropagoOrderData));
			
			$wpdb->insert($dbprefix . 'compropago_orders', array(
					'date' 				=> $recordTime,
					'modified' 			=> $recordTime,
					'compropagoId'		=> $compropagoResponse->id,
					'compropagoStatus'	=> $compropagoResponse->status,
					'storeCartId'		=> $order_id,
					'storeOrderId'		=> $order_id,
					'storeExtra'		=> 'COMPROPAGO_PENDING',
					'ioIn' 				=> $ioIn,
					'ioOut' 			=> $ioOut
					)
				);
			$idCompropagoOrder=$wpdb->insert_id;
			//record transaction
			$wpdb->insert($dbprefix . 'compropago_transactions', array(
					'orderId' 			=> $idCompropagoOrder,
					'date' 				=> $recordTime,
					'compropagoId'		=> $compropagoResponse->id,
					'compropagoStatus'	=> $compropagoResponse->status,
					'compropagoStatusLast'	=> $compropagoResponse->status,
					'ioIn' 				=> $ioIn,
					'ioOut' 			=> $ioOut
					)
				);
			
			$response='Su orden de pago ComproPago se ha creado con éxito';
			
			wc_add_notice($response, 'success' );
			
		} catch (Exception $e) {
			wc_add_notice( __('Compropago error:', 'compropago') . $e->getMessage(), 'error' );
			return;
		
		}
		
		
		
		// estatus en de la orden onhold, webhook actualizara a pending 
		$order->update_status('on-hold', __( 'ComproPago - On Hold', 'compropago' ));
		
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
	
		if(!$this->is_valid_for_use()){
			echo( __('Método de pago no disponible', 'compropago'));
			return;
		}
		try{
			
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
			try {	
				$this->compropagoClient = new Client($this->compropagoConfig);
				
				if(! Compropago\Utils\Store::validateGateway($this->compropagoClient)){
					//wc_add_notice("ComproPago Error: La tienda no se encuentra en un modo de ejecución valido",'error');
					return false;
				}
				return true;
			} catch (Exception $e) {
				//wc_add_notice( __('Compropago error:', 'compropago') . $e->getMessage(), 'error' );
				return false;
			}
		}else{
			//wc_add_notice( 'ComproPago solo esta disponible para pagos en Pesos Mexicanos (MXN)', 'error' );
			return false;
		}
		
	}
}