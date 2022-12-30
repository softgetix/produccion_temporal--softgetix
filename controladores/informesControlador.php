<?php
set_time_limit(600); 
$solapa = isset($_POST['solapa'])?$_POST['solapa']:(isset($_GET['solapa'])?$_GET['solapa']:'historico');
$solapa = ($solapa == 'historico_avanzado')?($objPerfil->validarSeccion('informes_historico_avanzado')?$solapa:NULL):$solapa;
$solapa = ($solapa == 'km_recorridos')?($objPerfil->validarSeccion('informes_km_recorridos')?$solapa:NULL):$solapa;
$solapa = ($solapa == 'viajes')?($objPerfil->validarSeccion('informes_viajes')?$solapa:NULL):$solapa;
$solapa = ($solapa == 'alertas')?($objPerfil->validarSeccion('informes_alertas')?$solapa:NULL):$solapa;

function index($objSQLServer, $seccion){
	global $solapa;
	global $lang;
	require_once 'clases/clsInformes.php';
	$objInformes = new Informes($objSQLServer);
		
	switch($solapa){
		case 'historico':
			$extraJS[] = 'js/openLayers/OpenLayers.js';
			//$extraJS[] = 'http://openlayers.org/api/OpenLayers.js';
			$extraJS[] = 'js/defaultMap.js';
			$extraJS[] = 'js/historicoFunciones.js';
			$extraJS[] = 'js/historicoMap.js';
			$extraJS[] = 'js/jquery/jquery.placeholder.js';
			
			if($_POST['rastreo'] && $_POST['idMovil']){
				require_once('clases/clsMoviles.php');
				$objMovil = new Movil($objSQLServer);
				$resp = $objMovil->getMovilUsuarioId($_POST['idMovil']);	
				if($resp){
					$arrMovilRastreo['nombre'] = $resp['valor'];
					$arrMovilRastreo['id'] = $_POST['idMovil'];
				}
			}
		break;
		case 'historico_avanzado':
		case 'km_recorridos':
		case 'viajes':
		/*case 'correo_enviado':	
			require_once('clases/clsMoviles.php');
			$objMovil = new Movil($objSQLServer);
			$arrMovilesUsuario = $objMovil->obtenerMovilesPorGrupoListaHistorial($_SESSION['idUsuario']);
		break;*/
		case 'alertas':
			require_once('clases/clsMoviles.php');
			$objMovil = new Movil($objSQLServer);
			$arrMovilesUsuario = $objMovil->obtenerMovilesPorGrupoListaHistorial($_SESSION['idUsuario']);
			
			require_once 'clases/clsDefinicionReportes.php';
			$objReporte = new DefinicionReporte($objSQLServer);
			$arrEventos = $objReporte->obtenerEventosAsignados($_SESSION['idAgente']);
			
			require_once 'clases/clsIdiomas.php';
			$objIdioma = new Idioma();
			$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
			foreach($arrEventos as $k => $item){
				$dato = 'evento_'.(int)$item['id'];
				$dato = $eventos->$dato->__toString()?$eventos->$dato->__toString():($eventos->default->__toString().' ('.$item['id'].')');
				$arrEventos[$k]['dato'] = $dato;
			}
			
			
			require_once 'clases/clsAlertasXGeocercas.php';
			$objAlertas = new AlertasXGeocerca($objSQLServer);
	
			$datos['idTipoEmpresa'] = $_SESSION['idTipoEmpresa'];
			$datos['idUsuario'] = $_SESSION['idUsuario'];
			$datos['idPerfil'] = $_SESSION['idPerfil'];
			$datos['filtro'] = 'getAllReg';
			
			$arrAlertas = $objAlertas->getAlertas($datos);			
		break;
		default:
			header('Location:boot.php?c=informes&solapa=historico');
			exit;
		break;	
	}
  	$extraJS[] = 'js/calendario.js';
	
		
	require("includes/template.php");
	
	if(!empty($_GET['mensaje'])){
		echo '<script language="javascript">viewMessage(true, "'.$_GET['mensaje'].'");</script>';	
	}
}

