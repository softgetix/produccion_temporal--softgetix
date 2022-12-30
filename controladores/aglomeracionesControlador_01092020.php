<?php
function index($objSQLServer, $seccion, $mensaje = "") {
	global $lang;

	if(!$_POST){
		$_POST['diasFiltro'] = 14;
	}

	$buscar = isset($_POST['txtFiltro']) ? !empty($_POST['txtFiltro']) ? trim($_POST['txtFiltro']) : 'NULL' : 'NULL'; 
	$dias = isset($_POST['diasFiltro']) ? intval($_POST['diasFiltro']) : NULL; 
	

	if ($dias == 1){
		$dias = $dias ? "'".date('Y-m-d')."'" : 'NULL';
	}
	else{
		$dias = $dias ? "'".date('Y-m-d',strtotime('-'.$dias.' days',strtotime(date('Y-m-d'))))."'" : 'NULL';
	}

	$query = " EXEC db_ble_crowded_list {$dias}, {$_SESSION['idUsuario']}";
	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);

	if($result){
		$grafico1 = array();
		for ($i = 0; $i <= 23; $i++) {
			$key = date('hA',strtotime($i.':00:00'));
			$grafico1[$key] = 0;
		}
		
		foreach($result as $item){
			$key = date('hA',strtotime($item['contact']));
			$grafico1[$key] = $grafico1[$key] + 1;
		}

		if(count($grafico1) > 0){
			$data1 = "['Hora','Cant']";
			foreach($grafico1 as $k => $item){
				$data1.= ",['".encode($k)."',".(int)$item."]";
			}
		}
		
		$extraJS[] = 'js/draws.js';
	}

	require("includes/template.php");
}

function exportar_xls($objSQLServer, $seccion){
	global $lang;
	
	$buscar = isset($_POST['txtFiltro']) ? !empty($_POST['txtFiltro']) ? trim($_POST['txtFiltro']) : 'NULL' : 'NULL'; 
	$dias = isset($_POST['diasFiltro']) ? intval($_POST['diasFiltro']) : NULL; 
	if ($dias == 1)	{
		$dias = $dias ? "'".date('Y-m-d')."'" : 'NULL';
	}
	else{
		$dias = $dias ? "'".date('Y-m-d',strtotime('-'.$dias.' days',strtotime(date('Y-m-d'))))."'" : 'NULL';
	}

	$query = " EXEC db_ble_crowded_list {$dias}, {$_SESSION['idUsuario']}";
	$result = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query),3);

	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->exposiciones)
		->setSubject($lang->menu->exposiciones)
		->setDescription($lang->menu->exposiciones)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','TELEFONO')
		->setCellValue('B1','TELEFONOS EN CERCANIA')
		->setCellValue('C1','FECHA EN LA QUE SE ROMPIO EL DISTANCIAMIENTO SOCIAL')
		->setCellValue('D1','DURACION')
		->setCellValue('E1','DURACION (Minutos)');
		
	$arralCol = array('A','B','C','D','E');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('A','B');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('C','D','E');
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
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->exposiciones);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->exposiciones).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}
?>
