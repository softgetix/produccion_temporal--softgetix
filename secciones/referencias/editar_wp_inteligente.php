<script language="javascript">	
	var perfilADT = false; //permite drag
	var recargar = true;
	imgW = 24;
	imgH = 35;
</script>
<?php 
$operacion = 'modificar';
require_once 'includes/botoneraABMs.php';
$arrEntidades[0]['re_tr_id'] = 1;
$arrEntidades[0]['re_radioIngreso'] = 700;
?>	
<div id="mainBoxAM">
<input type="hidden" name="refer-referencia" value="edicion-inteligencia" />
<input type="hidden" name="hidLat" id="hidLat" value="<?=$arrPuntos['rc_latitud']?>" />
<input type="hidden" name="hidLng" id="hidLng" value="<?=$arrPuntos['rc_longitud']?>" />
<input name="hidPuntos" id="hidPuntos" type="hidden" value=";<?=isset($_POST["hidPuntos"]) ? $_POST["hidPuntos"] : $strPuntos?>" />
<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
<input type="hidden" name="cmbTipoReferencia" id="cmbTipoReferencia" value="<?=$arrEntidades[0]['re_tr_id']?>" />

<div style="display:none"><!-- Si no es combo se rompe el JS !!!! -->
<select name="cmbRadioIngreso" id="cmbRadioIngreso" style="width:204px;" onchange="cambioRadio();">
	<option value="700" <?=($arrEntidades[0]['re_radioIngreso']==700 )? "selected":"";?>><?=$lang->system->chica?></option>
    <option value="1000" <?=($arrEntidades[0]['re_radioIngreso']==1000 )? "selected":"";?>><?=$lang->system->mediana?></option>
    <option value="2000" <?=($arrEntidades[0]['re_radioIngreso']==2000 )? "selected":"";?>><?=$lang->system->grande?></option>
</select>
</div>

<div id="popup-content">
<fieldset>
<legend><?=$lang->system->datos_referencia?></legend>
<table class="widefat" width="100%">
	<tr>     			
		<td class="label"><label for="txtNombre"><?=$lang->system->nombre_referencia?></label></td> 
		<td> 			
            <input type="text" name="txtNombre" class="" id="txtNombre" value="<?=encode(isset($_POST['txtNombre'])?$_POST['txtNombre']:'')?>"  style="width:200px;"  size=50>
            <span> * </span>
		</td>
    </tr>
    <tr>    		
		<td class="label"><label for="cmbGrupo"><?=$lang->system->categoria?></label></td>
		<td>
			<select name="cmbGrupo" id="cmbGrupo" style="width:204px;">
				<option value="0"><?=$lang->system->seleccione?></option>
				<?php for($i = 0;$i < count($arrGrupos) && $arrGrupos;$i++) { ?>
				<option value="<?=$arrGrupos[$i]['rg_id']?>"  <?=($arrEntidades[0]['re_rg_id']==$arrGrupos[$i]['rg_id'] )?"selected":"";?>><?=encode($arrGrupos[$i]['rg_nombre'])?></option>
				<?php } ?>
			</select><span> * </span>
		</td>
		
	</tr>
</table>
</fieldset>
	<div id="mapa25" style="display:block;height:260px;width:100%;position:relative; margin-top:5px;"></div>
</div>
