{*
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
*
* Providers View TPL template 
* @author Rolando Lucio <rolando@compropago.com>
* @author Jonathan Coutiño <jonathan@compropago.com>
* @since 1.0.1
*}
<div id="compropagoWrapper">
{$compropagoData['description']}
<hr class="compropagoHr">
<b>{$compropagoData['instrucciones']}</b>
{if $compropagoData['showlogo']=='yes' }
	<ul>
		{foreach from=$compropagoData['providers'] item=provider}
		<li>	       
	        <input id="compropago_{$provider->internal_name}" type="radio" name="compropagoProvider" value="{$provider->internal_name}" image-label="{$provider->internal_name}">
	        <label for="compropago_{$provider->internal_name}" class="compropagoProviderDesc">
	          <img src="{$provider->image_medium}" alt="{$provider->internal_name}" class="compropagoStore" onclick="this.style.opacity=.8;">
	        </label>        		
        </li>	
		{/foreach}
	</ul>
{else}
	<select name="compropagoProvider">
	{foreach from=$compropagoData['providers'] item=provider}
			<option value="{$provider->internal_name}">{$provider->name}</option>		
	{/foreach}
	</select>
{/if}	
<hr class="compropagoHr">	
</div>