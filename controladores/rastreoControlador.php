<?php
function index($objSQLServer, $seccion, $mensaje=""){
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$popup 	= isset($_GET['popupRastreo']) ? 1 : 0;
	$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:""; 
	require_once("clases/clsRastreo.php");
	
	require_once 'clases/clsPerfiles.php';
    $objPerfil = new Perfil($objSQLServer);
	
	/* Motivos de confirmacion */
    require_once 'clases/clsMotivoConfirmacion.php';
    $objMotConf = new MotivoConfirmacion($objSQLServer);
    $arrMotivosConfirmacion = $objMotConf->obtenerRegistros();
        
	/* Estado del Trafico */
	/*
	if(tienePerfil(19)){
		require_once 'clases/clsReferencias.php';
		$objReferencia = new Referencia($objSQLServer);
	
		$arrTrafico = array();
		$arrTrafico = $objReferencia->obtenerTrafico();
	}*/
		
	$operacion = 'listar';
	$extraCSS[] = 'css/estilosAbmRastreo.css';
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] = 'js/jquery/combobox.js';
	$extraJS[] = 'js/jquery.blockUI.js';
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] ='js/popupHostFunciones.js';
	
	if(tienePerfil(array(5,7,9,11,19))){
		$extraJS[] = 'js/sm2/soundmanager2-nodebug-jsmin.js';
	}
	
	$sinDefaultJS = true;// No quiero q incluya "rastreoFunciones.js" porque lo incluyo en secciones porq necesito definir permisos[alertas] q contengo permisos de la session.	
			
	if($popup){
		$extraCSS[]='css/estilosAbmPopup.css';	
		require("includes/frametemplate.php");
	}else{
		require("includes/template.php");
	}		
}

