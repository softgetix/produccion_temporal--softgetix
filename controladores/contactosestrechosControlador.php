<?php
function index($objSQLServer, $seccion, $mensaje = "") {
	global $lang;


	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	
	$result = $objMovil->obtenerMovilesUsuario($_SESSION['idUsuario'], $filtro, 0);
	
	require("includes/template.php");
}

function exportar_xls($objSQLServer, $seccion){
	global $lang;
	
	if(!$_POST['hidId']){
		index($objSQLServer, $seccion);
		exit;
	} //un_mostrarComo

	$movil = escapear_string($_POST['hidId']);
	$dias = date('Y-m-d',strtotime('-14 days',strtotime(date('Y-m-d'))));

	$query = " EXEC db_ble_list '{$movil}', '{$dias}', {$_SESSION['idUsuario']}";
	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);

	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->contactosestrechos)
		->setSubject($lang->menu->contactosestrechos)
		->setDescription($lang->menu->contactosestrechos)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','TELEFONO')
		->setCellValue('B1','TELEFONO EN CERCANIA')
		->setCellValue('C1','FECHA EN LA QUE SE ROMPIO EL DISTANCIAMIENTO SOCIAL')
		->setCellValue('D1','DURACION')
		->setCellValue('E1','DURACION (Minutos)');
		
	$arralCol = array('A','B','C','D','E');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','B','C');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('D','E');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	if($result){
	foreach($result as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $row['id'],PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit('B'.$i, encode($row['phone']),PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('C'.$i, formatearFecha($row['contact']))
			->setCellValue('D'.$i, encode($row['distance']))
			->setCellValue('E'.$i, encode($row['Minutes']));
		$i++;	
	}}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->contactosestrechos);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->contactosestrechos).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}
?>
