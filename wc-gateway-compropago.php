<?php
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @since 3.0.0
 */

use Compropago\Sdk\Client;
use Compropago\Sdk\Service;
use Compropago\Sdk\Controllers\Views;
use Compropago\Sdk\Utils\Store;

class WC_Gateway_Compropago extends WC_Payment_Gateway
{
    const VERSION="3.0.3";

    private $compropagoConfig;
    private $compropagoClient;
    private $compropagoService;

    private $orderProvider;

    private $controlVision;
    private $modopruebas;
    private $activeplugin;

    private $debug;
	private $errmsg;
	
	private $filterStores;
    /**
     * init compropago
     * @since 3.0.0
     */
    public function __construct()
    {
        global $woocommerce;

        $this->id='compropago';
        $this->has_fields=true;
        $this->method_title='ComproPago';
        $this->method_description=__('<p>ComproPago allows you to accept payments at Mexico stores like OXXO, 7Eleven and More.</p>','compropago');

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
        $this->activeplugin = $this->settings['enabled'];

        $this->debug = $this->settings['debug'];
        $this->completeorder = $this->settings['COMPROPAGO_COMPLETED_ORDER'];
        $this->initialstate = $this->settings['COMPROPAGO_INITIAL_STATE'];
		
       $this->filterStores = $this->settings['COMPROPAGO_STORES'];
        
        //paso despues de selccion de gateway
        $this->has_fields	= true;
        $this->controlVision='no';

        // Logs
        if ( 'yes' == $this->debug ) {
            if ( floatval( $woocommerce->version ) >= 2.1 ) {
                if ( class_exists( 'WC_Logger' ) ) {
                    $this->log = new WC_Logger();
                } else {
                    $this->log = WC()->logger();
                }
            }else{
                $this->log = $woocommerce->logger();
            }
        }

        $this->setCompropagoConfig();
        
       // var_dump($this->filterStores);
        
        //just validate on admin site 
        if(is_admin()){
        	$this->feedBackCompropago();
        }
       
       
       

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

    }
	
    /**
     * Plugin key|mode Validation and Warnings
     * @since 3.0.2
     */
    private function feedBackCompropago()
    {
    	$flagerror = false;
    	$alert = file_get_contents(__DIR__."/templates/alert.html");
    	
    	
    	
    	if($this->activeplugin=='yes'){
    		if(isset($this->publickey) && isset($this->privatekey) &&
    				!empty($this->publickey) && !empty($this->privatekey)  ){
    					$this->controlVision='yes';
    					if($this->modopruebas=='yes'){
    						$moduleLive=true;
    					}else {
    						$moduleLive=false;
    					}
    	
    					try{
    						$this->compropagoClient = new Client($this->compropagoConfig);
    						$this->compropagoService = new Service($this->compropagoClient);
    						//eval keys
    						if(!$compropagoResponse = $this->compropagoService->evalAuth()){
    							$this->errmsg = __('Invalid Keys, The Public Key and Private Key must be valid before using this module.','compropago');
    							$flagerror = true;
    						}else{
    							if($compropagoResponse->mode_key != $compropagoResponse->livemode){
    								// compropagoKey vs compropago Mode
    								$this->errmsg = __('Your Keys and Your ComproPago account are set to different Modes.','compropago');
    								$flagerror = true;
    							}else{
    								if($moduleLive != $compropagoResponse->livemode){
    									// store Mode vs compropago Mode
    									$this->errmsg = __('Your Store and Your ComproPago account are set to different Modes.','compropago');
    									$flagerror = true;
    								}else{
    									if($moduleLive != $compropagoResponse->mode_key){
    										// store Mode vs compropago Keys
    										$this->errmsg = __('ComproPago ALERT:Your Keys are for a different Mode.','compropago');
    										$flagerror = true;
    									}else{
    										if(!$compropagoResponse->mode_key && !$compropagoResponse->livemode){
    											//can process orders but watch out, NOT live operations just testing
    											$this->errmsg = __('WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS','compropago');
    											$flagerror = true;
    										}else{
    											$this->errmsg = '';
    										}
    									}
    								}
    							}
    						}
    					}catch (Exception $e) {
    						//something went wrong on the SDK side
    						$this->errmsg = $e->getMessage(); //may not be show or translated
    	
    						$this->log->add('compropago',$e->getMessage());
    	
    						$flagerror = true;
    					}
    		}else{
    			$this->errmsg =__('The Public Key and Private Key must be set before using ComproPago','compropago');
    			$this->controlVision='no';
    			$flagerror = true;
    		}
    	}else{
    		$this->errmsg =__('ComproPago is not Enabled','compropago');
    		$this->controlVision='no';
    		$flagerror = true;
    	}
    	
    	if($flagerror){
    		$alert = str_replace(":message:",$this->errmsg,$alert);
    		$this->method_description .= $alert;
    	}
    }