function export_historico_xls($objSQLServer, $seccion){
	global $solapa;
	global $lang;
	$idMovil = isset($_POST['idMovil'])?$_POST['idMovil']:exit;
	$fecha = isset($_POST['fecha'])?$_POST['fecha']:exit;
	
	$strValidaciones = "abcdefghijklmnñopqrstuvwxyz0123456789-.,;:";
	
	$hexToBin[0] = "0000";
	$hexToBin[1] = "0001";
	$hexToBin[2] = "0010";
	$hexToBin[3] = "0011";
	$hexToBin[4] = "0100";
	$hexToBin[5] = "0101";
	$hexToBin[6] = "0110";
	$hexToBin[7] = "0111";
	$hexToBin[8] = "1000";
	$hexToBin[9] = "1001";
	$hexToBin["A"] = "1010";
	$hexToBin["B"] = "1011";
	$hexToBin["C"] = "1100";
	$hexToBin["D"] = "1101";
	$hexToBin["E"] = "1110";
	$hexToBin["F"] = "1111";
	
	require_once("clases/clsHistorico.php");
	$objHistorico = new Historico($objSQLServer);
			
	require_once "clases/clsMoviles.php";
	$objMovil = new Movil($objSQLServer);
	
	require_once("clases/clsEquipos.php");   
	$objEquipo = new Equipo($objSQLServer);
	
	include "clases/clsNomenclador.php";
	$objNomenclador = new Nomenclador($objSQLServer);

	$velMax = $objMovil->obtenerMovilesVelocidadMaxima($_SESSION["idUsuario"], $idMovil);
	$velocidadMaxima = $velMax['um_velocidadMaxima'];
    $velocidadAmarilla = $velocidadMaxima * 1.1;
		
	$tipo = $objMovil->tiposUnidad($idMovil , $objSQLServer);
	$arrHistorico = $objHistorico->getObtenerHistorico($idMovil, $fecha, $fecha, $tipo);
	if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
		if($arrHistorico == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
		
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
		
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->menu->$seccion))
		->setSubject(sanear_string($lang->menu->$seccion))
		->setDescription(sanear_string($lang->menu->$seccion))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$arrCols = array('A','B','C','D','E','F','G','H','I','J','K');      
	$posCol = 0;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol].'1','N°')
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->movil);
	if($objHistorico->tipoMovil == 'vehiculo'){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->motor);
	}
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->fecha)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->evento);
	
	if ($objHistorico->tipoMovil == 'vehiculo' || $objHistorico->tipoMovil == 'celular'){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->velocidad)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->sentido)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->odometro);
		//->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->telemetria);
	}
	$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->ubicacion)
        ->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->conductor);	
	
	$arrColsFormat = $arrCols;					
	for($i=count($arrColsFormat); $i>$posCol; $i--){
		unset($arrColsFormat[$i]);	
	}
	$objPHPExcel->setFormatoRows($arrColsFormat);
	$objPHPExcel->alignCenter($arrColsFormat);
						
	$i = 2;
	foreach($arrHistorico as $item){
	
	    $lat = $item['lat'];
        $lng = $item['lon'];
       
		//-- Nomenclatura --//
        $geocodificacion = $objNomenclador->obtenerNomenclados($lat, $lng, $item['movil']);
        $nomenclado = "";
		if ($geocodificacion){
            $ubicacion = $geocodificacion;
		}
        else{
            $ubicacion = "-";
		}
        $nomenclado = stripcslashes($geocodificacion);
       	//-- --//

		//-- Nomenclatura Evento --//
		$arrEvento 		= $objNomenclador->obtenerNomencladosGeocercas($item['idHe']);	
		$nomencladoRef 	= encode($arrEvento[0]);
		//-- --//
		

        $bAux1 = $hexToBin[substr($item['entrada'], 0, 1)];
		$backgroundMotor = (substr($bAux1, 0, 1) == 1)?'b7fe9c':'c3c3c3';

        if ($item['velocidadGPS'] > $velocidadAmarilla) {
            $backgroundVelocidad = 'ff0000';
        }
		else if ($item['velocidadGPS'] >= $velocidadMaxima) {
            $backgroundVelocidad = 'ffffae';
        }
		else if ($item['velocidadGPS'] == 0) {
            $backgroundVelocidad = 'c3c3c3';
        }
		else{
            $backgroundVelocidad = 'b7fe9c';
        }
       
	    $backgroundEvento = "";
        if(in_array($item['idEvento'],array(2,5,6,20,22,33,38))){
			$backgroundEvento = 'ff0000';	
		}
		elseif(in_array($item['idEvento'],array(3,4,9,19,21,36))){
			$backgroundEvento = 'ffb76f';
		}
		elseif(in_array($item['idEvento'],array(7,23,34,37))){
			$backgroundEvento = 'b7fe9c';
		}
		elseif(in_array($item['idEvento'],array(8,11,24,35))){
			$backgroundEvento = 'a8ffff';
		}
		elseif(in_array($item['idEvento'],array(10,12))){
			$backgroundEvento = 'ffffae';
		}
		elseif(in_array($item['idEvento'],array(18))){
			$backgroundEvento= 'c3c3c3';
		}
        
		$conductor = (!empty($item['co_apellido']))?($item['co_apellido'].(!empty($item['co_nombre'])?', '.$item['co_nombre']:'')):(!empty($item['co_nombre'])?$item['co_nombre']:'');		
        $conductor.= (!empty($item['co_ibutton'])?((!empty($conductor))?' - ':'').$item['co_ibutton']:'');
		
		//-- validacion de DataLoger --//
		$diff_hora_recepcion = (strtotime($item['fechaRecibido']) - strtotime(str_replace('/','-',$item['fechaGenerado']))) / 60;
		$diff_min = 10;
		$isDataloger = false;
		if($diff_hora_recepcion > $diff_min && $item['idEvento'] != 71 && $item['idEvento'] != 72){//falta de reporte
			$isDataloger = true;	
		}
		//-- --//
		/*
		//-- Telemetria --//
		$arrTelemetria[1] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 1, true);
		$arrTelemetria[2] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 2, true);
		$arrTelemetria[3] = $objEquipo->obtenerUnidadTelemetria($item['idMovil'], 3, true);
		
		$telemtria = '';
		if(!empty($arrTelemetria[1]['ut_unidad']) && !empty($item[22]) && $item[22] != 'null'){
			$telemtria.= number_format($item[22],2,',','.').decode($arrTelemetria[1]['ut_unidad']);
			$separador = '/';
		}
		if(!empty($arrTelemetria[2]['ut_unidad'])  && !empty($item[23]) && $item[23] != 'null'){
			$telemtria.= $separador.number_format($item[23],2,',','.').decode($arrTelemetria[2]['ut_unidad']);
			$separador = '/';
		}
		if(!empty($arrTelemetria[3]['ut_unidad'])  && !empty($item[24]) && $item[24] != 'null'){
			$telemtria.= $separador.number_format($item[24],2,',','.').decode($arrTelemetria[3]['ut_unidad']);
			$separador = '/';
		}
		$telemtria = empty($telemtria)?'--':$telemtria;
		*/
		$posCol = 0;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol].$i,$item['orden'])
			->setCellValue($arrCols[$posCol=$posCol+1].$i, encode($item['movil']));
		if($objHistorico->tipoMovil == 'vehiculo'){
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol=$posCol+1].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundMotor))));
		}
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, ($isDataloger?'** ':'').formatearFecha($item['fecha']))
			->setCellValue($arrCols[$posCol=$posCol+1].$i, $item['evento_txt'].$nomencladoRef); 
		if(!empty($backgroundEvento)){
			$objPHPExcel->getActiveSheet()->getStyle($arrCols[$posCol].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundEvento))));
		}
		
		if ($objHistorico->tipoMovil == 'vehiculo' || $objHistorico->tipoMovil == 'celular'){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, formatearVelocidad($item['velocidadGPS'])) 
			->setCellValue($arrCols[$posCol=$posCol+1].$i, calcularRumbo($item['curso']))
			->setCellValue($arrCols[$posCol=$posCol+1].$i,  formatearDistancia($item['odometro']));
			//->setCellValue($arrCols[$posCol=$posCol+1].$i, $telemtria);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol-3].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundVelocidad))));
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, $nomenclado)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, encode($conductor));	
			
			$objPHPExcel->setActiveSheetIndex(0)->getCell($arrCols[$posCol-1].$i)->getHyperlink()->setUrl(strip_tags('http://maps.google.com/maps?q='.$lat.','.$lng));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol-1].$i)->applyFromArray(array('font' => array('color' => array('rgb' => '0072FF'), 'underline' => 'single')));
		$i++;
	}
					
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->menu->$seccion));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->menu->$seccion)).'_'.str_replace('-','',$fecha).'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function export_historico_kml($objSQLServer){
	global $solapa;
	global $lang;
	$idMovil = isset($_POST['idMovil'])?$_POST['idMovil']:exit;
	$fecha = isset($_POST['fecha'])?$_POST['fecha']:exit;
	
	$strValidaciones = "abcdefghijklmnñopqrstuvwxyz0123456789-.,;:";
	
	$hexToBin[0] = "0000";
	$hexToBin[1] = "0001";
	$hexToBin[2] = "0010";
	$hexToBin[3] = "0011";
	$hexToBin[4] = "0100";
	$hexToBin[5] = "0101";
	$hexToBin[6] = "0110";
	$hexToBin[7] = "0111";
	$hexToBin[8] = "1000";
	$hexToBin[9] = "1001";
	$hexToBin["A"] = "1010";
	$hexToBin["B"] = "1011";
	$hexToBin["C"] = "1100";
	$hexToBin["D"] = "1101";
	$hexToBin["E"] = "1110";
	$hexToBin["F"] = "1111";
	
	require_once("clases/clsHistorico.php");
	$objHistorico = new Historico($objSQLServer);
			
	require_once "clases/clsMoviles.php";
	$objMovil = new Movil($objSQLServer);
	
	require_once("clases/clsEquipos.php");   
	$objEquipo = new Equipo($objSQLServer);
	
	include "clases/clsNomenclador.php";
	$objNomenclador = new Nomenclador($objSQLServer);

	$tipo = $objMovil->tiposUnidad($idMovil , $objSQLServer);
	$arrHistorico = $objHistorico->getObtenerHistorico($idMovil, $fecha, $fecha, $tipo);
	if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
		if($arrHistorico == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
	
	$out = '';
    $out .= '<?xml version="1.0" encoding="UTF-8"?>';
    $out .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
    $out .= '<Document>';

    foreach($arrHistorico as $item){
        
		$lat = $item['lat'];
        $lng = $item['lon'];
        
		//-- Nomenclatura --//
        $geocodificacion = $objNomenclador->obtenerNomenclados($lat, $lng, $item['movil']);
        
		if ($geocodificacion){
            $ubicacion = $geocodificacion;}
        else{
            $ubicacion = "-";
		}
        $nomenclado = stripcslashes($geocodificacion);
       	$nomenclado = str_replace('','',$nomenclado);
		//-- --//
		
		$bAux1 = $hexToBin[substr($item['entrada'], 0, 1)];
        $estadoMotor = (substr($bAux1, 0, 1) == 1)?$lang->system->motor_encendido:$lang->system->motor_apagado;
		
		
        $contenido = "<table>";
        $contenido .= "<tr><td wdith='400px'>N:</td><td>".$item['orden']."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->movil.":</td><td style='font-weight:bold;'>".encode($item['movil'])."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->motor.":</td><td style='font-weight:bold;'>".$estadoMotor."</td></tr>";
        $contenido .= "<tr height='10'><td colspan='2'> </td></tr><tr><td colspan='2' style='font-weight:bold;'> ".$item['fecha']."</td></tr>";
        $contenido .= "<tr height='10'><td colspan='2'> </td></tr><tr><td>".$lang->system->evento.":</td><td style='font-weight:bold;'>".encode($item['evento_txt'])."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->ubicacion.": </td><td style='font-weight:bold;'>".$nomenclado."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->velocidad.":</td><td style='font-weight:bold;'> ".formatearVelocidad($item['velocidadGPS'])."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->sentido.":</td><td style='font-weight:bold;'> ".calcularRumbo($item['curso'])."</td></tr>";
        $contenido .= "<tr><td>".$lang->system->odometro.": </td><td style='font-weight:bold;'>".formatearDistancia($item['odometro'])."</td></tr>";
		$contenido .= "</table>";
       
	    $out .= '<Placemark>';

        $out .= '<name>';
        $out .= $item['orden'];
        $out .= '</name>';

        $out .= '<description>';
        $out .= $contenido;
        $out .= '</description>';

        $out .= '<Point>';
        $out .= '<coordinates>';
        $out .= $item['lon'];
        $out .= ',';
        $out .= $item['lat'];
        $out .= '</coordinates>';
        $out .= '</Point>';

        $out .= '</Placemark>';
    }

    $out .= '</Document>';
    $out .= '</kml>';

    $filename = strtolower(sanear_string($lang->system->historico)).'_'.str_replace('-','',$fecha). '.kml';

    header("Content-disposition: inline; filename=" . $filename);
    header("Content-Type: application/vnd.google-earth.kml+xml kml; charset=utf8");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . strlen($out));

    echo $out;
}

