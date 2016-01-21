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
 * @author Rolando Lucio <rolando@compropago.com>
 * @since 1.0.3
 */
?>
<div id="compropagoWrapper">
	<hr class="compropagoHr">
	
	 	<div class="compropagoDivFrame" id="compropagodContainer" style="width: 100%;">
	 		<iframe style="width: 100%;" id="compropagodFrame"  src="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $compropagoData->id;?>"  frameborder="0" scrolling="yes"> </iframe>
	 	</div>
	<script type="text/javascript">
	function resizeIframe() {
	   var container=document.getElementById("compropagodContainer");
	   var iframe=document.getElementById("compropagodFrame");
	   if(iframe && container){
		   var ratio=585/811;
		   var width=container.offsetWidth;
		   var height=(width/ratio);
		   if(height>937){ height=937;}
		   iframe.style.width=width + 'px';
		   iframe.style.height=height + 'px';
		}
	}
	
	window.onload = function(event) {
		resizeIframe();
	 };
	 window.onresize = function(event) {
			resizeIframe();
		 };

	
	 
	</script>	
</div>	