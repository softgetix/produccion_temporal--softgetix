<style>
table.widefat2 td.label {
	text-align:right; 
	padding-right:10px;
}
#mapa25{border:1px solid #95B3D0; display:block; float:left;width:610px;height:385px;
	 position:relative; left:6px; top:16px;}
</style>
<script language="javascript">	
	var perfilADT = true; //no permite drag
	var zoomADT = true;
	var actualizaPanicoADT = true;	
	var recargar = false;
	var latCentro = '<?=$_SESSION['lat']?>';
	var lngCentro = '<?=$_SESSION['lng']?>';
   
	function recrearMapa(){
		// invierto el permiso de dragueo
		if(perfilADT == true){
			perfilADT = false; //permite drag
			$("#EstadoPosManual").html('<?=$lang->system->manual?>');
			$("#btnPosManual").html('<?=$lang->system->volver_automatico?>');
		}
		else{
			perfilADT = true; //no permite drag
			$("#EstadoPosManual").html('<?=$lang->system->automatico?>');
			$("#btnPosManual").html('<?=$lang->system->volver_manual?>');
		}
		Cargar();		
	}
	   
	$(document).ready(function(){
		var $this=$("#cmbPais");			
		if ($this.val()!=='0'){
			value = $this.val();
			getProvincia('cmbProvincia', value, '<?=$arrEntidades[0]['re_provincia']?>');
		}
	});
</script>
<?php
$esReferencia = 1;
require_once 'includes/botoneraABMs.php';
?>			
<div id="mainBoxAM" style="overflow:hidden !important; margin-bottom:0px !important;">
    <div id="popup-content">
    <?php if($arrEntidades[0]['re_lt']){?>	
        <div style="background:#F7F8E0;color:red;width:100%;height:15px;padding:5px;"><?=$lang->system->msg_advertencia_referencias?></div>	
    <?php } ?>
	<fieldset style="width:240px;margin:10px 0 0 0;padding-top:10px; padding-left:10px;float:left;">
	<legend><?=$lang->system->title_datos_ubicacion?></legend>
<table class="widefat2"> 
	<tr> 
    	<td class="label"><label for="txtLt"><?=$lang->system->lt?> -</label></td> 
		<td colspan=2><input type="text" name="txtLt" class="" id="txtLt" maxlength="4" value="<?=isset($_POST['txtLt'])?$_POST['txtLt']:$arrEntidades[0]['re_lt']?>" style="width:140px;" size=50></td>
	</tr>
	<tr>
		<td class="label"><label for="cmbTipoUbicacion"><?=$lang->system->tipo?></label></td> 
		<td colspan=2> 
			<select name="cmbTipoUbicacion" id="cmbTipoUbicacion" style="width:144px;" onchange="/*cambioRadio();*/" >
				<option value="0" <?=($arrEntidades[0]['re_tipo_ubicacion']=='' )? "selected":"";?>><?=$lang->system->seleccione?></option>
				<option value="1" <?=($arrEntidades[0]['re_tipo_ubicacion']=='1' )? "selected":"";?>><?=$lang->system->avenida?></option>
                <option value="2" <?=($arrEntidades[0]['re_tipo_ubicacion']=='2' )? "selected":"";?>><?=$lang->system->calle?></option>
			</select>
		</td>		
	</tr>
	<tr>	
		<td class="label"><label for="txtNombreDireccion"><?=$lang->system->direccion?></label></td> 
		<td colspan=2><input type="text" name="txtNombreDireccion" class="" id="txtNombreDireccion" value="<?=encode(isset($_POST['txtNombreDireccion'])?$_POST['txtNombreDireccion']:$arrEntidades[0]['re_direccion'])?>" style="width:140px;" size=50 onblur="javascript:armarConsulta();centrarDireccion();"></td>
	</tr>
	<tr>	
		<td class="label"><label for="txtAltura"><?=$lang->system->altura?></label></td> 
		<td colspan=2><input type="text" name="txtAltura" class="" id="txtAltura" value="<?=isset($_POST['txtAltura'])?$_POST['txtAltura']:$arrEntidades[0]['re_altura']?>" style="width:140px;" size=50 onkeypress="javascript:despintarAltura();" onclick="javascript:despintarAltura();" onblur="javascript:armarConsulta();centrarDireccion();"></td>				
	</tr>
	<tr>
		<td class="label"><label for="txtPiso"><?=$lang->system->piso?></label></td> 
		<td><input type="text" name="txtPiso" class="" id="txtPiso" value="<?=encode(isset($_POST['txtPiso'])?$_POST['txtPiso']:$arrEntidades[0]['re_piso'])?>" style="width:140px;" size=50></td>
	</tr>
	<tr>	
		<td class="label"><label for="txtDpto"><?=$lang->system->dpto?></label></td> 
		<td><input type="text" name="txtDpto" class="" id="txtDpto" value="<?=encode(isset($_POST['txtDpto'])?$_POST['txtDpto']:$arrEntidades[0]['re_dpto'])?>" style="width:140px;" size=50></td>		
	</tr>
	<tr>	
		<td class="label"><label for="txtTorre"><?=$lang->system->torre?></label></td> 
		<td><input type="text" name="txtTorre" class="" id="txtTorre" value="<?=encode(isset($_POST['txtTorre'])?$_POST['txtTorre']:$arrEntidades[0]['re_torre'])?>" style="width:140px;" size=50></td>
	</tr>
	<tr>		
		<td class="label"><label for="txtEntre"><?=$lang->system->entre?></label></td> 
		<td colspan=2><input type="text" name="txtEntre" class="" id="txtEntre" value="<?=encode(isset($_POST['txtEntre'])?$_POST['txtEntre']:$arrEntidades[0]['re_entre'])?>" style="width:140px;" size=50></td>
		<!--<td class="label"><label for="txtYEntre">y la calle </label></td>
		<td colspan=2><input type="text" name="txtYEntre" class="" id="txtYEntre" value="<?=encode(isset($_POST['txtYEntre'])?$_POST['txtYEntre']:$arrEntidades[0]['re_y_entre'])?>" style="width:140px;" size=50></td>
		-->
	</tr>
	<tr>	
		<td class="label"><label for="txtLocalidad"><?=$lang->system->localidad?></label></td> 
		<td colspan=2><input type="text" name="txtLocalidad" class="" id="txtLocalidad" value="<?=encode(isset($_POST['txtLocalidad'])?$_POST['txtLocalidad']:$arrEntidades[0]['re_localidad'])?>" style="width:140px;" size=50 onblur="javascript:armarConsulta();centrarDireccion();"></td>
	</tr>
	<tr>	
		<td class="label"><label for="cmbProvincia"><?=$lang->system->provincia?></label></td>
		<td colspan=2> 			
            <select name="cmbProvincia" id="cmbProvincia" style="width:144px;" onchange="javascript:armarConsulta();centrarDireccion();" >
				<?php //<option value="0" <?=($arrEntidades[0]['re_provincia']=='' )?"selected":"">Seleccione...</option>?>				               
			</select>
		</td>
	</tr>
	<tr>	
		<td class="label"><label for="txtCp"><?=$lang->system->cod_postal?></label></td> 
		<td colspan=2><input type="text" name="txtCp" class="" id="txtCp" value="<?=encode(isset($_POST['txtCp'])?$_POST['txtCp']:$arrEntidades[0]['re_cp'])?>" style="width:140px;" size=50></td>
	</tr>
	<tr>	
		<td class="label"><label for="txtPartido"><?=$lang->system->ciudad?></label></td> 
		<td colspan=2><input type="text" name="txtPartido" class="" id="txtPartido" value="<?=encode(isset($_POST['txtPartido'])?$_POST['txtPartido']:$arrEntidades[0]['re_partido'])?>" style="width:140px;" size=50 onblur="javascript:armarConsulta();centrarDireccion();"></td>
	</tr>
	<!--<tr>	
		<td colspan=2>
			<a href="javascript:armarConsulta();centrarDireccion();" class="button colorin" style="margin-top: 5px; padding: 4px 6px;">Buscar en el Mapa</a>
		</td>
	</tr>--> 
	<tr>	
		<td colspan=2>
			<center>
			<?=$lang->system->posicionamiento_actual?>: <span id="EstadoPosManual"><?=$lang->system->automatico?></span>
			<a href="javascript:recrearMapa();armarConsulta();centrarDireccion();" class="button colorin" style="width: 190px; margin-top: 5px; padding: 4px 6px;"><span id="btnPosManual"><?=$lang->system->volver_manual?></span></a>
            </center>
		</td>
	</tr>
