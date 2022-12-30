<?php
//$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function listado($objSQLServer, $seccion, $mensaje = "") {
	//$method = (isset($_GET['method'])) ? $_GET['method'] : null;

	if($_GET['action'] == 'changeUser'){
        popupChangeUser($objSQLServer, $_GET['id']);
        exit;
	}
	elseif($_GET['action'] == 'sendmessage'){
        popupSendMessage($objSQLServer, $seccion);
        exit;
    }
    elseif($_GET['action'] == 'viewhistory'){
        popupViewHistory($objSQLServer, $seccion);
        exit;
    }
	
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	
    $txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	
	global $arrEntidades;
	$arrEntidades = obtenerListadoMoviles($objMovil, 'list', $txtFiltro);
	
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}

function popupChangeUser($objSQLServer, $id){ 
	$seccion = 'cuenta_moviles';
	$id = (int)$id;

	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	$response = $objMovil->obtenerMovilesUsuario($_SESSION['idUsuario'], NULL, NULL, $id);
	if($response){
		$nombre = $response[0]['mo_otros'];
	}
	
    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';
    
    //$vista = 'changeuser';
    require("includes/frametemplate.php");
}

function solapaPopup($objSQLServer, $seccion){
	
	$id = isset($_REQUEST['hidId']) ? ((int)$_REQUEST['hidId'] ? intval($_REQUEST['hidId']) : NULL) : NULL;
	$action = isset($_REQUEST['hidAction']) ? ($_REQUEST['hidAction'] ? trim($_REQUEST['hidAction']) : NULL) : NULL;
	$nombre = isset($_REQUEST['nombre']) ? ($_REQUEST['nombre'] ? trim($_REQUEST['nombre']) : NULL) : NULL;
	$status = true;

	if($action == 'popupPostChangeUser'){
		if(!empty($nombre)){
			
			require_once 'clases/clsMoviles.php';
			$objMovil = new Movil($objSQLServer);
			$response = $objMovil->obtenerMovilesUsuario($_SESSION['idUsuario'], NULL, NULL, $id);
			if(isset($response[0])){
				$objSQLServer->dbQueryUpdate(array('mo_otros'=>$nombre), 'tbl_moviles', 'mo_id = '.$id);
				$status = true;
				$message = 'Los datos se procesaron correctamente.';
			}
			else{
				$status = false;
				$message = 'El dispositivo no pertenece a su flota.';
			}
		}
		else{
			$status = false;
			$message = 'Debe completar un nombre';
		}
	}
	
	$seccion = 'cuenta_moviles';
	$popup = true;
	
	global $lang;
	$extraCSS[] = 'css/estilosPopup.css';
	$extraJS[] = 'js/popupHostFunciones.js';
	$extraCSS[] = 'css/popup.css';
		
	require("includes/frametemplate.php");
}

function popupPostChangeUser($objSQLServer, $seccion){
	
}

