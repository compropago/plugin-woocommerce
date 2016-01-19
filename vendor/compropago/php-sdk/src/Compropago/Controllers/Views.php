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
namespace Compropago\Controllers;

use Compropago\Exception;

class Views{
	
	/**
	 * Views Loader
	 * @param string $view
	 * @param mixed $compropagoData
	 * @param string $method
	 * @param string $ext
	 * @param string $path
	 * @throws Exception
	 * @return null on Exception
	 * @return boolean according to methods
	 * @return mixed buffers,etc
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	public static function loadView($view='raw',$compropagoData=null,$method='include',$ext='php',$path=null){
		if($path==null){
			//path relativo al vendor Compropago/views
			$path=dirname(__FILE__). '/../../../views/'.$ext.'/';
		}
		$filename=$path.$view.'.'.$ext;
		if( !file_exists($filename) ){
			throw new Exception('Compropago Error: No se encontro el archivo de View solicitado');
			return;
		}
		switch ($method){
			case 'ob':
				return self::loadOb($filename , $compropagoData);
			break;
			case 'path':
				return $filename;
			break;
			case 'include':
			default:
				 self::loadInclude($filename, $compropagoData);
				 return true;
			
		}
		
	}
	/**
	 * include file
	 * @param string $filename
	 * @param mixed $compropagoData
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	private static function loadInclude($filename,$compropagoData){
		require $filename;
	}
	
	/**
	 * Process by PHP output buffering
	 * Some plugins might require to (string)output
	 * @param string $file Php path/File to output buffer to var
	 * @param mixed $compropagoData data to be processed
	 * @return bool
	 * @return buffer
	 * @since 1.0.1
	 * @version 1.0.1
	 */
	private static function loadOb($filename,$compropagoData){
		ob_start();
		require $filename;
		return ob_get_clean();
	}
	
}