    /**
     * setup admin page
     * @since 3.0.0
     */
    public function init_form_fields()
    {
    	
    	
        $this->form_fields=array(

            'enabled' => array(
                'title'			=> __( 'Enable/Disable', 'compropago' ),
                'label' 		=> __( 'Enable Compropago', 'compropago' ),
                'type' 			=> 'checkbox',
                'description'	=> __('Activate ComproPago payment method. (<a href="https://compropago.com/" target="_new">Sign Up for Compropago</a>)','compropago'),
                'default' 		=> 'no'
            ),
            'COMPROPAGO_PUBLICKEY'  => array(
                'title'         => __( 'Public Key','compropago' ),
                'type'          => 'text',
                'description'   => __( 'Get your keys: <a href="https://compropago.com/panel/configuracion" target="_new">Compropago Panel</a>', 'compropago' ),
                'default'       => '',
                'css'           => "width: 300px;"
            ),
            'COMPROPAGO_PRIVATEKEY' => array(
                'title'         => __( 'Private Key','compropago'),
                'type'          => 'text',
                'description'   => __( 'Get your keys: <a href="https://compropago.com/panel/configuracion" target="_new">Compropago Panel</a>', 'compropago' ),
                'default'       => '',
                'css'           => "width: 300px;"
            ),
            'COMPROPAGO_MODE' => array(
                'title'         => __( 'Active Mode', 'compropago' ),
                //'label' => __( 'Cambiar a modo Activo', 'compropago' ),
                'type'          => 'checkbox',
                'description'   => __( 'Are you on live or testing?,Change your Keys according to the mode <a href="https://compropago.com/panel/configuracion" target="_new">Compropago Panel</a>', 'compropago' ),
                'default'       => 'no'
            ),
            'webhook' => array(
                'title'         => __('WebHook','compropago'),
                'css'           => 'color:#0000FF',
                'type'	        => 'textarea',
                'desc_tip'      =>__('Set this Url at ComproPago Panel to use it  to confirm to your store when a payment has been confirmed','compropago'),
                'description'   =>__('Copy & Paste this Url to WebHooks section of your ComproPago Panel to recive instant notifications when a payment is confirmed <a href="https://compropago.com/panel/webhooks" target="_new">Compropago Panel:WebHooks</a>','compropago'),
                'default'       => plugins_url( 'webhook.php', __FILE__ ),
            ),
            'showlogo' => array(
                'title'         => __( 'Show Logos', 'compropago' ),
                'label'         => __( 'Activate Logos', 'compropago' ),
                'type'          => 'checkbox',
                'description'   => __( 'Want to show store logos or a select box?', 'compropago' ),
                'default'       => 'yes'
            ),
        	'COMPROPAGO_STORES' => array(
        		'title'			=> __('Tiendas','compropago'),
        		'type'			=> 'multiselect',
        		'description'	=> __('Seleccione las tiendas que desea mostrar','compropago'),
        		'desc_tip'      =>__('ctrl + click para seleccionar','compropago'),
        		'options'		=> array(
        			'OXXO'=> 'OXXO',
        			'SEVEN_ELEVEN'=> 'Seven Eleven',
        			'CHEDRAUI'=> 'Chedrahui',
        			'COPEL'=> 'Copel',
        			'EXTRA'=> 'Extra',
        			'FARMACIA_BENAVIDES'=> 'Farmacias Benavides',
        			'FARMACIA_ESQUIVAR'=> 'Farmacias Esquivar',
        			'ELEKTRA'=> 'Elektra',
        			'PITICO'=> 'Pitico',
        			'TELECOMM'=> 'Telecomm',
        			'FARMACIA_ABC'=> 'Farmacias ABC'	
        		)
        	),	
            'title' => array(
                'title'         => __( 'Title', 'compropago' ),
                'type'          => 'text',
                'description'   => __( 'This controls the title which the user sees during checkout.', 'compropago' ),
                'default'       => __( 'ComproPago (OXXO, 7Eleven, etc.)', 'compropago' ),
            ),
            'description' => array(
                'title'         => __( 'Description', 'compropago' ),
                'type'          => 'textarea',
                'description'   => __( 'This controls the description which the user sees during checkout.', 'compropago' ),
                'default'       => __('With ComproPago make your payment at OXXO, 7Eleven and more stores','compropago'),
            ),
            'instrucciones' => array(
                'title'         => __( 'Selection Text','compropago' ),
                'type'          => 'text',
                'description'   => __( 'Instruction text to select a store', 'compropago' ),
                'default'       => __('Select a Store','compropago'),

            ),
            'COMPROPAGO_COMPLETED_ORDER' => array(
                'title'             => __( 'Stock Management', 'compropago' ),
                'type'              => 'select',
                'description'       => __( 'It indicates the time when the stock is reduced.', 'compropago' ),
            	'desc_tip'      =>__('Set when to reduce stock for the order','compropago'),
                'options'           => array(
                    'init' => 'At place order',
                    'fin' => 'At Confirmed payment',
                    'no' => 'Never'
                ),
            	'default' => 'init'
            ),
            'COMPROPAGO_INITIAL_STATE' => array(
                'title'             => __( 'Order Initial State', 'compropago' ),
                'type'              => 'select',
                'description'       => __( 'Order Status when new order is created', 'compropago' ),
                'options'           => array(
                                        'on-hold' => 'On hold',
                                        'pending' => 'Pending'
                                    ),
            	'default'			=> 'pending'
            ),
            'debug' => array(
                'title'             => __( 'Debug', 'woocommerce' ),
                'type'              => 'checkbox',
                'label'             => __( 'Enable  (<code>woocommerce/logs/compropago.txt</code>)', 'woocommerce' ),
                'default'           => 'no'
            )

        );

    }


