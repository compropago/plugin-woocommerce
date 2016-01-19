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
 * @example Como obtener la lista de sitios de pago
 * @since 1.0.1
 * @author Rolando Lucio <rolando@compropago.com>
 * @version 1.0.1
 */
require_once dirname(__FILE__).'/../vendor/autoload.php';

use Compropago\Client;
use Compropago\Controllers\Views;
use Compropago\Service;


//Registrate en https://compropago.com/ para poder obtener llaves de acceso
$compropagoConfig= array(

		 // Obten tus llaves desde el menú de configuración de tu panel de control de ComproPago

		//Llave pública
		'publickey'=>'pk_test_TULLAVEPUBLICA',
		//Llave privada 
		'privatekey'=>'sk_test_TULLAVE PRIVADA',
		//Estas probando?, descomenta la sig. línea y utiliza tus llaves de Modo Pruebas
		//'live'=>false
		'live'=>true
);

$compropagoClient= new Client($compropagoConfig);
$compropagoService= new Service($compropagoClient);
$compropagoData['providers']=$compropagoService->getProviders();
$compropagoData['showlogo']='yes';
$compropagoData['description']='Plugin Descriptor compropago';
$compropagoData['instrucciones']='Compropago Instrucciones';
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../assets/css/compropago.css">
</head>
<body>
	<?php Views::loadView('providers',$compropagoData);?>

</body>
</html>