function export_historico_avanzado_xls($objSQLServer, $seccion){
	global $solapa;
	global $lang;
	$idMovil = isset($_POST['idMovil'])?$_POST['idMovil']:exit;
	$fecha_desde = isset($_POST['fecha_desde'])?$_POST['fecha_desde']:exit;
	$fecha_hasta = isset($_POST['fecha_hasta'])?$_POST['fecha_hasta']:exit;
	
	##--- FALIDACION PARA Q NO SUPERE LA EXPORTACIÓN DE HISTORICOS MAS DE 7 DÍAS ---##
	if($_SESSION['idAgente'] == 121){
		$dif_seg = strtotime($fecha_hasta) - strtotime($fecha_desde);
		$dif_dias = intval($dif_seg/60/60/24);
		
		if($dif_dias > 7){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = 'Recuerde que el rango de fechas no puede superar los 7 d&iacute;as.';
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
	##--- ---##
	
	
	$strValidaciones = "abcdefghijklmnñopqrstuvwxyz0123456789-.,;:";
	
	$hexToBin[0] = "0000";
	$hexToBin[1] = "0001";
	$hexToBin[2] = "0010";
	$hexToBin[3] = "0011";
	$hexToBin[4] = "0100";
	$hexToBin[5] = "0101";
	$hexToBin[6] = "0110";
	$hexToBin[7] = "0111";
	$hexToBin[8] = "1000";
	$hexToBin[9] = "1001";
	$hexToBin["A"] = "1010";
	$hexToBin["B"] = "1011";
	$hexToBin["C"] = "1100";
	$hexToBin["D"] = "1101";
	$hexToBin["E"] = "1110";
	$hexToBin["F"] = "1111";
	
	require_once("clases/clsHistorico.php");
	$objHistorico = new Historico($objSQLServer);
			
	require_once "clases/clsMoviles.php";
	$objMovil = new Movil($objSQLServer);
	
	require_once("clases/clsEquipos.php");   
	$objEquipo = new Equipo($objSQLServer);
	
	include "clases/clsNomenclador.php";
	$objNomenclador = new Nomenclador($objSQLServer);

	$velMax = $objMovil->obtenerMovilesVelocidadMaxima($_SESSION["idUsuario"], $idMovil);
	$velocidadMaxima = $velMax['um_velocidadMaxima'];
    $velocidadAmarilla = $velocidadMaxima * 1.1;
		
	$tipo = $objMovil->tiposUnidad($idMovil , $objSQLServer);
	$arrHistorico = $objHistorico->getObtenerHistorico($idMovil, $fecha_desde, $fecha_hasta, $tipo);
	if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
		if($arrHistorico == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
		
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->system->historico_avanzado))
		->setSubject(sanear_string($lang->system->historico_avanzado))
		->setDescription(sanear_string($lang->system->historico_avanzado))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$arrCols = array('A','B','C','D','E','F','G','H','I','J','K');      
	$posCol = 0;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol].'1','N°')
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->movil);
	if($objHistorico->tipoMovil == 'vehiculo'){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->motor);
	}
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->fecha)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->evento);
	
	if ($objHistorico->tipoMovil == 'vehiculo' || $objHistorico->tipoMovil == 'celular'){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->velocidad)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->sentido)
		->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->odometro);
		//->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->telemetria);
	}
	$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->ubicacion)
        ->setCellValue($arrCols[$posCol=$posCol+1].'1', $lang->system->conductor);	
	
	$arrColsFormat = $arrCols;					
	for($i=count($arrColsFormat); $i>$posCol; $i--){
		unset($arrColsFormat[$i]);	
	}
	$objPHPExcel->setFormatoRows($arrColsFormat);
	$objPHPExcel->alignCenter($arrColsFormat);
						
	$i = 2;
	
	foreach($arrHistorico as $item){
		$lat = $item['lat'];
        $lng = $item['lon'];
       
		//-- Nomenclatura --//
        $geocodificacion = $objNomenclador->obtenerNomenclados($lat, $lng, $item['movil']);
        $nomenclado = "";
		if ($geocodificacion){
            $ubicacion = $geocodificacion;
		}
        else{
            $ubicacion = "-";
		}
        $nomenclado = stripcslashes($geocodificacion);
       	//-- --//

		//-- Nomenclatura Evento --//
		$arrEvento 		= $objNomenclador->obtenerNomencladosGeocercas($item['idHe']);	
		$nomencladoRef 	= encode($arrEvento[0]);
		//-- --//
		

        $bAux1 = $hexToBin[substr($item['entrada'], 0, 1)];
		$backgroundMotor = (substr($bAux1, 0, 1) == 1)?'b7fe9c':'c3c3c3';

        if ($item['velocidadGPS'] > $velocidadAmarilla) {
            $backgroundVelocidad = 'ff0000';
        }
		else if ($item['velocidadGPS'] >= $velocidadMaxima) {
            $backgroundVelocidad = 'ffffae';
        }
		else if ($item['velocidadGPS'] == 0) {
            $backgroundVelocidad = 'c3c3c3';
        }
		else{
            $backgroundVelocidad = 'b7fe9c';
        }
       
	    $backgroundEvento = "";
        if(in_array($item['idEvento]'],array(2,5,6,20,22,33,38))){
			$backgroundEvento = 'ff0000';	
		}
		elseif(in_array($item['idEvento]'],array(3,4,9,19,21,36))){
			$backgroundEvento = 'ffb76f';
		}
		elseif(in_array($item['idEvento]'],array(7,23,34,37))){
			$backgroundEvento = 'b7fe9c';
		}
		elseif(in_array($item['idEvento]'],array(8,11,24,35))){
			$backgroundEvento = 'a8ffff';
		}
		elseif(in_array($item['idEvento]'],array(10,12))){
			$backgroundEvento = 'ffffae';
		}
		elseif(in_array($item['idEvento]'],array(18))){
			$backgroundEvento= 'c3c3c3';
		}
        
		$conductor = (!empty($item['co_apellido']))?($item['co_apellido'].(!empty($item['co_nombre'])?', '.$item['co_nombre']:'')):(!empty($item['co_nombre'])?$item['co_nombre']:'');		
        $conductor.= (!empty($item['co_ibutton'])?((!empty($conductor))?' - ':'').$item['co_ibutton']:'');
		
		//-- validacion de DataLoger --//
		$diff_hora_recepcion = (strtotime($item['fechaRecibido']) - strtotime(str_replace('/','-',$item['fechaGenerado']))) / 60;
		$diff_min = 10;
		$isDataloger = false;
		if($diff_hora_recepcion > $diff_min && $item['idEvento'] != 71 && $item['idEvento'] != 72){//falta de reporte
			$isDataloger = true;	
		}
		//-- --//
		
		$posCol = 0;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol].$i,$item['orden'])
			->setCellValue($arrCols[$posCol=$posCol+1].$i, encode($item['movil']));
		if($objHistorico->tipoMovil == 'vehiculo'){
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol=$posCol+1].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundMotor))));
		}

		$auxFecha = explode('a',$item['fecha']);
		$auxFecha = (isset($auxFecha[0])?(formatearFecha($auxFecha[0]).(isset($auxFecha[1])?(strtotime($auxFecha[1])?(' a '.formatearFecha($auxFecha[1])):(strtotime(date('Y',strtotime($auxFecha[0])).'-'.trim($auxFecha[1]))?(' a '.formatearFecha(date('Y',strtotime($auxFecha[0])).'-'.trim($auxFecha[1]))):NULL)):NULL)):NULL);	
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, ($isDataloger?'** ':'').$auxFecha)
			//->setCellValue($arrCols[$posCol=$posCol+1].$i, ($isDataloger?'** ':'').formatearFecha($item['fecha']))
			->setCellValue($arrCols[$posCol=$posCol+1].$i, trim($item['evento_txt'].' '.$nomencladoRef)); 
		if(!empty($backgroundEvento)){
			$objPHPExcel->getActiveSheet()->getStyle($arrCols[$posCol].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundEvento))));
		}
		
		if ($objHistorico->tipoMovil == 'vehiculo' || $objHistorico->tipoMovil == 'celular'){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, formatearVelocidad($item['velocidadGPS'])) 
			->setCellValue($arrCols[$posCol=$posCol+1].$i, calcularRumbo($item['curso']))
			->setCellValue($arrCols[$posCol=$posCol+1].$i, formatearDistancia($item['odometro']));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol-3].$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $backgroundVelocidad))));
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, $nomenclado)
			->setCellValue($arrCols[$posCol=$posCol+1].$i, encode($conductor));	
			
			$objPHPExcel->setActiveSheetIndex(0)->getCell($arrCols[$posCol-1].$i)->getHyperlink()->setUrl(strip_tags('http://maps.google.com/maps?q='.$lat.','.$lng));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle($arrCols[$posCol-1].$i)->applyFromArray(array('font' => array('color' => array('rgb' => '0072FF'), 'underline' => 'single')));
		$i++;
	}
			
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->system->historico_avanzado));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->system->historico_avanzado)).'_'.getFechaServer('dmY').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function export_km_recorridos_xls($objSQLServer){
	global $solapa;
	global $lang;
	//$fecha_desde = isset($_POST['fecha_desde'])?$_POST['fecha_desde']:exit;
	//$fecha_hasta = isset($_POST['fecha_hasta'])?$_POST['fecha_hasta']:exit;
	$fecha_desde = isset($_POST['fecha'])?$_POST['fecha']:exit;
	$fecha_hasta = isset($_POST['fecha'])?$_POST['fecha']:exit;
	
	require_once 'clases/clsInformes.php';
	$objInformes = new Informes($objSQLServer);
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	
	$objInformes->vistaMovil = $objMovil->getVistaMoviles($_SESSION['idUsuario']);
	$arrKmRecorridos = $objInformes->obtenerReporteKmsRecorridos($fecha_desde,$fecha_hasta,$_POST['arrMovil']);
	
	if(!is_array($arrKmRecorridos)){//-- Error 408: Supero tiempo de espera --//
		if($arrKmRecorridos == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->system->km_recorridos))
		->setSubject(sanear_string($lang->system->km_recorridos))
		->setDescription(sanear_string($lang->system->km_recorridos))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->fecha)
		->setCellValue('B1', $lang->system->movil)
		->setCellValue('C1', $lang->system->distancia)
		->setCellValue('D1', $lang->system->vel_max);
		
	$objPHPExcel->setFormatoRows(array('A','B','C','D'/*,'E','F'*/));
	$objPHPExcel->alignCenter(array('A','C','D'));
						
	$i = 2;
	foreach($arrKmRecorridos as $item){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,formatearFecha($item['Fecha']))
			->setCellValue('B'.$i,$item['Patente'])
			->setCellValue('C'.$i,formatearDistancia($item['KmRecorrido']))
			->setCellValue('D'.$i,formatearVelocidad($item['VelMax']));
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->system->km_recorridos));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->system->km_recorridos)).'_'.date('dmY',strtotime($fecha_desde)).'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}


