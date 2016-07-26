<?php
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @since 3.0.0
 */

require_once __DIR__ . "/controllers/Utils.php";

use CompropagoSdk\Client;
use CompropagoSdk\Tools\Validations;
use CompropagoSdk\Models\PlaceOrderInfo;

class WC_Gateway_Compropago extends WC_Payment_Gateway
{
    const VERSION="3.0.4";

    private $compropagoConfig;
    private $client;

    private $orderProvider;

    private $controlVision;
    private $live;
    private $activeplugin;

    private $debug;


    /**
     * init compropago
     * @since 3.0.0
     */
    public function __construct()
    {
        global $woocommerce;

        $this->id                 = 'compropago';
        $this->has_fields         = true;
        $this->method_title       = 'ComproPago';
        $this->method_description = __('<p>ComproPago allows you to accept payments at Mexico stores like OXXO, 7Eleven and More.</p>','compropago');

        $this->init_form_fields();
        $this->init_settings();

        $this->title 		 = $this->settings['title'];
        $this->activeplugin  = $this->settings['enabled'];

        $this->debug         = $this->settings['debug'];
        $this->completeorder = $this->settings['COMPROPAGO_COMPLETED_ORDER'];
        $this->initialstate  = $this->settings['COMPROPAGO_INITIAL_STATE'];


        $this->publickey     = get_option('compropago_publickey');
        $this->privatekey    = get_option('compropago_privatekey');
        $this->live          = get_option('compropago_live') == 'yes' ? true : false;
        $this->showlogo      = get_option('compropago_showlogo') == 'yes' ? true : false;
        $this->descripcion   = get_option('compropago_descripcion');
        $this->instrucciones = get_option('compropago_instrucciones');
        $this->provallowed   = get_option('compropago_provallowed');

        
        //paso despues de selccion de gateway
        $this->has_fields	 = true;
        $this->controlVision = 'no';

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
    	$alert = file_get_contents(__DIR__."/templates/alert.html");

        $flagerror = Utils::retroalimentacion($this->publickey, $this->privatekey, $this->live, $this->settings);
    	
    	if($flagerror[0]){
    		$alert = str_replace(":message:",$flagerror[1],$alert);
    		$this->method_description .= $alert;
    	}

        $this->controlVision = $flagerror[2];
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
            'title' => array(
                'title'         => __( 'Title', 'compropago' ),
                'type'          => 'text',
                'description'   => __( 'This controls the title which the user sees during checkout.', 'compropago' ),
                'default'       => __( 'ComproPago (OXXO, 7Eleven, etc.)', 'compropago' ),
            ),
            'COMPROPAGO_COMPLETED_ORDER' => array(
                'title'             => __( 'Stock Management', 'compropago' ),
                'type'              => 'select',
                'description'       => __( 'It indicates the time when the stock is reduced.', 'compropago' ),
            	'desc_tip'          =>__('Set when to reduce stock for the order','compropago'),
                'options'           => array(
                    'init' => 'At place order',
                    'fin'  => 'At Confirmed payment',
                    'no'   => 'Never'
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
            'publickey'  =>$this->publickey,
            'privatekey' =>$this->privatekey,
            'live'       => $this->live,
            'contained'  =>'plugin; cpwc '.self::VERSION.';woocommerce '.$woocommerce->version.'; wordpress '.$wp_version.';'
        );
    }


    /**
     * handling payment and processing the order
     * @param $order_id
     * @return array
     * @throws \Exception
     * @since 3.0.0
     * https://docs.woothemes.com/document/payment-gateway-api/
     */
    public function process_payment( $order_id )
    {
        try{

            if(!$this->is_valid_for_use()){
                wc_add_notice( __('This payment method is not available.', 'compropago'),'error');
                return false;
            }

            global $woocommerce;
            global $wpdb;
            $order = new WC_Order( $order_id );


            $orderItems = $order->get_items();
            $orderDetails='';
            foreach ($orderItems as $product){
                $product_name[] = $product['name'] .' x '. $product['qty'];
            }
            $product_list = implode( ' , ', $product_name );
            $orderDetails .= $product_list;


            $ordercp = new PlaceOrderInfo(
                $order_id,
                $orderDetails,
                $order->get_total(),
                $order->billing_first_name . ' ' . $order->billing_last_name,
                $order->billing_email,
                $this->orderProvider,
                null,
                'woocommerce',
                $woocommerce->version
            );

            $this->client = new Client(
                $this->compropagoConfig['publickey'],
                $this->compropagoConfig['privatekey'],
                $this->compropagoConfig['live'],
                $this->compropagoConfig['contained']
            );


            $compropagoResponse = $this->client->api->placeOrder($ordercp) ;


            if(!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
                !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")){
                throw new Exception('ComproPago Tables Not Found');
            }

            if($compropagoResponse->getStatus()!='pending'){
                throw new Exception('ComproPago is not available - status not pending - '.$compropagoResponse->getStatus());
            }

            $dbprefix = $wpdb->prefix;

            $recordTime = time();
            $ioIn = base64_encode(serialize($compropagoResponse));
            $ioOut = base64_encode(serialize($order));

            $wpdb->insert($dbprefix . 'compropago_orders', array(
                    'date' 				=> $recordTime,
                    'modified' 			=> $recordTime,
                    'compropagoId'		=> $compropagoResponse->getId(),
                    'compropagoStatus'	=> $compropagoResponse->getStatus(),
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
                    'compropagoId'		=> $compropagoResponse->getId(),
                    'compropagoStatus'	=> $compropagoResponse->getStatus(),
                    'compropagoStatusLast'	=> $compropagoResponse->getStatus(),
                    'ioIn' 				=> $ioIn,
                    'ioOut' 			=> $ioOut
                )
            );
            //success

            //wc_add_notice(__('Your payment order at ComproPago is ready','compropago'), 'success' );
            wc_add_notice(__('Su orden de pago en ComproPago está lista.','compropago'), 'success' );
        } catch (Exception $e) {
            wc_add_notice( __('Compropago error place order:', 'compropago') . $e->getMessage(), 'error' );
            $this->log->add('compropago',$e->getMessage());
            return false;
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
        try{
            $this->client = new Client(
                $this->compropagoConfig['publickey'],
                $this->compropagoConfig['privatekey'],
                $this->compropagoConfig['live'],
                $this->compropagoConfig['contained']
            );


            global $woocommerce;
            $cart_subtotal = $woocommerce->cart->get_displayed_subtotal();

            if(!$this->is_valid_for_use()){
                echo( __('This payment method is not available.', 'compropago'));
                return;
                //die('IF s valid for use');
            }
        
            $providers = $this->client->api->listProviders(false,$cart_subtotal);

            $filtered = array();

            if(empty($this->provallowed)){
                $comprodata['providers']= $providers;
            }else{
                $aux = explode(',',$this->provallowed);

                foreach ($providers as $provider){
                    foreach ($aux as $internal){

                        if($provider->internal_name == $internal){
                            $filtered[] = $provider;
                        }
                    }
                }

                $comprodata['providers']= $filtered;
            }

            $comprodata['showlogo']=$this->showlogo;
            $comprodata['description']=$this->descripcion;
            $comprodata['instrucciones']=$this->instrucciones;


            include __DIR__ . "/templates/providers-select.php";
        } catch (Exception $e) {
            wc_add_notice( __('Compropago error providers:', 'compropago') . $e->getMessage(), 'error' );
            $this->log->add('compropago',$e->getMessage());
            //return;
            echo($e->getMessage());
        }
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
                $this->client = new Client(
                    $this->compropagoConfig['publickey'],
                    $this->compropagoConfig['privatekey'],
                    $this->compropagoConfig['live'],
                    $this->compropagoConfig['contained']
                );

                Validations::validateGateway($this->client);

                return true;
            } catch (Exception $e) {
                wc_add_notice( __('Compropago error is valid:', 'compropago') . $e->getMessage(), 'error' );
                $this->log->add('compropago',$e->getMessage());
                return false;
            }
        }else{
            //wc_add_notice( 'ComproPago solo esta disponible para pagos en Pesos Mexicanos (MXN)', 'error' );
            return false;
        }
    }
}
