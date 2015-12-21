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
 */
namespace Compropago;

use Compropago;
use Compropago\Http\Rest;


class Service{
	/**
	 *
	 * @var GuzzleHttp\Client client
	 */
	private $client;
	/**
	 * @var array auth
	 */
	private $auth;
	
	/**
	 * 
	 * @param GuzzleHttp\Client $client
	 */
	public function __construct(Compropago\Client $client){
		$this->client=$client->getHttp();
		$this->auth=$client->getAuth();
	}
	
	/**
	 * @return json
	 */
	public function getProviders(){
		$response=Rest::doExecute($this->auth,$this->client,'providers/true');
		return json_decode( $response->getBody() );
	}
	/**
	 * 
	 * @param string $orderId
	 * @return json
	 */
	public function verifyOrder( $orderId){
		$response=Rest::doExecute($this->auth,$this->client,'charges/'.$orderId);
		return json_decode( $response->getBody() );
	}
	
	public function placeOrder($params){
		$response=Rest::doExecute($this->auth,$this->client,'charges/',$params,'POST');
		return json_decode( $response->getBody() );
		
	}
}