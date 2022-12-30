<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

//$filtro['id_agente'] = 316;//ID de usuario max administracion
$filtro['id_distribuidor'] = $_SESSION['idEmpresa']; // ID de la empresa q es agente.
$filtro['excluir_clientes'] = array(181); 	
	
function index($objSQLServer, $seccion){
	global $filtro;
	
	include('clases/clsEstadisticas.php');
	$objStats = new Estadisticas($objSQLServer);
	
	//-- Cant. de Clientes --//
	$stats['clientes'] = $objStats->getClientes($filtro, true);
	
	
	//-- Cant. de Licencias Vendidas --//
	$stats['licencias_vendidas'] = $objStats->getLicenciasVendidas($filtro, true);
	$stats['licencias_vendidas_prom'] = round($stats['licencias_vendidas']/$stats['clientes'],2).' L/C';
	
	//-- Cant. de Licencias Activas --//
	$stats['licencias_activas'] = $objStats->getLicenciasActivas($filtro, true);
	$stats['licencias_activas_prom'] = round((($stats['licencias_activas']/$stats['licencias_vendidas'])*100),2).'%';
	
	//-- Cant. de Licencias Inactivas --//
	$stats['licencias_inactivas'] = $stats['licencias_vendidas'] - $stats['licencias_activas'];
	$stats['licencias_inactivas_prom'] = round(100 - $stats['licencias_activas_prom'],2).'%';
	
	//-- Cant. de Licencias con Panicos Probados --//
	$stats['panicos_probados'] = $objStats->getPanicosProbados($filtro, true);
	$stats['panicos_probados_prom'] = round((($stats['panicos_probados']/$stats['licencias_vendidas'])*100),2).'%';
	
	//-- Cant. de Clientes --//
	$stats['moviles_sin_reportar'] = $objStats->getMovilesSinReportar($filtro, true);
	
	$operacion = 'listar';
	require("includes/template.php");
}


