<?php
//$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function listado($objSQLServer, $seccion, $mensaje = "") {
	//$method = (isset($_GET['method'])) ? $_GET['method'] : null;

	if($_GET['action'] == 'stock'){
		listarStock($objSQLServer, $seccion, $_GET['idRef']);
		exit();	
	}

	require_once 'clases/clsReferencias.php';
	require_once 'clases/clsUsuarios.php';
	
	$objReferencia = new Referencia($objSQLServer);
	$objUsuario = new Usuario($objSQLServer);
		
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	
	global $arrEntidades;
	$arrEntidades = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"], $txtFiltro);
	
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}

function solapaAlta($objSQLServer, $seccion, $mensaje=""){
	global $solapa;
	global $lang;
	 
	
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/boxes.js';
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	$extraJS[] = 'js/abmReferenciasFunciones.js';

	$operacion = 'alta';
	$tipoBotonera='AM';	
	require("includes/template.php");
}


function solapaModificar($objSQLServer, $seccion, $mensaje = "", $id = 0) {
	global $solapa;
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
 
	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
    
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$mPermitido = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $filtro =  NULL,$id);
	if($id==0){$mPermitido=0;}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	$arrEntidades = $objReferencia->obtenerReferencias($id);
	$arrPuntos = $objReferencia->obtenerCoordenadas($id);
	
	$extraJS[] = 'js/jquery/jquery.placeholder.js';
	$extraJS[] = 'js/boxes.js';
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	$extraJS[] = 'js/abmReferenciasFunciones.js';

	$operacion = 'modificar';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function solapaBaja($objSQLServer, $seccion) {
    global $lang;
    require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
   	$id = $_POST['hidId']?$_POST['hidId']:0;
	
    ////////////////////////////////////////////////////Protejo contra inyeccion JS////////////////////////////////////////
	  $mPermitido=$objReferencia->obtenerReferenciasPorEmpresa2($_SESSION['idEmpresa'], $filtro =  NULL,$id);
	  $hablitado=validarModificar($mPermitido,$objSQLServer);
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	if($id){
		$msj = "";
		$error = false;
		$err['viajes'] = array();
		$err['alertas'] = array();
		
			$err_ref = false;
			
			// Verificar si la referecnia a eliminar tiene Viajes asignados 
			$sql = " SELECT DISTINCT(vi_codigo) AS viaje, re_nombre";
			$sql.= " FROM tbl_referencias  ";
			$sql.= " INNER JOIN tbl_viajes_destinos ON vd_re_id = re_id  ";
			$sql.= " INNER JOIN tbl_viajes ON vd_vi_id = vi_id  ";
			$sql.= " WHERE re_id = ".(int)$id." AND vi_borrado = 0 ";
			$objViajes = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objViajes,3);
			if($arrObjRow){	
				$err_ref = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['viajes'][$objRow['re_nombre']].= $coma.$objRow['viaje']; 
					$coma = ", ";
				}
			} 
			
			// Verificar si el movil a eliminar tiene Alertas 
			$sql = " SELECT DISTINCT(al_nombre) as alerta, re_nombre  ";
			$sql.= " FROM tbl_referencias ";
			$sql.= " INNER JOIN tbl_alertas_referencias ON ar_re_id = re_id  ";
			$sql.= " INNER JOIN tbl_alertas ON al_id = ar_al_id ";
			$sql.= " WHERE re_id = ".$id." AND al_borrado = 0 ";
			$objAlertas = $objSQLServer->dbQuery($sql);
			$arrObjRow = $objSQLServer->dbGetAllRows($objAlertas, 3);
			if($arrObjRow){	
				$err_ref = true;
				$coma = "";
				foreach($arrObjRow as $objRow){
					@$err['alertas'][$objRow['re_nombre']].= $coma.$objRow['alerta']; 
					$coma = ", ";
				}
			}
			
			if($err_ref == false){
				$coordGuardadas = $objReferencia->obtenerCoordenadas($id);
				if($objReferencia->eliminarRegistro($id)){
					$objReferencia->generarLog(2,$id,$lang->system->baja_referencia.': '.$coordGuardadas[0]['re_nombre'].(!empty($coordGuardadas[0]['re_numboca'])?' - ID #'.$coordGuardadas[0]['re_numboca']:'')); 							
				}
			}
		
		if(count($err['viajes']) > 0){
			$msj.= "<strong>".$lang->message->msj_baja_referencia_viajes."</strong>";
			foreach($err['viajes'] as $k => $item){
				$msj.= "<br>- ".$lang->system->referencia.": <i>".$k."</i>, ".$lang->system->asociado_a.": <i>".$item."</i>";	
			}
			$error = true;	
		}
		
		if(count($err['alertas']) > 0){
			if($msj != ""){$msj.="<br><br>";}
			$msj.= "<strong>".$lang->message->msj_baja_referencia_alertas."</strong>";
			foreach($err['alertas'] as $k => $item){
				$msj.= "<br>- ".$lang->system->referencia.": <i>".$k."</i>, ".$lang->system->asociado_a.": <i>".$item."</i>";	
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
    index($objSQLServer, $seccion, $msj);
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["txtFiltro"]))?$_POST["txtFiltro"] : '');

	require_once 'clases/clsReferencias.php';
    $objReferencia = new Referencia($objSQLServer);
    
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	$arrEntidades = $objReferencia->obtenerReferenciasPorEmpresa2($_SESSION["idEmpresa"], $txtFiltro);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->nombre_referencia)
		->setCellValue('B1',$lang->system->email)
		->setCellValue('C1',$lang->system->num_boca)
		->setCellValue('D1','Identificador de cliente')
		->setCellValue('E1','Persona de contacto')
		->setCellValue('F1','Whatsapp')
		->setCellValue('G1','Lat')
		->setCellValue('H1','Lng')
		->setCellValue('I1','Stock');

	$objPHPExcel->getActiveSheet()->setTitle('Lugares');

	$arralCol = array('A','B','C','D','E','F','G','H','I');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('G','H','C','I');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','D','E','F');
	$objPHPExcel->alignLeft($alingLeftCol);

	
	//--Ini. Solapa 2 Stock
	$objPHPExcel->createSheet(1);
	$objPHPExcel->setActiveSheetIndex(1)
		->setCellValue('A1','Cliente')
		->setCellValue('B1','Ubicación')
		->setCellValue('C1','Stock Entregado')
		->setCellValue('D1','Stock Pendiente de Retiro')
		->setCellValue('E1','Stock Retirado')
		->setCellValue('F1','Fecha de Entrega/Retiro')
		->setCellValue('G1','Código de transacción')
		->setCellValue('H1','Código de Viaje')
		->setCellValue('I1','Fabricante Encargado del Viaje')
		->setCellValue('J1','Viaje Vinculado')
		->setCellValue('K1','Fabricante Dueño')
		->setCellValue('L1','Tipo Viaje');

	$objPHPExcel->getActiveSheet()->setTitle('Stock');

	$arralCol = array('A','B','C','D','E','F','G','H','I','J','K','L');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('C','D','E','F','G','L');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','H','I','J','K');
	$objPHPExcel->alignLeft($alingLeftCol);
	//--Fin. Solapa 2 Stock

	$i = 2;
	foreach($arrEntidades as $row){		
		$auxLatLng = explode(',',$row['LatLng']);
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['re_nombre']))
			->setCellValue('B'.$i, encode($row['re_email']))
			->setCellValue('C'.$i, encode($row['re_numboca']))
			->setCellValue('D'.$i, encode($row['re_identificador']))
			->setCellValue('E'.$i, encode($row['re_contacto']))
			->setCellValue('F'.$i, encode($row['re_whatsapp']))
			->setCellValue('G'.$i, $auxLatLng[0])
			->setCellValue('H'.$i, $auxLatLng[1])
			->setCellValue('I'.$i, ($row['stock_cliente'] < 0) ? 'Transacción en curso' : $row['stock_cliente']);
		$i++;	

		//--Ini. Solapa 2 Stock
		if(!empty($row['stock_cliente']) && $row['stock_cliente'] > 0){
			$strSQL = "exec detalle_stock_pallets {$_SESSION['idEmpresa']},{$_SESSION['idEmpresa']}, {$row['re_id']},0,-1";
			$arrListado = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 2);
			if($arrListado){
				foreach($arrListado as $k => $item){
					$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue('A'.($k+2), encode($item[0]))
						->setCellValue('B'.($k+2), encode($item[1]))
						->setCellValue('C'.($k+2), encode($item[2]))
						->setCellValue('D'.($k+2), encode($item[3]))
						->setCellValue('E'.($k+2), encode($item[4]))
						->setCellValue('F'.($k+2), formatearFecha($item[5]))
						->setCellValue('G'.($k+2), encode($item[6]))
						->setCellValue('H'.($k+2), encode($item[7]))
						->setCellValue('I'.($k+2), encode($item[8]))
						->setCellValue('J'.($k+2), encode($item[9]))
						->setCellValue('K'.($k+2), encode($item[10]))
						->setCellValue('L'.($k+2), encode($item[11]));
				}
			}
		}
		//--Fin. Solapa 2 Stock
	}

	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ','-','Lugares')).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