function export_kml($objSQLServer, $seccion){
	global $lang;
	
	$idMovil = (int)$_POST['hidId'];
	
	require_once 'includes/tipomovil.inc.php';
	
	require_once 'clases/clsNomenclador.php';
	$objNomenclador = new Nomenclador($objSQLServer);

	if ($idMovil > 0) {

		//Seguridad ----------------
		$movilPermitido = false;
		for($ii=0;$ii<count($_SESSION["rastreo_".$_SESSION["idUsuario"]]);$ii++){
			if($_SESSION["rastreo_".$_SESSION["idUsuario"]][$ii]["mo_id"] == $idMovil){
				$movilPermitido = true;
			}
		}
		if($movilPermitido!=true){
			echo "Acceso denegado al m&oacute;vil.";
			die;
		}
		//--------------------------
	
		require_once("clases/clsRastreo.php");
		$objRastreo = new Rastreo($objSQLServer);
		//-- Obtener datos del procedimiento para actualizar ventana flotante rederecho --//
		$arrReportes = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], $idMovil);
		$arrReportes = $arrReportes[0];
		$lat = round($arrReportes["sh_latitud"],4);
		$lng = round($arrReportes["sh_longitud"],5);
		//-- -//
	
		if(!isset($arrReportes["ubicacion"])) {
			$geocodificacion = $objNomenclador->obtenerNomenclados($lat, $lng, $idMovil);
			$arrReportes["ubicacion"] = $geocodificacion;
			$arrReportes["nivelUbicacion"] = 1;
		}
            
		//CHEQUEO SI EL MOTOR SE ENCUENTRA ENCENDIDO O APAGADO
		$bEncendido = getEstadoMotor($arrReportes);
		$textoMotor = $bEncendido?$lang->system->motor_encendido:$lang->system->motor_apagado;
           
		$datos = '<table>';
		$datos.= '	<tr>';
		$datos.= '		<td width="150">'.$lang->system->movil.'</td>';
		$datos.= '		<td>'.$arrReportes['movil'].'</td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->matricula.'</td>';
		$datos.= '		<td>'.$arrReportes['mo_matricula'].'</td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->dia_hora_gps.'</td>';
		$datos.= '		<td>'.formatearFecha($arrReportes["sh_fechaGeneracion"],'short').'</td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->ultima_conexion.'</td>';
		$datos.= '		<td>'.formatearFecha($arrReportes["sh_fechaRecepcion"],'short').'</td>';
		$datos.= '	</tr>';
		
		//-- --//
		if($arrReportes["um_velocidadMaxima"] > 0 && $arrReportes["dg_velocidad"] > 0){
			if($arrReportes["dg_velocidad"] < $arrReportes["um_velocidadMaxima"]){
				$fontColor = "#008000";//VERDE
				$bgColorV = "";
			}
			elseif(($arrReportes["dg_velocidad"] >= $arrReportes["um_velocidadMaxima"]) && ($arrReportes["dg_velocidad"] <= ($arrReportes["um_velocidadMaxima"] * 1.1))) {
				$fontColor = "#E8E800"; //AMARILLO
				$bgColorV = "#808080";
			}
			else{
				$fontColor = "#FF6A6A";//ROJO
				$bgColorV = "";
			}
		}
		else{
			$fontColor = "#000000";
			$bgColorV = "";
		}
	
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->velocidad.'</td>';
		$datos.= '		<td><span style="color:'.$fontColor.';background-color:'.$bgColorV.'">'.formatearVelocidad($arrReportes["dg_velocidad"]).'</span></td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->sentido.'</td>';
		$datos.= '		<td>'.$lang->system->$arrReportes['dg_curso'].'</td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->ubicacion.'</td>';
		$datos.= '		<td><span>'.$geocodificacion.'</span></td>';
		$datos.= '	</tr>';
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->coordenadas.'</td>';
		$datos.= '		<td>'.$lat.','.$lng.'</td>';
		$datos.= '	</tr>';
	
		/*if($_SESSION['idPerfil'] == 2){
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->tipo_movil.'</td>';
			$datos.= '		<td>'.$arrReportes['mo_id_tipo_movil'].'</td>';
			$datos.= '	</tr>';                    
		}*/
	
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->evento.'</td>';
		$datos.= '		<td>'.$arrReportes['tr_descripcion'].'</td>';
		$datos.= '	</tr>';
				
		if($arrReportes['mo_id_tipo_movil'] != TIPOMOVIL::CELLPHONE){
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->motor.'</td>';
			$datos.= '		<td>'.$textoMotor.'</td>';
			$datos.= '	</tr>';
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->dato_gps.'</td>';
			$datos.= '		<td>'.htmlentities($arrReportes['sh_datoGPS']).'</td>';
			$datos.= '	</tr>';
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->senial_gps.'</td>';
			$cobertura = 'cobertura_'.$objRastreo->getSenalGPS((int)$arrReportes['edadgps'],(int)$arrReportes["sh_datoGPS"]);
			$datos.= '		<td>'.$lang->system->$cobertura.'</td>';
			$datos.= '	</tr>';
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->equipo_instalado.'</td>';
			$datos.= '		<td>'.$arrReportes['un_mostrarComo'].'</td>';
			$datos.= '	</tr>';
		}
				
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->empresa.'</td>';
		$datos.= '		<td>'.(trim($arrReportes['nombreEmpresa']) == ''?' ':$arrReportes['nombreEmpresa']).'</td>';
		$datos.= '	</tr>';                
		$datos.= '	<tr>';
		$datos.= '		<td>'.$lang->system->telefono.'</td>';
		$datos.= '		<td>'.(trim( $arrReportes['telEmpresa']) == ''?' ':$arrReportes['telEmpresa']).'</td>';
		$datos.= '</tr>';                
	
		if($arrReportes[$i]['mo_id_tipo_movil'] == TIPOMOVIL::CELLPHONE){
			$datos.= '	<tr>';
			$datos.= '		<td>'.$lang->system->bateria.'</td>';
			$datos.= '		<td>'.trim($arrReportes['entradas']).'%</td>';
			$datos.= '	</tr>';                  
		}
	
		if(in_array($arrReportes['mo_id_tipo_movil'], array(TIPOMOVIL::CAR, TIPOMOVIL::TRUCK))){
			$arrReportes['mo_marca'] = trim($arrReportes['mo_marca']);
			$arrReportes['mo_modelo'] = trim($arrReportes['mo_modelo']);
			$arrReportes['mo_anio'] = trim($arrReportes['mo_anio']);
			$dt = $dd = null;
			if(!empty($arrReportes['mo_marca'])){
				$dt = $lang->system->marca;
				$dd = $arrReportes['mo_marca'];
			}
			if(!empty($arrReportes['mo_modelo'])){
				$dt .= '/'.$lang->system->modelo;
				$dd .= '/'.$arrReportes['mo_modelo'];
			}
			if($dt != NULL){
				$datos.= '	<tr>';
				$datos.= '		<td>'.$dt.'</td>';
				$datos.= '		<td>'.$dd.'</td>';
				$datos.= '	</tr>';
			}
			// ---------------
			$dt = $dd = NULL;
			if(!empty($arrReportes['mo_anio'])){
				$dt = $lang->system->anio;
				$dd = $arrReportes['mo_anio'];
			}
			if(!empty($arrReportes['mo_color'])){
				if($dt != NULL){
					$dt.= '/';
				}
				$dt.= $lang->system->color;
				if($dd != NULL){
					$dd .= '/';
				}
				$dd .= '/'.$arrReportes['mo_color'];
			}
			if($dt != NULL){
				$datos.= '	<tr>';
				$datos.= '		<td>'.$dt.'</td>';
				$datos.= '		<td>'.$dd.'</td>';
				$datos.= '	</tr>';                      
			}
		}
	
		if(in_array($arrReportes['mo_id_tipo_movil'], array(TIPOMOVIL::CAR, TIPOMOVIL::TRUCK))){
			$arrReportes['conductor1'] = trim($arrReportes['conductor1']);
			$arrReportes['conductor2'] = trim($arrReportes['conductor2']);
			if(!empty($arrReportes['conductor1'])){		
				$datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->conductor.' 1</td>';
				$datos.= '		<td>'.$arrReportes['conductor1'].'</td>';
				$datos.= '	</tr>';
			}
			if(!empty($arrReportes['conductor2'])){		
				$datos.= '	<tr>';
				$datos.= '		<td>'.$lang->system->conductor.' 2</td>';
				$datos.= '		<td>'.$arrReportes['conductor2'].'</td>';
				$datos.= '	</tr>';
			}
		}
		
		
		$datos.= '</table>';
	}

	//-----//

    $out = '';
	$out .= '<?xml version="1.0" encoding="UTF-8"?>';
    $out .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
    $out .= '<Document>';
	$out .= '	<Placemark>';
	$out .= '		<name>';
    $out .= $arrReportes['movil'];
    $out .= '		</name>';

    $out .= '		<description>';
    $out .= $datos;
    $out .= '		</description>';

    $out .= '		<Point>';
    $out .= '			<coordinates>';
    $out .= $lng.','.$lat;
    $out .= '			</coordinates>';
    $out .= '		</Point>';
    $out .= '	</Placemark>';
    $out .= '</Document>';
    $out .= '</kml>';
	
	
	$movil = str_replace(' ','-',$arrReportes['movil']).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y');
	header("Content-disposition: inline; filename=".strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.$movil.'.kml');
	header("Content-Type: application/vnd.google-earth.kml+xml kml; charset=utf8");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . strlen($out));
	echo $out;
}