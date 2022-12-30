<?php
set_time_limit(300);
define("PANICO_MINUTOS_SYS_HEART", 40);//100
define("PANICO_MINUTOS_LAPSO_TOLERABLE_PRUEBAS_FALLIDAS", 100);//100
define("PANICO_MINUTOS_CANTIDAD_TOLERABLE_PRUEBAS_FALLIDAS", 2);//2
//define("PANICO_ESTADO_SIN_RESULTADO", -1); //!!!
//define("PANICO_ESTADO_EXITOSO", 1); //!!!
define("PANICO_ESTADO_FUERA_DE_ZONA", 2);
define("PANICO_SIN_DISPONIBILIDAD_TECNICA", 3);
//define("PANICO_SIN_RECEPCION_RESULTADO", 4); //!!!

require_once 'includes/funciones.php';
require_once 'includes/conn.php';
require_once 'clases/clsProbadorDePanico.php';
$objPanico = new ProbadorDePanico($objSQLServer);

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

switch($_POST['action']){
	case 'moviles_referencias':
		$arrMoviles = $objPanico->obtenerMoviles($_POST["cod_cliente"], $_SESSION['idEmpresa']);	
		$arrReferencias = $objPanico->obtenerReferencias($_POST["cod_cliente"], $_SESSION['idEmpresa']);	
		$arr_resp = array();
		$arr_resp['moviles'] = $arrMoviles;
		$arr_resp['referencias'] = $arrReferencias;
		echo json_encode($arr_resp);
		exit;
	break;
	case 'definir_contenido':
		$arrMoviles = $objPanico->obtenerMoviles($_POST["cod_cliente"], $_SESSION['idEmpresa']);	
		$arrReferencias = $objPanico->obtenerReferencias($_POST["cod_cliente"], $_SESSION['idEmpresa']);	
		$arrPruebas = $objPanico->obtenerUltimasPruebas($_POST["cod_cliente"], $_SESSION['idEmpresa']);
		?>
		<table class="widefat" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
            <tr class="titulo">
				<td align="center" width="30%"><?=$lang->system->movil?></td>
                <td align="center" width="55%"><?=$lang->system->zonas_seguras_adt?></td>
                <td align="center" colspan="3" width="15%"><?=$lang->system->estado?></td>
			</tr>
            </tbody>
	<?php if(!empty($arrMoviles[0]) && !empty($arrReferencias[0])) {
		$index = 1;
		foreach($arrMoviles as $movil){
			$class = ($class != 'filaImpar')?'filaImpar':'filaPar';
			$strMovil = htmlspecialchars($movil["mo_matricula"].' ['.$movil["mo_identificador"].' '.$movil["mo_otros"].' '.$movil["mo_marca"].']', ENT_QUOTES); 
           	for($i=1; $i<=3; $i++) {
				$hp_re_id = '';
			?>
            <tr class="<?=$class?>">
				<td id="col1_<?=$index?>"><?=$strMovil?></td>
                <td id="col2_<?=$index?>" style="text-align:left !important">
                	<span class="float_l"><?=$i?>&nbsp;&nbsp;</span>
                	<?php $selected_find = $options = '';
						foreach($arrReferencias as $referencia) {
							$selected = '';
							if(empty($selected_find)){
								foreach($arrPruebas as $k =>$item){
									if($item['hp_re_id'] == $referencia['re_id'] && $item['hp_mo_id'] == $movil['mo_id']){
										$selected = 'selected="selected"';
										$selected_find = true;
										$hp_re_id = $referencia['re_id'];
										unset($arrPruebas[$k]);
										break;
									}
								}
							}
                        $options.= '<option value="'.$referencia['re_id'].'" '.$selected.'>'.encode($referencia['re_ubicacion']).'</option>';
					}?>
                    <select class="float_l combo_<?=$movil['mo_id']?>_<?=$hp_re_id?>" id="combo_<?=$index?>" <?=$selected_find?'disabled="disabled"':''?>
                    	onchange="javascript:changeReferencia(<?=$index?>,<?=$movil['mo_id']?>,this.value)">
                    	<option value=""><?=$lang->system->seleccione?></option>
                       	<?=$options?>
                    </select>
                    <?php if($selected_find){?>
					<?php }?>
                 </td>
                 <td width="10%"><span id="zona_<?=$movil['mo_id']?>_<?=$hp_re_id?>" class="status_prueba_<?=$index?> float_l"><?=$lang->system->sin_probar?></span></td><!-- class:panico_global_tabla_detalle-->
                 <td width="2%" ><span id="resultado-<?=$movil['mo_id']?>_<?=$hp_re_id?>" class="result_prueba_<?=$index?> float_l"></span></td>
                 <td width="3%"><span id="prueba_<?=$movil['mo_id']?>_<?=$hp_re_id?>" class="boton_prueba_<?=$index?> float_l"></span></td>
            </tr>
            <?php $strMovil = '';	$index ++;	
			}
		}
	}elseif(empty($arrMoviles[0])){?>
    	<tr class="filaPar"><td colspan="5"><?=$lang->system->probador_panico_txt10?></td></tr>
    <?php }elseif(empty($arrReferencias[0])){?>
    	<tr class="filaPar"><td colspan="5"><?=$lang->system->probador_panico_txt9?></td></tr>
    <?php }?>    
			</tbody>
		</table>
	<?php 
	break;
	case 'ultimas_pruebas':
		$arrPruebas = $objPanico->obtenerUltimasPruebas($_POST["cod_cliente"], $_SESSION['idEmpresa']);
		if(count($arrPruebas) > 0) {
			foreach($arrPruebas as $k => $prueba) {
				$arrPruebas[$k]['fecha_hora'] = date('d-m-Y H:i',strtotime($prueba['fecha_hora']));
			}	
		}
		
		$arrDisponibilidad = $objPanico->obtenerDisponibilidadPruebas($_POST["cod_cliente"], PANICO_MINUTOS_SYS_HEART, $_SESSION['idEmpresa']);
		
		$arrResult = array();
		foreach($arrDisponibilidad  as $item){
			$arr = array();
			$item['service_panic'] = true;//Quitar esta linea una vez q se active los servicios de eventos en la app.
			if(!$item['service_panic']){
				$arr['resp'] = false;
				$arr['msg'] = $lang->system->probador_panico_txt11->__toString(); 
			}
			elseif($item['diferencia'] <= PANICO_MINUTOS_SYS_HEART){
				if($item['dg_entradas'] >= 20 ){//OK
					$arr['resp'] = true;
					$arr['msg'] = ''; 
				}
				else{//Bateria Baja
					$arr['resp'] = false;
					$arr['msg'] = $lang->system->probador_panico_txt12->__toString(); 
				}
			}
			else{//Sin disponibilidad
				$arr['resp'] = false;
				$arr['msg'] = $lang->system->probador_panico_txt13->__toString();
			}
			$arr['mo_id'] = $item['mo_id'];
			
			array_push($arrResult, $arr);
		}
		
		$arr_resp = array(
			'ultimas_pruebas' => $arrPruebas
			,'disponibilidad_pruebas' => $arrResult
		);
		
		echo json_encode($arr_resp);
		exit;
	break;
	case 'reset_estado_prueba':
		echo $objPanico->borrarPrueba($_POST['idReferencia'],$_POST['idMovil'], $_SESSION['idEmpresa']);
		exit;
	break;
	//--//
	case 'insertar_prueba':
		/*echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
		echo "<respuesta>";
			echo "<resultado>";
			echo $objPanico->insertarPrueba((int)$_POST["cod_movil"], (int)$_POST["cod_zona"]);
			echo "</resultado>";
		echo "</respuesta>";
		*/
		echo $objPanico->insertarPrueba((int)$_POST["cod_movil"], (int)$_POST["cod_zona"]);
		exit;	
	break;
	case 'revisar_prueba':
		//-1 = PANICO_ESTADO_SIN_RESULTADO
		//1 = PANICO_ESTADO_EXITOSO
		$resultado = $objPanico->revisarPrueba($_POST["cod_prueba"], $_SESSION['idEmpresa']);
		if ($resultado == 0) {
			$resultado = $objPanico->revisarPruebasFallidasRecientes((int)$_POST["cod_movil"], (int)$_POST["cod_zona"], (int)$_POST["cod_prueba"], PANICO_MINUTOS_LAPSO_TOLERABLE_PRUEBAS_FALLIDAS);
			if ($resultado < PANICO_MINUTOS_CANTIDAD_TOLERABLE_PRUEBAS_FALLIDAS) {
				$resultado = PANICO_ESTADO_FUERA_DE_ZONA;
			}
			else {
				$resultado = PANICO_SIN_DISPONIBILIDAD_TECNICA;
			}
		}
		echo (int)$resultado;
		exit;	
	break;
	case 'anular_prueba':
		$objPanico->anularPrueba((int)$_POST["cod_prueba"], $_SESSION['idEmpresa']);
		echo true;
		exit;	
	break;
	
	
}
?>