function solapaGuardarA($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 1, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Nombre', 'ig_idCampo' => 'txtNombre', 'ig_value' => 're_nombre');
	$arrElementos[] = array('ig_ti_id' => 7, 'ig_requerido' => 1, 'ig_min' => 0, 'ig_max' => 100, 'ig_tipoDato' => 2, 'ig_nombre' => 'Tipo Referencia', 'ig_idCampo' => 'cmbTipoReferencia', 'ig_value' => 're_tr_id');
	$arrElementos[] = array('ig_ti_id' => 2, 'ig_requerido' => 1, 'ig_min' => 0, 'ig_max' => 100, 'ig_tipoDato' => 2, 'ig_nombre' => 'Grupo', 'ig_idCampo' => 'cmbGrupo', 'ig_value' => 're_rg_id');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 1, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Identificador de sucursal', 'ig_idCampo' => 'txtBoca', 'ig_value' => 're_numboca');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'E-Mail', 'ig_idCampo' => 'txtEmail', 'ig_value' => 're_email');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Identificador de cliente', 'ig_idCampo' => 'txtIdentClient', 'ig_value' => 're_identificador');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Persona de contacto', 'ig_idCampo' => 'txtContacto', 'ig_value' => 're_contacto');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Whatsapp', 'ig_idCampo' => 'txtWhatsapp', 'ig_value' => 're_whatsapp');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Dirección', 'ig_idCampo' => 'txtDireccion', 'ig_value' => 're_ubicacion');
	
	$mensaje = validarCampos($objReferencia, $arrElementos, $idReferencia);
	if(empty($mensaje)){
		if(isset($_POST["txtBoca"]) && $_POST["txtBoca"]!=''){
			if($objReferencia->exiteNumBoca($_POST["txtBoca"])){
				$mensaje.="* ".$lang->message->error->msj_alta->__toString().'('.$lang->system->num_boca->__toString().')<br/> ';
			}
		}
	}

	if(empty($mensaje)){		
		$coma = '';
		foreach($arrElementos as $item){
			$campos.= $coma.$item['ig_value'];
			$valorCampos.= $coma.(isset($_POST[$item['ig_idCampo']])?"''" .$_POST[$item['ig_idCampo']]. "''":'NULL');
			$coma = ',';
		}

		$campos.= ",re_us_id";
		$valorCampos.= ",".(int)$_POST["hidUsuario"];
		
		$strPuntos = "";    
		if($id = $objReferencia->insertarRegistro($campos, $valorCampos)){
			if(isset($_POST["hidPuntos"])){
				if($_POST["hidPuntos"] != ""){
					$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
					$arrPuntos = explode(";", $_POST["hidPuntos"]);
						
					for ($i = 0; $i < count($arrPuntos) && $arrPuntos; $i++) {
						$arrAux = explode(", ", $arrPuntos[$i]);
						if ($arrAux[0]){
							$strLat = trim($arrAux[0]);
							$strLng = trim($arrAux[1]);
							$campos = "rc_latitud";
							$campos .= ",rc_longitud";
							$campos .= ",rc_re_id";
							$valorCampos = "''" . $strLat . "''";
							$valorCampos .= ",''" . $strLng . "''";
							$valorCampos .= ",''" . $id . "''";
							$objReferencia->insertarCoordenadas($campos, $valorCampos);
						}
					}
						
					////-- Log System --//
					//$arrPuntos = array_filter($arrPuntos);
					//$objReferencia->generarLog(2,$id,$lang->system->alta_referencia.': '.$_POST['txtNombre'].(!empty($_POST['txtBoca'])?' - ID #'.$_POST['txtBoca']:'').' ['.implode(';',$arrPuntos).']'); 
					////-- --//
				}
			}

			$mensaje = $lang->message->ok->msj_alta->__toString();
			index($objSQLServer, $seccion, $mensaje);
			exit;
		}
		else{
			$mensaje = 'Se produjo un error y no se pudo guardar los datos.';
			solapaAlta($objSQLServer, $seccion, $mensaje, $idReferencia);
			exit;	
		}
	}
	else{
		solapaAlta($objSQLServer, $seccion, $mensaje, $idReferencia);
		exit;	
	}
}