</table>	
	<!-- Campos ocultos -->	
	<input type="hidden" name="txtNombre" class="" id="txtNombre" value="Zona Segura ADT <?=!empty($arrEntidades[0]['re_panico'])?$arrEntidades[0]['re_panico']:$_GET['panico']?>" style="width:200px;" size=50>  
	<input type="hidden" name="cmbGrupo" class="" id="cmbGrupo" value="<?=$grupoZonaPanico?>" style="width:200px;" size=50>  
	<input type="hidden" name="hidUsuario" class="" id="hidUsuario" value="<?=isset($_GET['usr'])?$_GET['usr']:$arrEntidades[0]['re_us_id']?>" style="width:200px;" size=50>  
	<input type="hidden" name="hidPanico" class="" id="hidPanico" value="<?=isset($_GET['panico'])?$_GET['panico']:$arrEntidades[0]['re_panico']?>" style="width:200px;" size=50>  
	<input type="hidden" name="cmbPais" class="" id="cmbPais" value="<?=$_SESSION['idPais']?$_SESSION['idPais']:1?>" style="width:200px;" size=50>  
	<input type="hidden" name="txtPais" id="txtPais" value="<?=encode($objReferencia->getPais($_SESSION['idPais']))?>">	
    <input type="hidden" name="txtDireccion" id="txtDireccion_2" value="<?=encode($arrEntidades[0]['re_ubicacion'])?>" size="30" style="width:300px;" onkeypress="javascript:setEnter(event);">	
	
	<input type="hidden" name="hidCl" id="hidCl" value="<?=isset($_GET['cli'])?$_GET['cli']:''?>" size="30" style="width:300px;" >	
		
	<!-- Campos escondidos -->	
	<div style="background:green;width:25px;display:none;">	
		<select name="cmbRadioIngreso" id="cmbRadioIngreso" style="width:10px;" >	           
			<option value="100" selected >100 mts.</option>				         
		</select>		
		<select name="cmbTipoReferencia" id="cmbTipoReferencia" style="width:10px;"> 
			<option value="1" selected >WP</option>            
		</select>
	</div>
	<!---------------------->	
</fieldset>
	<div id="mapa25"></div>
    <div style="clear:both;"></div>
</div>
