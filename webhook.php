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
//mod_security is enabled,throws 406 error (API user agent?) disable error 500
// webhook error on shared hosting as hostgator, bluehost
//<IfModule mod_security.c>
//SecFilterEngine Off
//SecFilterScanPOST Off
//</IfModule>
//<IfModule mod_security2.c>
//SecRuleEngine Off
//</IfModule>
 require_once('../../../wp-load.php');
global $wpdb;
$body = @file_get_contents('php://input'); 
echo '<pre>'.print_r($body).'</pre><b>{error:none}</b>';
?>

