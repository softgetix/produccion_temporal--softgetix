<input type="hidden" name="server_credencial" id="server_credencial" />
<br />

<fieldset class="bg_white"  style="padding:20px 30px;">
	<p>Localizart ofrece un medio de integración de datos bidireccional mediante el uso de webservices de tipo REST y SOUP.</p>
	<p>En este apartado podrá comparir la documentación y credenciales a la persona encargada de la implementación, indicando una dirección de email de contacto y la documentación que sea requerida.</p>
    <br />
    <table style="width:30%" >
    <tr>
    	<td align="right" height="20" valign="middle"><?=$lang->system->email?>&nbsp;&nbsp;</td>
        <td><input type="email" name="email_send" value="<?=$_POST['email_send']?>" style="width:250px;"/></td>
   	</tr>
	<tr>
    	<td align="left" height="20" valign="middle" colspan="2">Documentaci&oacute;n a Enviar:</td>
	</tr>
    <?php foreach($doc as $k => $item){?>
    <tr>
    	<td align="left" height="20" valign="middle" colspan="2">
        	<input type="checkbox" name="adjunto[]" id="adjunto_<?=$k?>" value="<?=$item['url']?>" class="float_l" />&nbsp;
            <label for="adjunto_<?=$k?>"><?=$item['titulo']?></label>
		</td>
	</tr>
    <?php }?>
    </table>
    <center>
    	<a href="javascript:;" onclick="javascript:enviar('guardarM');" class="button extra-wide colorin"><?=$lang->botonera->enviar?></a>
    </center>
</fieldset>
<fieldset class="bg_white">
	<legend>Credenciales</legend>
    
    <fieldset style="width:80%"> 
        <legend style="color:#333">Testing</legend>
        <?php if(!empty($arrEntidades_Testing['cl_clientID']) && !empty($arrEntidades_Testing['cl_clientSecret'])){?>
        <table style="width:480px" >
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">URL:</span>&nbsp;&nbsp;</td>
                <td>https://www.localizar-t.com:81</td>
            </tr>
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">Client ID:</span>&nbsp;&nbsp;</td>
                <td><?=$arrEntidades_Testing['cl_clientID']?></td>
            </tr>
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">Client Secrect:</span>&nbsp;&nbsp;</td>
                <td><?=$arrEntidades_Testing['cl_clientSecret']?></td>
            </tr>
        </table>
        <?php }
        else{?>
        <br />
        <center>
        	<!--<span>Solicitar credenciales a <a href="mailto:soporte@localizar-t.com.ar">soporte@localizar-t.com.ar</a>-->
        	<a href="javascript:;" onclick="javascript:$('#server_credencial').val('testing'); enviar('guardarA')"  class="button colorin" style="width:173px;">OBTENER CREDENCIALES</a>
        </center>
        <?php }?>
        <br />
    </fieldset>    
    <br />    
    <fieldset style="width:80%">
        <legend style="color:#09F">Producci&oacute;n</legend>
        <?php if(!empty($arrEntidades['cl_clientID']) && !empty($arrEntidades['cl_clientSecret'])){?>
        <table style="width:480px" >
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">URL:</span>&nbsp;&nbsp;</td>
                <td>https://www.localizar-t.com</td>
            </tr>
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">Client ID:</span>&nbsp;&nbsp;</td>
                <td><?=$arrEntidades['cl_clientID']?></td>
            </tr>
            <tr>
                <td align="right" height="20" valign="middle"><span class="campo1">Client Secrect:</span>&nbsp;&nbsp;</td>
                <td><?=$arrEntidades['cl_clientSecret']?></td>
            </tr>
        </table>
        <?php }
        else{?>
        <br />
        <center>
            <a href="javascript:;" onclick="javascript: $('#server_credencial').val('produccion'); enviar('guardarA')"  class="button colorin" style="width:173px;">OBTENER CREDENCIALES</a>
        </center>
        <?php }?>
        <br />        
    </fieldset>
    <br />
</fieldset>

<?php foreach($doc as $item){?>
<br />
<fieldset class="bg_white" style="padding:0 30px 10px 30px;">
	<legend><?=$item['titulo']?></legend>
    <table align="left" width="100%">
		<tr><td><?=$item['info']?></td></tr>
        <tr><td><span>Para descargar la documentaci&oacute;n <a href="templates/<?=$item['url']?>" target="_blank">click agu&iacute;</a></span></td></tr>
	</table>
	<br />        
</fieldset>
<?php } ?>