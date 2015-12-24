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

class CP_Views{
	
	public static function loadView($view,$data){
		require __DIR__ . '/../Views/'.$view.'.php';
	}
	public static function loadTpl($view,$data){
		$file=  file_get_contents( __DIR__ . '/../Views/'.$view.'.php'); 
		//$pattern='/{{(.*)}}/';
		//$pregarray= preg_grep($pattern, $file);
		/*preg_match_all($pattern, $file, $matches,PREG_PATTERN_ORDER);
		
		
		foreach ($matches[1] as $phpvalue ){
			
			$result=str_replace('{{'.$phpvalue.'}}', $$phpvalue , $file) ;
		}
		
		return $result;*/
		return (string)$file;
	}
}