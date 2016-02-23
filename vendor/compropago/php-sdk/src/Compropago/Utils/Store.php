<?php
/*
* Copyright 2016 Compropago.
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
 * @since 1.0.2
 * @author Rolando Lucio <rolando@compropago.com>
 */
namespace Compropago\Sdk\Utils;

use Compropago\Sdk\Client;
use Compropago\Sdk\Exception;
use Compropago\Sdk\Service;

class Store{
	/**
	 * Validate if config params allow transactions
	 * @return bool
	 * @param Client $Client
	 * @since 1.0.2
	 */
	public static function validateGateway(Client $Client)
	{
		if(!isset($Client)){
			return false;
		}
		$moduleLive=$Client->getMode();
		try{
		    //lets make new service
			$compropagoService = new Service($Client);
			if(!$compropagoResponse = $compropagoService->evalAuth()){
				//not proper keys?
				return false;
			}
			if($compropagoResponse->mode_key != $compropagoResponse->livemode){
				//compropagoKey vs compropago Mode
				return false;
			}
			if($moduleLive != $compropagoResponse->livemode){
				// store Mode vs compropago Mode
				return false;
			}
			if($moduleLive != $compropagoResponse->mode_key){
				// store Mode vs compropago Keys
				return false;
			}
		}catch (Exception $e) {
			//should rethrow?
			//echo  $e->getMessage();
			return false;
		}	
		// Ok Move on
		return true;
	}

	/**
	 * SQL query for Droping ComproPago Tables
	 * @return string[]
	 * @since 1.0.2
	 */
	public static function sqlDropTables($prefix=null)
	{
		return array(
				'DROP TABLE IF EXISTS `' . $prefix . 'compropago_orders`;',
				'DROP TABLE IF EXISTS `' . $prefix . 'compropago_transactions`;'
		);
	}
	/**
	 * SQL query for Creating ComproPago Tables
	 * @return string[]
	 * @since 1.0.2
	 */
	public static function sqlCreateTables($prefix=null)
	{
		return array(
				'CREATE TABLE `' . $prefix . 'compropago_orders` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`date` int(11) NOT NULL,
			`modified` int(11) NOT NULL,
			`compropagoId` varchar(50) NOT NULL,
			`compropagoStatus`varchar(50) NOT NULL,
			`storeCartId` varchar(255) NOT NULL,
			`storeOrderId` varchar(255) NOT NULL,
			`storeExtra` varchar(255) NOT NULL,
			`ioIn` mediumtext,
			`ioOut` mediumtext,
			PRIMARY KEY (`id`), UNIQUE KEY (`compropagoId`)
	
			)ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;',
					
				'CREATE TABLE `' . $prefix . 'compropago_transactions` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`orderId` int(11) NOT NULL,
			`date` int(11) NOT NULL,
	 		`compropagoId` varchar(50) NOT NULL,
			`compropagoStatus` varchar(50) NOT NULL,
			`compropagoStatusLast` varchar(50) NOT NULL,
			`ioIn` mediumtext,
			`ioOut` mediumtext,
			PRIMARY KEY (`id`)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;'
		);
	}
}