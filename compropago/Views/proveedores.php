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
 * Proveedores view Donde pagar
 * @author Rolando Lucio <rolando@compropago.com>
 */


?>
<div id="compropagoWrapper">
<?php echo $data['description'];?>
<hr class="compropagoHr">
<b><?php echo $data['instrucciones'];?></b>
<?php if($data['showlogo']=='yes'){?>

	<ul>
		<?php foreach ($data['providers'] as $provider){ ?>
		<li>	
			<input id="compropago_<?php echo $provider->internal_name ?>" type="radio" name="compropagoProvider" value="<?php echo $provider->internal_name ?>" image-label="<?php echo $provider->internal_name ?>">
            <label for="compropago_<?php echo $provider->internal_name ?>" class="compropagoProviderDesc" ">
            	<img src="<?php echo $provider->image_medium ?>">
           </label>       		
        </li>	
		<?php }?>
	</ul>
<?php }else{?>
	<select name="compropagoProvider">
	<?php foreach ($data['providers'] as $provider){ ?>
			<option value="<?php echo $provider->internal_name ?>"><?php echo $provider->name ?></option>		
	<?php }?>
	</select>
<?php }?>	
	
</div>
