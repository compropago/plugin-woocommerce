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
    if(file_exists($wpFile)){
        include_once $wpFile;
    }else{
        echo "ComproPago Warning: No se encontro el archivo:".$wpFile."<br>";
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
	die('Tipo de Request no Valido');
}


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );



//Check if WooCommerce is active
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	die("No se pudo inicializar WooCommerce");
}




//Compropago Plugin Active?
if (!is_plugin_active( 'compropago/compropago.php' ) ){
	die('ComproPago no se encuentra activo en Wordpress');
}

//Get ComproPago  Config values
$config = get_option('woocommerce_compropago_settings');
if($config['enabled'] != 'yes'){
	die('ComproPago no se encuentra activo en Woocommerce');
}


$publickey     = get_option('compropago_publickey');
$privatekey    = get_option('compropago_privatekey');
$live          = get_option('compropago_live') == 'yes' ? true : false;


$complete_order = get_option('compropago_completed_order');



//keys set?
if (empty($publickey) || empty($privatekey)){
    die("Se requieren las llaves de compropago");
}


$compropagoConfig= array(
    'publickey'  => $publickey,
    'privatekey' => $privatekey,
    'live'       => $live,
    //'contained'  => 'plugin; cpwc 3.0.0 ; woocommerce '.$woocommerce->version.'; wordpress '.$wp_version.'; webhook;'
);



// consume sdk methods
try{
	$client = new Client(
        $compropagoConfig['publickey'],
        $compropagoConfig['privatekey'],
        $compropagoConfig['live']
      //$compropagoConfig['contained']
    );

    Validations::validateGateway($client);
}catch (Exception $e) {
	//something went wrong at sdk lvl
	die($e->getMessage());
}



//webhook Test?
if($resp_webhook->id=="ch_00000-000-0000-000000"){
	die("Probando el WebHook?, <b>Ruta correcta.</b>");
}



try{
	$response = $client->api->verifyOrder($resp_webhook->id);

	if($response->type == 'error'){
		die('Error procesando el número de orden');
	}

	if(!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
    !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")
    ){
        die('ComproPago Tables Not Found');
	}

	switch ($response->type){
		case 'charge.success':
			$nomestatus = "COMPROPAGO_SUCCESS";
			break;
		case 'charge.pending':
			$nomestatus = "COMPROPAGO_PENDING";
			break;
		case 'charge.declined':
			$nomestatus = "COMPROPAGO_DECLINED";
			break;
		case 'charge.expired':
			$nomestatus = "COMPROPAGO_EXPIRED";
			break;
		case 'charge.deleted':
			$nomestatus = "COMPROPAGO_DELETED";
			break;
		case 'charge.canceled':
			$nomestatus = "COMPROPAGO_CANCELED";
			break;
		default:
			die('Invalid Response type');
	}

	$sql = "SELECT * FROM ".$wpdb->prefix."compropago_orders WHERE compropagoId = '".$response->id."' ";

	if ($row = $wpdb->get_row($sql)){

		$id_order = intval($row->storeOrderId);
		$recordTime = time();

		$order = new WC_Order( $id_order );

		switch($nomestatus){
			case 'COMPROPAGO_SUCCESS':
                if($complete_order == 'fin'){
                    $order->payment_complete();
                }else{
                    $order->update_status('processing', __( 'ComproPago - Payment Confirmed', 'compropago' ));
                }
			    break;

			case 'COMPROPAGO_PENDING':
                $order->update_status('pending', __( 'ComproPago - Pending Payment', 'compropago' ));
			    break;

			case 'COMPROPAGO_DECLINED':
				$order->update_status('cancelled', __( 'ComproPago - Declined', 'compropago' ));
			    break;

			case 'COMPROPAGO_EXPIRED':
				$order->update_status('cancelled', __( 'ComproPago - Expired', 'compropago' ));
			    break;

			case 'COMPROPAGO_DELETED':
				$order->update_status('cancelled', __( 'ComproPago - Deleted', 'compropago' ));
			    break;

			case 'COMPROPAGO_CANCELED':
				$order->update_status('cancelled', __( 'ComproPago - Canceled', 'compropago' ));
			    break;

			default:
				$order->update_status('on-hold', __( 'ComproPago - On Hold', 'compropago' ));
		}



		$sql = "UPDATE `".$wpdb->prefix."compropago_orders` SET `modified` = '".$recordTime."',
		`compropagoStatus` = '".$response->type."', `storeExtra` = '".$nomestatus."' WHERE `id` = '".$row->id."'";

		if(!$wpdb->query($sql)){
			die("Error Updating ComproPago Order Record at Store");
		}

		//save transaction
		$ioIn=base64_encode(serialize($resp_webhook));
		$ioOut=base64_encode(serialize($response));

		$wpdb->insert($wpdb->prefix . 'compropago_transactions', array(
				'orderId' 			=> $row->id,
				'date' 				=> $recordTime,
				'compropagoId'		=> $response->id,
				'compropagoStatus'	=> $response->type,
				'compropagoStatusLast'	=> $row->compropagoStatus,
				'ioIn' 				=> $ioIn,
				'ioOut' 			=> $ioOut
				)
			);

		echo('Orden '.$resp_webhook->id.', transacción ejecutada');

	}else{
		die('El número de orden no se encontro en la tienda');
	}
}catch (Exception $e){
	//something went wrong at sdk lvl
	die($e->getMessage());
}
