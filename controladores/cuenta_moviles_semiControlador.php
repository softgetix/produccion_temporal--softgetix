<?php

//$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function listado($objSQLServer, $seccion, $mensaje = "") {
	//$method = (isset($_GET['method'])) ? $_GET['method'] : null;
	
	if($_GET['action'] == 'newConfig'){
        popupNewConfig($objSQLServer, $seccion);
        exit;
	}
	
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	/*$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}*/

	global $arrEntidades;
	$arrEntidades = obtenerListado($filtro);
	
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}

function solapaPopup($objSQLServer, $seccion){
	
	$id = isset($_REQUEST['hidId']) ? ((int)$_REQUEST['hidId'] ? intval($_REQUEST['hidId']) : NULL) : NULL;
	$action = isset($_REQUEST['hidAction']) ? ($_REQUEST['hidAction'] ? trim($_REQUEST['hidAction']) : NULL) : NULL;
	$nombre = isset($_REQUEST['nombre']) ? ($_REQUEST['nombre'] ? trim($_REQUEST['nombre']) : NULL) : NULL;
	$status = true;

	if($action == 'popupPostNewConfig'){
		popupPostNewConfig($objSQLServer, $seccion);
		exit;
	}

	$seccion = 'cuenta_moviles_semi';
	$popup = true;
	
	global $lang;
	$extraCSS[] = 'css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	$extraCSS[] = 'css/popup.css';
		
	require("includes/frametemplate.php");
}

function popupNewConfig($objSQLServer, $aux_status = NULL, $aux_msg = NULL){
	
	$strSQL = "EXEC pa_obtenerClienteCombo 0, 0, {$_SESSION['idEmpresa']}";
	$cboTransportista = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$strSQL = "EXEC db_ws_trip_configuration_available {$_SESSION['idUsuario']}, 0";
	$cboConfigracion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$strSQL = "EXEC db_ws_trip_load_available {$_SESSION['idUsuario']}, 0";
	$cboCargaBruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$strSQL = "EXEC db_ws_trip_tara_available {$_SESSION['idUsuario']}, 0";
	$cboTara = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	if(!empty($aux_msg)){
		$message = $aux_msg;
		$status = $aux_status;
	}

	$seccion = 'cuenta_moviles_semi';
	$popup = true;
	global $lang;
	$extraCSS[] = 'css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	$extraCSS[] = 'css/popup.css';

	$action = 'popupPostNewConfig';
    $vista = 'newconfig';
	require("includes/frametemplate.php");
}

function popupPostNewConfig($objSQLServer){

	$transportista = (int)$_POST['transportista'];
	$tractor = trim($_POST['tractor']);
	$semi = trim($_POST['semi']);
	$configuracion = (int)$_POST['configuracion'];
	$carga_bruta = (int)$_POST['carga_bruta'];
	$tara = (int)$_POST['tara'];

	$status = false;

	if(empty($tractor) || empty($semi) || empty($configuracion) || empty($carga_bruta) || empty($transportista) || empty($tara)){
		$status = false;
		$message = 'Todos los campos son obligatorios.';
	}
	else{
		$exec = "EXEC db_ws_new_configuration '{$tractor}', '{$semi}', {$configuracion}, {$carga_bruta}, {$transportista}, {$tara}, {$_SESSION['idUsuario']}";
		$result = $objSQLServer->dbGetRow($objSQLServer->dbQuery($exec), 0, 2);
		if($result[0]){
			$status = true;
			$message = 'Los datos se registraron con éxito.';
		}
		else{
			$status = false;
			$message = 'Los datos no puedieron ser registrados. Por favor verifique los datos ingresados.';
		}
	}

	popupNewConfig($objSQLServer, $status, $message);
}

function solapaModificar($objSQLServer, $seccion, $mensaje = "", $id = 0) {
   	global $solapa;
	global $lang;
    $id = (int)(isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	
	$query = " SELECT ms.*, tr.mo_id_cliente_facturar
		FROM tbl_moviles_semi ms  WITH(NOLOCK)
		INNER JOIN tbl_moviles tr  WITH(NOLOCK) ON ms_mo_id = tr.mo_id  ";
	$query.= " WHERE ms_id = {$id}";
	$arrEntidades = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query, false), 0, 3);
	
	$strSQL = "EXEC pa_obtenerClienteCombo 0, 0, {$_SESSION['idEmpresa']}";
	$cboTransportista = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);
	
	$strSQL = "EXEC db_ws_trip_vehicles_available {$_SESSION['idUsuario']}";
	$cboTractor = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);
	
	$strSQL = "EXEC db_ws_trip_second_vehicles_available {$_SESSION['idUsuario']}, 0";
	$cboSemi = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);
	
	$strSQL = "EXEC db_ws_trip_configuration_available {$_SESSION['idUsuario']}, 0";
	$cboConfigracion = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$strSQL = "EXEC db_ws_trip_load_available {$_SESSION['idUsuario']}, 0";
	$cboCargaBruta = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$strSQL = "EXEC db_ws_trip_tara_available {$_SESSION['idUsuario']}, 0";
	$cboTara = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 3);

	$operacion = 'modificar';
   	$tipoBotonera='AM';
	require("includes/template.php");
}