function solapaGuardarM($objSQLServer, $seccion) {
    global $lang;
	$idReferencia = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 1, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Nombre', 'ig_idCampo' => 'txtNombre', 'ig_value' => 're_nombre');
	$arrElementos[] = array('ig_ti_id' => 7, 'ig_requerido' => 1, 'ig_min' => 0, 'ig_max' => 100, 'ig_tipoDato' => 2, 'ig_nombre' => 'Tipo Referencia', 'ig_idCampo' => 'cmbTipoReferencia', 'ig_value' => 're_tr_id');
	$arrElementos[] = array('ig_ti_id' => 2, 'ig_requerido' => 1, 'ig_min' => 0, 'ig_max' => 100, 'ig_tipoDato' => 2, 'ig_nombre' => 'Grupo', 'ig_idCampo' => 'cmbGrupo', 'ig_value' => 're_rg_id');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 1, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Identificador de sucursal', 'ig_idCampo' => 'txtBoca', 'ig_value' => 're_numboca');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'E-Mail', 'ig_idCampo' => 'txtEmail', 'ig_value' => 're_email');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Identificador de cliente', 'ig_idCampo' => 'txtIdentClient', 'ig_value' => 're_identificador');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Persona de contacto', 'ig_idCampo' => 'txtContacto', 'ig_value' => 're_contacto');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Whatsapp', 'ig_idCampo' => 'txtWhatsapp', 'ig_value' => 're_whatsapp');
	$arrElementos[] = array('ig_ti_id' => 1, 'ig_requerido' => 0, 'ig_min' => 3, 'ig_max' => 100, 'ig_tipoDato' => 1, 'ig_nombre' => 'Dirección', 'ig_idCampo' => 'txtDireccion', 'ig_value' => 're_ubicacion');
	$set = "";

	
	$mensaje = validarCampos($objReferencia, $arrElementos, $idReferencia);
	if(empty($mensaje)){
		$coma = '';
		foreach($arrElementos as $item){
			$set.= $coma.$item['ig_value'].'='. "''" .$_POST[$item['ig_idCampo']]. "''";	
			$coma = ',';
		}
	

		$coordGuardadas = $objReferencia->obtenerCoordenadas($idReferencia);
		$cod = $objReferencia->modificarRegistro($set,$idReferencia);
		$objReferencia->eliminarCoordenadas($idReferencia);
		if (isset($_POST["hidPuntos"])) {
			if ($_POST["hidPuntos"] != "") {
				$_POST["hidPuntos"] = str_replace('(','',str_replace(')','',$_POST["hidPuntos"]));
				$arrPuntos = explode(";", $_POST["hidPuntos"]);
				
				for ($i = 0; $i < count($arrPuntos) && $arrPuntos; $i++) {
					$arrAux = explode(", ", $arrPuntos[$i]);
					if ($arrAux[0]) {
						$strLat = trim($arrAux[0]);
						$strLng = trim($arrAux[1]);
						
						$campos = "rc_latitud";
						$campos .= ",rc_longitud";
						$campos .= ",rc_re_id";
						$valorCampos = "''" . $strLat . "''";
						$valorCampos .= ",''" . $strLng . "''";
						$valorCampos .= ",''" . $idReferencia . "''";
						$objReferencia->insertarCoordenadas($campos, $valorCampos);
					}
				}
			}
		}
		
		//-- Log System --//
		/*$strCoord = array();
		if($coordGuardadas){
			foreach($coordGuardadas as $kCoord => $coord){
				array_push($strCoord,$coord['rc_latitud'].', '.$coord['rc_longitud']);		
			}
		}
		
		$arrPuntos = array_filter($arrPuntos);
		$auxNumBoca = ($coordGuardadas[0]['re_numboca'] != $_POST['txtBoca'])?$_POST['txtBoca']:NULL;
		$auxLatLon = (implode(';',$strCoord) != implode(';',$arrPuntos))?implode(';',$arrPuntos):NULL;
		if(!empty($auxNumBoca) || !empty($auxLatLon)){
			$objReferencia->generarLog(2,$idReferencia,str_replace('[DATOS_EDITADOS]',$_POST['txtNombre'].(!empty($auxNumBoca)?' - ID #'.$auxNumBoca:'').(!empty($auxLatLon)?' ['.implode(';',$arrPuntos).']':''),str_replace('[DATOS_ACTUALES]',$coordGuardadas[0]['re_nombre'].(!empty($auxNumBoca)?' - ID #'.$coordGuardadas[0]['re_numboca']:'').(!empty($auxLatLon)?' ['.implode(';',$strCoord).']':''),$lang->system->edicion_referencia))); 						
		}*/
		//-- --//
		
		switch ($cod) {
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe->__toString();
			break;
			case 1:
				$mensaje = $lang->message->ok->msj_alta->__toString();
				index($objSQLServer, $seccion, $mensaje);
				exit;
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar->__toString();
			break;
		}
	}
	else{
		solapaModificar($objSQLServer, $seccion, $mensaje, $idReferencia);
		exit;
	
	}
}

