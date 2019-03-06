<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

use CompropagoSdk\Resources\Payments\Spei;


class WC_Gateway_Compropago_Spei extends WC_Payment_Gateway
{
    const VERSION = '2.0.0.3';
    const GATEWAY_ID = 'cpspei';

    private $debug;
    private $publicKey;
    private $privateKey;
    private $logger;
    private $initialstate;
    private $completeorder;
    private $spei;
    
    /**
     * WC_Gateway_Compropago_Spei Constructor
     */
    public function __construct()
    {
        global $woocommerce;

        $this->id			= self::GATEWAY_ID;
        $this->has_fields	= true;
        $this->method_title	= 'ComproPago SPEI';

        $this->method_description = __(
            '<p>Accept SPEI transfers for your orders.</p>',
            'compropago'
        );

        # Init Wordpress settings for plugin
        $this->init_settings();
        $this->initConfig();

        if (is_admin()) $this->initFormFields();
        

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
            'enabled' => [
                'title'			=> __( 'Enable/Disable', 'compropago' ),
                'label' 		=> __( 'Enable ComproPago SPEI', 'compropago' ),
                'type' 			=> 'checkbox',
                'description'	=> __('Para confirgurar ComproPago dirigete a su panel en el menu de administración de Wordpress desde <a href="' . $page . '">AQUI</a>','compropago'),
                'default' 		=> 'no'
            ]
        );
    }

    /**
     * Init all ComproPago config
     */
    private function initConfig()
    {
        $this->title            = get_option('compropago_spei_title');
        $this->debug            = get_option('compropago_debug');
        $this->publicKey        = get_option('compropago_publickey');
        $this->privateKey       = get_option('compropago_privatekey');
        $this->initialstate     = get_option('compropago_initial_state');
        $this->completeorder    = get_option('compropago_completed_order');

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

        try
        {
            $order = new WC_Order($orderId);
            $currency = $order->get_data()['currency'];

            if (!$this->valiateCurrency($currency)) {
                wc_add_notice(__("Invalid Currency $currency. ComproPago Only allows MXN, USD, GBP or EUR as a currency.", 'compropago'), 'error');
                return false;
            }

            $order_info = [
                "product" => [
                    "id"        => "$orderId",
                    "price"     => floatval($order->get_total()),
                    "name"      => "No. Orden: $orderId",
                    "url"       => "",
                    "currency"  => $currency
                ],
                "customer" => [
                    "name"      => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    "email"     => $order->get_billing_email(),
                    "phone"     => $order->get_billing_phone()
                ],
                "payment" =>  [
                    "type" => "SPEI"
                ],
                # TODO: Test this
                'app_client_name'       => 'woocommerce_' . $woocommerce->version,
                'app_client_version'    => $this::VERSION
            ];

            $this->spei = (new Spei)->withKeys(
                $this->publicKey,
                $this->privateKey
            );
            $cpResponse = $this->spei->createOrder($order_info)['data'];

            if (empty($order->get_meta('compropago_id'))) {
                $order->add_meta_data('compropago_id', $cpResponse['id']);
            } else {
                $order->update_meta_data('compropago_id', $cpResponse['id']);
            }

            if (empty($order->get_meta('compropago_short_id'))) {
                $order->add_meta_data('compropago_short_id', $cpResponse['shortId']);
            } else {
                $order->update_meta_data('compropago_short_id', $cpResponse['shortId']);
            }

            if (empty($order->get_meta('compropago_store'))) {
                $order->add_meta_data('compropago_store', 'SPEI');
            } else {
                $order->update_meta_data('compropago_store', 'SPEI');
            }

            wc_add_notice(__('Su orden de pago en ComproPago está lista.', 'compropago'), 'success');
        }
        catch (\Exception $e)
        {
            $errors = [
                'ComproPago: Request Error [409]: ',
                'Request Error [422]: ',
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
        echo 'Realiza tu pago a través de SPEI para cualquier banco, es muy rapido y sencillo.<br>
              Crea la orden y recibiras las instrucciones a tu email para dar de alta la cuenta y realizar la 
              transferencia desde tu banca en linea.';
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
}

function cp_register_compropago_spei_method($methods)
{
    $methods[] = "WC_Gateway_Compropago_Spei";
    cp_register_styles();
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'cp_register_compropago_spei_method');
