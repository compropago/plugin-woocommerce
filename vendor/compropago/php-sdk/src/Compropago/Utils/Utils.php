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
 * @since 1.0.1
 */
namespace Compropago\Utils;

class Utils{

	/**
	 * Normalize all keys in an array to lower-case.
	 * @param array $arr
	 * @return array Normalized array.
	 * @since 1.0.1
	 */
	public static function normalize($arr)
	{
		if (!is_array($arr)) {
			return array();
		}
		$normalized = array();
		foreach ($arr as $key => $val) {
			$normalized[strtolower($key)] = $val;
		}
		return $normalized;
	}
	/**
	 * Convert a string to camelCase
	 * @param  string $value
	 * @return string
	 * @since 1.0.1
	 */
	public static function camelCase($value)
	{
		$value = ucwords(str_replace(array('-', '_'), ' ', $value));
		$value = str_replace(' ', '', $value);
		$value[0] = strtolower($value[0]);
		return $value;
	}
	
	/**
	 * Convert Array to QueryString or validate string
	 * @param array $query | string QueryString
	 * @param string $prefix
	 * @since 1.0.1
	 */
	public static function encodeQueryString( $query, $prefix=null) {
		if (!is_array($query)){
			if (parse_url($query, PHP_URL_QUERY)){
				return $query;
			}else{
				return $query;
			}
		}
		
		
		return http_build_query($query,$prefix);
	}
	
	/**
	 * Append & normalize old Api versions response to latest
	 * @param mixed $json array or json object
	 * @return object JSON
	 * @throws Exception
	 * @since 1.0.2
	 */
	public static function normalizeResponse($json){
		if(!json_encode($json)){
			throw new Exception('Invalid Object Type');
		}
		
		switch ($json->api_version){
			case '1.0':
				
					//charge 
					if(isset($json->payment_id) && !empty($json->payment_id)){
						return self::normalizeCharge($json);	
					}
					//verify 
					
					if(isset($json->data->object->id) &&  !empty($json->data->object->id)){
						
						return self::normalizeVerify($json);
					}
				
					return $json; //cant find you a place to live
				break;
			case '1.1':
				return $json;
				break;
				// not supported version
			default:
				return $json;
		}
	}
	/**
	 * Verify Normalize
	 * Append current Api response for old ones
	 * @param mixed $json array or json object
	 * @return object JSON
	 * @throws Exception
	 * @since 1.0.2
	 */
	private static function normalizeVerify($json){
		if(!json_encode($json)){
			throw new Exception('Invalid Object Type');
		}
		if(isset($json->data->object->id) &&  !empty($json->data->object->id) && $json->api_version=='1.0'){
			$json->sdk->action='normalized';
			$json->sdk->description='verify 1.0 to 1.1, appended to object';
				
			$json->id=$json->data->object->id;
			$json->short_id=$json->data->object->short_id;
			//$json->type; nothing to map, same stuff
			//$json->object = 'charge'; //cant override key exists in both, may be i use by someone?
			//$json->livemode; //no info in 1.0
			$json->created = strtotime($json->data->object->created);
			$json->paid=$json->data->object->paid;
			$json->amount=$json->data->object->amount;
			$json->currency=$json->data->object->currency;
			$json->refunded=$json->data->object->refunded;
			$json->fee=$json->data->object->fee;
			$json->fee_details=$json->data->object->fee_details;
			//$json->fee_details->tax; //not defined 1.0
			$json->order_info->order_id=$json->data->object->payment_details->product_id;
			$json->order_info->order_name=$json->data->object->payment_details->product_name;
			$json->order_info->order_price=$json->data->object->payment_details->product_price;
			$json->order_info->payment_method=$json->data->object->payment_details->object;
			$json->order_info->store=$json->data->object->payment_details->store;
			$json->order_info->country=$json->data->object->payment_details->country;
			$json->order_info->image_url=$json->data->object->payment_details->image_url;
			$json->order_info->success_url=$json->data->object->payment_details->success_url;
			$json->customer->customer_name=$json->data->object->payment_details->customer_name;
			$json->customer->customer_email=$json->data->object->payment_details->customer_email;
			$json->customer->customer_phone=$json->data->object->payment_details->customer_phone;
			$json->captured=$json->data->object->captured;
			$json->failure_message=$json->data->object->failure_message;
			$json->failure_code=$json->data->object->failure_code;
			$json->amount_refunded=$json->data->object->amount_refunded;
			$json->description=$json->data->object->description;
			$json->dispute=$json->data->object->dispute;	
		
		}
		return $json;
	}
	/**
	 * Charge Normalize
	 * Append current Api response for old ones
	 * @param mixed $json array or json object
	 * @return object JSON
	 * @throws Exception
	 * @since 1.0.2
	 */
	private static function normalizeCharge($json){
		if(!json_encode($json)){
			throw new Exception('Invalid Object Type');
		}
		if(isset($json->payment_id) && !empty($json->payment_id) && $json->api_version=='1.0'){
			$json->sdk->action='normalized';
			$json->sdk->description='charge 1.0 to 1.1, appended to object';
			
			$json->id=$json->payment_id;
			$json->short_id=$json->short_payment_id;
			$json->object = 'charge';
			$json->created = strtotime($json->creation_date);
			$json->exp_date = strtotime($json->expiration_date);
			$json->status=strtolower($json->payment_status);
			//$json->livemode; //no info in 1.0	
			$json->order_info->order_id=$json->product_information->product_id;
			$json->order_info->order_name=$json->product_information->product_name;
			$json->order_info->order_price=$json->product_information->product_price;		
			//$json->fee_details;//no info in 1.0	
			$json->instructions->description = $json->payment_instructions->description;
			$json->instructions->step_1 = $json->payment_instructions->step_1;
			$json->instructions->step_2 = $json->payment_instructions->step_2;
			$json->instructions->step_3 = $json->payment_instructions->step_3;
			$json->instructions->note_extra_comition = $json->payment_instructions->note_extra_comition;
			$json->instructions->note_expiration_date = $json->payment_instructions->note_expiration_date;
			$json->instructions->note_confirmation = $json->payment_instructions->note_confirmation;
			$json->instructions->details->amount = $json->payment_instructions->details->payment_amount;
			$json->instructions->details->store = $json->payment_instructions->details->payment_store;
			$json->instructions->details->bank_account_number = $json->payment_instructions->details->bank_account_number;
			$json->instructions->details->bank_name = $json->payment_instructions->details->bank_name;
			
		}
		return $json;
	}
}