/*
function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}
*/

function validarCampos($objReferencia, $arrElementos,$idReferencia = NULL){
	global $lang;
	$mensaje = '';
	
	for ($i = 0; $i < count($arrElementos) && $arrElementos; $i++) {
		$msjError = "";
	 	$msjError = checkAll($arrElementos[$i], @$_POST);
		if($msjError){
			$mensaje.="* ".$msjError."<br/> ";
		}
	}
	
	return $mensaje;	
}

function listarStock($objSQLServer, $seccion, $idRef) {
	global $lang;
	$idRef = intval($idRef);

	//$strSQL = "Exec sp_stock_pallets {$idRef},{$_SESSION['idUsuario']}";
	$strSQL = "exec detalle_stock_pallets {$_SESSION['idEmpresa']},{$_SESSION['idEmpresa']}, {$idRef},0,-1";
	$arrListado = $objSQLServer->dbGetAllRows($objSQLServer->dbQuery($strSQL), 2);
	
	$popup = true;
	$seccion = 'cuenta_referencias';
	$operacion = 'listardetallestock';
	
	$extraCSS[] = 'css/estilosPopup.css';
    $extraJS[] = 'js/popupHostFunciones.js';
    $extraCSS[] = 'css/popup.css';
	
	$scroll = true;
	require("includes/frametemplate.php");
}
?>