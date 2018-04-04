<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

 use CompropagoSdk\Tools\Request;

class WC_Gateway_Compropago_Spei extends WC_Payment_Gateway
{
    const VERSION = '1.0.0.0';
    const GATEWAY_ID = 'cpspei';

    private $debug;
    private $publicKey;
    private $privateKey;
    private $logger;
    private $initialstate;
    private $completeorder;
    
    /**
     * WC_Gateway_Compropago_Spei Constructor
     */
    public function __construct()
    {
        $this->id = self::GATEWAY_ID;
        $this->has_fields = true;
        $this->method_title = 'ComproPago SPEI';

        $this->method_description = __(
            '<p>Accept SPEI transfers for your orders.</p>',
            'compropago'
        );

        $this->initConfig();

        if (is_admin()) {
            $this->initFormFields();
        }

        add_action(
            'woocommerce_update_options_payment_gateways_' . $this->id, 
            array($this, 'process_admin_options')
        );
    }

    /**
     * Init WooCommerce config page fields
     */
    public function initFormFields()
    {
        $page = get_site_url() . '/wp-admin/admin.php?page=compropago-config';

        $this->form_fields=array(
            'enabled' => array(
                'title'			=> __( 'Enable/Disable', 'compropago' ),
                'label' 		=> __( 'Enable ComproPago SPEI', 'compropago' ),
                'type' 			=> 'checkbox',
                'description'	=> __('Para confirgurar ComproPago dirigete a su panel en el menu de administracion de Wordpress desde <a href="' . $page . '">AQUI</a>','compropago'),
                'default' 		=> 'no'
            )
        );
    }

    /**
     * Init all ComproPago config
     */
    private function initConfig()
    {
        $this->init_settings();

        $this->title = get_option('compropago_spei_title');
        $this->debug = get_option('compropago_debug') == 'yes';
        $this->publicKey = get_option('compropago_publickey');
        $this->privateKey = get_option('compropago_privatekey');
        $this->initialstate = get_option('compropago_initial_state');
        $this->completeorder = get_option('compropago_completed_order');

        $this->initLogger();
    }

    /**
     * Active Logger
     */
    private function initLogger()
    {
        global $woocommerce;

        if ($this->debug) {
            if (floatval($woocommerce->version) >= 2.1) {
                if (class_exists('WC_Logger')) {
                    $this->logger = new WC_Logger();
                } else {
                    $this->logger = WC()->logger();
                }
            } else {
                $this->logger = $woocommerce->logger();
            }
        }
    }

    /**
     * Log depending of the configuration of the logger
     * @param string $field
     * @param string $value
     */
    private function log($field, $value)
    {
        if (!empty($this->logger)) {
            $this->logger->add($field, $value);
        }
    }

    /**
     * Handling payment an processing the order
     * @param mixed $orderId
     * @return boolean|array
     */
    public function process_payment($orderId)
    {
        global $woocommerce;
        global $wpdb;

        try {
            if (!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
                !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")) {
                throw new \Exception('ComproPago Tables Not Found');
            }

            $order = new WC_Order($orderId);
            $currency = $order->get_data()['currency'];

            if (!$this->valiateCurrency($currency)) {
                wc_add_notice( __("Invalid Currency $currency. ComproPago Only allows MXN, USD, GBP or EUR as a currency.", 'compropago'), 'error' );
                return false;
            }

            $clientName = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

            $orderInfo = [
                "product" => [
                    "id" => "$orderId",
                    "price" => floatval($order->get_total()),
                    "name" => "No. Orden: $orderId",
                    "url" => "",
                    "currency" => $currency
                ],
                "customer" => [
                    "name" => $clientName,
                    "email" => $order->get_billing_email(),
                    "phone" => $order->get_billing_phone()
                ],
                "payment" =>  [
                    "type" => "SPEI"
                ]
            ];

            $response = $this->speiRequest($orderInfo);

            $recordTime = time();
            $ioIn = base64_encode(serialize($response));
            $ioOut = base64_encode(serialize($order));

            $insert = $wpdb->insert(
                $wpdb->prefix . 'compropago_orders',
                array(
                    'ioIn' => $ioIn,
                    'date' => $recordTime,
                    'type' => 'SPEI',
                    'ioOut' => $ioOut,
                    'modified' => $recordTime,
                    'storeExtra' => 'change.pending',
                    'storeCartId' => $orderId,
                    'storeOrderId' => $orderId,
                    'compropagoId' => $response->id,
                    'compropagoStatus' => $response->status
                )
            );

            $cpOrderId = $wpdb->insert_id;

            $wpdb->insert(
                $wpdb->prefix . 'compropago_transactions',
                array(
                    'ioIn' => $ioIn,
                    'date' => $recordTime,
                    'ioOut' => $ioOut,
                    'orderId' => $cpOrderId,
                    'compropagoId' => $response->id,
                    'compropagoStatus' => $response->status,
                    'compropagoStatusLast' => $response->status
                )
            );

            wc_add_notice(__('Su orden de pago en ComproPago está lista.', 'compropago'), 'success');
        } catch (\Exception $e) {
            wc_add_notice(__('Compropago error place order:', 'compropago') . $e->getMessage(), 'error');
            $this->log('compropago', $e->getMessage());
            return false;
        }

        $order->update_status(
            $this->initialstate,
            __( 'ComproPago - Pending', 'compropago')
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
     * Display the description of the SPEI payment
     */
    public function payment_fields()
    {
        echo 'Realiza una transferencia por SPEI para realiza tu pago.<br>';
        echo 'Se te enviaran las instrucciones a tu correo junto con la clave bancaria para realizar tu deposito.';
    }

    /**
     * Validate SPEI payment
     */
    public function validate_fields()
    {
        return true;
    }

    /**
     * Validate if a currency is valid for ComproPago
     * @param string $currency
     * @return boolean
     */
    private function valiateCurrency($currency= '')
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

    /**
     * Create the SPEI order
     * @param array $data
     * @return StdObject
     * @throws \Exception
     */
    private function speiRequest($data)
    {
        $url = 'https://ms-api.compropago.io/v2/orders';

        $auth = [
            "user" => $this->privateKey,
            "pass" => $this->publicKey
        ];

        $response = Request::post($url, $data, $auth);
        $response = json_decode($response);

        if ($response->status != 'success') {
            throw new \Exception("SPEI Error #: {$response->message}");
        }

        return $response->data;
    }
}

function cp_register_compropago_spei_method($methods) {
    $methods[] = "WC_Gateway_Compropago_Spei";
    register_styles();
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'cp_register_compropago_spei_method');