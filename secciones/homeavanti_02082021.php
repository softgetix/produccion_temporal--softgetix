<style>
    #frm_homeavanti fieldset{ width:30%; display:inline-block; margin:6px; padding:10px !important;}
    #frm_homeavanti fieldset div.block_left{ float:left; width:70%; text-align: left;}
    #frm_homeavanti fieldset div.block_right{ float:right; width:30%;}
    #frm_homeavanti fieldset h4{font-size: 16px; font-weight: bold; line-height: 20px;}
    #frm_homeavanti fieldset p{font-size: 11px; line-height: 14px;}
    #frm_homeavanti fieldset h1{font-size: 20px; font-weight: bold; line-height: 22px; background: transparent; text-align: center;}
</style>
<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
            
            <div style="height:100%" class="contenido flaps clear"> 
                <centeR>
                <?php if($result){
                    foreach($result as $i => $item) {?>
                        <fieldset style="background:#<?=$item['colorcelda']?>; color:#<?=$item['colorletra']?>">
                            <div class="block_left">
                                <h4><?=$item['titulocelda']?></h4>
                                <p><?=$item['descripcioncelda']?></p>
                            </div>
                            <div class="block_right">
                                <h1><?=$item['valorcelda']?></h1>
                            </div>
                        </fieldset> 
                    <?php }
                }    
                else{ 
                    echo '<center>'.$lang->message->sin_resultados.'</center>';
                }?> 
                </center>    
                <!-- -->
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>
