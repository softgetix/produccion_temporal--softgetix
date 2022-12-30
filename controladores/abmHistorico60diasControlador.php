<?php

$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;

	if($_POST){
		if(!empty($_POST['movil']) && !empty($_POST['fecha'])){
			$dif_dias = (((strtotime(date('Y-m-d')) - strtotime(str_replace('/','-',$_POST['fecha'])))/60)/60)/24;
			if($dif_dias >= 60){
				$strSQL = " SELECT mo.mo_identificador , mo.mo_matricula , un.un_mostrarComo, un.un_id ";
				$strSQL.= " FROM  tbl_unidad un ";
				$strSQL.= " INNER JOIN tbl_unidad_gprs ug ON ug.ug_un_id = un.un_id ";
				$strSQL.= " INNER JOIN tbl_sys_heart sh ON sh.sh_un_id = un.un_id ";
				$strSQL.= " INNER JOIN tbl_dato_gp dp ON dp.dg_sh_id = sh.sh_id ";
				$strSQL.= " LEFT JOIN tbl_moviles mo ON mo.mo_id = un.un_mo_id ";
				$strSQL.= " WHERE mo.mo_matricula LIKE '%".$_POST['movil']."%' 
								OR  mo.mo_otros LIKE '%".$_POST['movil']."%'
								OR mo.mo_identificador LIKE '%".$_POST['movil']."%'
								OR un.un_mostrarComo LIKE '%".$_POST['movil']."%'
								OR ug.ug_identificador LIKE '%".$_POST['movil']."%'
							";
				$result = $objSQLServer->dbQuery($strSQL);
				$arr_moviles = $objSQLServer->dbGetAllRows($result,3);
			}
			else{
				$mensaje = 'La fecha a buscar debe superar los 60 d&iacute;as a partir de hoy.';	
			}
		}
		else{
			$mensaje = 'Ingrese el m&oacute;vil y/o fecha a filtrar';		
		}
	}
	else{
		$fecha = date('d/m/Y', strtotime('-61 day',strtotime(date('Y-m-d'))));	
	}
	require("includes/template.php");
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsIdiomas.php';
	$objIdioma = new Idioma();
	$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
	##-- CONEXION A BASE DE DATOS --##
	require_once("clases/clsSqlServer.php");
	$objSQLServerHistory = new SqlServer();
	$objSQLServerHistory->dirConfig = 'localizart_historico';
	$objSQLServerHistory->dbConnect();
	##-- --##

	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$fecha = date('d-m-Y', strtotime($_POST['fecha_result']));
	$idMovil = (int)$_POST['hidId'];
	$historyID = str_replace('-','',$fecha);
	
	$strSQL = " DECLARE @temp_eventos TABLE (id INT,evento VARCHAR(50))";
	foreach($eventos->children() as $k => $ev){
		$idEv = explode('_',$k);
		if($idEv[1]){
			$strSQL.= " INSERT INTO @temp_eventos VALUES(".(int)$idEv[1].", '".trim($ev)."') ";
		}
	}
	
	$strSQL.= " DECLARE @matricula VARCHAR(50) = NULL; ";
	$strSQL.= " SET @matricula = (SELECT CASE WHEN mo_matricula IS NULL THEN '--' ELSE mo_matricula END +'/'+un_mostrarComo FROM LocalizarT.dbo.tbl_unidad LEFT JOIN LocalizarT.dbo.tbl_moviles ON mo_id = un_mo_id WHERE un_id = ".(int)$idMovil.") ";
	$strSQL.= " SELECT hy_id, @matricula as matricula, hy_fechaGenerado
		, ISNULL(evento,'".$eventos->default->__toString()." ('+CONVERT(VARCHAR,hy.hy_evento)+')') as tr_descripcion 
		, gp.dgh_velocidad, LocalizarT.dbo.geoCodificar (hy_latitud, hy_longitud,0) as nomenclado, hy_latitud, hy_longitud ";
	$strSQL.= " FROM tbl_history_".$historyID." hy ";
	//$strSQL.= " INNER JOIN tbl_dato_gp_historico_".$historyID." gp ON hy.hy_id = gp.dgh_hy_id ";
	$strSQL.= " INNER JOIN tbl_history_dato_gp_".$historyID." gp ON hy.hy_id = gp.dgh_hy_id ";
	$strSQL.= " LEFT JOIN @temp_eventos ON hy.hy_evento = id ";
	$strSQL.= " WHERE hy_un_id = ".(int)$idMovil." ORDER BY hy_fechaGenerado ";
	
	$query  =  $objSQLServerHistory->dbQuery($strSQL);
	$arrEntidades = $objSQLServerHistory->dbGetAllRows($query,3);	
	$objSQLServerHistory->dbDisconnect();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Matricula / Equipo')
					->setCellValue('B1', 'Nomenclado')
					->setCellValue('C1', 'Velocidad')
					->setCellValue('D1', 'Evento')
					->setCellValue('E1', 'Fecha Generado');			
					
	$arralCol = array('A','B','C','D','E');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','C','E');
	$objPHPExcel->alignCenter($alingCenterCol);
	//$alingLeftCol = array();
	//$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	$matricula = '';
	if($arrEntidades){
		foreach($arrEntidades as $row){
			$matricula = empty($matricula)?$row['matricula']:$matricula;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, $row['matricula'])
				->setCellValue('B'.$i, $row['nomenclado'])
				->setCellValue('C'.$i, $row['dgh_velocidad'])
				->setCellValue('D'.$i, $row['tr_descripcion'])
				->setCellValue('E'.$i, date('d-m-Y H:i',strtotime($row['hy_fechaGenerado'])));
			$i++;
		}
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->$seccion);
	$objPHPExcel->setActiveSheetIndex(0);
	$movil = str_replace(' ','-',$matricula).'-'.$fecha;
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.$movil.'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function export_kml($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsIdiomas.php';
	$objIdioma = new Idioma();
	$eventos = $objIdioma->getEventos($_SESSION['idioma']);
	
	##-- CONEXION A BASE DE DATOS --##
	require_once("clases/clsSqlServer.php");
	$objSQLServerHistory = new SqlServer();
	$objSQLServerHistory->dirConfig = 'localizart_historico';
	$objSQLServerHistory->dbConnect();
	##-- --##

	$fecha = date('d-m-Y', strtotime($_POST['fecha_result']));
	$idMovil = (int)$_POST['hidId'];
	$historyID = str_replace('-','',$fecha);
	
	$strSQL = " DECLARE @temp_eventos TABLE (id INT,evento VARCHAR(50))";
	foreach($eventos->children() as $k => $ev){
		$idEv = explode('_',$k);
		if($idEv[1]){
			$strSQL.= " INSERT INTO @temp_eventos VALUES(".(int)$idEv[1].", '".trim($ev)."') ";
		}
	}
	
	$strSQL.= " DECLARE @matricula VARCHAR(50) = NULL; ";
	$strSQL.= " SET @matricula = (SELECT CASE WHEN mo_matricula IS NULL THEN '--' ELSE mo_matricula END +'/'+un_mostrarComo FROM LocalizarT.dbo.tbl_unidad LEFT JOIN LocalizarT.dbo.tbl_moviles ON mo_id = un_mo_id WHERE un_id = ".(int)$idMovil.") ";
	$strSQL.= " SELECT hy_id, @matricula as matricula, hy_fechaGenerado
		, ISNULL(evento,'".$eventos->default->__toString()." ('+CONVERT(VARCHAR,hy.hy_evento)+')') as tr_descripcion 
		, gp.dgh_velocidad, LocalizarT.dbo.geoCodificar (hy_latitud, hy_longitud,0) as nomenclado, hy_latitud, hy_longitud ";
	$strSQL.= " FROM tbl_history_".$historyID." hy ";
	//$strSQL.= " INNER JOIN tbl_dato_gp_historico_".$historyID." gp ON hy.hy_id = gp.dgh_hy_id ";
	$strSQL.= " INNER JOIN tbl_history_dato_gp_".$historyID." gp ON hy.hy_id = gp.dgh_hy_id ";
	$strSQL.= " LEFT JOIN @temp_eventos ON hy.hy_evento = id ";
	$strSQL.= " WHERE hy_un_id = ".(int)$idMovil." ORDER BY hy_fechaGenerado ";

	$query  =  $objSQLServerHistory->dbQuery($strSQL);
	$arrEntidades = $objSQLServerHistory->dbGetAllRows($query,3);	
	$objSQLServerHistory->dbDisconnect();
	
	
	$out = '';
	$out .= '<?xml version="1.0" encoding="UTF-8"?>';
	$out .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
	$out .= '<Document>';
		
	$matricula = '';	
	if($arrEntidades){
		foreach($arrEntidades as $row){
			$matricula = empty($matricula)?$row['matricula']:$matricula;
			$row['nomenclado'] = str_replace('', '', $row['nomenclado']);
				
			$contenido = "<table>";
			$contenido .= "<tr><td wdith='400px'>Matricula / Equipo:</td><td> ".$row['matricula']."</td></tr>";
			$contenido .= "<tr><td>Ubicacion:</td><td> ".$row['nomenclado']."</td></tr>";
			$contenido .= "<tr><td>Velocidad:</td><td> ".$row['dgh_velocidad']."</td></tr>";
	   		$contenido .= "<tr><td>Evento:</td><td> ".$row['tr_descripcion']."</td></tr>";
			$contenido .= "<tr><td>Fecha:</td><td> ".date('d-m-Y H:i',strtotime($row['hy_fechaGenerado']))."</td></tr>";
			$contenido.= "</table>";
				
			$arrNom = explode(',',$row['nomenclado']);
				
			$out .= '	<Placemark>';
			//$out .= '		<name>';
			//$out .= $arrNom[0];
			//$out .= '		</name>';
			
			$out .= '		<description>';
			$out .= $contenido;
			$out .= '		</description>';
			
			$out .= '		<Point>';
			$out .= '			<coordinates>';
			$out .= $row['hy_longitud'].','.$row['hy_latitud'];
			$out .= '			</coordinates>';
			$out .= '		</Point>';
			$out .= '	</Placemark>';
		}
	}
		
	$out .= '</Document>';
	$out .= '</kml>';
		
	$movil = str_replace(' ','-',$matricula).'-'.$fecha;
	header("Content-disposition: inline; filename=".strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.$movil.'.kml');
	header("Content-Type: application/vnd.google-earth.kml+xml kml; charset=utf8");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . strlen($out));
	echo $out;
}
