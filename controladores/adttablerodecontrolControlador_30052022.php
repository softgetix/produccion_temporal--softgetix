<?php

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	$_GET['action'] = isset($_REQUEST['action'])?$_REQUEST['action'] : $popup;




	require_once 'clases/ADT.php';
	$objADT = new ADT($objSQLServer);

	$tables = $objADT->getContenidoTableroControl();

	require("includes/template.php");
}

function exportar_xls($objSQLServer, $seccion){
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();

	
	require_once 'clases/ADT.php';
	$objADT = new ADT($objSQLServer);



	$tables = $objADT->getContenidoTableroControl(1);
	if($tables){

		$objPHPExcel->getProperties()
			->setCreator("Localizar-t")
			->setLastModifiedBy("Localizar-t")
			->setTitle('ADT')
			->setSubject('ADT')
			->setDescription('Tablero de Control')
			->setKeywords("Excel Office 2007 openxml php")
			->setCategory("Localizar-t");
		
		foreach($tables as $keyTable =>  $table){
			
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($keyTable);

			//--Titulo de cada columna
			$k = 0;
			foreach($table[0] as $key => $item){
				$titleTab = ($k==0)?$key:$titleTab;
				$ColumLetter = $objADT->getColumLetter($k);
				$objPHPExcel->getActiveSheet()->setCellValue($ColumLetter.'1', encode($key));
				$objPHPExcel->setFormatoRows(array($ColumLetter));
				$objPHPExcel->alignCenter(array($ColumLetter.'1'));
				$k++;
			}
			//--//	

			//--Contenido de cada columna
			$i = 2;
			foreach($table as $row){
				$k = 0;
				foreach($row as $item){
					//$objPHPExcel->getActiveSheet()->setCellValueExplicit($objADT->getColumLetter($k).$i, encode($item),PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($objADT->getColumLetter($k).$i, encode($item));
					$k++;
					//$objPHPExcel->alignLeft($alingLeftCol);
				}
				$i++;
			}
			//--//	
			
			$titleTab = str_replace('>>','',$titleTab);
			$titleTab = str_replace('/','',$titleTab);
			$titleTab = substr($titleTab,0,30);
			$objPHPExcel->getActiveSheet()->setTitle(trim(encode($titleTab)));

		}
		
		$objPHPExcel->setActiveSheetIndex(0);

		if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
		header('Content-Type: application/force-download');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="adt-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
		header('Cache-Control: max-age=0');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		header('Pragma: private');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}




}

?>