function solapaModificar($objSQLServer, $seccion, $mensaje = "", $id = 0) {
   	global $solapa;
	global $lang;
    $id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
    
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$mPermitido = 0;
	$arr_moviles = obtenerListadoMoviles($objMovil, 'update');
	foreach($arr_moviles as $item){
		if($item['mo_id'] == $id){
			$mPermitido = 1;
			break;
		}
	}
	$hablitado=validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmMoviles');
   
   	foreach($arrElementos as $k => $item){
		if($item['ig_value'] == 'mo_id_cliente_facturar'){
			$arrElementos[$k]['ic_store'] = 'pa_obtenerClienteCombo 0, 0, '.(int)$_SESSION["idEmpresa"];
		}	
	}
   	
	$arrEntidades = $objMovil->obtenerRegistros($id);	
	
	if(tienePerfil(array(19,37))){
		$equipos = $objMovil->obtenerEquiposCombo($id);
	}
	
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function solapaBaja($objSQLServer, $seccion) {
    global $lang;
	$idMovil = (int)(isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsMoviles.php';
    $objMovil = new Movil($objSQLServer);
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_moviles = obtenerListadoMoviles($objMovil, 'delete');
	
	$mPermitido = 0;
	foreach($arr_moviles as $item){
		if($item['mo_id'] == $idMovil ){
			$mPermitido = 1;
			$idEquipo = $item['un_id'];
			break;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	$msj = "";
	if($idMovil){
		$auxItemBaja = $objMovil->obtenerRegistros($idMovil);
		$auxItemBaja = $auxItemBaja[0];
		
		$resp = $objMovil->eliminarRegistro($idMovil);
		
		require_once 'clases/clsEquipos.php';
    	$objEquipo = new Equipo($objSQLServer);
		$objEquipo->eliminarRegistro($idEquipo);
		
		if($resp){
			$objMovil->generarLog(3,$idMovil,decode($lang->system->baja_vehiculo.' '.$auxItemBaja['mo_matricula']));
			$msj = $lang->message->ok->msj_baja;
		}
		else{
			$msj = $lang->message->error->msj_baja;
		}
	}
	index($objSQLServer, $seccion, $msj);
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
   
   	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	$arrEntidades = obtenerListadoMoviles($objMovil, 'list', $txtFiltro);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle(normaliza($lang->menu->abmMoviles))
		->setSubject(normaliza($lang->menu->abmMoviles))
		->setDescription(normaliza($lang->menu->abmMoviles))
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->identificador)
		->setCellValue('B1',$lang->system->matricula)
		->setCellValue('C1',$lang->system->marca)
		->setCellValue('D1',$lang->system->modelo)
		->setCellValue('E1',$lang->system->tipo_movil)
		->setCellValue('F1',$lang->system->cliente)
		->setCellValue('G1',$lang->system->unidad)
		->setCellValue('H1',$lang->system->ultimo_reporte_recibido);
		
	$arralCol = array('A','B','C','D','E','F','G','H');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('C','D','E','H');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','F','G');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['mo_identificador']))
			->setCellValue('B'.$i, encode($row['mo_matricula']))
			->setCellValue('C'.$i, encode($row['mo_marca']))
			->setCellValue('D'.$i, encode($row['mo_modelo']))
			->setCellValue('E'.$i, $lang->system->$row['tv_nombre']?$lang->system->$row['tv_nombre']->__toString():$row['tv_nombre'])
			->setCellValue('F'.$i, encode($row['cl_razonSocial']))
			->setCellValue('G'.$i, encode($row['un_mostrarComo']))
			->setCellValue('H'.$i, encode($row['sh_fechaRecepcion']));
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.normaliza($lang->menu->abmMoviles));
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',normaliza($lang->menu->abmMoviles))).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function solapaGuardarM($objSQLServer, $seccion) {
    global $lang;
	$idMovil = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
    
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmMoviles');
	
    $mensaje = $set = $coma = "";
    for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
        
		$idCampo = $arrElementos[$i]['ig_idCampo'];
        if ($arrElementos[$i]['ig_validacionExistencia']){$campoValidador = $_POST[$idCampo];}
		$msjError="";
		$msjError = checkAll($arrElementos[$i], $_POST);
		if($msjError){
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		$set.= $coma.$arrElementos[$i]['ig_value'].'='.(!empty($_POST[$idCampo])?"''".trim($_POST[$idCampo]) ."''":'NULL');
		$coma = ', ';
    }
	
	//$set.= $coma.'mo_matricula ='."''".trim($_POST['txtVehiculo']) ."''";
	//$coma = ', ';

	//FIN FRAGMENTO
    if (!$mensaje) {
		require_once 'clases/clsMoviles.php';
        $objMovil = new Movil($objSQLServer);
		$auxInfoActual = $objMovil->obtenerRegistros($idMovil);
		$auxInfoActual = $auxInfoActual[0];
		
		$cod = $objMovil->modificarRegistro($set, $idMovil, $campoValidador);
		$objMovil->asignarMovilAUsuaruariosTransportistas($idMovil, $_POST['cmbClientes']);
		
		if(isset($_POST['equipo_instalado'])){
			$objMovil->asignarEquipo($idMovil, $_POST['equipo_instalado'], $_POST['equipo_viejo']) ;
		}
			
		switch ($cod) {
            case 0:
                $mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
                solapaModificar($objSQLServer, $seccion, $mensaje, $idMovil);
            break;
            case 1:
				//-- Generar Log --//
				$auxInfoUpdate = $objMovil->obtenerRegistros($idMovil);
				$auxInfoUpdate = $auxInfoUpdate[0];
				
				$txtActual = $txtUpdate = $coma = NULL;
				if($auxInfoActual['mo_matricula'] != $auxInfoUpdate['mo_matricula']){
					$txtActual.= $coma.$lang->system->matricula.'['.$auxInfoActual['mo_matricula'].']';	
					$txtUpdate.= $coma.$lang->system->matricula.'['.$auxInfoUpdate['mo_matricula'].']';
					$coma = ', ';	
				}
				if($auxInfoActual['mo_marca'] != $auxInfoUpdate['mo_marca']){
					$txtActual.= $coma.$lang->system->marca.'['.$auxInfoActual['mo_marca'].']';	
					$txtUpdate.= $coma.$lang->system->marca.'['.$auxInfoUpdate['mo_marca'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['mo_modelo'] != $auxInfoUpdate['mo_modelo']){
					$txtActual.= $coma.$lang->system->modelo.'['.$auxInfoActual['mo_modelo'].']';	
					$txtUpdate.= $coma.$lang->system->modelo.'['.$auxInfoUpdate['mo_modelo'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['mo_anio'] != $auxInfoUpdate['mo_anio']){
					$txtActual.= $coma.$lang->system->anio.'['.$auxInfoActual['mo_anio'].']';	
					$txtUpdate.= $coma.$lang->system->anio.'['.$auxInfoUpdate['mo_anio'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['mo_color'] != $auxInfoUpdate['mo_color']){
					$txtActual.= $coma.$lang->system->color.'['.$auxInfoActual['mo_color'].']';	
					$txtUpdate.= $coma.$lang->system->color.'['.$auxInfoUpdate['mo_color'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['mo_id_tipo_movil'] != $auxInfoUpdate['mo_id_tipo_movil']){
					$txtActual.= $coma.$lang->system->tipo_movil.'['.(!empty($lang->system->$auxInfoActual['tv_nombre'])?$lang->system->$auxInfoActual['tv_nombre']:$auxInfoActual['tv_nombre']).']';	
					$txtUpdate.= $coma.$lang->system->tipo_movil.'['.(!empty($lang->system->$auxInfoUpdate['tv_nombre'])?$lang->system->$auxInfoUpdate['tv_nombre']:$auxInfoUpdate['tv_nombre']).']';	
					$coma = ', ';
				}	
				if($auxInfoActual['mo_id_cliente_facturar'] != $auxInfoUpdate['mo_id_cliente_facturar']){
					$txtActual.= $coma.$lang->system->cliente_facturar.'['.$auxInfoActual['cl_razonSocial'].']';	
					$txtUpdate.= $coma.$lang->system->cliente_facturar.'['.$auxInfoUpdate['cl_razonSocial'].']';
					$coma = ', ';	
				}	
				if($auxInfoActual['un_id'] != $auxInfoUpdate['un_id']){
					$txtActual.= $coma.$lang->system->equipo_instalado.'['.$auxInfoActual['un_mostrarComo'].']';	
					$txtUpdate.= $coma.$lang->system->equipo_instalado.'['.$auxInfoUpdate['un_mostrarComo'].']';	
					$coma = ', ';
				}	
				
				if(!empty($txtUpdate)){
					$objMovil->generarLog(3,$idMovil,decode(str_replace('[DATOS_EDITADOS]',$txtUpdate,str_replace('[DATOS_ACTUALES]',$txtActual,$lang->system->edicion_vehiculo))));
				}
				//-- Fin. Log --//
				
                $mensaje = $lang->message->ok->msj_modificar;
		        index($objSQLServer, $seccion, $mensaje);
            break;
            case 2:
                $mensaje = $lang->message->error->msj_modificar;
                solapaModificar($objSQLServer, $seccion, $mensaje, $idMovil);
           	break;
        }
    }
	else{
		solapaModificar($objSQLServer, $seccion, $mensaje, $idMovil);
    }
}

/*
function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}
*/


function obtenerListadoMoviles($objMovil, $tipo, $filtro = NULL){
	$objMovil->allData = true;
	if ($_SESSION['idTipoEmpresa'] == 2){
		$filtro = ($filtro=='getAllReg')?NULL:$filtro;
		$arrEntidades = $objMovil->obtenerMovilesUsuario($_SESSION['idUsuario'], $filtro, 0);
	} 
	else{
        if($tipo == 'update' || $tipo == 'delete'){
			$filtro = 'getAllReg';
		}	
		$arrEntidades = $objMovil->obtenerRegistros(0, $filtro);
    }
	return $arrEntidades;	
}

function popupSendMessage($objSQLServer, $seccion){
	$idusuario = $_SESSION['idUsuario'];
    $idmovil = $_GET['idmovil'];
    $strQuery = "EXEC db_envio_push_notification_avanti {$idusuario},{$idmovil}";
    $results = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));

	$popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';
    $extraJS[] = 'js/cuentaFunciones.js';
    
    $vista = 'sendmessage';
	$seccion = 'cuenta_moviles';
	require("includes/frametemplate.php");
}

function popupViewHistory($objSQLServer, $seccion){
	$idusuario = $_SESSION['idUsuario'];
    $idmovil = $_GET['idmovil'];
    $strQuery = "EXEC db_envio_push_notification_avanti_historial {$idusuario},{$idmovil}";
    $results = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strQuery));

    $popup = true;
	
    global $lang;
    $extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';

	$seccion = 'cuenta_moviles';
    $vista = 'viewhistory';    
    require("includes/frametemplate.php");
}
?>