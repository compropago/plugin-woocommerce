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
 * @since 1.0.1
 * @author Rolando Lucio <rolando@compropago.com>
 */

namespace Compropago\Sdk;

use Compropago\Sdk\Http\Request;


class Client
{
	
	const VERSION="1.1.0";
	const API_LIVE_URI='https://api.compropago.com/v1/';
	const API_SANDBOX_URI='https://api.compropago.com/v1/';
	
	const USER_AGENT_SUFFIX = "compropago-php-sdk/";
	/**
	 * @since 1.0.1
	 * @var string deployUri
	 */
	private $deployUri;	
	/**
	 * @since 1.0.1
	 * @var boolean deployMode
	 */
	private $deployMode;
	/**
	 * @since 1.0.1
	 * @var array auth
	 */
	private $auth;	
	/**
	 * @since 1.0.1
	 * @var Compropago\Request $http
	 */
	private $http;
	
	
	/**
	 * Compropago Client Constructor
	 * @param array $params
	 * @throws Exception Missing compropago Keys
	 * @since 1.0.1
	 */
	public function __construct($params = array())
	{
		if( !array_key_exists('publickey', $params) ||
			!array_key_exists('privatekey', $params) ||
			empty($params['publickey']) || empty($params['privatekey'])
			){
			$error= "Se requieren las llaves del API de Compropago";
			throw new Exception($error);
		}else{
			$this->auth=array($params['privatekey'],$params['publickey']); 
				
			
		//Modo Activo o Pruebas 
			if($params['live']==true){
				$this->deployUri=self::API_LIVE_URI;
				$this->deployMode=true;
			}else{
				$this->deployUri=self::API_SANDBOX_URI;
				$this->deployMode=false;
			}
			
			
			if(isset($params['contained']) && !empty($params['contained']) ){
				$extra=$params['contained'];
			}else {
				$extra='SDK; PHP '. phpversion().';';
			}
			$http= new Request($this->deployUri);
			$http->setUserAgent(self::USER_AGENT_SUFFIX, $this->getVersion(),$extra);
			$http->setAuth($this->auth);
			$this->http=$http;
			
		}
			
	}
	
	/**
	 * Get deploy mode
	 * @return bool
	 * @since 1.0.2
	 */
	public function getMode()
	{
		return $this->deployMode;
	}
	
	
	/**
	 * Get current version
	 * @return string 
	 * @since 1.0.1
	 */
	public function getVersion()
	{
		return self::VERSION;	
	}
	/**
	 * Get Request Object
	 * @return Compropago\Http\Request
	 * @since 1.0.1
	 */
	public function getHttp()
	{
		return $this->http;
	}
	
	
	
	
}	
