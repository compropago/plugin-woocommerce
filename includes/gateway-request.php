<?php
if(!class_exists("Compropago")){
	require_once(dirname(__FILE__) ."/Compropago.php");
}

/**
* Gateway API request class - sends given POST data to Gateway server
**/
class compropago_request {
	
	var $_payment;
	
	/** constructor */
	public function __construct( $config=array()) {
		if(!empty($config)){
			Compropago::setApiKey($config['secret_key']);
		}
	}
	
	/**
     * Create and send the request
	 * 
     * @param array $options array of options to be send in POST request
	 * @return gateway_response response object
	 * 
     */
	public function send($options, $type = '') {
		$result = '';
		try {
			if($type=='subscription') {
				$result = Compropago_Customer::create($options);
			} elseif($type=='plan'){
				$result = Compropago_Plan::create($options);
			} elseif($type=='retrieve'){
				$result = Compropago_Plan::retrieve($options);
			} elseif($type=='customer'){
				$result = Compropago_Customer::create($options);
			} elseif($type=='invoice') {
				$result = Compropago_InvoiceItem::create($options);
				// Compropago_Customer::invoiceItems($options);
			} elseif($type=='cancel') {
				$cu = Compropago_Customer::retrieve($options['customer']);
				$result = $cu->cancelSubscription();
			} else {
				$result = Compropago_Charge::create($options);
			}		
		} catch(Exception $ex) {
			$result = $ex;
		}

		$response = new compropago_response($result);
		return $response;
	}
}
?>