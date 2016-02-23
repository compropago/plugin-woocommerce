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
 * @since 1.0.1
 * @author Rolando Lucio <rolando@compropago.com>
 */

namespace Compropago\Sdk;

use Compropago\Sdk\Client;
use Compropago\Sdk\Http\Rest;
use Compropago\Sdk\Utils\Utils;


class Service{
	/**
	 * @var Compropago\Client client
	 */
	private $client;
	
	/**
	 * @param Compropago\Client $client
	 * @since 1.0.1
	 */
	public function __construct(Client $client)
	{
		$this->client=$client;
	}
	/**
	 * Validate API key
	 * @return boolean
	 * @retunr json responseBody
	 * @since 1.0.2
	 */
	public function evalAuth()
	{
		
		$response=Rest::doExecute($this->client,'users/auth');
		
		//Error Mng Imp Test
		$httpCode=$response['responseCode'];
		switch ($httpCode){
			case '401':
				return false;
			break;
					
			case '200':
				return json_decode($response['responseBody']);
			break;
				
			default:
				$error= 'ComproPago Unexpected http code error';
				throw new Exception($error, $httpCode);
				return;	
		}	
	}
	
	/**
	 * Get where to pay providers
	 * @return json
	 * @since 1.0.1
	 */
	public function getProviders()
	{
		$response=Rest::doExecute($this->client,'providers/true');
		$jsonObj= json_decode($response['responseBody']);	
		usort($jsonObj, function($a, $b) { 
			return $a->rank > $b->rank ? 1 : -1; 
		});	
		return $jsonObj;
	}
	/**
	 * Verify order Id status
	 * @param string $orderId
	 * @return json
	 * @since 1.0.1
	 */
	public function verifyOrder( $orderId )
	{
		$response=Rest::doExecute($this->client,'charges/'.$orderId);
		$jsonObj= json_decode($response['responseBody']);
		

		//normalize to latest api version structure if charge response
		if($jsonObj->api_version=='1.0' &&  isset($jsonObj->data->object->id) &&  !empty($jsonObj->data->object->id))
			$jsonObj= Utils::normalizeResponse($jsonObj);
		
		return $jsonObj;
	}
	/**
	 * place new order
	 * @param array $params
	 * @since 1.0.1
	 */
	public function placeOrder( $params )
	{
		$response=Rest::doExecute($this->client,'charges/',$params,'POST');
		$jsonObj= json_decode($response['responseBody']);
		
		//normalize to latest api version structure if charge was created
		if($jsonObj->api_version=='1.0' && $jsonObj->payment_status=='PENDING')
			$jsonObj= Utils::normalizeResponse($jsonObj);
		
		return $jsonObj;	
	}
	
}