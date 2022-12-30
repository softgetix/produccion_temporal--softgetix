<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" enctype="multipart/form-data">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <!--
            <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
            -->
            <div style="height:100%" class="contenido clear"> 
				 <fieldset class="not_bg_white" style="margin:auto !important; max-width:500px;">
                	<h2>Webservices Connection Control</h2>
                    <br /><br />
                    <p>Podr&aacute; corroborar si la informaci&oacute;n que est&aacute; enviando via webservices es correcta o deber&aacute; aplicar alg&uacute;n cambio sobre la misma.</p>
                    <p>La documentaci&oacute;n requerida para la integraci&oacute;n la puede descagar desde <a href="templates/protocolo_mercosur_localizart.pdf" target="_blank">agu&iacute;</a>, es caso que requiera haga <a href="templates/Localizart.postman_collection_position_report" target="_blank" download="Localizart.postman_collection_position_report">click aqu&iacute;</a> para desgargar un ejemplo de envio de informaci&oacute;n mediante Postman.</p>
                    <p>A continuaci&oacute;n, indique la IP P&uacute;blica desde donde enviar&aacute; la informaci&oacute;n. (<a href="http://www.cualesmiip.com/" target="_blanck">www.cualesmiip.com</a>)</p>
                    <br />
                    <br />
                    <label class="etiqueta">IP : </label>
                    <input type="text" name="txtIp" id="txtIp" value="<?=isset($_POST['txtIp'])?$_POST['txtIp']:$ip?>" style="width:210px;" maxlength="15">
                </fieldset>
                <fieldset class="not_bg_white">
                    <center>
                        <a href="javascript:;" onclick="javascript:iniciarCheckin($('#txtIp').val());" id="buttonAction"  class="button colorin" style="width:173px; margin-top:18px;">Verificar Conexi&oacute;n</a>
                    </center>
                </fieldset>
                <fieldset class="not_bg_white clear">
                	<label style="margin-left:10px;"><strong>Último Control:</strong> <?=$last_test?></label>
                    <label style="margin-left:10px;">/</label>
                    <label style="margin-left:10px;"><strong>Última Conexi&oacute;n Webservices:</strong> <?=$last_conexion?></label>                    
                    <div class="steps">
                        <div class="step first">
                            <div class="text">IP</div>
                            <div class="arrow right"></div>
                        </div>
                        <div class="step">
                            <div class="arrow left"></div>
                            <div class="text">Credenciales</div>
                            <div class="arrow right"></div>
                        </div>
                        <div class="step last">
                            <div class="arrow left"></div>
                            <div class="text">Respuesta</div>
                            <div class="arrow right"></div>
                        </div>
                        <span class="clear"></span> 
                    </div>
                    <fieldset id="message_log" style="min-height:50px;"></fieldset>
                </fieldset>
            	
                <span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>