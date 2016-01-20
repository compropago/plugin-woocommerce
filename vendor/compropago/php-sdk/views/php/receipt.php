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
<hr class="compropagoHr">
<a href="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $compropagoData->id;?>" target="_blank"><?php echo $compropagoReceiptLink; ?></a>

<hr class="compropagoHr">
<h3><?php echo $compropagoOrderTitle;?></h3>

<div class="expiration-date">
<?php echo $compropagoDueDate;?>
<span >
<?php echo $compropagoData->exp_date;?>
</span>
</div>
      
<div class="compropagoInstructions">
<p><?php echo $compropagoData->instructions->description;?></p>
<p>- <?php echo $compropagoData->instructions->step_1;?></p>
<p>- <?php echo $compropagoData->instructions->step_2;?></p>
<p>- <?php echo $compropagoData->instructions->step_3;?></p>
</div>

<div class="compropagoNotes">
<?php if( isset($compropagoData->instructions->note_extra_comition) && !empty($compropagoData->instructions->note_extra_comition) ){?>
<p>- <?php echo $compropagoData->instructions->note_extra_comition;?></p>
<?php }
if ( isset($compropagoData->instructions->note_expiration_date) && !empty($compropagoData->instructions->note_expiration_date) ){?>
<p>- <?php echo $compropagoData->instructions->note_expiration_date;?></p>
<?php }
if( isset($compropagoData->instructions->note_confirmation) && !empty($compropagoData->instructions->note_confirmation) ){?>
<p>- <?php echo $compropagoData->instructions->note_confirmation;?></p>
<?php }?>
</div>


<hr class="compropagoHr">
<a href="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $compropagoData->id;?>" target="_blank"><?php echo $compropagoReceiptLink;?></a>
<hr class="compropagoHr">
</div>
