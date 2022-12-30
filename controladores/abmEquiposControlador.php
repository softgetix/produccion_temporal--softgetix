<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";
define("CantidadEntradasXEquipo", 8);
$method = (isset($_GET['method'])) ? $_GET['method'] : NULL;

function index($objSQLServer, $seccion, $mensaje=""){
	global $lang;
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
	$filtro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"");
	$idTipoEmpresaExcuyente= ($_SESSION["idTipoEmpresa"] <= 3) ? 4 : 0; //todos excluyen localizart
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	require_once 'clases/clsPerfiles.php';
    $objPerfil = new Perfil($objSQLServer);
	
	$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	$arrEntidades = obtenerListadoEquipos($objEquipo, 'list',$txtFiltro);
	$cantRegistros = count($arrEntidades);
	
	$popup = isset($_GET["action"]) ? $_GET["action"] : "";
    if ($popup == "popup") {
        alta($objSQLServer, $seccion, "", true);
    } else {
        $extraCSS[]='css/estilosPopup.css';
		$extraJS[] ='js/popupHostFunciones.js';
		$operacion = 'listar';
		$tipoBotonera='LI';
		require("includes/template.php");
    }
}

function alta($objSQLServer, $seccion, $mensaje="", $popup = false){
	global $lang;
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	$tipo_pers = (isset($_SESSION["idTipoEmpresa"]) ? $_SESSION["idTipoEmpresa"] : 0);
	if ($tipo_pers <= 2) {
		foreach ($arrElementos as $clave => $elem) {
			if ($elem["ig_nombre"] == "distribuidor") {
				unset($arrElementos[$clave]);
				break;
			}
		}
	}
	
	$arrEntradas = $objEquipo->getEntradaEquipos();
	
	$operacion = 'alta';
	$tipoBotonera='AM';
	if ($popup) {
        $extraCSS[] = 'css/estilosAbmPopup.css';
        $extraCSS[] = 'css/popup.css';
        $extraJS[] = 'js/popupFunciones.js?1';
        $extraJS[] = 'js/jquery.blockUI.js';
        $recargarAlCerrar = true;
        require("includes/frametemplate.php");
    } else {
        require("includes/template.php");
    }
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idEquipo=0, $popup = false){
	global $lang;
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idEquipo)? $idEquipo: 0);
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$mPermitido = 0;
	$arr_equipos = obtenerListadoEquipos($objEquipo, 'update');
	foreach($arr_equipos as $item){
		if($item['un_id'] == $id){
			$mPermitido = 1;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	$arrEntidades = $objEquipo->obtenerEquipos($id);
	
	
	$tipo_pers = (isset($_SESSION["idTipoEmpresa"]) ? $_SESSION["idTipoEmpresa"] : 0);
	if ($tipo_pers <= 2) {
		foreach ($arrElementos as $clave => $elem) {
			if ($elem["ig_nombre"] == "distribuidor") {
				unset($arrElementos[$clave]);
				break;
			}
		}
	}
	
	$arrEntradas =  $objEquipo->getEntradaEquipos();
	
	$arrModelos = $objEquipo->obtenerModeloEquipos($arrEntidades[0]["un_me_id"]);
	$arrEntradasEquipo = $objEquipo->obtenerEntradasEquipos($id);
	
	$operacion = 'modificar';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsEquipos.php';
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
	$objEquipo = new Equipo($objSQLServer);
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_equipos = obtenerListadoEquipos($objEquipo, 'delete');
	foreach($arrCheks as $itemCheck){
		$mPermitido = 0;
		foreach($arr_equipos as $item){
			if($item['un_id'] == $itemCheck){
				$mPermitido = 1;
			}
		}
		if($mPermitido == 0){
			break;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	$idEquipos="";
	for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idEquipos.=$arrCheks[$i];
		else $idEquipos.=$arrCheks[$i].",";
	}

	if($idEquipos ){
		$msj = "";
		for ( $i = 0; $i < count( $arrCheks ); $i++ ){
			$sql = "
				SELECT un_mostrarComo, un_mo_id
				FROM tbl_unidad 
				WHERE un_id = ".$arrCheks[$i];

			$resEquipo = $objSQLServer->dbQuery( $sql );
			$arrEquipo = $objSQLServer->dbGetAllRows( $resEquipo );

			if ( intval( $arrEquipo[0]['un_mo_id'] ) != 0 ){ // SI ESTE REGISTRO POSEE ALGUNA ASIGNACION
				$msj .= "- ".$arrEquipo[0]['un_mostrarComo']."<br/>";
			}
			else{ // SINO POSEE NINGUNA ASIGNACION, ENTONCES ELIMINA
				if($objEquipo->eliminarRegistro($arrCheks[$i])){	
					$objEquipo->eliminarEquipoSH($arrCheks[$i]);
					$mensaje = $lang->message->ok->msj_baja;
				}
				else{
					$mensaje = $lang->message->error->msj_baja;
				}
			}

			if ( $msj != "" ) {
				$mensaje = "<strong>".$lang->message->msj_baja_equipos_moviles."</strong>";	
				$mensaje .= "<br/>"."<br/>".$msj;
			} else {
				$mensaje = $lang->message->ok->msj_baja; 
			}
		}
	}
	index($objSQLServer, $seccion, $mensaje);
}

function guardarM($objSQLServer, $seccion){
	global $lang;
	$idEquipo = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	$mensaje="";
	$setUnidad="";
	$setUnidadGprs="";
	$setUnidadOrbcomm="";
	$camposGprs="";
	$valorCamposGprs="";
	$camposOrbcomm="";
	$valorCamposOrbcomm="";
	for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo];
		$msjError="";
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//--

		$arrAux = explode("_",$arrElementos[$i]["ig_value"]);
		$prefijo = $arrAux[0];

		//SI SE COMPLETO EL CAMPO ME FIJO A QUE TABLA PERTENECE CON EL SWITCH Y LO SERIALIZO
		if(isset($_POST[$idCampo])){
			$_POST[$idCampo] = empty($_POST[$idCampo])?'NULL':$_POST[$idCampo];
			
			switch($prefijo){
				case 'un':
					//SERIALIZACION DE DATOS Y CAMPOS PARA TABLA tbl_unidad
					if($setUnidad=="") $setUnidad.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''";
					else $setUnidad.= ",".$arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''";
					//--
					break;
				case 'ug':
					//SERIALIZACION DE DATOS Y CAMPOS PARA TABLA tbl_unidad_gprs (MODIFICACION)
					if($setUnidadGprs=="") $setUnidadGprs.= $arrElementos[$i]["ig_value"]."="."'".$_POST[$idCampo]."'";
					else $setUnidadGprs.= ",".$arrElementos[$i]["ig_value"]."="."'".$_POST[$idCampo]."'";
					//--
					//SERIALIZACION DE DATOS Y CAMPOS PARA TABLA tbl_unidad_gprs (ALTA)
					$camposGprs.= $arrElementos[$i]["ig_value"].",";
					$valorCamposGprs.= "''".$_POST[$idCampo]."'',";
					//--
					break;
				case 'uc':
					//SERIALIZACION DE DATOS Y CAMPOS PARA tabla tbl_unidad_orbcomm (MODIFICACION)
					if($setUnidadOrbcomm=="") $setUnidadOrbcomm.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''";
					else $setUnidadOrbcomm.= ",".$arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."''";
					//--
					//SERIALIZACION DE DATOS Y CAMPOS PARA tabla tbl_unidad_orbcomm (ALTA)
					$camposOrbcomm.= $arrElementos[$i]["ig_value"].",";
					$valorCamposOrbcomm.= "''".$_POST[$idCampo]."'',";
					//--
					break;
			}
		}
		//--		
		if(!esVacio($_POST["txtIdentificadorSatelital"])){
			require_once 'clases/clsEquipos.php';
			$objEquipo = new Equipo($objSQLServer);
			$identificadorUnico = $objEquipo->validarIdentificadorInterno($campoValidador,$idEquipo);
			if(!$identificadorUnico){
				$mensaje.= $lang->message->msj_identificador_no_disponible;
			}
		}
	}
	
	$setUnidadGprs = str_replace("''NULL''",'NULL',$setUnidadGprs);
	$setUnidadGprs = str_replace("'NULL'",'NULL',$setUnidadGprs);
	$setUnidad = str_replace("''NULL''",'NULL',$setUnidad);
	$setUnidad = str_replace("'NULL'",'NULL',$setUnidad);
	$setUnidadGprs = str_replace("''NULL''",'NULL',$setUnidadGprs);
	$setUnidadGprs = str_replace("'NULL'",'NULL',$setUnidadGprs);
	$camposGprs = str_replace("''NULL''",'NULL',$camposGprs);
	$camposGprs = str_replace("'NULL'",'NULL',$camposGprs);
	$valorCamposGprs = str_replace("''NULL''",'NULL',$valorCamposGprs);
	$valorCamposGprs = str_replace("'NULL'",'NULL',$valorCamposGprs);
	$setUnidadOrbcomm = str_replace("''NULL''",'NULL',$setUnidadOrbcomm);
	$setUnidadOrbcomm = str_replace("'NULL'",'NULL',$setUnidadOrbcomm);
	$valorCamposOrbcomm = str_replace("''NULL''",'NULL',$valorCamposOrbcomm);
	$valorCamposOrbcomm = str_replace("'NULL'",'NULL',$valorCamposOrbcomm);
	
	
	//FIN FRAGMENTO
	if(!$mensaje){
		require_once 'clases/clsEquipos.php';
		$objEquipo = new Equipo($objSQLServer);
		$cod = $objEquipo->modificarEquipo($setUnidad, $idEquipo, $campoValidador);
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idEquipo);
				break;
			case 1:
				//MODIFICACION DE DATOS GPRS (SI EXISTE LO MODIFICA SINO LO CREA)
				if(!esVacio($_POST["txtIdentificadorGprs"])){
					if($objEquipo->obtenerDatosGprs($idEquipo)){
						if(esVacio($_POST["txtIdentificadorGprs"])){
							$mensaje.= $lang->message->msj_baja_error_identificador;
						}else{
							$objEquipo->modificarRegistro($setUnidadGprs,$idEquipo,NULL,'tbl_unidad_gprs','ug_un');
						}
					}else{
						$camposGprs.= "ug_un_id";
						$valorCamposGprs.= "''".$idEquipo."''";
						$objEquipo->insertarRegistro($camposGprs, $valorCamposGprs, NULL, 'tbl_unidad_gprs');
					}
				}
				//---------------------------------------------------------------				
				//MODIFICACION DE DATOS ORBCOMM
				if(!esVacio($_POST["txtIdentificadorSatelital"])){
					if($objEquipo->obtenerDatosOrbcomm($idEquipo)){
						if(esVacio($_POST["txtIdentificadorSatelital"])){							
							$mensaje.= $lang->message->msj_baja_error_identificador;
						}else{							
							$objEquipo->modificarRegistro($setUnidadOrbcomm,$idEquipo,NULL,'tbl_unidad_orbcomm','uc_un');
						}
					}
					else{
						//INSERCION DE DATOS ORBCOMM
						$camposOrbcomm.= "uc_un_id";
						$valorCamposOrbcomm.= "''".$idEquipo."''";
						$objEquipo->insertarRegistro($camposOrbcomm, $valorCamposOrbcomm, NULL, 'tbl_unidad_orbcomm');
					}
				}
				//---------------------------------------------------------------

				//MODIFICACION ENTRADAS
				for($i=0;$i < CantidadEntradasXEquipo && CantidadEntradasXEquipo; $i++){
					$numeroEntradaEquipo = $i+1;
					$dato = $_POST["cmbEntrada".$numeroEntradaEquipo];
					if($dato!==0){
						$existe = $objEquipo->buscarEntradaEquipo($idEquipo,$numeroEntradaEquipo);	
						//echo $existe;die;
						if($existe){							
							$objEquipo->modificarEntradaEquipo($idEquipo,$numeroEntradaEquipo,$dato);
						}else{				
							$objEquipo->insertarEntradaEquipo($idEquipo,$numeroEntradaEquipo,$dato);					
						}
					}
				}
				//---------------------

				$mensaje.= "<br><br>".$lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
				break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje,$idEquipo);
				break;
				
		}
	}else{
		//redireccionar al alta con los datos cargados.
		modificar($objSQLServer, $seccion, $mensaje,$idEquipo);
	}
}

