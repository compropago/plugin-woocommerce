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
* GuzzleHttp implementation of Compropago API
* @author Rolando Lucio <rolando@compropago.com>
*/

namespace Compropago\Http;

use GuzzleHttp;


class Rest{
	
/**
 * 
 * @param array $auth
 * @param GuzzleHttp\Client $client
 * @param unknown $service
 * @param string $query
 * @param string $method
 * @returns Array
 */
	public static function doExecute($auth,GuzzleHttp\Client $client,$service,$query=FALSE,$method='GET') {
		$requestParams=['auth'=>$auth];
		if($query && $method=='POST'){
			$requestParams['json']=$query;
		}
		$res = $client->request($method,$service,$requestParams);
		return $res;		
	}
}