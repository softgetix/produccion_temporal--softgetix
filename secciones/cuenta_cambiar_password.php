<script language="javascript">
	var arrLang = [];
	arrLang['too_short'] = '<?=$lang->system->password_muy_corta?>';
	arrLang['very_weak'] = '<?=$lang->system->password_muy_insegura?>';
	arrLang['weak'] = '<?=$lang->system->password_insegura?>';
	arrLang['good'] = '<?=$lang->system->password_aceptable?>';
	arrLang['strong'] = '<?=$lang->system->password_segura?>';
	arrLang['msg_cambio_password'] = '<?=$lang->system->msg_cambio_password?>';
	
	arrLang['password_actual'] = '<?=$lang->system->contrasena_actual?>';
	arrLang['contrasena_nueva'] = '<?=$lang->system->contrasena_nueva?>';
	arrLang['contrasena_nueva_repetir'] = '<?=$lang->system->contrasena_nueva_repetir?>';
</script>		
<script type="text/javascript" src="js/password/jquery.validate.password.js"></script>
<table class="no-border">
	<tr>
    	<td style="text-align:right"><?=$lang->system->nombre?></td>
        <td colspan="2"><input name="txtNombre" value="<?=isset($_POST['txtNombre'])?$_POST['txtNombre']:$_SESSION['us_nombre']?>" style="width:250px;" size="30" type="text"></td>
    <tr>    
    <tr>
        <td style="text-align:right"><?=$lang->system->apellido?></td>
        <td colspan="2"><input name="txtApellido" value="<?=isset($_POST['txtApellido'])?$_POST['txtApellido']:$_SESSION['us_apellido']?>" style="width:250px;" size="30" type="text"></td>
    </tr>
    <tr>
		<td  class="tdInfo"><?=$lang->system->contrasena_actual?></td>
		<td  colspan="2"><input type="password" name="txtPassActual" id="txtPassActual" class="text" autocomplete="off" style="width:250px;">&nbsp;*</td>
	</tr>
	
    <tr>
		<td class="tdInfo" align="left"><?=$lang->system->contrasena_nueva?></td>
		<td style="width:300px;"><input type="password" name="txtPassNuevo" id="txtPassNuevo" class="text password" autocomplete="off" style="width:250px;">&nbsp;*</td>
        <td> 
        	<div class="password-meter">
            	<div class="password-meter-message">&nbsp;</div>
                <div class="password-meter-bg">
                	<div class="password-meter-bar"></div>
                </div>
			</div>
		</td>
	</tr>
    <tr>
    	<td class="tdInfo" align="left"><?=$lang->system->contrasena_nueva_repetir?></td>
        <td colspan="2"><input type="password" name="txtPassNuevo2" id="txtPassNuevo2" class="text formulario_cuenta" autocomplete="off" style="width:250px;">&nbsp;*</td>
	</tr>
    <tr>
    	<td align="left" colspan="3">
        	<br /><br />
            <span style="color:#676767; font-family:Verdana, Geneva, sans-serif; font-size:12px">
            	<?=str_replace(']','>',str_replace('[','<',$lang->system->ayuda_cambio_contrasena))?>
			</span>
		</td>
	</tr>
    <tr>
    	<td style="padding-top:16px;">
        	<a href="javascript:;" onclick="javascript:verificarDatos()" style="margin:5px; width:120px; font-weight:bold; float:left" class="button extra-wide colorin">
				<?=$lang->system->cambiar_password?>
            </a>
        </td>
        <td colspan="2">
        	<a href="javascript:;" style="margin:5px; width:120px; font-weight:bold; background-color:#666 !important; border-color:#666 !important" name="btnAceptar" id="btnAceptar" onclick="javascript: location.href='<?php echo "boot.php?c=".$_SESSION['paginaDefecto']?>';" class="button extra-wide colorin">
				<?=$lang->botonera->cancelar?>
            </a>
		</td>
	</tr>
</table>

<!--- --->
<?php /*if($_SESSION['idEmpresa'] == 2272 || $_SESSION['idEmpresa'] == 156){?>
<span class="clear"></span>
<br />
<fieldset style="padding:10px 30px; background:#FFF">
	<strong>Manuales del Sistema:</strong><br />
<?php
$url = 'templates/kcc';
if(file_exists($url)){
	$handle = opendir($url);
	sort($handle);
	while(false !== ($file = readdir($handle))){
		if($file != "." && $file != "..") {
			?><br /><a href="<?=$url.'/'.$file?>" target="_blank" style=" line-height:20px;"><?=$file?></a><?php
	   }
	}
	closedir($handle);
}
?>
</fieldset>
<?php }*/?>
<!--- --->


