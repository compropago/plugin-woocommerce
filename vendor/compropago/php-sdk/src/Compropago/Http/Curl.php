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
 * @since 1.0.1
 * @author Rolando Lucio <rolando@compropago.com>
 * @version 1.0.1
 */
 
namespace Compropago\Sdk\Http;

use Compropago\Sdk\Client;
use Compropago\Sdk\Exception;
use Compropago\Sdk\Http\Request;


class Curl
{
	/**
	 * cURL hex representation of version 7.30.0
	 * @since 1.0.1
	 */
	const NO_QUIRK_VERSION = 0x071E00;
	
	
	/**
	 * connection headers 
	 * @var array
	 */
	private static $CONNECTION_ESTABLISHED_HEADERS = array(
			"HTTP/1.0 200 Connection established\r\n\r\n",
			"HTTP/1.1 200 Connection established\r\n\r\n",
	);
	
	/**
	 * @throws Compropago\Exception en error de librerias 
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	
	//Singleton Curl or Client, or not?
	public function __construct()
	{
		if (!extension_loaded('curl') || !function_exists('curl_init')) {
			$error="Compropago no se puede ejecutar: se requiere la extensión Curl en el servidor";
			throw new Exception($error);
		}
	}
	
	/**
	 * format and make curl response
	 * @param Compropago\Request $request objet with valida request parameters
	 * @return array  ASSOC responseBody responseHeaders responseCode
	 * @throws Curl exception 
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function executeRequest(Request $request)
	{
		$curl = curl_init();
		if ($request->getData() && $request->getRequestMethod()=='POST') {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getData());
		}
		$requestHeaders = $request->getRequestHeaders();
		if ($requestHeaders && is_array($requestHeaders)) {
			$curlHeaders = array();
			foreach ($requestHeaders as $k => $v) {
				$curlHeaders[] = "$k: $v";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
		}
		curl_setopt($curl, CURLOPT_URL, $request->getServiceUrl());		
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getRequestMethod());
		curl_setopt($curl, CURLOPT_USERAGENT, $request->getUserAgent());
		curl_setopt($curl, CURLOPT_USERPWD, $request->getAuth());		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		if(function_exists('curl_setopt_array')){
			curl_setopt_array($curl, $request->getOptions());
		}else{
			//throws Warning
			foreach ($request->getOptions() as $key => $var) {
				curl_setopt($curl, $key, $var);
			}
		}
		$response = curl_exec($curl);
		if ($response === false) {
			$error = curl_error($curl);
			$code = curl_errno($curl);
			if ($code == 60 || $code == 77) {
				curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacerts.pem');
				$response = curl_exec($curl);
			}
			if($response==false){
				$error = curl_error($curl);
				$code = curl_errno($curl);
				throw new Exception($error, $code);
			}
			
		}
		$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		list($responseHeaders, $responseBody) = $this->parseHttpResponse($response, $headerSize);
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		return array(
				'responseBody'=>$responseBody, 
				'responseHeaders'=>$responseHeaders, 
				'responseCode'=>$responseCode				
		);
	}
	
	/**
	 * Split response header & body
	 * @param unknown $respData
	 * @param unknown $headerSize
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function parseHttpResponse($respData, $headerSize)
	{
		// check proxy header
		foreach (self::$CONNECTION_ESTABLISHED_HEADERS as $established_header) {
			if (stripos($respData, $established_header) !== false) {
				
				$respData = str_ireplace($established_header, '', $respData);
				
				if (!$this->needsQuirk()) {
					$headerSize -= strlen($established_header);
				}
				break;
			}
		}
		if ($headerSize) {
			$responseBody = substr($respData, $headerSize);
			$responseHeaders = substr($respData, 0, $headerSize);
		} else {
			$responseSegments = explode("\r\n\r\n", $respData, 2);
			$responseHeaders = $responseSegments[0];
			$responseBody = isset($responseSegments[1]) ? $responseSegments[1] :
			null;
		}
		$responseHeaders = $this->getHttpResponseHeaders($responseHeaders);
		return array($responseHeaders, $responseBody);
	} 
	
	/**
	 * parse header selector
	 * @param mixed $rawHeaders string or array headers
	 * @return mixed parsed headers
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getHttpResponseHeaders($rawHeaders)
	{
		if (is_array($rawHeaders)) {
			return $this->parseArrayHeaders($rawHeaders);
		} else {
			return $this->parseStringHeaders($rawHeaders);
		}
	}
	
	/**
	 * parse headers in array form
	 * @param array $rawHeaders
	 * @return array parsed header array
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	private function parseArrayHeaders($rawHeaders)
	{
		$header_count = count($rawHeaders);
		$headers = array();
		for ($i = 0; $i < $header_count; $i++) {
			$header = $rawHeaders[$i];
			
			$header_parts = explode(': ', $header, 2);
			if (count($header_parts) == 2) {
				$headers[strtolower($header_parts[0])] = $header_parts[1];
			}
		}
		return $headers;
	}
	/**
	 * parse headers to array
	 * @return array headers
	 * @param string $rawHeaders
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	private function parseStringHeaders($rawHeaders)
	{
		$headers = array();
		$responseHeaderLines = explode("\r\n", $rawHeaders);
		foreach ($responseHeaderLines as $headerLine) {
			if ($headerLine && strpos($headerLine, ':') !== false) {
				list($header, $value) = explode(': ', $headerLine, 2);
				$header = strtolower($header);
				if (isset($headers[$header])) {
					$headers[$header] .= "\n" . $value;
				} else {
					$headers[$header] = $value;
				}
			}
		}
		return $headers;
	}
	/**
	 * verify curl version
	 * @return boolean
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	protected function needsQuirk()
	{
		$ver = curl_version();
		$versionNum = $ver['version_number'];
		return $versionNum < self::NO_QUIRK_VERSION;
	}
}
?>