<?php
error_reporting(0);
@session_start();
$idUsuario = $_SESSION["idUsuario"];

//set_time_limit(300);
include "includes/funciones.php";


if(isset($_POST['accion'])) {
	switch ($_POST['accion']){
		case 'get-datos':
			include "includes/conn.php";
			$idMovil = isset($_POST['idMovil'])?$_POST['idMovil']:exit;
			$fecha = isset($_POST['fecha'])?$_POST['fecha']:exit;
			
			require_once("clases/clsHistorico.php");
			$objHistorico = new Historico($objSQLServer);
			
			require_once "clases/clsMoviles.php";
			$objMovil = new Movil($objSQLServer);
			
			require_once 'clases/clsModeloEquipos.php';
			$objModeloEquipo = new ModeloEquipo($objSQLServer);
			
			include ('clases/clsIdiomas.php');
			$objIdioma = new Idioma();
			$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
	
			$tipo = $objMovil->tiposUnidad($idMovil , $objSQLServer);
			$arrHistorico = $objHistorico->getObtenerHistorico($idMovil, $fecha, $fecha, $tipo);
			if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
				$return = array();
				$return['msg'] = 'false';
				echo json_encode($return);
				exit;
			}
			$arrModeloEquipos = $objModeloEquipo->getBitMotor($idMovil);
			
			$velMax = $objMovil->obtenerMovilesVelocidadMaxima($_SESSION["idUsuario"], $idMovil);
			if($arrHistorico) {
				if(isset($velMax)){
					$arrHistorico[0][100] = $velMax['um_velocidadMaxima'];
				}
			}
	
			$return = array();	
			$return['msg'] = 'ok';
			$return['celulares'] = ($objHistorico->tipoMovil == 'celular')?true:false;
			$return['token'] = ($objHistorico->tipoMovil == 'token')?true:false;
			$return['result'] = $arrHistorico;
			$return['bit'] = $arrModeloEquipos;
			
			echo json_encode($return);
		break;
		case 'vista-historico':
			include ('clases/clsIdiomas.php');
			$objIdioma = new Idioma();
			$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
			
			$data = json_decode($_POST['data']);
			$arr_datos = objectToArray($data->arr_datos);
			$arrBitMotor = objectToArray($data->arrBitMotor);
			$esCelular = objectToArray($data->esCelular);
			
			//$idMovil = $arr_datos[0]['idMovil'];
			
			$tableHTML = "";
			foreach($arr_datos as $i => $item){
				$esGeocerca = 0;
    			$clase = ($i % 2 == 0)?'filaPar':'filaImpar';
				$colorMotor = "#C0C0C0";//MOTOR ENCENDIDO
    
				$arr['byteEncendido'] = getBinary($item['entrada']);
				$arr['mo_bit_motor'] = $arrBitMotor[$item['idMovil']]['bit'];
				$arr['mo_motor_encendido'] = $arrBitMotor[$item['idMovil']]['motor_encendido'];
				if(getEstadoMotor($arr)){ 
					$claseColorMotor = "verde2";
					$classEstadoMotor = 'motor-encendido';
				}
				else{
					$claseColorMotor = "gris2";
					$classEstadoMotor = 'motor-apagado';
				}
		
    			$velocidadAmarilla = $arr_datos[0][100] * 1.1;
				if($item['velocidadGPS'] > $velocidadAmarilla) {
					$claseColorReporte = 'rojo';
				}
				elseif($item['velocidadGPS'] >= $arr_datos[0][100]){
					$claseColorReporte = 'amarillo';	
				}
				elseif($item['velocidadGPS'] == 0){
					$claseColorReporte = 'gris3';	
				} 
				else{
					$claseColorReporte = 'verde';
				}
						
				$eventoIcon = "";		
				if($item['idEvento'] != 1){
					$eventoIcon = '<img width="15" src="imagenes/iconos/eventos/'.$item['eventoImg'].'" />';
				}
	
				$velocidad = formatearVelocidad($item['velocidadGPS']);	
				$rumbo = calcularRumbo($item['curso']);	
	
				$lat = $item['lat'];
				$lng = $item['lon'];
		
				//-- validacion de DataLoger --//
				$diff_hora_recepcion = (strtotime($item['fechaRecibido']) - strtotime(str_replace('/','-',$item['fechaGenerado']))) / 60;
				$diff_min = 10;
				$isDataloger = false;
				if(tienePerfil(array(5,9,13))){
					if($diff_hora_recepcion > $diff_min && $item['idEvento'] != 71 && $item['idEvento'] != 72){//falta de reporte
						$isDataloger = true;	
					}
				}
				//-- --//
		
				$contenidoTable = '<tr class="'.$clase.' '.(!isset($arr_datos[$i+1])?'tr-last':'').' claseInfoPunto">';
				if(tienePerfil(array(16,9,10,11,12))){//-- Tiene asignado Historico Reducido --//
					$contenidoTable .= '	<td>';
					$contenidoTable .= '		<span class="float_l"><strong>'.$item['movil'].'</strong></span>';//Matricula
					$contenidoTable .= '	</td>';
					$contenidoTable .= '	<td>';
					$contenidoTable .= '		<fieldset class="float_l not_bg_white">';
					$contenidoTable .= '			<span class="float_l" style="margin-right:4px;">'.$item['evento_txt'].'</span>&nbsp;';//Evento
					$contenidoTable .= '			<span id="nomenclado_'.$i.'">&nbsp;</span>';//Ubicacion
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '		<fieldset class="float_r not_bg_white">';
					$contenidoTable .= '			<span title="'.($isDataloger?('** '.$lang->system->infomacion_recibida.': '.formatearFecha($item['fechaRecibido'])):'').'">'.($isDataloger?'** ':'').$item['fecha_txt'].'</span>';//Fecha
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '	</td>';	
				}
				else{//-- Historico por default (NO REDUCIDO)--//
					$contenidoTable .= '	<td>';
					$contenidoTable .= '		<fieldset class="not_bg_white">';
					if($esCelulares == false){
						$contenidoTable .= '			<span class="float_l movil-motor '.$classEstadoMotor.'" title=""></span>';//Motor
					}
					$contenidoTable .= '			<span class="float_l"><strong>'.$item['movil'].'</strong></span>';//Matricula
					$contenidoTable .= '			<br >';
					if($esCelulares == false){
						$contenidoTable .= '		<span class="float_l" style="margin-left:5px;">'.$rumbo.'</span>';//Rumbo
					}
					$contenidoTable .= '			<span class="float_l" style="margin-left:5px;">'.$velocidad.'</span>';//Velocidad
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '		<fieldset class="float_r not_bg_white">';
					$contenidoTable .= '			<span class="float_r">&nbsp;&nbsp;'.$eventoIcon.'</span>';
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '	</td>';
					$contenidoTable .= '	<td>';
					$contenidoTable .= '		<fieldset class="float_l not_bg_white">';
					$contenidoTable .= '			<span class="float_l" style="margin-right:4px;">'.$item['evento_txt'].'</span>&nbsp;';//Evento
					$contenidoTable .= '			<span id="nomenclado_'.$i.'">&nbsp;</span><br />';//Ubicacion
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '		<fieldset class="float_r not_bg_white">';
					$contenidoTable .= '			<span title="'.($isDataloger?('** '.$lang->system->infomacion_recibida.': '.formatearFecha($item['fechaRecibido'])):'').'">'.($isDataloger?'** ':'').$item['fecha_txt'].'</span>';//Fecha
					$contenidoTable .= '		</fieldset>';
					$contenidoTable .= '	</td>';
				}//-- Fin contenido listado historico --//
		
				$contenidoTable .= '<td class="td-last">
						<fieldset class="float_r not_bg_white"><a href="javascript:;" onclick="cargarInfoPtos('.($item['orden'] - 1).', 16,0,'.$lat.','.$lng.','."'click-nomeclatura'".');">';//Links
				$contenidoTable .= '<img valign="absmiddle" src="imagenes/raster/black/flecha12b.png" style="margin-top:2px;" class="float_r" />';
				if(!tienePerfil(array(15,16))){//No mostramos el nro de registro
					$contenidoTable .= '<label class="float_r" style="text-align: center; width: 50px !important;">#'.$item['orden'].'</label>';//Nro de registro
				}
				$contenidoTable .= '</a></fieldset></td>';
				$contenidoTable .= '</tr>';
				
					/*//-- Telemetria --//
					include "includes/conn.php";
					require_once("clases/clsEquipos.php");   
					$objEquipo = new Equipo($objSQLServer);
					$arrTelemetria[1] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 1, true);
					$arrTelemetria[2] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 2, true);
					$arrTelemetria[3] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 3, true);
					
					$telemtria = '';
					if(!empty($arrTelemetria[1]['ut_unidad']) && !empty($item[22]) && $item[22] != 'null'){
						$telemtria.= number_format($item[22],2,',','.').encode($arrTelemetria[1]['ut_unidad']);
						$separador = '/';
					}
					if(!empty($arrTelemetria[2]['ut_unidad'])  && !empty($item[23]) && $item[23] != 'null'){
						$telemtria.= $separador.number_format($item[23],2,',','.').encode($arrTelemetria[2]['ut_unidad']);
						$separador = '/';
					}
					if(!empty($arrTelemetria[3]['ut_unidad'])  && !empty($item[24]) && $item[24] != 'null'){
						$telemtria.= $separador.number_format($item[24],2,',','.').encode($arrTelemetria[3]['ut_unidad']);
						$separador = '/';
					}
					$telemtria = empty($telemtria)?'--':$telemtria;
					*///-- --//
			
				$tableHTML .= $contenidoTable;
			}//-- fin foreach --//
			
			echo $tableHTML;
		break;
		case 'nomenclar-historico':
			include "includes/conn.php";
			require_once 'clases/clsNomenclador.php';
			$objNomenclador = new Nomenclador($objSQLServer);
			
			$i = $_POST['i'];
			$idMovil = $_POST['idMovil'];
			$lat = $_POST['lat'];
			$lng = $_POST['lng'];
			$id_referencia = (int)$_POST['id_referencia'];
			
			$geocodificacion = ($objNomenclador->obtenerNomenclados($lat, $lng, $idMovil));
			$nomenclado = '<a onclick="cargarInfoPtos('.($i).', 16,0,'.$lat.','.$lng.',&quot;click-nomeclatura&quot;);" href="javascript:;">'.$geocodificacion.'</a>';
	
			$arr_evento = $objNomenclador->obtenerNomencladosGeocercas($id_referencia);
			$evento_txt = ($arr_evento[0]?$arr_evento[0].'&nbsp;':'');	
			
			$contenidoTable = '<span class="float_l" style="margin-right:4px;">'.$evento_txt.'</span>';//Evento
			$contenidoTable.= '<strong class="float_l"><span>'.$nomenclado.'</span></strong>';//Ubicacion
			
			echo $contenidoTable;
		break;
		default:
			echo "error";
		break;
	}
}
exit;
