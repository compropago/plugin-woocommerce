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
 * @example Ejemplo básico de conexión
 * @since 1.0.1
 * @author Rolando Lucio <rolando@compropago.com>
 * @version 1.0.1
 */

require_once dirname(__FILE__).'/../vendor/autoload.php';


//Registrate en https://compropago.com/ para poder obtener llaves de acceso
$compropagoConfig= array(
		/**
		 * Obten tus llaves desde el menú de configuración de tu panel de control de ComproPago 
		 */
				//Llave pública
				'publickey'=>'pk_test_TULLAVEPUBLICA',
				//Llave privada 
				'privatekey'=>'sk_test_TULLAVE PRIVADA',
				//Estas probando?, descomenta la sig. línea y utiliza tus llaves de Modo Pruebas
				//'live'=>false
				'live'=>true
		);

$compropagoClient= new Compropago\Client($compropagoConfig);
$compropagoService= new Compropago\Service($compropagoClient);


//Campos Obligatorios para poder realizar una nueva orden
$data = array(
		'order_id'    		 => 'testorderid',
		'order_price'        => '123.45',
		'order_name'         => 'Test Order Name',
		'customer_name'      => 'Compropago Test',
		'customer_email'     => 'test@compropago.com',
		'payment_type'       => 'OXXO'
);
echo '<pre>'. json_encode( $compropagoService->placeOrder($data) ). '</pre>';


