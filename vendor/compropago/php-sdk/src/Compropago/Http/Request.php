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
 * @version 1.0.1
 */
namespace Compropago\Http;

use Compropago\Utils\Utils;
use Compropago\Exception;

class Request{
	
	protected $userAgent;
	protected $requestMethod;
	protected $url;
	protected $options =array();
	protected $data;
	protected $requestHeaders;
	protected $auth;
	protected $serviceUrl;
	protected $service;
	
	/**
	 * 
	 * @param string $url
	 * @param string $method
	 * @param array $headers
	 * @param mixed $data
	 * @throws Exception
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function __construct($url,$method = 'GET',$headers = array(),$data = null) {
		if(empty($url)){
			throw new Exception('Missing Url');
		}
				$this->setUrl($url);
				$this->setRequestMethod($method);
				$this->setRequestHeaders($headers);
				$this->setData($data);
	}
	/**
	 * set url
	 * @param string $url
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setUrl($url){
		$this->url=$url;
	}
	/**
	 * set service to request
	 * @param unknown $service
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setServiceUrl($service){
		if($service){
			$this->serviceUrl=$this->url.$service;
			$this->service=$service;
		}
	}
	/**
	 * get service url
	 * @return request url
	 * @since 1.0.1
	 * @version 1.0.1 
	 */
	public function getServiceUrl(){
		return (isset($this->serviceUrl) && !empty($this->serviceUrl)) ? $this->serviceUrl : $this->url;
	}
	/**
	 * set auth
	 * @param array $arr
	 * @return false
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setAuth($arr){
		if(!is_array($arr)){
			return false;
		}
		//eval keys reg express
		$this->auth= $arr[0] . ":" . $arr[1];
	}
	/**
	 * @return string auth
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getAuth(){
		return $this->auth;
	}
	
	/**
	 * merge options for curl
	 * @param unknown $options
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setOptions($options){
	    $this->options = array_merge($options, $this->options);
	}
	/**
	 * @return array options
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getOptions(){
		return $this->options;
	}
	
	/**
	 * Set Method Options
	 * @param string $method
	 * @throws Exception
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setMethodOptions($method){
		switch ($method){
			case 'GET':
				$this->setOptions(array(CURLOPT_HTTPGET=>1));
			break;
			case 'DELETE':
				$this->setOptions(array(CURLOPT_CUSTOMREQUEST=>'DELETE'));
			break;
			case 'POST':
				$this->data=json_encode($this->data);
				$this->setOptions(array( 
						CURLOPT_POST=>1,
						CURLOPT_POSTFIELDS => $this->data
				) );
				
			break;
			case 'PUT':
				$this->data=json_encode($this->data);
				$this->setOptions(array(
						CURLOPT_CUSTOMREQUEST=>'PUT',
						CURLOPT_POSTFIELDS => $this->data
				) );
			break;			
			default:
				throw new Exception('Invalid Request Method');
		}
		if(!$this->evalData() && $method!='GET'){
			throw new Exception('Method require Data');
		}	
	}
	/** 
	 * @param string $method
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setRequestMethod($method){
		$this->requestMethod = strtoupper($method);
		return true;
	}
	/**
	 * normalize header
	 * @param array $headers
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setRequestHeaders($headers){
		$headers = Utils::normalize($headers);
		if ($this->requestHeaders) {
			$headers = array_merge($this->requestHeaders, $headers);
		}
		$this->requestHeaders = $headers;
	}
	/**
	 * @return array
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getRequestHeaders(){
		return $this->requestHeaders;
	}
	/**
	 * set user agent
	 * @param string $suffix
	 * @param string $prefix
	 * @param string $contained
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setUserAgent($suffix,$prefix,$contained=null){
		$this->userAgent= ($contained) ? $suffix.$prefix.' ('.$contained.')' : $suffix.$prefix;
	}
	/**
	 * @return string useragent
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getUserAgent(){
		return $this->userAgent;
	}
	/**
	 * @return string method
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function getRequestMethod(){
		return $this->requestMethod;
	}
	
	/**
	 * set data to post
	 * @param mixed $data
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function setData($data){
		$this->data=$data;
	}
	public function getData(){
		return $this->data;
	}
	
	/**
	 * Check if data is going to be sent or no data
	 * @return boolean 
	 * @throws Exception
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public function evalData(){
	    if (($this->getRequestMethod() == "POST" || $this->getRequestMethod() == "PUT" ) && !empty($this->data)) {
	    	if(!json_decode($this->data)){
	    		throw new Exception('Invalid Json for Data');
	    	}
	    	
			$this->setRequestHeaders(
		          array(
		            "content-type" => "application/json",
		          	"content-length" => strlen($this->data)
		          )
		    );
			return true;
	    }
	    
	    if(($this->getRequestMethod() == "GET" || $this->getRequestMethod() == "DELETE" ) && !empty($this->data)){
	    	
	    	$this->data=Utils::encodeQueryString($this->data);
	    	if(!$this->data){
	    		throw new Exception('Invalid Query String for Data');
	    	}
	    	$this->setServiceUrl($this->service.'?'.$this->data);
	    	return true;
	    }
	    
	    if(!empty($this->data)){
	    	throw new Exception('Method should be defined');
	    }
	    //no data
	    return false;   
	} 
	
	
}