function solapaBaja($objSQLServer, $seccion) {
    global $lang;
	$id = (int)(isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	if($objSQLServer->dbQueryUpdate(array('ms_borrado' => 1), 'tbl_moviles_semi', 'ms_id = '.$id)){
		$mensaje = $lang->message->ok->msj_baja;
	}
	else{
		$mensaje = $lang->message->error->msj_baja;
	}
	index($objSQLServer, $seccion, $mensaje);
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$arrEntidades = obtenerListado($txtFiltro);

	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(normaliza('Configuración Tractor-Semi'))
		->setSubject(normaliza('Configuración Tractor-Semi'))
		->setDescription(normaliza('Configuración Tractor-Semi'))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->transportista)	
		->setCellValue('B1','Tractor')
		->setCellValue('C1','Semi')
		->setCellValue('D1','Configuración')
		->setCellValue('E1','Carga bruta')
		->setCellValue('F1','Tara');
		
	$arralCol = array('A','B','C','D','E','F');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('B','C');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','D','E','F');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['transportista']))
			->setCellValue('B'.$i, encode($row['tractor']))
			->setCellValue('C'.$i, encode($row['semi']))
			->setCellValue('D'.$i, encode($row['configuracion']))
			->setCellValue('E'.$i, encode($row['carga_bruta']))
			->setCellValue('F'.$i, encode($row['tara']));
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.normaliza('Configuración Tractor-Semi'));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',normaliza('Configuración Tractor-Semi'))).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function solapaGuardarM($objSQLServer, $seccion) {
    global $lang;
	$id = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	$transportista = (int)$_POST['transportista'];
	$tractor = (int)$_POST['tractor'];
	$semi = (int)$_POST['semi'];
	$configuracion = (int)$_POST['configuracion'];
	$carga_bruta = (int)$_POST['carga_bruta'];
	$tara = (int)$_POST['tara'];
	

	if(empty($tractor) || empty($semi) || empty($configuracion) || empty($carga_bruta)){
		$mensaje.="* ".$msjError."<br/> ";
		solapaModificar($objSQLServer, $seccion, $mensaje, $id);
	}
	else{
		$params = array(
			'ms_mo_id' => $tractor
			,'ms_semi_mo_id' => $semi
			,'ms_vc_id' => $configuracion
			,'ms_vcb_id' => $carga_bruta
			,'ms_vt_id' => $tara
		);
		
		if($objSQLServer->dbQueryUpdate($params, 'tbl_moviles_semi', 'ms_id = '.$id)){

			$objSQLServer->dbQueryUpdate(array('mo_id_cliente_facturar' => $transportista), 'tbl_moviles', 'mo_id = '.$tractor);
			$objSQLServer->dbQueryUpdate(array('mo_id_cliente_facturar' => $transportista), 'tbl_moviles', 'mo_id = '.$semi);

			$mensaje = $lang->message->ok->msj_modificar;
		    index($objSQLServer, $seccion, $mensaje);
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
            solapaModificar($objSQLServer, $seccion, $mensaje, $id);
		}
	}
}

/*
function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}
*/

function obtenerListado($filtro){
	global $objSQLServer;

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);

	$vistaMovil = $objMovil->getVistaMoviles($_SESSION['idUsuario']);	

	$query = " SELECT ms.*, tr.mo_{$vistaMovil} as tractor, se.mo_{$vistaMovil} as semi, vc.vc_descripcion as configuracion, vcb.vcb_descripcion as carga_bruta
			, cl_razonSocial as transportista, vt_descripcion as tara
		FROM tbl_moviles_semi ms  WITH(NOLOCK)
		INNER JOIN tbl_usuarios WITH (NOLOCK) ON us_id = ms_us_id
		INNER JOIN tbl_moviles tr  WITH(NOLOCK) ON ms_mo_id = tr.mo_id
		LEFT JOIN tbl_moviles se  WITH(NOLOCK) ON ms_semi_mo_id = se.mo_id
		LEFT JOIN tbl_viajes_configuracion vc  WITH(NOLOCK) ON ms_vc_id = vc.vc_id
		LEFT JOIN tbl_viajes_carga_Bruta vcb  WITH(NOLOCK) ON ms_vcb_id = vcb.vcb_id 
		LEFT JOIN tbl_clientes WITH(NOLOCK) ON cl_id = tr.mo_id_cliente_facturar
		LEFT JOIN tbl_viajes_tara WITH(NOLOCK) ON vt_id = ms_vt_id
		WHERE ms_borrado = 0 and cl_id = case when {$_SESSION['idEmpresa']}  = 4835 then  cl_id else {$_SESSION['idEmpresa']} end  ";

	if(!empty($filtro)){
		$query.= " AND (tr.mo_{$vistaMovil} LIKE '%{$filtro}%' OR se.mo_{$vistaMovil} LIKE '%{$filtro}%') ";
	}	

	$query.= "ORDER BY tr.mo_{$vistaMovil}, se.mo_{$vistaMovil}";	

	return $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($query, false), 3);
}
?>