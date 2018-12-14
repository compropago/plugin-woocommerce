<?php
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @author Christian Aguirre <christian@compropago.com>
 * @author Alfredo Gómez <alfredo@compropago.com>
 * @since 3.0.0
 */

use CompropagoSdk\Resources\Payments\Cash;

class WC_Gateway_Compropago_Cash extends WC_Payment_Gateway
{
    const VERSION = "5.0.0.1";
    const GATEWAY_ID = 'cpcash';

    private $compropagoConfig;
    private $cash;
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

        $this->id			= self::GATEWAY_ID;
        $this->has_fields	= true;
        $this->method_title	= 'ComproPago Efectivo';

        $this->method_description = __(
            '<p>Accept payments at Mexico stores like OXXO, 7Eleven and More.</p>',
            'compropago'
        );

        $this->init_form_fields();
        $this->init_settings();

        $this->activeplugin  = $this->settings['enabled'];
        $this->debug         = get_option('compropago_debug');
        $this->initialstate  = get_option('compropago_initial_state');
        $this->completeorder = get_option('compropago_completed_order');
        $this->title 		 = !empty(get_option('compropago_cash_title')) ? get_option('compropago_cash_title') : 'Pago en efectivo';
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

        // Just validate on admin site
        if(is_admin()){
            $this->title = "ComproPago - Pagos en efectivo";
        }

        add_action(
            'woocommerce_update_options_payment_gateways_' . $this->id,
            array( $this, 'process_admin_options' )
        );
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
                'description'	=> __("Para confirgurar ComproPago diríjase a su panel en el menu de administración de Wordpress desde <a href=\"{$page}\">AQUÍ</a>", 'compropago'),
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
            'contained'  => 'plugin; cpwc ' . self::VERSION . ";woocommerce {$woocommerce->version}; wordpress {$wp_version};"
        );
    }

    /**
     * handling payment and processing the order
     * @param $order_id
     * @return mixed
     */
    public function process_payment( $order_id )
    {
        global $woocommerce;

        try {
            $order = new WC_Order( $order_id );
            $orderCurrency = $order->get_data()['currency'];

            if (!$this->valiate_currency($orderCurrency)) {
                wc_add_notice( __("Invalid Currency $orderCurrency. ComproPago Only allows MXN, USD, GBP or EUR as a currency.", 'compropago'), 'error' );
                return false;
            }

            $order_info = [
                'order_id'              => $order_id,
                'order_name'            => 'No. orden: '.$order_id,
                'order_price'           => $order->get_total(),
                'customer_name'         => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'customer_email'        => $order->get_billing_email(),
                'payment_type'          => $this->orderProvider,
                'currency'              => $orderCurrency,
                'image_url'             => null,
                'app_client_name'       => 'woocommerce',
                'app_client_version'    => $woocommerce->version
            ];

            $this->cash = (new Cash)->withKeys(
                $this->compropagoConfig['publickey'],
                $this->compropagoConfig['privatekey']
            );
            $cpResponse = $this->cash->createOrder($order_info);

            if ($cpResponse['type'] != 'charge.pending')
            {
                $errMessage = __("API Error, comuniquese con soporte@compropago.com para realizar actualización del API");
                throw new Exception($errMessage);
            }

            if (empty($order->get_meta('compropago_id'))) {
                $order->add_meta_data('compropago_id', $cpResponse['id']);
            } else {
                $order->update_meta_data('compropago_id', $cpResponse['id']);
            }

            if (empty($order->get_meta('compropago_short_id'))) {
                $order->add_meta_data('compropago_short_id', $cpResponse['short_id']);
            } else {
                $order->update_meta_data('compropago_short_id', $cpResponse['short_id']);
            }

            if (empty($order->get_meta('compropago_store'))) {
                $order->add_meta_data('compropago_store', $this->orderProvider);
            } else {
                $order->update_meta_data('compropago_store', $this->orderProvider);
            }

            wc_add_notice(__('Su orden de pago en ComproPago está lista.', 'compropago'), 'success' );
        }
        catch (Exception $e)
        {
            $errors = [
                'ComproPago: Request Error [409]: ',
                'Request Error [200]: ',
                'Request Error [400]: '
            ];
            $message = json_decode(str_replace($errors, '', $e->getMessage()), true);
            $message = isset($message['message'])
                ? $message['message']
                : $e->getMessage();

            wc_add_notice(
                __('Compropago error place order:<br/>' . $message, 'compropago'),
                'error'
            );

            return false;
        }

        $order->update_status(
            $this->initialstate,
            __( 'ComproPago - Pending', 'compropago' )
        );

        if ($this->completeorder == 'init') {
            $order->reduce_order_stock();
        }

        $woocommerce->cart->empty_cart();

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
        global $woocommerce;

        try {
            
            $cart_subtotal = $woocommerce->cart->get_displayed_subtotal();

            if (!$this->is_valid_for_use()) {
                echo(__('This payment method is not available.', 'compropago'));
                return;
            }

            $providers = $this->cash->getProviders();

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
                            if ($provider['internal_name'] == $internal) {
                                $filtered[] = $provider;
                            }
                        }
                    }

                    $comprodata['providers'] = $filtered;
                }
            }

            include __DIR__ . "/../../templates/providers-select.php";
        } catch (Exception $e) {
            wc_add_notice(
                __('Compropago error providers:', 'compropago') . $e->getMessage(),
                'error'
            );
            $this->log->add('compropago',$e->getMessage());
            echo($e->getMessage());
        }
    }

    /**
     * Validate provider selected
     * @return true success
     * @return boolean
     * @throws WP_Exception
     */
    public function validate_fields() 
    {
        if (!isset($_POST['compropagoProvider']) || empty($_POST['compropagoProvider'])) {
            wc_add_notice(
                __('Seleccione un establecimiento para realizar su pago', 'compropago'),
                'error'
            );
        } else {
            $this->orderProvider=$_POST['compropagoProvider'];
        }

        return true;
    }

    /**
     * Compropago Valid Use Validation
     * @return boolean
     */
    public function is_valid_for_use() 
    {
        $currency = get_option('woocommerce_currency');

        if ($this->valiate_currency($currency)) {
            try {
                $this->cash = (new Cash)->withKeys(
                    $this->compropagoConfig['publickey'],
                    $this->compropagoConfig['privatekey']
                );

                return true;
            } catch (Exception $e) {
                wc_add_notice(
                    __('Compropago error is valid:', 'compropago') . $e->getMessage(),
                    'error'
                );
                $this->log->add('compropago', $e->getMessage());
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

        return in_array($currency, $validCurrencies);
    }
}

function cp_register_compropago_cash_method($methods) {
    $methods[] = 'WC_Gateway_Compropago_Cash';
    cp_register_styles();
    
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'cp_register_compropago_cash_method');
