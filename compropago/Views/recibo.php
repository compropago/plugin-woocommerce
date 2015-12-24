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
?>
<div id="compropagoWrapper">
	<a href="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $data->id;?>" target="_new">Consulta los detalles de la orden haciendo click Aquí</a>
	<h2><?php //echo $data->order_info->$order_id ?></h2>	
	vence: <?php echo $data->id;?>
</div>