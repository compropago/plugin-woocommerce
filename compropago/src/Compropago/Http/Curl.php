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
 * cUrl implementation of Compropago API
 *
 * @author Rolando Lucio <rolando@compropago.com>
 */
 
namespace Compropago\Http;

use Compropago;

class Curl{

	/**
	 * @param Compropago_Client los datos 
	 * @return void
	 * @throws Compropago_Exception en error de librerias 
	 * dev-notes, future: puede extender de una clase abstracta o ser extendida de una para funciones rest si la misma API debe hacer llamadas externas a 3ros
	 */
	public function __construct(Compropago\Client $client){
		if (!extension_loaded('curl') && function_exists('curl_init')) {
			$error="Compropago no se puede ejecutar: se requiere la extensión Curl en el servidor";
			throw new Compropago_Exception($error);
			
		}
	}
	
	/**
	 * @param Compropago_Request $request objeto con los parametros validados de una petición
	 * @return array [headers,body,http] 
	 */
	public function executeRequest(Compropago_Request $request){
		
	}
	 
}
?>