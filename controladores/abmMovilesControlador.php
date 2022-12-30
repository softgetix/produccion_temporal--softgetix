<?php

$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";
$method = (isset($_GET['method'])) ? $_GET['method'] : null;
if ($method == "ajax") {
	guardarM($objSQLServer, $seccion, true);
    die();
}

function index($objSQLServer, $seccion, $mensaje = "") {
	$method = (isset($_GET['method'])) ? $_GET['method'] : null;
	$filtro = (isset($_POST["hidFiltro"])) ? $_POST["hidFiltro"] : "";
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	
    $txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	$arrEntidades = obtenerListadoMoviles($objMovil, 'list', $txtFiltro);
	$cantRegistros = count($arrEntidades);
	
	
	$popup = isset($_GET["action"]) ? $_GET["action"] : "";
    if($popup == "popup") {
        $idMovil = isset($_GET["idM"]) ? $_GET["idM"] : 0;
        if ($idMovil) {
            modificar($objSQLServer, $seccion, "", $idMovil, true);
        }
    } 
	else{
		$operacion = 'listar';
    	$tipoBotonera = 'LI';
        require("includes/template.php");
    }
}

function alta($objSQLServer, $seccion, $mensaje = "", $popup = false) {
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	require_once 'clases/clsMoviles.php';
    $objMovil = new Movil($objSQLServer);
	$equipos = $objMovil->obtenerEquiposCombo();
 	
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	$operacion = 'alta';
	$tipoBotonera = 'AM';
    if (!$popup) {
        require("includes/template.php");
    }
}

function modificar($objSQLServer, $seccion = "", $mensaje = "", $idMovil = 0, $popup = false) {
   	global $lang;
    $id = (isset($_POST["chkId"])) ? $_POST["chkId"][0] : (($idMovil) ? $idMovil : 0);
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
    
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$mPermitido = 0;
	$arr_moviles = obtenerListadoMoviles($objMovil, 'update');
	foreach($arr_moviles as $item){
		if($item['mo_id'] == $id){
			$mPermitido = 1;
		}
	}
	$hablitado=validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
	$arrEntidades = $objMovil->obtenerRegistros($id);	
	
	$patch_privada = 'imagenes\moviles\\' . $arrEntidades[0]['mo_id'] . '.' . $arrEntidades[0]['mo_imagen'];
    $patch_publica = 'imagenes\moviles\\' . $arrEntidades[0]['mo_id'] . '.' . $arrEntidades[0]['mo_imagen'];
    $imagen_exists = file_exists($patch_privada);
	
	$equipos = $objMovil->obtenerEquiposCombo($id);
	
	$extraCSS[]='css/estilosPopup.css';
	$extraJS[] ='js/popupHostFunciones.js';
	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    if (!$popup) {
        require("includes/template.php");
    } else {
		$extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
        $popup = true;
        require("includes/frametemplate.php");
    }
}