function export_viajes_xls($objSQLServer){
	global $solapa;
	global $lang;
	
	require_once 'clases/clsInformes.php';
	$objInformes = new Informes($objSQLServer);
	
	require_once 'clases/clsNomenclador.php';
	$objNomenclador = new Nomenclador($objSQLServer);
	
	//$fecha_desde = date('Y-m-d',strtotime($_POST['fecha_desde'])).' 00:00:00';	
	//$fecha_hasta = date('Y-m-d',strtotime($_POST['fecha_hasta'])).' 23:59:59';
	$fecha_desde = date('Y-m-d',strtotime($_POST['fecha'])).' 00:00:00';
	$fecha_hasta = date('Y-m-d',strtotime($_POST['fecha'])).' 23:59:59';
	
	
	$arrMoviles = implode(',',$_POST['arrMovil']);
	$arrViajes = $objInformes->obtenerReporteViajes($fecha_desde, $fecha_hasta, $arrMoviles, $_SESSION['idUsuario']);
	if(!is_array($arrViajes)){//-- Error 408: Supero tiempo de espera --//
		if($arrViajes == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
		
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
		
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->system->viajes))
		->setSubject(sanear_string($lang->system->viajes))
		->setDescription(sanear_string($lang->system->viajes))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $lang->system->movil)
		->setCellValue('B1', $lang->system->fecha_partida)
		->setCellValue('C1', $lang->system->distancia)
		->setCellValue('D1', $lang->system->vel_max)
		->setCellValue('E1', $lang->system->fecha_llegada)
		->setCellValue('F1', $lang->system->lugar_partida);
		
	$objPHPExcel->setFormatoRows(array('A','B','C','D','E','F'));
	$objPHPExcel->alignCenter(array('B','C','D','E'));
	
	$i = 2;
	foreach($arrViajes as $item){
		
		$arrLatLng = explode(',',$item['Lugar-Partida']);
		$item['Latitud'] = trim($arrLatLng[0]);
		$item['Longitud'] = trim($arrLatLng[1]);
		
		$lugar_partida = $item['Lugar-Partida'];//$objNomenclador->obtenerNomenclados($item['Latitud'], $item['Longitud'], $item['Matricula']);
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,$item['Matricula'])
			->setCellValue('B'.$i,formatearFecha($item['Fecha-Partida']))
			->setCellValue('C'.$i,formatearDistancia($item['Distancia']))
			->setCellValue('D'.$i,formatearVelocidad($item['VelMax']))
			->setCellValue('E'.$i,formatearFecha($item['Fecha-Llegada']))
			->setCellValue('F'.$i,$lugar_partida);
			
			$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$i)->getHyperlink()->setUrl(strip_tags('http://maps.google.com/maps?q='.$item['Latitud'].','.$item['Longitud']));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('F'.$i)->applyFromArray(array('font' => array('color' => array('rgb' => '0072FF'), 'underline' => 'single')));
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->system->viajes));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->system->viajes)).'_'.date('dmY',strtotime($fecha_desde)).'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function export_mails_enviados_xls($objSQLServer){
	global $solapa;
	global $lang;
	require_once 'clases/clsInformes.php';
	$objInformes = new Informes($objSQLServer);
	
	$fecha_desde = date('Y-m-d',strtotime($_POST['fecha_desde'])).' 00:00:00';	
	$fecha_hasta = date('Y-m-d',strtotime($_POST['fecha_hasta'])).' 23:59:59';
	$arrMoviles = implode(',',$_POST['arrMovil']);
	$arrMailsEnviados = $objInformes->obtenerReporteMailsEnviados($_SESSION['idUsuario'], $arrMoviles, $fecha_desde, $fecha_hasta);
	if(!$arrMailsEnviados){
		$_GET['solapa'] = $solapa;
		$_GET['mensaje'] = $lang->message->sin_resultados;
		index($objSQLServer, $_POST['hidSeccion']);
		exit;
	}
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->system->correos_enviados))
		->setSubject(sanear_string($lang->system->correos_enviados))
		->setDescription(sanear_string($lang->system->correos_enviados))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','N°')
		->setCellValue('B1', $lang->system->movil)
		->setCellValue('C1', $lang->system->fecha)
		->setCellValue('D1', $lang->system->fecha_gps)
		->setCellValue('E1', $lang->system->evento)
		->setCellValue('F1', $lang->system->velocidad)
		->setCellValue('G1', $lang->system->sentido)
		->setCellValue('H1', $lang->system->conductor)
		->setCellValue('I1', $lang->system->email)
		->setCellValue('J1', $lang->system->ubicacion);
		
	$objPHPExcel->setFormatoRows(array('A','B','C','D','E','F','G','H','I','J'));
	$objPHPExcel->alignCenter(array('A','C','D','F','G'));
	
	$i = 2;
	foreach($arrMailsEnviados as $item){
		
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,$item['orden'])
			->setCellValue('B'.$i,encode($item['movil']))
			->setCellValue('C'.$i,formatearFecha($item['fechaEnviado']))
			->setCellValue('D'.$i,formatearFecha($item['fechaGPS']))
			->setCellValue('E'.$i,encode($item['evento']))
			->setCellValue('F'.$i,formatearVelocidad($item['velocidad']))
			->setCellValue('G'.$i,calcularRumbo($item['rumbo']))
			->setCellValue('H'.$i,encode($item['nombreConductor'].' '.$item['apellidoConductor']))
			->setCellValue('I'.$i,$item['mail'])
			->setCellValue('J'.$i,ucwords(strtolower(htmlentities($item['nomenclado']))));
			
			$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$i)->getHyperlink()->setUrl(strip_tags('http://maps.google.com/maps?q='.$item['latitud'].','.$item['longitud']));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('J'.$i)->applyFromArray(array('font' => array('color' => array('rgb' => '0072FF'), 'underline' => 'single')));
		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->system->correos_enviados));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->system->correos_enviados)).'_'.getFechaServer('dmY').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function export_alertas_xls($objSQLServer){
	global $solapa;
	global $lang;
	
	$fecha_desde = date('Y-m-d',strtotime($_POST['fecha_desde'])).' 00:00:00';	
	$fecha_hasta = date('Y-m-d',strtotime($_POST['fecha_hasta'])).' 23:59:59';
	$arrMoviles = implode(',',$_POST['arrMovil']);
	$arrEventos = implode(',',$_POST['arrEvento']);
	$arrAlertas = !empty($_POST['idAlerta'])?(int)$_POST['idAlerta']:NULL;
	
	require_once("clases/clsHistorico.php");
	$objHistorico = new Historico($objSQLServer);
	
	include "clases/clsNomenclador.php";
	$objNomenclador = new Nomenclador($objSQLServer);

	$arrHistorico = $objHistorico->llenarTablaTemporal($fecha_desde, $fecha_hasta, $arrMoviles, $_SESSION['idUsuario'],$arrEventos,$arrAlertas);
	
	if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
		if($arrHistorico == 'Error 408'){
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->tiempo_carga;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
		else{
			$_GET['solapa'] = $solapa;
			$_GET['mensaje'] = $lang->message->sin_resultados;
			index($objSQLServer, $_POST['hidSeccion']);
			exit;
		}
	}
	
	//$tipo = $objMovil->tiposUnidad($idMovil , $objSQLServer);
	$objHistorico->tipoMovil = 'vehiculo';
	$arrHistorico = $objHistorico->agruparHistorico($arrHistorico, $fecha_desde, $fecha_hasta);
	$arrHistorico = $objHistorico->historicoVista($arrHistorico);
	//calcularRumbo2($arrHistorico);
	//$arrHistorico = $objHistorico->historicoDistancia($arrHistorico);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
		
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(sanear_string($lang->system->alertas))
		->setSubject(sanear_string($lang->system->alertas))
		->setDescription(sanear_string($lang->system->alertas))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','N°')
		->setCellValue('B1', $lang->system->movil)
		->setCellValue('C1', $lang->system->fecha)
		->setCellValue('D1', $lang->system->evento);
		//->setCellValue('E1', $lang->system->velocidad)
		//->setCellValue('F1', $lang->system->sentido)
		//->setCellValue('G1', $lang->system->ubicacion);
		//->setCellValue('H1', $lang->system->odometro)
	    // ->setCellValue('I1', $lang->system->conductor);	

	$objPHPExcel->setFormatoRows(array('A','B','C','D'/*,'E','F','G'*/));
	$objPHPExcel->alignCenter(array('A','C'/*,'E','F','H'*/));
						
	$i = 2;
	foreach($arrHistorico as $item){
	
	    $lat = $item['lat'];
        $lng = $item['lon'];
       
		//-- Nomenclatura --//
        /*$geocodificacion = $objNomenclador->obtenerNomenclados($lat, $lng, $item['movil']);
        $nomenclado = "";
		if ($geocodificacion){
            $ubicacion = $geocodificacion;}
        else{
            $ubicacion = "-";
		}
        $nomenclado.= stripcslashes($geocodificacion);*/
        //-- --//

		//-- Nomenclatura Evento --//
		$arrEvento 		= $objNomenclador->obtenerNomencladosGeocercas($item['idHe']);	
		$nomencladoRef 	= htmlentities($arrEvento[0]);
		//-- --//
		//$conductor = (!empty($item['co_apellido']))?($item['co_apellido'].(!empty($item['co_nombre'])?', '.$item['co_nombre']:'')):(!empty($item['co_nombre'])?$item['co_nombre']:'');		
        //$conductor.= (!empty($item['co_ibutton'])?((!empty($conductor))?' - ':'').$item['co_ibutton']:'');
				
		//-- validacion de DataLoger --//
		$diff_hora_recepcion = (strtotime($item['fechaRecibido']) - strtotime(str_replace('/','-',$item['fechaGenerado']))) / 60;
		$diff_min = 10;
		$isDataloger = false;
		if($diff_hora_recepcion > $diff_min && $item['idEvento'] != 71 && $item['idEvento'] != 72){//falta de reporte
			$isDataloger = true;	
		}
		//-- --//
		
		$posCol = 0;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,$item['orden'])
			->setCellValue('B'.$i, $item['movil'])
			->setCellValue('C'.$i, $item['fecha'])
			->setCellValue('D'.$i, trim(($isDataloger?'** ':'').$item['evento_txt'].' '.$nomencladoRef));
			//->setCellValue('E'.$i, formatearVelocidad($item['velocidadGPS'])) 
			//->setCellValue('F'.$i, calcularRumbo($item['curso']))
			//->setCellValue('G'.$i, $nomenclado);
			//->setCellValue('H'.$i, $item['odometro'])
			//->setCellValue('I'.$i, $conductor);	
			
		//$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$i)->getHyperlink()->setUrl(strip_tags('http://maps.google.com/maps?q='.$lat.','.$lng));
		//$objPHPExcel->setActiveSheetIndex(0)->getStyle('H'.$i)->applyFromArray(array('font' => array('color' => array('rgb' => '0072FF'), 'underline' => 'single')));

		$i++;
	}
						
	$objPHPExcel->getActiveSheet()->setTitle(''.sanear_string($lang->system->alertas));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(sanear_string($lang->system->alertas)).'_'.getFechaServer('dmY').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}