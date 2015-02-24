<?php
/**
 * Gateway class
 **/

class WC_Gateway_Compropago extends WC_Payment_Gateway {

	/**
	 * Test mode
	 */
	var $testmode;
	
	/**
	 * notify url
	 */
	var $notify_url;
	
	function __construct() { 
		global $woocommerce;
		
		$this->id				= 'compropago';
		$this->method_title 	= __('Compropago', 'woocommerce');
		$this->icon 			= apply_filters('woocommerce_compropago_icon', plugins_url('/images/compropago.png', __FILE__));
		$this->has_fields 		= false;
		
		// Load the form fields
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Get setting values
		$this->title 				= $this->settings['title'];
		$this->description 			= $this->settings['description'];
		$this->test_secret_key 		= $this->settings['test_secret_key'];
		$this->test_public_key 		= $this->settings['test_public_key'];
		$this->live_secret_key 		= $this->settings['live_secret_key'];
		$this->live_public_key 		= $this->settings['live_public_key'];
		// $this->send_customer 		= $this->settings['send_customer'];
		// $this->store_card 			= $this->settings['store_card'];
		
		$this->cvc 					= $this->settings['cvc'];
		$this->testmode 			= $this->settings['testmode'];
		$this->debug 				= $this->settings['debug'];
		
		// Logs
		if ($this->debug=='yes') $this->log = $woocommerce->logger();
		
		add_action('woocommerce_receipt_compropago', array(&$this, 'receipt_page'));
		add_action( 'admin_notices', array( &$this, 'ssl_check') );
		
		$this->notify_url = add_query_arg('compropagoListener', 'compropago', get_permalink(woocommerce_get_page_id('pay')));
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '<' ) ) {
			add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			add_action( 'init', array( $this, 'notify_handler' ) );
		} else {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			
			add_action( 'woocommerce_api_wc_gateway_compropago', array( &$this, 'notify_handler' ) );
			$this->notify_url   = add_query_arg( 'wc-api', 'WC_Gateway_Compropago', $this->notify_url );
		}
		
		if ( !$this->is_valid_for_use() ) $this->enabled = false;
		
		//support subscriptions
		$this->supports = array( 'subscriptions', 'products', 'subscription_cancellation', 'subscription_reactivation');
		
		// When a subscriber or store manager cancel's a subscription in the store, suspend it with compropago
		add_action( 'cancelled_subscription_'.$this->id, array($this, 'cancel_subscriptions_for_order'), 10, 2 );
		// add_action( 'suspended_subscription_'.$this->id, array($this, 'suspend_subscription_for_order'), 10, 2 );
		add_action( 'reactivated_subscription_'.$this->id, array($this, 'reactivate_subscription_for_order'), 10, 2 );
	}
	
	/**
 	* Check if SSL is enabled and notify the user if SSL is not enabled
 	**/
	function ssl_check() {
		if (get_option('woocommerce_force_ssl_checkout')=='no' && $this->enabled=='yes') :
			echo '<div class="error"><p>'.sprintf(__('Compropago is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate - Compropago will only work in test mode.', 'woocommerce'), admin_url('admin.php?page=woocommerce')).'</p></div>';
		endif;
	}
	
	/**
     * Initialize Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
    		'enabled' => array(
						'title' => __( 'Enable/Disable', 'woocommerce' ), 
						'label' => __( 'Enable Compropago', 'woocommerce' ), 
						'type' => 'checkbox', 
						'description' => '', 
						'default' => 'no'
					), 
					
			'title' => array(
						'title' => __( 'Title', 'woocommerce' ), 
						'type' => 'text', 
						'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ), 
						'default' => __( 'Efectivo', 'woocommerce' ),						
					), 
			
			'description' => array(
						'title' => __( 'Description', 'woocommerce' ), 
						'type' => 'textarea', 
						'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ), 
						'default' => "The quikest way to pay with Compropago",					
					),
			
			'webhook' => array(
						'title' => __( 'Notificaciones Automáticas', 'woocommerce' ), 
						'type' => 'text', 
						'description' => __( 'Si requiere notificaciones automáticas, agrege esta URL dentro de la sección Webhook del panel de control de compropago', 'woocommerce' ), 
						'default' => plugins_url( 'webhook.php' , __FILE__ ),					
					),
			
			'live_secret_key' => array(
						'title' => __( 'Live Secret Key' ), 
						'type' => 'text', 
						'description' => __( 'Get your Live Secret Key credentials from Compropago', 'woocommerce' ), 
						'default' => '',
						'css' => "width: 300px;"
					),
			'live_public_key' => array(
						'title' => __( 'Live Public Key' ), 
						'type' => 'text', 
						'description' => __( 'Get your Live Public Key credentials from Compropago', 'woocommerce' ), 
						'default' => '',
						'css' => "width: 300px;"
					),
 			'test_secret_key' => array(
						'title' => __( 'Test Secret Key' ), 
						'type' => 'text', 
						'description' => __( 'Get your Test Secret Key credentials from Compropago', 'woocommerce' ), 
						'default' => '',
						'css' => "width: 300px;"
					),
			'test_public_key' => array(
						'title' => __( 'Test Public Key' ), 
						'type' => 'text', 
						'description' => __( 'Get your Test Public Key credentials from Compropago', 'woocommerce' ), 
						'default' => '',
						'css' => "width: 300px;"
					),
					
			// 'send_customer' => array(
					// 'title' => __( 'Send Customer Data', 'woocommerce' ),
					// 'type' 	=> 'select',
					// 'description' => __( '<br />Sending customer data will create a customer in Compropago when an order is processed, based on the email address for the order. The credit card used will be attached to this customer, allowing you to charge them again in the future in Compropago.', 'woocommerce' ), 
					//'default' => 'choice',
					// 'options' => array(
					// 'never' => __('Never', 'woocommerce'),
					// 'choice' => __("Customer's choice", 'woocommerce'),
					// 'always' => __("Always", 'woocommerce'),
			// ),
		                 
			// 'store_card' => array(
					// 'title' => __( 'Allow Customers to Use Stored Cards', 'woocommerce' ),
					// 'type' => 'checkbox', 
						
					// 'label' => __( 'Allow Store Card Information', 'woocommerce' ), 
						
					// 'description' => '',
						
					// 'default' => 'no'
						
			// ),			
			'cvc' => array(
						'title' => __( 'CVC', 'woocommerce' ), 
						'label' => __( 'Con ComproPago puedes hacer tu pago en más de 130,000 puntos, entre tiendas restaurantes y farmacias.', 'woocommerce' ), 
						'type' => 'checkbox', 
						'description' => '', 
						'default' => 'yes'
					),
			
			'testmode' => array(
						'title' => __( 'Test Mode', 'woocommerce' ), 
						'label' => __( 'Enable Compropago Test', 'woocommerce' ), 
						'type' => 'checkbox', 
						'description' => __( 'Process transactions in Test Mode via the Compropago Test account.', 'woocommerce' ), 
						'default' => 'no'
					),
			'debug' => array(
						'title' => __( 'Debug', 'woocommerce' ), 
						'type' => 'checkbox', 
						'label' => __( 'Enable logging (<code>woocommerce/logs/compropago.txt</code>)', 'woocommerce' ), 
						'default' => 'no'
					)
			);
    }
    
    /**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 */
	function admin_options() {
    	?>
    	<h3><?php _e( 'Compropago Payment Gateway', 'woocommerce' ); ?></h3>
    	<p><?php _e( 'Con ComproPago puedes hacer tu pago en m&aacute;s de 130,000 puntos, entre tiendas restaurantes y farmacias.', 'woocommerce' ); ?></p>
    	<table class="form-table">
    		<?php
    		if ( $this->is_valid_for_use() ) :
    			// Generate the HTML For the settings form.
    			$this->generate_settings_html();
    		else :
    			?>
            		<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( 'Compropago does not support your store currency.', 'woocommerce' ); ?></p></div>
        		<?php
        		
    		endif;
    		?>
		</table><!--/.form-table-->
    	<?php
    }
	
	/**
	 * Get payment config
	 */
	function get_config($attr=''){
		$config = array(
			'livemode'=>'', 
			'apikey'=>'');
		
		if ($this->testmode=="no"){
			$config['livemode'] = true;
			$config['secret_key'] = $this->live_secret_key;
			$config['public_key'] = $this->live_public_key;
		} else {
			$config['livemode'] = false;
			$config['secret_key'] = $this->test_secret_key;
			$config['public_key'] = $this->test_public_key;
		}
		
		if(!empty($attr) && !empty($config[$attr])) {
			return $config[$attr];
		} 
		
		return $config;
	}
	
	/**
     * Check if this gateway is enabled and available in the user's country
     */
    function is_valid_for_use() {
    	/*
        if (!in_array(get_option('woocommerce_currency'), 
        	array('AED', 'AMD', 'ANG', 'ARS', 'AUD', 'AWG', 'AZN', 'BBD', 'BDT', 'BGN'
        		, 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BYR', 'BZD', 'CAD'
        		, 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP'
        		, 'DZD', 'EEK', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP'
        		, 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HTG', 'HUF', 'IDR', 'ILS'
        		, 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD'
        		, 'KZT', 'LAK', 'LBP', 'LKR', 'LTL', 'LVL', 'MAD', 'MDL', 'MNT', 'MOP'
        		, 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK'
        		, 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR'
        		, 'RON', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL'
        		, 'SOS', 'STD', 'SVC', 'SZL', 'THB', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS'
        		, 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VEF', 'WST', 'XAF', 'XCD', 'XOF'
        		, 'XPF', 'YER', 'ZAR', 'ZMK', 'ZWD'))) 
        	return false;
		 */ 
        return true;
    }
	
	/**
     * Payment form on checkout page
     */
	function payment_fields() {
?>
		<?php if ($this->testmode=='yes') : ?><p><?php _e('TEST MODE/SANDBOX ENABLED', 'woocommerce'); ?></p><?php endif; ?>
		<?php if ($this->description) : ?><p><?php echo wpautop(wptexturize($this->description)); ?></p><?php endif; ?>
<?php

	}
	
 	/**
	 * Get args for passing
	 * 
	 **/
	function get_params( $order) {
		global $woocommerce;
		
		if ($this->debug=='yes') 
			$this->log->add( 'compropago', 'Generating payment form for order #' . $order->id);
		
		$token = $this->get_request('compropago_token');
		
		$params = array();
		
		//Order info------------------------------------		
		$params['amount'] 			= number_format($order->order_total, 2, '.', '') * 100;		
		$params['currency'] 		= get_option('woocommerce_currency');
		
		//Item name
		$item_names = array();
		if (sizeof($order->get_items())>0) : foreach ($order->get_items() as $item) :
			if ($item['qty']) $item_names[] = $item['name'] . ' x ' . $item['qty'];
		endforeach; endif;
		
		$params['description'] 		= sprintf( __('Order %s' , 'woocommerce'), $order->id ) . " - " . implode(', ', $item_names);
		
		$params['card'] 	= $token;
		
		return $params;
	}
	
	
	/**
     * Process the payment
	 * 
     */
	function process_payment($order_id) {
		global $woocommerce;
		
		$order = new WC_Order( $order_id );
		if ($this->debug=='yes') 
			$this->log->add( 'compropago', 'Redirect url: ' . add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay')))));
		// Return thank you redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
		);
	}

	/**
	 * receipt_page
	 * 
	 **/
	function receipt_page( $order_id ) {
		global $woocommerce;
		$product_name = NULL;
		
		$order = new WC_Order( $order_id );
		$public_key = $this->get_config('public_key');
		$items = $order->get_items();
		if ( count( $items ) > 0 ) {
			echo '<p>'.__('¡Gracias por tu compra!', 'woocommerce').'</p>';
			foreach ( $items as $item ) {
				if ( is_null( $product_name ) ) {
					$product_name = $item['name'];
				} else {
					$product_name = $product_name . ' ' . $item['name'];
				}
				$product_id = $item['product_id'];
			}
			
			$payment_url = "https://www.compropago.com/comprobante/?public_key=".$public_key;
			$payment_url .= "&customer_data_blocked=true";
			$payment_url .= "&app_client_name=woocommerce";
			$payment_url .= "&app_client_version=".WOOCOMMERCE_VERSION;
			$payment_url .= "&customer_name=".$order->billing_first_name . " " . $order->billing_last_name;
			$payment_url .= "&customer_email=".$order->billing_email;
			$payment_url .= "&product_price=".$order->get_total();
			$payment_url .= "&product_id=".$order_id;
			$payment_url .= "&product_name=".urlencode($product_name);
			$payment_url .= "&success_url=".urlencode($this->get_return_url( $order ));
?>
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('css/jquery.fancybox.css', __FILE__) ?>" media="screen" />
<script type="text/javascript" src="<?php echo plugins_url('js/jquery.fancybox.pack.js', __FILE__) ?>"></script>

<form action="<?php echo $this->notify_url ?>" method="post" id="compropago_payment_form">
	<?php wp_nonce_field('compropago_payment_submit') ?>
	<input type="hidden" name="key" value="<?php echo $order->order_key ?>" />
	<input type="hidden" name="order" value="<?php echo $order_id ?>" />
	
	<div class="left"/>
		<h3 style="margin: 10px;">Para completar la compra, haz clic en:</h3>
	</div>
	<div class="left">
		<a id="payment_btn" href="#" class=""><img src="<?php echo plugins_url('images/compropago-payment-green-btn.png', __FILE__) ?>" alt="PAGAR"></a>
	</div>
	<a class="button cancel" href="<?php echo $order->get_cancel_order_url() ?>"><?php _e('Cancelar pedido', 'woocommerce') ?></a>
	
	<script type='text/javascript'><?php
	echo "var gateway_compropago='".$payment_url."';";
?></script>
	<script type="text/javascript" src="<?php echo plugins_url('js/compropago.js', __FILE__) ?>"></script>
<?php
		}
	}

	/**
	 * notify handler
	 * @since 2.2.0
	 */
	function notify_handler() {
		global $woocommerce;
		
		$redirect = get_permalink(woocommerce_get_page_id('cart'));
		
		if (isset($_GET['compropagoListener']) && $_GET['compropagoListener'] == 'compropago') {
			if ($this->debug=='yes') {
				$this->log->add( 'compropago', __('Post form: ', 'woocommerce') . print_r($_POST, true));
			}
			
			if(wp_verify_nonce('compropago_payment_submit')) {
				$order_id = $this->get_request('order');
				$order = new WC_Order( $order_id );
				
				if($order->order_key != $_REQUEST['key']) {
					$woocommerce->add_error(__('Order key do not match!', 'woocommerce'));
					wp_redirect($redirect); //redirect page
					exit;
				}
				
				$order_items = $order->get_items();
				
				$product = $order->get_product_from_item( array_pop( $order_items ) );
				$this->product_type = $product->product_type;
		
				$params = $this->get_params( $order);
				
				if ($this->debug=='yes') 
					$this->log->add( 'compropago', __('Post paramaters: ', 'woocommerce') . print_r($params, true));
				
				$request = new compropago_request($this->get_config());
				
				$response = '';
				
				if( 'subscription' == $product->product_type || 'subscription_variation' == $product->product_type ) {
					if ($this->debug=='yes') 
						$this->log->add( 'compropago', 'Starting subscription ... ');
					
					$sign_up_fee = WC_Subscriptions_Order::get_sign_up_fee( $order );
		
					$price_per_period = WC_Subscriptions_Order::get_price_per_period( $order );
		
					$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $order );
		
					$subscription_length = WC_Subscriptions_Order::get_subscription_length( $order );
		
					$subscription_trial_length = WC_Subscriptions_Order::get_subscription_trial_length( $order );
					
					// Subscription unit of duration
					switch( strtolower( WC_Subscriptions_Order::get_subscription_period( $order ) ) ) {
						case 'year':
							$subscription_period = 'year';
							break;
						case 'month':
						default:
							$subscription_period = 'month';
							break;
					}
					// add more param
					$sparams = array();
					
					$plan_name = get_post($product->id)->post_title;
					//$plan_id = $product->id;
					$plan_id = $order_id;
					
					$response = $request->send($plan_id, 'retrieve');
					
					if(!$response->success()) { //create plan if not exists
						if ($this->debug=='yes') 
							$this->log->add( 'compropago', sprintf(__('Create plan id: %s', 'woocommerce'), $plan_id));
						
						$response = $request->send(array('amount'=> number_format($price_per_period, 2, '.', '') * 100
							, 'interval'=>$subscription_period
							, "currency" => $params['currency']
							, "id" => $plan_id
							, 'name'=> $plan_name
							, 'trial_period_days'=> $subscription_trial_length
						), 'plan');
						
					}
					
					if($response->success()) {
						if ($this->debug=='yes') 
							$this->log->add( 'compropago', print_r($response, true));
						
						$response = $request->send(array(
							"card" => $params['card'],
							"plan" => $plan_id,
							"email" => $order->billing_email), 'customer');
							
						if ($this->debug=='yes') 
							$this->log->add( 'compropago', 'Customer create: ' . print_r($response->results, true));
						
						if($response->success() && $sign_up_fee > 0) {
							$response = $request->send(array(
									"customer" => $response->results->id,
									"amount" => number_format($sign_up_fee, 2, '.', '') * 100,
									"currency" => $params['currency'],
									"description" => __("Sign-up Fee", 'woocommerce')));
								
							if ($this->debug=='yes') 
								$this->log->add( 'compropago', 'Sign-up fee response: ' . print_r($response->results, true));
						} 
						
					} else {
						//error
						if ($this->debug=='yes') 
							$this->log->add( 'compropago', __('Error can not create plan', 'woocommerce'));
						
						$woocommerce->add_error(__('Error can not create plan', 'woocommerce'));
					
					}
					
				} else {
					$response = $request->send($params);
				}
				
				//response result
				if($response->success()){
					$order->add_order_note( __('Compropago payment completed', 'woocommerce') . ' (Transaction ID: ' . $response->get_transaction_id() . ')' );
					$order->payment_complete();
		
					$woocommerce->cart->empty_cart();
					$redirect = add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('thanks'))));
				} else {
					if ($this->debug=='yes') 
						$this->log->add( 'compropago', 'Error: ' . $response->get_error(), true);
					
					$woocommerce->add_error(__('Payment error', 'woocommerce') . ': ' . $response->get_error() . '');
				}
			}
			
			wp_redirect($redirect); //redirect page
			exit;
		}
	}
	
	/**
	 * When a store manager or user cancels a subscription in the store, also cancel the subscription with compropago. 
	 *
	 * @since 2.0.0
	 * 
	 */
	function cancel_subscriptions_for_order( $order ){
		global $woocommerce;
		
		$request = new compropago_request($this->get_config());
		$response = $request->send(array('customer'=>$order->id), 'cancel');
		
		if($response->success('cancel')) {
			if ($this->debug=='yes') 
				$this->log->add( 'compropago', 'Order ID: #' . $order->id .' has been cancelled');
		} else {
			$woocommerce->add_error(__('Error! ' . $response->get_error(), 'woocommerce'));
			if ($this->debug=='yes') 
				$this->log->add( 'compropago', 'Error! ' . $response->get_error());
		}
		
	}
	
	/**
	 * When a store manager or user reactivates a subscription in the store, also reactivate the subscription with compropago. 
	 *
	 * @since 2.0.0
	 * 
	 */
	public static function reactivate_subscription_for_order( $order, $product_id ) {
		
	}
	
	
	/**
	 * Performs an Express Checkout NVP API operation as passed in $api_method.
	 * 
	 * Although the compropago Standard API provides no facility for cancelling a subscription, the compropago
	 * Express Checkout  NVP API can be used.
	 *
	 * @since 2.0.0
	 * 
	 */
	public static function change_subscription_status( $profile_id, $new_status ) {
		
	}
	
	/**
	 * Get post data if set
	 **/
	private function get_request($name) {
		if(isset($_REQUEST[$name])) {
			return trim($_REQUEST[$name]);
		}
		return NULL;
	}
	
} // end woocommerce_compropago
