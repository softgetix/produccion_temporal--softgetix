<br />
<fieldset class="bg_white">
   <center>
   		<div style="padding:35px 10px 10px 10px;">
            <input name="hidOperacion" type="hidden" value="index" />
            <input name="hidSeccion" type="hidden" value="<?=$seccion;?>" />
            <input name="parameter" type="hidden" value="<?=$arrEntidades['parameter']?>" />
           
          	<?php if($arrEntidades['status'] == 'inactive'){?>
                <input name="hidAction" type="hidden" value="subscription" />
                
                <span style="display:inline-block; height:40px; vertical-align:middle; margin-right:10px; font-size:16px;">Sale <strong>USD <?=$arrEntidades['monto']?></strong> to <?=$arrEntidades['periodo']?></span>
                <div style="display:inline-block;"><!-- margin-top:20px; padding:8px 20px;-->
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit">
                <img border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
                </div>
                <!--
                <a href="https://www.localizar-t.com:81/paypal/payment_subscription.php?c=<?php //=$arrEntidades['parameter']?>" class="button colorin clear" style="margin-top:20px; padding:8px 20px;">SUSCRIPTION</a>
                -->
            <?php }
            else{?>
            	<input name="hidAction" type="hidden" value="cancel_subscription" />
            	
                <a href="javascript:;" onclick="javascript:enviar('index')" class="clear" style="margin-top:20px; padding:8px 20px;">
            		<img border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_unsubscribe_LG.gif">
				</a>
            	<!--
                <a href="https://www.localizar-t.com:81/paypal/cancel_subscription.php?c=<?php //=$arrEntidades['parameter']?>" class="button colorin clear" style="margin-top:20px; padding:8px 20px;">CANCEL SUSCRIPTION</a>
            	-->
			<?php }?>
            
            
            <center>
            	<img src="imagenes/paypal.jpg" border="0" style="margin-top:40px;" />
            </center>
        </div>
    </center>
    
    <?php if(isset($_POST['msg'])){?>
    <center>
    	<div class="<?=(($_POST['success'] == 'ok')?'msj_ok':'msj_error')?>">
        	<span><?=$_POST['msg']?></span>	
        </div>	
    </center>
    <?php }?>	
</fieldset>