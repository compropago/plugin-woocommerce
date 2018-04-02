<?php
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @author Christian Aguirre <christian@compropago.com>
 * @author Alfredo Gómez <alfredo@compropago.com>
 * @since 3.0.0
 */

use CompropagoSdk\Client;
use CompropagoSdk\Tools\Validations;
use CompropagoSdk\Factory\Factory;

class WC_Gateway_Compropago_Cash extends WC_Payment_Gateway
{
    const VERSION="4.3.0.0";

    private $compropagoConfig;
    private $client;
    private $orderProvider;
    private $controlVision;
    private $live;
    private $activeplugin;
    private $debug;
    private $publickey;
    private $privatekey;

    /**
     * Gateway Constructor
     * @since 3.0.0
     */
    public function __construct()
    {
        global $woocommerce;

        $this->id                 = 'compropago';
        $this->has_fields         = true;
        $this->method_title       = 'ComproPago';
        $this->method_description = __(
            '<p>ComproPago allows you to accept payments at Mexico stores like OXXO, 7Eleven and More.</p>',
            'compropago'
        );

        $this->init_form_fields();
        $this->init_settings();

        $this->activeplugin  = $this->settings['enabled'];

        $this->debug         = get_option('compropago_debug');
        $this->initialstate  = get_option('compropago_initial_state');
        $this->completeorder = get_option('compropago_completed_order');
        $this->title 		 = "<img style='max-height: 1.5em; vertical-align: middle;' src='https://compropago.com/plugins/logo.png' alt='ComproPago'> Pagos en efectivo";
        $this->publickey     = get_option('compropago_publickey');
        $this->privatekey    = get_option('compropago_privatekey');
        $this->live          = get_option('compropago_live') == 'yes' ? true : false;
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

        //just validate on admin site
        if(is_admin()){
        	$this->feedBackCompropago();
            $this->title = "ComproPago - Pagos en efectivo";
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Plugin key|mode Validation and Warnings
     * @since 3.0.2
     */
    private function feedBackCompropago()
    {
    	$alert = file_get_contents(__DIR__ . "/../../templates/alert.html");

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
        $page = get_site_url() . '/wp-admin/admin.php?page=compropago-config';

        $this->form_fields=array(
            'enabled' => array(
                'title'			=> __( 'Enable/Disable', 'compropago' ),
                'label' 		=> __( 'Enable Compropago', 'compropago' ),
                'type' 			=> 'checkbox',
                'description'	=> __('Para confirgurar ComproPago dirigete a su panel en el menu de administracion de Wordpress desde <a href="' . $page . '">AQUI</a>','compropago'),
                'default' 		=> 'no'
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
            'publickey'  => $this->publickey,
            'privatekey' => $this->privatekey,
            'live'       => $this->live,
            'contained'  =>'plugin; cpwc '.self::VERSION.';woocommerce '.$woocommerce->version.'; wordpress '.$wp_version.';'
        );
    }

    /**
     * handling payment and processing the order
     * @param $order_id
     * @return mixed
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
            $product_name = [];

            foreach ($orderItems as $product){
                $product_name[] = $product['name'] .' x '. $product['qty'];
            }

            $orderCurrency = $order->get_data()['currency'];

            if (!$this->valiate_currency($orderCurrency)) {
                wc_add_notice( __("Invalid Currency $orderCurrency. ComproPago Only allows MXN, USD, GBP or EUR as a currency.", 'compropago'), 'error' );
                return false;
            }

            $order_info = [
              'order_id' => $order_id,
              'order_name' => 'No. orden: '.$order_id,
              'order_price' => $order->get_total(),
              'customer_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
              'customer_email' => $order->billing_email,
              'payment_type' => $this->orderProvider,
              'currency' => $orderCurrency,
              'image_url' => null,
              'app_client_name' => 'woocommerce',
              'app_client_version' => $woocommerce->version
            ];

	        $ordercp = Factory::getInstanceOf('PlaceOrderInfo', $order_info);

            $this->client = new Client(
                $this->compropagoConfig['publickey'],
                $this->compropagoConfig['privatekey'],
                $this->compropagoConfig['live']
            );

            $compropagoResponse = $this->client->api->placeOrder($ordercp) ;

            if (!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
                !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")) {
                throw new Exception('ComproPago Tables Not Found');
            }

            if ($compropagoResponse->type != 'charge.pending') {
                $errMessage = 'ComproPago is not available - status not pending - '.$compropagoResponse->type;
                throw new Exception($errMessage);
            }

            $dbprefix = $wpdb->prefix;

            $recordTime = time();
            $ioIn = base64_encode(serialize($compropagoResponse));
            $ioOut = base64_encode(serialize($order));

            $wpdb->insert(
                $dbprefix . 'compropago_orders',
                array(
                    'ioIn' => $ioIn,
                    'date' => $recordTime,
                    'ioOut' => $ioOut,
                    'modified' => $recordTime,
                    'storeExtra' => 'change.pending',
                    'storeCartId' => $order_id,
                    'storeOrderId' => $order_id,
                    'compropagoId' => $compropagoResponse->id,
                    'compropagoStatus' => $compropagoResponse->type
                )
            );

            $idCompropagoOrder = $wpdb->insert_id;
            $wpdb->insert(
                $dbprefix . 'compropago_transactions',
                array(
                    'ioIn' => $ioIn,
                    'date' => $recordTime,
                    'ioOut' => $ioOut,
                    'orderId' => $idCompropagoOrder,
                    'compropagoId' => $compropagoResponse->id,
                    'compropagoStatus' => $compropagoResponse->type,
                    'compropagoStatusLast' => $compropagoResponse->type
                )
            );

            wc_add_notice(__('Su orden de pago en ComproPago está lista.','compropago'), 'success' );
        } catch (Exception $e) {
            wc_add_notice(__('Compropago error place order:', 'compropago') . $e->getMessage(), 'error');
            $this->log->add('compropago',$e->getMessage());
            return false;
        }

        // estatus en de la orden onhold, webhook actualizara a pending
        $order->update_status(
            $this->initialstate,
            ($this->initialstate == 'pending') ?
                __( 'ComproPago - Pending Payment', 'compropago' ) :
                __( 'ComproPago - On Hold', 'compropago' )
        );

        if ($this->completeorder == 'init') {
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
     */
    public function payment_fields()
    {
        try {
            $this->client = new Client(
                $this->compropagoConfig['publickey'],
                $this->compropagoConfig['privatekey'],
                $this->compropagoConfig['live']
            );

            global $woocommerce;

            $cart_subtotal = $woocommerce->cart->get_displayed_subtotal();

            if (!$this->is_valid_for_use()) {
                echo(__('This payment method is not available.', 'compropago'));
                return;
            }

            $providers = $this->client->api->listProviders($cart_subtotal, get_option('woocommerce_currency'));

            if (empty($providers)) {
              $providers = 0;
            }

            $filtered = array();

            if (empty($this->provallowed)) {
                $comprodata['providers'] = $providers;
            } else {
                if ($providers == 0) {
                    $comprodata['providers'] = $providers;
                } else {
                    $aux = explode(',',$this->provallowed);

                    foreach ($providers as $provider) {
                        foreach ($aux as $internal) {
                            if ($provider->internal_name == $internal) {
                                $filtered[] = $provider;
                            }
                        }
                    }

                    $comprodata['providers'] = $filtered;
                }
            }

            include __DIR__ . "/../../templates/providers-select.php";
        } catch (Exception $e) {
            wc_add_notice(__('Compropago error providers:', 'compropago') . $e->getMessage(), 'error');
            $this->log->add('compropago',$e->getMessage());
            echo($e->getMessage());
        }
    }

    /**
     * Validate store selected
     * @return true success
     * @return boolean
     * @throws WP_Exception
     */
    public function validate_fields() {
        if (!isset($_POST['compropagoProvider']) || empty($_POST['compropagoProvider'])) {
            wc_add_notice(__('Seleccione un establecimiento para realizar su pago', 'compropago'), 'error');
        } else {
            $this->orderProvider=$_POST['compropagoProvider'];
        }

        return true;
    }

    /**
     * Compropago Valid Use Validation
     * @return boolean
     */
    public function is_valid_for_use() {
        $currency = get_option('woocommerce_currency');

        if ($this->valiate_currency($currency)) {
            try {
                $this->client = new Client(
                    $this->compropagoConfig['publickey'],
                    $this->compropagoConfig['privatekey'],
                    $this->compropagoConfig['live']
                );

                Validations::validateGateway($this->client);

                return true;
            } catch (Exception $e) {
                wc_add_notice(__('Compropago error is valid:', 'compropago') . $e->getMessage(), 'error');
                $this->log->add('compropago',$e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Validate if a currency is valid for ComproPago
     * @param string $currency
     * @return boolean
     */
    private function valiate_currency($currency= '')
    {
        $validCurrencies = [
            'USD',
            'MXN',
            'GBP',
            'EUR'
        ];

        if (in_array($currency, $validCurrencies)) {
            return true;
        }

        return false;
    }
}

function cp_register_compropago_cash_method($methods) {
    $methods[] = 'WC_Gateway_Compropago_Cash';
    register_styles();
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'cp_register_compropago_cash_method');