<div class="elementos">
    	 
        <div  class="alineadorElementos">
    	<?php 
		$volver=0; 
		foreach($arrElementos as $elem){if(($elem['wp_transicion']==3)||(($elem['wp_transicion']==4))){$volver=1;}}
		if($volver==0)
		{
		?>
        <input type="button" name="" onclick="goBack(<?=$wizard ?>);"  class="boton" value="Volver">
        <?php
		}
		foreach($arrElementos as $elem)
		{
			
			if($elem['wp_transicion']==1)
			{
		       if(is_numeric($elem['wp_wn_siguiente'])){ $onclick='cargarNodo(\''.$elem['wp_wn_siguiente'].'\',\''.$wizard.'\')'; }else{$onclick=$elem['wp_wn_siguiente'];}?>
            	<input type="button" name="" value="<?=$elem['wp_transicion_texto']  ?>" class="boton" onclick="<?=$onclick?>"/> 		
		<?php 
			}elseif($elem['wp_transicion']==2){
		?>
        
            <input type="radio" name="opcion"  id="opcion" value="<?= $elem['wp_wn_siguiente'] ?>" class="radio" /> 
		    <label class="label" for="opcion"><?=$elem['wp_transicion_texto']  ?></label>
		   
           
		<?php 	 
			}elseif($elem['wp_transicion']==3){?>
			 <?php $alineacion=($elem['wn_id']==52)?'alineacion':'';?>
			 
			 <input type="button" name="" value="<?=$elem['wp_transicion_texto']  ?>" class="boton <?=$alineacion?>" onclick="<?=$elem['wp_wn_siguiente'] ?>" />
			 
			<?php }
			
		}//FOREACH
		
		?>
           <?php if($arrElementos[0]['wp_transicion']==2){?><input type="button" name="post_input" value="Continuar" class="boton" onclick="cargarNodo($('input[name=\'opcion\']:checked').val(),<?=$wizard ?>)" /><br><?php } ?>
       </div>
      
      
    </div>

</div>