function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}

function obtenerListadoEquipos($objEquipo, $tipo, $filtro = NULL){
	$tipo_pers = (isset($_SESSION["idTipoEmpresa"]) ? $_SESSION["idTipoEmpresa"] : 0);
	$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
	
	if($tipo == 'update' || $tipo == 'delete'){
		$filtro = 'getAllReg';
	}	
		
	if ($tipo_pers <= 2) {
		$arrEntidades = $objEquipo->obtenerEquipos(0, $filtro, 0, $IdEmpresa);
	}
	else {
		$arrEntidades = $objEquipo->obtenerEquipos(0,$filtro);
	}
	return $arrEntidades;
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
   
   	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	$arrEntidades = obtenerListadoEquipos($objEquipo, 'list',$txtFiltro);
	
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
		->setCellValue('B1',$lang->system->marca)
		->setCellValue('C1',$lang->system->modelo)
		->setCellValue('D1',$lang->system->simcard)
		->setCellValue('E1',$lang->system->telefono)
		->setCellValue('F1',$lang->system->cliente)
		->setCellValue('G1',$lang->system->movil);
		
	$arralCol = array('A','B','C','D','E','F','G');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('B','C','D','E');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','F','G');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['un_mostrarComo']))
			->setCellValue('B'.$i, encode($row['me_nombre']))
			->setCellValue('C'.$i, encode($row['mo_nombre']))
			->setCellValue('D'.$i, encode($row['ug_simcard']))
			->setCellValue('E'.$i, encode($row['ug_telefono']))
			->setCellValue('F'.$i, encode($row['cl_razonSocial']))
			->setCellValue('G'.$i, encode($row['mo_identificador']));
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

function export_equipos_status_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
   
   	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	$idEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
	$arrEntidades = $objEquipo->getEquiposStatus($idEmpresa);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion.'_status')
		->setSubject($lang->menu->$seccion.'_status')
		->setDescription($lang->menu->$seccion.'_status')
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->identificador)
		->setCellValue('B1',$lang->system->movil)
		->setCellValue('C1',$lang->system->marca)
		->setCellValue('D1',$lang->system->modelo)
		->setCellValue('E1',$lang->system->simcard)
		->setCellValue('F1',$lang->system->cliente)
		->setCellValue('G1',$lang->system->ultimo_reporte_recibido)
		->setCellValue('H1',$lang->system->telefono)
		->setCellValue('I1',$lang->system->estado);
		
	$arralCol = array('A','B','C','D','E','F','G','H','I');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('C','D','E','G','H','I');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','F');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['un_mostrarComo']))
			->setCellValue('B'.$i, encode($row['mo_identificador']))
			->setCellValue('C'.$i, encode($row['me_nombre']))
			->setCellValue('D'.$i, encode($row['mo_nombre']))
			->setCellValue('E'.$i, encode($row['ug_simcard']))
			->setCellValue('F'.$i, encode($row['ag_nombre']))
			->setCellValue('G'.$i, $row['sh_fechaGeneracion']?(date('d-m-Y H:i',strtotime($row['sh_fechaGeneracion'])).'hs'):'')
			->setCellValue('H'.$i, encode($row['ug_telefono']))
			->setCellValue('I'.$i, encode($row['estatus']));
			
			$bgColor = ($row['estatus'] == 'ONLINE')?'A7FF81':'FF7575';
			$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $bgColor))));
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->$seccion.'_status');
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->$seccion.'_status').'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}