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
?>
<div id="compropagoWrapper">

	<a href="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $compropagoData->id;?>" target="_blank">Consulta los detalles de la orden haciendo click <b>Aquí</b></a>
	<hr class="compropagoHr">
	
	vence: <?php echo $compropagoData->id;?>
	
	<table class="table-receipt">
                <thead>
                <tr>
                  <th style="width: 60%;"><span style="margin-left: 10px;">PRODUCTO/SERVICIO</span></th>
                  <th style="width: 40%"><span class="line"></span><span></span></th>
                </tr>
                </thead>
                <tbody>
                <tr style="line-height: 18px; padding: 2px;">
                  <td style="width: 60%;padding-left: 10px;margin: 5px 0px 10px 0px;" class="description"><span style="margin-bottom: 10px"><?php echo $compropagoData->order_info->id;?></span></td>
                  <td style="width: 40%;margin: 5px 0px 10px 0px;" class="description"><span style="margin-left: 3px;"><?php echo $compropagoData->order_info->order_price;?></span></td>
                </tr>
                </tbody>
	</table>
	<p><?php echo $compropagoData->instructions->description;?></p>
	<ol>
		<li><?php echo $compropagoData->instructions->step_1;?></li>
		<li><?php echo $compropagoData->instructions->step_2;?></li>
		<li><?php echo $compropagoData->instructions->step_3;?></li>
	</ol>
</div>


