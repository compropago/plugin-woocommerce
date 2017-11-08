<?php

/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 * @since 3.0.0
 */

$wpFiles= array(
	__DIR__.'/../../../wp-load.php',
);


foreach($wpFiles as $wpFile){
    if (file_exists($wpFile)) {
        include_once $wpFile;
    } else {
        die(json_encode([
            'status' => 'error',
            'message' => 'Unresolved wordpress dependencies',
            'short_id' => null,
            'reference' => null
        ]));
    }
}


/**
 * Archivos de compropago
 */
require_once __DIR__.'/vendor/autoload.php';

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Client;
use CompropagoSdk\Tools\Validations;

$request = @file_get_contents('php://input');
if(!$resp_webhook = Factory::getInstanceOf("CpOrderInfo",$request)){
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid request',
        'short_id' => null,
        'reference' => null
    ]));
}


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );



//Check if WooCommerce is active
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die(json_encode([
        'status' => 'error',
        'message' => 'WooCommerce init failed',
        'short_id' => null,
        'reference' => null
    ]));
}




//Compropago Plugin Active?
if (!is_plugin_active( 'compropago/compropago.php' ) ){
    die(json_encode([
        'status' => 'error',
        'message' => 'Inactive plugin',
        'short_id' => null,
        'reference' => null
    ]));
}

//Get ComproPago  Config values
$config = get_option('woocommerce_compropago_settings');
if($config['enabled'] != 'yes'){
    die(json_encode([
        'status' => 'error',
        'message' => 'Inactive plugin',
        'short_id' => null,
        'reference' => null
    ]));
}


$publickey     = get_option('compropago_publickey');
$privatekey    = get_option('compropago_privatekey');
$live          = get_option('compropago_live') == 'yes' ? true : false;


$complete_order = get_option('compropago_completed_order');



//keys set?
if (empty($publickey) || empty($privatekey)){
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid plugin credentials',
        'short_id' => null,
        'reference' => null
    ]));
}


$compropagoConfig= array(
    'publickey'  => $publickey,
    'privatekey' => $privatekey,
    'live'       => $live
);



// consume sdk methods
try{
	$client = new Client(
        $compropagoConfig['publickey'],
        $compropagoConfig['privatekey'],
        $compropagoConfig['live']
    );

    Validations::validateGateway($client);
}catch (Exception $e) {
	//something went wrong at sdk lvl
    die(json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'short_id' => null,
        'reference' => null
    ]));
}



//webhook Test?
if($resp_webhook->short_id == "000000"){
    die(json_encode([
        'status' => 'success',
        'message' => 'Test webhook - OK',
        'short_id' => null,
        'reference' => null
    ]));
}



try{
	$response = $client->api->verifyOrder($resp_webhook->id);

	if($response->type == 'error'){
        die(json_encode([
            'status' => 'error',
            'message' => 'invalid verified order',
            'short_id' => null,
            'reference' => null
        ]));
	}

	if(!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
    !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")
    ){
        die(json_encode([
            'status' => 'error',
            'message' => 'ComproPago tables not found',
            'short_id' => null,
            'reference' => null
        ]));
	}

	switch ($response->type){
		case 'charge.success':
			$nomestatus = "COMPROPAGO_SUCCESS";
			break;
		case 'charge.pending':
			$nomestatus = "COMPROPAGO_PENDING";
			break;
		case 'charge.expired':
			$nomestatus = "COMPROPAGO_EXPIRED";
			break;
		default:
            die(json_encode([
                'status' => 'error',
                'message' => 'Invalid response type',
                'short_id' => null,
                'reference' => null
            ]));
	}

	$sql = "SELECT * FROM ".$wpdb->prefix."compropago_orders WHERE compropagoId = '".$response->id."' ";

	if ($row = $wpdb->get_row($sql)){

		$id_order = intval($row->storeOrderId);
		$recordTime = time();

		$order = new WC_Order( $id_order );

		switch($nomestatus){
			case 'COMPROPAGO_SUCCESS':
                if ($complete_order == 'fin') {
                    $order->payment_complete();
                } else {
                    $order->update_status('processing', __( 'ComproPago - Payment Confirmed', 'compropago' ));
                    $new_status = 'processing';
                }
			    break;

			case 'COMPROPAGO_PENDING':
                $order->update_status('pending', __( 'ComproPago - Pending Payment', 'compropago' ));
                $new_status = 'pending';
			    break;

			case 'COMPROPAGO_EXPIRED':
				$order->update_status('cancelled', __( 'ComproPago - Expired', 'compropago' ));
				$new_status = 'cancelled';
			    break;

			default:
				$order->update_status('on-hold', __( 'ComproPago - On Hold', 'compropago' ));
				$new_status = 'on-hold';
		}

		if (!empty($new_status)) {
            $order->set_status($new_status);
        }

		$order->save();


		$sql = "UPDATE `".$wpdb->prefix."compropago_orders` SET `modified` = '".$recordTime."',
		`compropagoStatus` = '".$response->type."', `storeExtra` = '".$nomestatus."' WHERE `id` = '".$row->id."'";

		if(!$wpdb->query($sql)){
            die(json_encode([
                'status' => 'error',
                'message' => 'Error updating compropago order',
                'short_id' => $response->short_id,
                'reference' => $response->order_info->order_id
            ]));
		}

		//save transaction
		$ioIn=base64_encode(serialize($resp_webhook));
		$ioOut=base64_encode(serialize($response));

		$wpdb->insert(
		    $wpdb->prefix . 'compropago_transactions',
            array(
            'orderId' 			    => $row->id,
            'date' 				    => $recordTime,
            'compropagoId'		    => $response->id,
            'compropagoStatus'	    => $response->type,
            'compropagoStatusLast'	=> $row->compropagoStatus,
            'ioIn' 				    => $ioIn,
            'ioOut' 			    => $ioOut
            )
        );

        die(json_encode([
            'status' => 'success',
            'message' => 'OK - ' . $order->get_status() . ' - ' . $response->type,
            'short_id' => $response->short_id,
            'reference' => $response->order_info->order_id
        ]));

	}else{
        die(json_encode([
            'status' => 'error',
            'message' => 'invalid order: not found ID',
            'short_id' => null,
            'reference' => null
        ]));
	}
}catch (Exception $e){
    die(json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'short_id' => null,
        'reference' => null
    ]));
}
