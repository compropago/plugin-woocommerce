<?php
/*
* Copyright 2015 Compropago. 
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
/**
 * @author Rolando Lucio <rolando@compropago.com>
 * @since 3.0.0
 */
//mod_security is enabled,throws 406 error or disable error 500
// webhook error on shared hosting as hostgator, bluehost, could disable it
/*
<IfModule mod_security.c>
SecFilterEngine Off
SecFilterScanPOST Off
</IfModule>
<IfModule mod_security2.c>
SecRuleEngine Off
</IfModule>
*/

//validate request
$request = @file_get_contents('php://input');
if(!$jsonObj = json_decode($request)){
	die('Tipo de Request no Valido');
}
//include wp files
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
//wordpress Rdy?
if (!defined('WP_SITEURL')){
	die("No se pudo inicializar WordPress");
}
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
	$compropagoClient = new Compropago\Client($compropagoConfig);
	$compropagoService = new Compropago\Service($compropagoClient);
	// Valid Keys?
	if(!$compropagoResponse = $compropagoService->evalAuth()){
		die("ComproPago Error: Llaves no validas");
	}
	// Store Mode Vs ComproPago Mode, Keys vs Mode & combinations
	if(! Compropago\Utils\Store::validateGateway($compropagoClient)){
		die("ComproPago Error: La tienda no se encuentra en un modo de ejecución valido");
	}
}catch (Exception $e) {
	//something went wrong at sdk lvl
	die($e->getMessage());
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
	if(!Db::getInstance()->execute("SHOW TABLES LIKE '"._DB_PREFIX_ ."compropago_orders'") ||
			!Db::getInstance()->execute("SHOW TABLES LIKE '"._DB_PREFIX_ ."compropago_transactions'")
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

	$sql = "SELECT * FROM "._DB_PREFIX_."compropago_orders	WHERE compropagoId = '".$response->id."' ";

	if ($row = Db::getInstance()->getRow($sql)){

		$id_order=intval($row['storeOrderId']);
		$recordTime=time();

		$extraVars = array();
		$history = new OrderHistory();
		$history->id_order = $id_order;
		$history->changeIdOrderState((int)Configuration::get($nomestatus),$history->id_order);
		//$history->addWithemail(true,$extraVars);
		$history->addWithemail();
		$history->save();

		$sql = "UPDATE `"._DB_PREFIX_."compropago_orders`
				SET `modified` = '".$recordTime."', `compropagoStatus` = '".$response->status."', `storeExtra` = '".$nomestatus."'
				 WHERE `id` = '".$row['id']."'";
		if(!Db::getInstance()->execute($sql)){
			die("Error Updating ComproPago Order Record at Store");
		}
		//bas64 cause prestashop db
		//webhook
		$ioIn=base64_encode(json_encode($jsonObj));
		//verify response
		$ioOut=base64_encode(json_encode($response));

		Db::getInstance()->autoExecute(_DB_PREFIX_ . 'compropago_transactions', array(
				'orderId' 			=> $row['id'],
				'date' 				=> $recordTime,
				'compropagoId'		=> $response->id,
				'compropagoStatus'	=> $response->type,
				'compropagoStatusLast'	=> $row['compropagoStatus'],
				'ioIn' 				=> $ioIn,
				'ioOut' 			=> $ioOut
		),'INSERT');

		echo('Orden '.$jsonObj->id.' Confirmada');

	}else{
		die('El número de orden no se encontro en la tienda');
	}
}catch (Exception $e){
	//something went wrong at sdk lvl
	die($e->getMessage());
}
	

	
/*
	$status = $event_json->{'type'};
	if ( $status == 'charge.pending' ) {
		$status = 'pending';
	} elseif ( $status == 'charge.success' ) {
		$status = 'processing';
	}elseif ( $status == 'charge.decline' ) {
		$status = 'refunded';
	}elseif ( $status == 'charge.deleted' ) {
		$status = 'refunded';
	}
*/

	
	echo json_encode( $event_json );
//echo '<pre>'.print_r($body).'</pre><b>{error:none}</b>';
?>