function export_xls($objSQLServer, $seccion){ 
	global $filtro;
	global $lang;
	
	include('clases/clsEstadisticas.php');
	$objStats = new Estadisticas($objSQLServer);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Localizar-t")->setLastModifiedBy("Localizar-t");
	
	switch($_POST['hidId']){
		case 'clientes':
			$title = $lang->system->title_clientes;
			$arrStats = $objStats->getClientes($filtro, false);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->licencias_adquiridas)
				->setCellValue('D1', $lang->system->fecha_alta);
			
			$arralCol = array('A','B','C','D');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('C','D');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, encode($row['cl_razonSocial']))
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['us_cant_licencias'])
					->setCellValue('D'.$i, !empty($row['cl_fechaAlta'])?date('d-m-Y', strtotime($row['cl_fechaAlta'])):'');
				$i++;
			}
		break;
		case 'licencias_vendidas':
			$title = $lang->system->title_licencias_vendidas;
			$arrStats = $objStats->getLicenciasVendidas($filtro, false);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->cant_licencias)
				->setCellValue('D1', $lang->system->fecha_alta);
			
			$arralCol = array('A','B','C','D');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('C','D');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, encode($row['cl_razonSocial']))
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['us_cant_licencias'])
					->setCellValue('D'.$i, !empty($row['cl_fechaAlta'])?date('d-m-Y', strtotime($row['cl_fechaAlta'])):'');
				$i++;
			}
		break;
		case 'licencias_activas':
			$title = $lang->system->title_licencias_activas;
			$arrStats = $objStats->getLicenciasActivas($filtro, false);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->nro_celular)
				->setCellValue('D1', $lang->system->nombre_movil)
				->setCellValue('E1', $lang->system->sistema_operativo)
				->setCellValue('F1', $lang->system->fecha_alta);
			
			$arralCol = array('A','B','C','D','E','F');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('E','F');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, encode($row['cl_razonSocial']))
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['cl_telefono'])
					->setCellValue('D'.$i, encode($row['mo_matricula']))
					->setCellValue('E'.$i, $row['mo_marca'])
					->setCellValue('F'.$i, date('d-m-Y',strtotime($row['mo_fecha_creacion'])));
				$i++;
			}
		break;
		case 'licencias_inactivas':
			$title = $lang->system->title_licencias_inactivas;
			$arrStats = $objStats->getLicenciasInactivas($filtro);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->licencias_adquiridas)
				->setCellValue('D1', $lang->system->cant_licencias_activas)
				->setCellValue('E1', $lang->system->fecha_alta);
			
			$arralCol = array('A','B','C','D','E');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('C','D','E');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, encode($row['cl_razonSocial']))
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['us_cant_licencias'])
					->setCellValue('D'.$i, (int)$row['lic_activas'])
					->setCellValue('E'.$i, !empty($row['cl_fechaAlta'])?date('d-m-Y', strtotime($row['cl_fechaAlta'])):'');
				$i++;
			}
		break;
		case 'panicos_probados':
			$title = $lang->system->title_licencias_panicos_probados;
			$arrStats = $objStats->getPanicosProbados($filtro, false);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->nro_celular)
				->setCellValue('D1', $lang->system->nombre_movil)
				->setCellValue('E1', $lang->system->sistema_operativo)
				->setCellValue('F1', $lang->system->fecha_alta)
				->setCellValue('G1', $lang->system->fecha_recepcion_panico)
				->setCellValue('H1', $lang->system->ubicacion_panico);
				
			$arralCol = array('A','B','C','D','E','F','G','H');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('E','F','G');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $row['cl_razonSocial'])
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['cl_telefono'])
					->setCellValue('D'.$i, encode($row['mo_matricula']))
					->setCellValue('E'.$i, $row['mo_marca'])
					->setCellValue('F'.$i, date('d-m-Y H:i',strtotime($row['us_fechaCreado'])))
					->setCellValue('G'.$i, date('d-m-Y H:i',strtotime($row['hp_fecha_recepcion_panico'])))
					->setCellValue('H'.$i, encode($row['re_ubicacion']));
				$i++;
			}
		break;
		case 'moviles_sin_reportar':
			$title = $lang->system->title_moviles_sin_reportar;
			$arrStats = $objStats->getMovilesSinReportar($filtro, false);
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $lang->system->nro_entidad)
				->setCellValue('B1', $lang->system->email)
				->setCellValue('C1', $lang->system->nro_celular)
				->setCellValue('D1', $lang->system->nombre_movil)
				->setCellValue('E1', $lang->system->sistema_operativo)
				->setCellValue('F1', $lang->system->fecha_alta)
				->setCellValue('G1', $lang->system->fecha_ultimo_reporte)
				->setCellValue('H1', $lang->system->cant_dias_sin_reportar);
				
			$arralCol = array('A','B','C','D','E','F','G','H');
			$objPHPExcel->setFormatoRows($arralCol);
			
			$alingCenterCol = array('E','F','G','H');
			$objPHPExcel->alignCenter($alingCenterCol);
						
			$i = 2;
			foreach($arrStats as $row){
				$dias = floor(abs((strtotime(date('Y-m-d')) - strtotime($row['sh_fechaGeneracion']))/86400));
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $row['cl_razonSocial'])
					->setCellValue('B'.$i, $row['cl_email'])
					->setCellValue('C'.$i, $row['cl_telefono'])
					->setCellValue('D'.$i, encode($row['mo_matricula']))
					->setCellValue('E'.$i, $row['mo_marca'])
					->setCellValue('F'.$i, date('d-m-Y H:i',strtotime($row['us_fechaCreado'])))
					->setCellValue('G'.$i, date('d-m-Y H:i',strtotime($row['sh_fechaGeneracion'])))
					->setCellValue('H'.$i, $dias?$dias:1);
				$i++;
			}
		break;
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(' '.$title);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.str_replace(' ','',$title).'-'.date('d').date('m').date('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
