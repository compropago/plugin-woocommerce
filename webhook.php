<?php

/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @since 3.0.0
 */

$wpFiles= array(
	dirname(__FILE__).'/../../../wp-load.php',
);



foreach($wpFiles as $wpFile){
	if(file_exists($wpFile)){
		include_once $wpFile;
	}else{
		echo "ComproPago Warning: No se encontro el archivo:".$wpFile."<br>";
	}
}


//validate request
$request = @file_get_contents('php://input');
if(!$jsonObj = json_decode($request)){
	die('Tipo de Request no Valido');
}



//wordpress Rdy?
// error de compatibilidad con combinaciones de php y wp
//if (!defined('WP_SITEURL')){
//	die("No se pudo inicializar WordPress");
//}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );



//Check if WooCommerce is active
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	die("No se pudo inicializar WooCommerce");
}


//include ComproPago SDK & dependecies via composer autoload
$compropagoComposer= dirname(__FILE__).'/vendor/autoload.php';
if ( file_exists( $compropagoComposer ) ){
	require $compropagoComposer;
}else{
	die('No se encontro el autoload para Compropago y sus dependencias:'.$compropagoComposer);
}



//Compropago Plugin Active?
if (!is_plugin_active( 'compropago/index.php' ) ){
	die('ComproPago no se encuentra activo en Wordpress');
}

//Get ComproPago  Config values
$config = get_option('woocommerce_compropago_settings');
if($config['enabled']!='yes'){
	die('ComproPago no se encuentra activo en Woocommerce');
}

//Configuration::getMultiple(array('COMPROPAGO_PUBLICKEY', 'COMPROPAGO_PRIVATEKEY','COMPROPAGO_MODE'));
//keys set?
if (!isset($config['COMPROPAGO_PUBLICKEY']) || !isset($config['COMPROPAGO_PRIVATEKEY'])
		|| empty($config['COMPROPAGO_PUBLICKEY']) || empty($config['COMPROPAGO_PRIVATEKEY'])){
			die("Se requieren las llaves de compropago");
}



//Compropago SDK config
if($config['COMPROPAGO_MODE']=='yes'){
	$moduleLive=true;
}else {
	$moduleLive=false;
}



$compropagoConfig= array(
		'publickey'=>$config['COMPROPAGO_PUBLICKEY'],
		'privatekey'=>$config['COMPROPAGO_PRIVATEKEY'],
		'live'=>$moduleLive,
		'contained'=>'plugin; cpwc 3.0.0 ; woocommerce '.$woocommerce->version.'; wordpress '.$wp_version.'; webhook;'
	
);



// consume sdk methods
try{
	$compropagoClient = new Compropago\Sdk\Client($compropagoConfig);
	$compropagoService = new Compropago\Sdk\Service($compropagoClient);
	// Valid Keys?
	if(!$compropagoResponse = $compropagoService->evalAuth()){
		die("ComproPago Error: Llaves no validas");
	}
	// Store Mode Vs ComproPago Mode, Keys vs Mode & combinations
	if(! Compropago\Sdk\Utils\Store::validateGateway($compropagoClient)){
		die("ComproPago Error: La tienda no se encuentra en un modo de ejecución valido");
	}
}catch (Exception $e) {
	//something went wrong at sdk lvl
	die($e->getMessage());
}



//api normalization
if($jsonObj->api_version=='1.0'){
	$jsonObj->id=$jsonObj->data->object->id;
	$jsonObj->short_id=$jsonObj->data->object->short_id;  
}



//webhook Test?
if($jsonObj->id=="ch_00000-000-0000-000000" || $jsonObj->short_id =="000000"){
	die("Probando el WebHook?, <b>Ruta correcta.</b>");
}



try{
	$response = $compropagoService->verifyOrder($jsonObj->id);
	if($response->type=='error'){
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

		$id_order=intval($row->storeOrderId);
		$recordTime=time();
		
		//update store
		/*
		   $order_statuses = array(
		    'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
		    'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
		    'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
		    'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
		    'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
		    'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
		    'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
		  );
        */
		$order = new WC_Order( $id_order );
		switch($nomestatus){
			case 'COMPROPAGO_SUCCESS':
                if($config['COMPROPAGO_COMPLETED_ORDER'] == 'fin'){
                    $order->payment_complete();
                }else{
                    $order->update_status('processing', __( 'ComproPago - Payment Confirmed', 'compropago' ));
                }
			    break;

			case 'COMPROPAGO_PENDING':
                $order->update_status('pending', __( 'ComproPago - Pending Payment', 'compropago' ));

                /*if($config['COMPROPAGO_COMPLETED_ORDER'] == 'init'){
                    $order->reduce_order_stock();
                }*/
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
		
		

		$sql = "UPDATE `".$wpdb->prefix."compropago_orders`
				SET `modified` = '".$recordTime."', `compropagoStatus` = '".$response->type."', `storeExtra` = '".$nomestatus."'
				 WHERE `id` = '".$row->id."'";
		if(!$wpdb->query($sql)){
			die("Error Updating ComproPago Order Record at Store");
		}
		//save transaction
		$ioIn=base64_encode(json_encode($jsonObj));
		$ioOut=base64_encode(json_encode($response));

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
		
		echo('Orden '.$jsonObj->id.', transacción ejecutada');

	}else{
		die('El número de orden no se encontro en la tienda');
	}
}catch (Exception $e){
	//something went wrong at sdk lvl
	die($e->getMessage());
}