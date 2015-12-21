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
 * Compropago API
 * 
 * @author Rolando Lucio <rolando@compropago.com>
 */

namespace Compropago;
use GuzzleHttp;


class Client{
	
	const VERSION="1.0.0-dev";
	const API_LIVE_URI='https://api.compropago.com/v1/';
	//note a https://sandbox.compropago.com/v1/
	const API_SANDBOX_URI='https://api.compropago.com/v1/'; 
	const USER_AGENT_SUFFIX = "compropago-sdk-php-client/";
	
	
	/**
	 * @var string deployUri
	 */
	private $deployUri;
	
	/**
	 * @var boolean deployMode
	 */
	private $deployMode;
	
	/**
	 * @var array auth
	 */
	private $auth;
	
	
	/**
	 * @var GuzzleHttp\ClientInterface $http
	 */
	private $http;
	
	
	/**
	 * Compropago Client Constructor
	 * @param array $params
	 * @throws Exception
	 */
	public function __construct($params = array()){
		if( !array_key_exists('publickey', $params) ||
			!array_key_exists('privatekey', $params) ||
			empty($params['publickey']) || empty($params['privatekey'])
			){
			$error= "Se requieren las llaves del API de Compropago";
			throw new Exception($error);
		}else{
			$this->auth=[$params['privatekey'],$params['publickey']];
				
			
			//Modo Activo o Pruebas
			if($params['live']==true){
				$this->deployUri=self::API_LIVE_URI;
				$this->deployMode=true;
			}else{
				$this->deployUri=self::API_SANDBOX_URI;
				$this->deployMode=false;
			}
			
			$this->http= new GuzzleHttp\Client(['base_uri' => $this->deployUri]);
			
			
			
		}
			
	}
	
	/**
	 * @return string 
	 */
	public function getVersion(){
		return self::VERSION;	
	}

	public function getHttp(){
		return $this->http;
	}
	public function getAuth(){
		return $this->auth;
	}
	
	
	
}	