    /**
     * Set ComproPago config
     * @since 3.0.0
     */
    private function setCompropagoConfig()
    {
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
     * @param $order_id
     * @return array
     * @throws Compropago\Sdk\Exception
     * @since 3.0.0
     * https://docs.woothemes.com/document/payment-gateway-api/
     */
    public function process_payment( $order_id )
    {
        if(!$this->is_valid_for_use()){
            wc_add_notice( __('This payment method is not available.', 'compropago'),'error');
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

            if(!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||	!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")){
                throw new Exception('ComproPago Tables Not Found');
            }
            if(!isset($compropagoResponse->status) && $compropagoResponse->status!='pending'){
                throw new Exception('ComproPago is not available');
            }

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
            //success

            //wc_add_notice(__('Your payment order at ComproPago is ready','compropago'), 'success' );
            wc_add_notice(__('Su orden de pago en ComproPago está lista.','compropago'), 'success' );

        } catch (Exception $e) {
            wc_add_notice( __('Compropago error:', 'compropago') . $e->getMessage(), 'error' );

            $this->log->add('compropago',$e->getMessage());

            return;

        }

        // estatus en de la orden onhold, webhook actualizara a pending
        $order->update_status($this->settings['COMPROPAGO_INITIAL_STATE'],
        		($this->settings['COMPROPAGO_INITIAL_STATE']=='pending')?__( 'ComproPago - Pending Payment', 'compropago' ):__( 'ComproPago - On Hold', 'compropago' ));

        if($this->settings['COMPROPAGO_COMPLETED_ORDER'] == 'init') {
            // Reduce stock levels
            $order->reduce_order_stock();
        }

        // Remove cart
        $woocommerce->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }


    /**
     * Load store selector
     * @since 3.0.0
     */
    public function payment_fields()
    {

        if(!$this->is_valid_for_use()){
            echo( __('This payment method is not available.', 'compropago'));
            return;
        }

        try{
            $this->compropagoClient = new Client($this->compropagoConfig);
            $this->compropagoService = new Service($this->compropagoClient);
        } catch (Exception $e) {
            wc_add_notice( __('Compropago error:', 'compropago') . $e->getMessage(), 'error' );
            $this->log->add('compropago',$e->getMessage());
            return;
        }
		
        
        $providers= $this->compropagoService->getProviders();
     
        
		if(!$this->filterStores){
       		$data['providers']= $providers;
		}else{
			foreach ($providers as $provider){
				foreach ($this->filterStores as $internal){
					
					if($provider->internal_name == $internal){
						$filtered[] = $provider;
					}
				}
			}
			
			$data['providers']= $filtered;
		}
        
        $data['showlogo']=$this->showlogo;
        $data['description']=$this->description;
        $data['instrucciones']=$this->instrucciones;


        Views::loadView('providers',$data);
    }


    /**
     * Validate store selected
     * @return true success
     * @return null on ErrorException
     * @throws WP exception
     * @since 3.0.0
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
     * @since 3.0.0
     */
    public function is_valid_for_use() {
        //solo acepta total en Pesos Mexicanos
        if(get_option('woocommerce_currency')=='MXN'){
            try {
                $this->compropagoClient = new Client($this->compropagoConfig);

                if(! Store::validateGateway($this->compropagoClient)){
                    //wc_add_notice("ComproPago Error: La tienda no se encuentra en un modo de ejecución valido",'error');
                    return false;
                }
                return true;
            } catch (Exception $e) {
                //wc_add_notice( __('Compropago error:', 'compropago') . $e->getMessage(), 'error' );
                $this->log->add('compropago',$e->getMessage());
                return false;
            }
        }else{
            //wc_add_notice( 'ComproPago solo esta disponible para pagos en Pesos Mexicanos (MXN)', 'error' );
            return false;
        }

    }
}