function baja($objSQLServer, $seccion) {
    global $lang;
	
    //ELIMINA UNO O VARIOS REGISTROS DE LA TABLA CORRESPONDIENTE
    require_once 'clases/clsMoviles.php';
    $objMovil = new Movil($objSQLServer);
	$arrCheks = ($_POST["chkId"]) ? $_POST["chkId"] : 0;
    
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_moviles = obtenerListadoMoviles($objMovil, 'delete');
	
	$arrDeletes = array();
	foreach($arrCheks as $itemCheck){
		$mPermitido = 0;
		foreach($arr_moviles as $item){
			if($item['mo_id'] == $itemCheck){
				array_push($arrDeletes,array('mo_id' =>$item['mo_id'],'un_id' =>$item['un_id']));
				$mPermitido = 1;
			}
		}
		if($mPermitido == 0){
			break;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	
	/*
    $idMovils = "";
    for ($i = 0; $i < count($arrCheks) && $arrCheks; $i++) {
        if ($i + 1 == count($arrCheks))
            $idMovils.=$arrCheks[$i];
        else
            $idMovils.=$arrCheks[$i] . ",";
    }
	
	if($idMovils){
		$msj = "";
		$error = false;
		$err['usuarios'] = array();
		$err['alertas'] = array();
		for ($i=0; $i<count($arrCheks); $i++) {		
			$err_movil = false;
			
			// Verificar si el movil a eliminar tiene Usuarios asignados 
			$sql = " SELECT DISTINCT(us_nombre+' '+us_apellido+' ('+us_nombreUsuario+')') as usuario,  mo_matricula";
			$sql.= " FROM tbl_usuarios_moviles  ";
			$sql.= " INNER JOIN tbl_moviles ON um_mo_id = mo_id  ";
			$sql.= " INNER JOIN tbl_usuarios ON us_id = um_us_id ";
			$sql.= " WHERE mo_id = ".(int)$arrCheks[$i]." AND us_borrado = 0 ";
			$objMoviles = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objMoviles, 3);
			if($arrObjRow) {	
				$err_movil = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['usuarios'][$objRow['mo_matricula']].= $coma.$objRow['usuario']; 
					$coma = ", ";
				}
			} 
			
			// Verificar si el movil a eliminar tiene Alertas 
			$sql = " SELECT DISTINCT(al_nombre) as alerta, mo_id, mo_matricula  ";
			$sql.= " FROM tbl_alertas  ";
			$sql.= " INNER JOIN tbl_alertas_moviles ON al_id = am_mo_id  ";
			$sql.= " INNER JOIN tbl_moviles ON mo_id = am_mo_id ";
			$sql.= " WHERE mo_id = ".(int)$arrCheks[$i]." AND al_borrado = 0 ";
			$objAlertas = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objAlertas, 3);
			if($arrObjRow) {	
				$err_movil = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['alertas'][$objRow['mo_matricula']].= $coma.$objRow['alerta']; 
					$coma = ", ";
				}
			}
			
			
			if($err_movil == false){
				$objMovil->eliminarRegistro($arrCheks[$i]);
			}
		}
		
		if(count($err['usuarios']) > 0){
			$msj.= "<strong>Asignacion Moviles usuarios</strong>";
			foreach($err['usuarios'] as $k => $item){
				$msj.= "<br>- Matricula: <i>".$k."</i>, asociado a: <i>".$item."</i>";	
			}
			$error = true;	
		}
		
		if(count($err['alertas']) > 0){
			if($msj != ""){$msj.="<br><br>";}
			$msj.= "<strong>Asignacion Moviles Alertas</strong>";
			foreach($err['alertas'] as $k => $item){
				$msj.= "<br>- Matricula: <i>".$k."</i>, asociado a: <i>".$item."</i>";	
			}
			$error = true;	
		}
		
		if ($error == true) {
			if(strlen($msj) > 503){
				$msj = substr($msj,0,500)."..."; 	
			}
		}
		else {
			$msj = $lang->message->ok->msj_baja;
		}	
	}
	*/
	
	$msj = "";
	if($mPermitido){
		require_once 'clases/clsEquipos.php';
    	$objEquipo = new Equipo($objSQLServer);
		
		foreach($arrDeletes as $item) {		
			$resp = $objMovil->eliminarRegistro($item['mo_id']);
			if($resp){
				$objEquipo->eliminarRegistro($item['un_id']);
				
				$msj = $lang->message->ok->msj_baja;
			}
			else{
				$msj = $lang->message->error->msj_baja;
			}
		}
	}
				
	index($objSQLServer, $seccion, $msj);
}

function guardarM($objSQLServer, $seccion, $popup = false) {
    global $lang;
    $idMovil = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
    require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
    $mensaje = "";
    $set = "";
    for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
        
		$idCampo = $arrElementos[$i]["ig_idCampo"];
        if ($arrElementos[$i]["ig_validacionExistencia"]){$campoValidador = $_POST[$idCampo];}
		$msjError="";
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		//--
        //SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
        if ($i + 1 == count($arrElementos)) {
            $set.= $arrElementos[$i]["ig_value"] . "=" . "''" . trim($_POST[$idCampo]) . "''";
        } else {
            $set.= $arrElementos[$i]["ig_value"] . "=" . "''" . trim($_POST[$idCampo]) . "'',";
        }
        //--
    }

	if (!empty($_POST['txtActivacion'])) {
		$set .= ",mo_fecha_activacion = ''".$_POST['txtActivacion']."''";}
	else{
		$set .= ",mo_fecha_activacion = NULL";}	
		
		
	if (!empty($_POST['cmbConductor'])) {
		$set .= ",mo_co_id_primario = ''".(int)$_POST['cmbConductor']."''";}
	else{
		$set .= ",mo_co_id_primario = NULL";}		
		
		
	//$set.= ', mo_matricula ='."''".trim($_POST['txtVehiculo']) ."''";

	//FIN FRAGMENTO
    if (!$mensaje) {
		require_once 'clases/clsMoviles.php';
        $objMovil = new Movil($objSQLServer);
       	$cod = $objMovil->modificarRegistro($set, $idMovil, $campoValidador);
		$objMovil->asignarEquipo($idMovil, $_POST['equipo_instalado'], $_POST['equipo_viejo']) ;
		
		if (isset($_FILES['foto'])) {
			  $extension = $objMovil->obtenerExtensionImagen($idMovil);
			   $patch_privada = 'imagenes/moviles/' . $idMovil .'.'. $extension;
	
			  $borrar_foto = (isset($_POST["borrar_foto"])) ? $_POST["borrar_foto"] : false;
	
			  if($borrar_foto=="true") {
				  if(file_exists($patch_privada)) {
					unlink($patch_privada);
				  }
			  }
	
			  if(isset($_FILES['foto']['tmp_name']) && !empty($_FILES['foto']['tmp_name'])) {
				  $patch = 'imagenes\moviles\\';
				  $extension = explode('.', $_FILES['foto']['name']);
				  $extension = end($extension);
				  if (strtoupper($extension)=="JPG") {
					  $destination = $patch . $idMovil . '.' . $extension;
					  move_uploaded_file($_FILES['foto']['tmp_name'], $destination);
					  chmod($destination, 0755);
			
					  //AGREGO A LA BASE LA EXTENCION DE LA IMAGEN
					  $objMovil->modificarImagen($idMovil, $extension);
				  } else {
				  	  modificar($objSQLServer, $seccion, $lang->message->interfaz_generica->msj_extension_img." JPG", $idMovil);
					  die();
				  }
			  }
	    }
        
		if ($popup) {
            $jsonData['movil'] = $idMovil;
			$jsonData['ok'] = 'ok';
			$jsonData['cerrar'] = 'ok';
			$jsonData['mensaje'] = htmlentities($mensaje);
			echo json_encode($jsonData);
			die();
		}
        switch ($cod) {
            case 0:
                $mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
                modificar($objSQLServer, $seccion, $mensaje, $idMovil);
            break;
            case 1:
                $mensaje = $lang->message->ok->msj_modificar;
				
                index($objSQLServer, $seccion, $mensaje);
            break;
            case 2:
                $mensaje = $lang->message->error->msj_modificar;
                modificar($objSQLServer, $seccion, $mensaje, $idMovil);
           	break;
        }
    }
	else{
		if($popup) {
       		$jsonData['movil'] = $idMovil;
            $jsonData['error'] = 'ok';
			$jsonData['mensaje'] = htmlentities($mensaje);
	        echo json_encode($jsonData);
            die();
    	}
	    else{
			modificar($objSQLServer, $seccion, $mensaje, $idMovil);
        	return false;
		}
    }
    
	return true;
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function obtenerListadoMoviles($objMovil, $tipo, $filtro = NULL){
	if ($_SESSION['idTipoEmpresa'] == 2){
        $arrEntidades = $objMovil->obtenerMovilesUsuario(0, $filtro, $_SESSION['idUsuario']);
	} 
	else{
        if($tipo == 'update' || $tipo == 'delete'){
			$filtro = 'getAllReg';
		}	
		$arrEntidades = $objMovil->obtenerRegistros(0, $filtro);
    }
	return $arrEntidades;	
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
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
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
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
			->setCellValue('H'.$i, $row['sh_fechaRecepcion']?(formatearFecha($row['sh_fechaRecepcion'])):'');
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->$seccion);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-',$lang->menu->$seccion)).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}