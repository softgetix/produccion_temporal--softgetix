<?php 
if($arrElementos){
	$grupoID = NULL;
	foreach($arrElementos as $item){
		
		##-- Se imprime grupo --##
		if($grupoID != $item['ig_gr_id'] && isset($item['ig_gr_id'])){
			if($grupoID != NULL){//-- En caso q no sea el primero --//
				echo '</table>';	
				echo '</fieldset>';
			}
			$grupoID = $item['ig_gr_id'];
			echo '<br />';
			echo '<fieldset>';
			echo '<legend>'.($lang->system->$item['gi_nombre']?$lang->system->$item['gi_nombre']:$item['gi_nombre']).'</legend>';
			echo '<table width="100%">';
		}	
		##-- --##
		
		echo '<tr>';
		echo '	<td align="right" valign="middle" height="20">'.($lang->system->$item['ig_nombre']?$lang->system->$item['ig_nombre']:$item['ig_nombre']).'&nbsp;&nbsp;</td>';
		echo '	<td style="text-align:left;" width="80%">'.getContenido($item, $arrEntidades[0], $tipoBotonera, $arrEntidades).'<td>';
        echo '</tr>'; 
	}
	
	if($grupoID != NULL && isset($item['ig_gr_id'])){
		echo '</table>';	
		echo '</fieldset>'; 
	}
}

	
function getContenido($item = NULL, $valorCampo = NULL, $tipoBotonera, $arrEntidades = NULL){//, $arrEntidades = NULL, $_POST = NULL, $seccion
//-- --//
	global $lang;
	global $seccion;
	global $solapa;
	global $operacion;
	
	$return = NULL;
	
	if(isset($_POST[$item['ig_idCampo']])){
		$value = $_POST[$item['ig_idCampo']];
	}
	elseif(isset($valorCampo[trim($item['ig_value'])])){
		$value = $valorCampo[trim($item['ig_value'])];
	}
	else{
		$value = '';
	}
	
	$nameElemento = $item['ig_idCampo']; 
	
	$esRequerido = '';
	if($item['ig_requerido'] == 1){$esRequerido ='*';}
	
	$soloLectura = '';
	if($item['ig_soloLectura']){$soloLectura = 'readonly="readonly"';}
	
	$strEvento = '';
	if($item['ig_evento']){$strEvento = $item['ig_evento'];}
	
	

	switch($item['ig_ti_id']){
		case 1: //-- Text --//
			$size = $item['ig_max'];
			if ($item['ig_tipoDato'] == 2){
				$size = strlen($arrElementos[$i]["ig_max"]);
			}

			$return = '<input type="text" name="'.$nameElemento.'" id="'.$nameElemento.'" value="'.$value.'" style="width:300px;" '.$soloLectura.' '.$strEvento.' size="'.$size.'"/>&nbsp;'.$esRequerido;
		
		break;
		case 2: //-- Select --//
			if( 
				($seccion == 'abmUsuarios' && $item['ic_store'] == 'pa_obtenerClienteCombo') 
				|| ($seccion == 'cuenta' && $item['ic_store'] == 'pa_obtenerClienteCombo') 
				|| ($seccion == 'abmConductores' && $item['ic_store'] == 'pa_obtenerClienteCombo') 
			){
				$arrDatosCombo = array();
				$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 3)?$_SESSION["idEmpresa"]:0;
				$idExcluyente = 0;
				if($_SESSION["idTipoEmpresa"] == 3) $idExcluyente = 2;
				if($_SESSION["idTipoEmpresa"] == 1) $idExcluyente = 3;
				if($_SESSION["idTipoEmpresa"] == 2) $idExcluyente = 1;
				$arrDatosCombo = obtenerDatosCombo($item['ic_store'].' 0, 0,'.$IdEmpresa.','.$idExcluyente);
			}
			elseif($seccion == 'abmClientes'){
				$arrDatosCombo = obtenerDatosCombo($item['ic_store'],3,$item['ic_esConsulta']);
			}
			elseif($item['ic_store'] == 'pa_obtenerDistribuidoresCombo' 
				&& ($seccion == 'abmMoviles' || $seccion == 'abmUsuarios' || $seccion == 'abmEquipos')
			){
				$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
				$arrDatosCombo = obtenerDatosCombo('pa_obtenerDistribuidoresCombo '.$IdEmpresa);
			}
			elseif(($seccion == 'abmMoviles' ||  ($seccion == 'cuenta' && $solapa == 'moviles')) && $item['ic_store'] == 'pa_obtenerClienteCombo'){
				$arrDatosCombo = obtenerDatosCombo($item['ic_store'].' '.$_SESSION["idUsuario"].', '.$_SESSION["idPerfil"]);
			}
			elseif(($seccion == 'abmMoviles' ||  ($seccion == 'cuenta' && $solapa == 'moviles')) && $item['ic_esConsulta']){
				$arrDatosCombo = obtenerDatosCombo($item['ic_store'],NULL,$item['ic_esConsulta']);
				foreach($arrDatosCombo as $k => $item){
					$arrDatosCombo[$k]['dato'] = $lang->system->$item['dato']?$lang->system->$item['dato']->__toString():$item['dato'];	
				}
			}
			else{
				$arrDatosCombo = obtenerDatosCombo($item['ic_store'],NULL,$item['ic_esConsulta']);
			}
			
			$return.= '<select name="'.$nameElemento.'" id="'.$nameElemento.'" style="width:304px;" '.$soloLectura.' '.$strEvento.' >';
			$return.= '	<option value="">'.$lang->system->seleccione.'</option>';
			for($j=0;$j < count($arrDatosCombo) && $arrDatosCombo;$j++){
				$selected="";
				if(isset($arrDatosCombo[$j]['id'])){
					$selected = '';
					if ($value == $arrDatosCombo[$j]['id']){$selected = 'selected="selected"';}
					$return.= '	<option value="'.$arrDatosCombo[$j]['id'].'" '.$selected.'>'.($lang->system->$arrDatosCombo[$j]['dato']?$lang->system->$arrDatosCombo[$j]['dato']:$arrDatosCombo[$j]['dato']).'</option>';	
				}
			}
			$return.= '</select>&nbsp;'.$esRequerido;
		
		break;
		case 3: //-- Password --//
			$disabled = '';
			if($operacion == 'modificar'){$disabled = 'disabled = "disabled"';}
				
			$return = '<input type="password" autocomplete="off" name="'.$nameElemento.'" id="'.$nameElemento.'" value="'.$value.'" style="width:300px;" size="'.$item['ig_max'].'" '.$soloLectura.' '.$disabled.' '.$strEvento.' />&nbsp;'.$esRequerido;
		break;
		case 4: 
		case 5: //-- checkbox --//
			$checked = '';	
			if(!empty($value)){$checked = 'checked="checked"';}
			$value=1; //siempre es uno.
		
			$return = '<input type="checkbox" name="'.$nameElemento.'" id="'.$nameElemento.'" '.$checked.' value="'.$value.'" '.$soloLectura.' '.$strEvento.' >&nbsp;'.$esRequerido;
		break;
		case 6:
		break;
		case 7:
			$arrDatosCombo = obtenerDatosCombo($item['ic_store'],NULL,$item['ic_esConsulta']);
			
			$return = '<select name="'.$nameElemento.'" id="'.$nameElemento.'" style="width:304px;" '.$soloLectura.' '.$strEvento.'>';
			$return.= ' <option value="0">'.$lang->system->seleccione.'</option>';
			for($j=0;$j < count($arrDatosCombo) && $arrDatosCombo;$j++){
				$selected = '';
				if($value == $arrDatosCombo[$j]['id']){$selected ='selected="selected"';}
				
				$return.= ' <option id="'.$arrDatosCombo[$j]['id'].'" value="'.$arrDatosCombo[$j]['id'].'" '.$selected.'>'.($lang->menu->$arrDatosCombo[$j]['dato']?$lang->menu->$arrDatosCombo[$j]['dato']:$arrDatosCombo[$j]['dato']).'</option>';
			}
			$return.= '</select>&nbsp;'.$esRequerido;
		break;
		case 8: //-- Textarea --//
			$return = '<textarea name="'.$nameElemento.'" id="'.$nameElemento.'" style="width:300px;" rows="4" '.$soloLectura.' '.$strEvento.'>'.$value.'</textarea>&nbsp;'.$esRequerido;
		break;
	}
	
	
	return $return;
//-- --//
}
	
?>