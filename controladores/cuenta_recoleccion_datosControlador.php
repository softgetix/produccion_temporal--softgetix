<?php
//$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function listado($objSQLServer, $seccion, $mensaje = "") {
	//$method = (isset($_GET['method'])) ? $_GET['method'] : null;

	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	global $arrEntidades;
	$query = " EXEC db_ble_shared_data_last_14_days {$_SESSION['idUsuario']}";
	$arrEntidades = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}


function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$query = " EXEC db_ble_shared_data_last_14_days {$_SESSION['idUsuario']}";
	$arrEntidades = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(normaliza('Recolección de datos'))
		->setSubject(normaliza('Recolección de datos'))
		->setDescription(normaliza('Recolección de datos'))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->movil)
		->setCellValue('B1','Fecha de subida')
		->setCellValue('C1','Distanciamientos registrados')
		->setCellValue('D1','Bluetooth (al recibir los datos)')
		->setCellValue('E1','Estado de la App (al recibir los datos)')
		->setCellValue('F1','Ubicación (al recibir los datos)')
		->setCellValue('G1','Tecnología Low Energy');
		
	$arralCol = array('A','B','C','D','E','F','G');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('B','C','D','E','F','G');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$auxarr = array(0 => 'Apagado', 1 => 'Prendido', 2 => 'No disponible');
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, encode($row['device']),PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i, formatearFecha($row['date']))
			->setCellValue('C'.$i, $row['quantity'])
			->setCellValue('D'.$i, isset($auxarr[$row['bluetooth']]) ? $auxarr[$row['bluetooth']] : $auxarr[2])
			->setCellValue('E'.$i, isset($auxarr[$row['app_status']]) ? $auxarr[$row['app_status']] : $auxarr[2])
			->setCellValue('F'.$i, isset($auxarr[$row['ubicacion']]) ? $auxarr[$row['ubicacion']] : $auxarr[2])
			->setCellValue('G'.$i, isset($auxarr[$row['LowEnergy']]) ? $auxarr[$row['LowEnergy']] : $auxarr[2]);
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.normaliza('Recolección de datos'));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',normaliza('recoleccion_de_datos'))).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}