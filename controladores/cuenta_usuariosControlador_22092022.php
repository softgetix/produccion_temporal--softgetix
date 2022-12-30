<?php
//$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function listado($objSQLServer, $seccion, $mensaje = ""){
	//$method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	
	$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	
	//-- Para no tocar el procedimiento de clientes : obtenerUsuariosSP
	if(empty($filtro) && $_SESSION["idTipoEmpresa"] == 2){
		$txtFiltro = 'getAllReg';
	}
	//-- --//
	
	global $arrEntidades;
	$arrEntidades = obtenerListado($objSQLServer, 'list', $txtFiltro);
}

function solapaAlta($objSQLServer, $seccion, $mensaje = ""){
	global $solapa;
	
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmUsuarios');
	
	$assig = asignarMoviles($id);
	$arrClientes = $assig['arrClientes'];
	$arrMoviles = $assig['arrMoviles'];
	$arrMovilesUsuario = $assig['arrMovilesUsuario'];
	
	$extraJS[]='js/boxes.js';
	$extraJS[]='js/abmUsuariosFunciones.js';
    $extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
	$operacion = 'alta';
	$tipoBotonera='AM';
	
	require("includes/template.php");
	
}

function solapaModificar($objSQLServer, $seccion, $mensaje = "", $id = NULL){
	global $solapa;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$mPermitido = obtenerListado($objSQLServer, 'update');
	$aciertos=0;
	foreach($mPermitido as $permitido){
		if($id==$permitido['us_id']){
		  //Sumo al contador de aciertos y lo dejo seguir.
		  $aciertos ++;	
		  break;
		 }
	}

	if($aciertos==0){// el id que me quiere asignar el usuario no est� en lo que devuelve el listado
	   $mPermitido=NULL; //PASO un NULL para que entre en al rutina de inyecci�n.
	   validarModificar($mPermitido,$objSQLServer);		
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$arrEntidades = $objUsuario->obtenerUsuarios($id);
	
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmUsuarios');
   	
	$assig = asignarMoviles($id);
	$arrClientes = $assig['arrClientes'];
	$arrMoviles = $assig['arrMoviles'];
	$arrMovilesUsuario = $assig['arrMovilesUsuario'];

	$extraJS[]='js/boxes.js';
	$extraJS[]='js/abmUsuariosFunciones.js';
        $extraCSS[] = 'css/ui/jquery.ui.datepicker.css';
	$extraJS[] = 'js/jquery/jquery.datepicker.js';
   	$operacion = 'modificar';
	$tipoBotonera='AM';
	require("includes/template.php");
}

function solapaGuardarA($objSQLServer, $seccion){
	global $lang;
	$resp = validarCampos($_POST, 'alta');
	$mensaje = $resp['mensaje'];
	
	if(!$mensaje){	
		require_once 'clases/clsInterfazGenerica.php';
  		$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  		$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmUsuarios');
		$campos = $valorCampos = $pass = $passRepetido = "";
		$coma = '';
		
		for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
			$idCampo = $arrElementos[$i]['ig_idCampo'];
			
			if($idCampo != 'chkCambiarPass' && $idCampo != 'txtRepetirPass'){
				//VERIFICO SI ES EL CAMPO CLAVE PARA VALIDAR LA EXISTENCIA DEL REGISTRO EN LA TABLA PREVIA CARGA DE DATOS.
				if($arrElementos[$i]['ig_validacionExistencia']) $campoValidador = $_POST[$idCampo];
			
				if($idCampo == "txtPass"){
                                    //$_POST[$idCampo] = md5($_POST[$idCampo]);
                                    $_POST[$idCampo] = hash('sha256',$_POST[$idCampo]);
				}
				
				$campos.= $coma.$arrElementos[$i]['ig_value'];
				$valorCampos.= $coma."''".$_POST[$idCampo]."''";
				$coma = ', ';
			}
		}
		
		if($campos && $valorCampos){
			$campos.= ",us_usuarioCreador";
			$idUsuario= $_SESSION['idUsuario'];
			$valorCampos.= ",''".$idUsuario."''";
			
			$campos.= ",us_mailAlertas";
			$valorCampos.= ",''".$_POST['txtUsuario']."''";
			
			$campos.= ",us_accesoMobile";
			$valorCampos.= ",''1''";
                        
                        $campos.= ",us_expira";
			$valorCampos.= ",".(strtotime($_POST['txtExpiracion'])?"''".date('Y-m-d',strtotime($_POST['txtExpiracion']))."''":'NULL');
		}
		
		require_once 'clases/clsUsuarios.php';
		$objUsuario = new Usuario($objSQLServer);
		
		$campoValidador	= !empty($campoValidador)?("us_nombreUsuario = '".$campoValidador."'"):$campoValidador;
		if($idUsuario = $objUsuario->insertarRegistro($campos, $valorCampos, $campoValidador)){
			$objUsuario->insertarPreferenciasMovil($idUsuario);
			
			//-- Generar Log --//
			$auxInfoNew = $objUsuario->obtenerUsuarios($idUsuario);
			$auxInfoNew = $auxInfoNew[0];
			
			$txtActual = $coma = NULL;
			if(!empty($auxInfoNew['us_nombre'])){
				$txtActual.= $coma.$lang->system->nombre.'['.$auxInfoNew['us_nombre'].']';	
				$coma = ', ';
			}
			if(!empty($auxInfoNew['us_apellido'])){
				$txtActual.= $coma.$lang->system->apellido.'['.$auxInfoNew['us_apellido'].']';	
				$coma = ', ';
			}
			if(!empty($auxInfoNew['cl_razonSocial'])){
				$txtActual.= $coma.$lang->system->empresa.'['.$auxInfoNew['cl_razonSocial'].']';	
				$coma = ', ';	
			}
			if(!empty($auxInfoNew['pe_nombre'])){
				$txtActual.= $coma.$lang->system->perfil.'['.$auxInfoNew['pe_nombre'].']';	
				$coma = ', ';
			}
			if(!empty($auxInfoNew['us_nombreUsuario'])){
				$txtActual.= $coma.$lang->system->usuario.'['.$auxInfoNew['us_nombreUsuario'].']';	
				$coma = ', ';
			}
			if(!empty($auxInfoNew['us_ipAutorizada'])){
				$txtActual.= $coma.$lang->system->ip_autorizada.'['.$auxInfoNew['us_ipAutorizada'].']';	
				$coma = ', ';	
			}	
				
			if(!empty($auxInfoNew)){
				$objUsuario->generarLog(4,$idUsuario,decode($lang->system->alta_usuario.' '.$txtActual));
			}
			//-- Fin. Log --//
			
			
			//-- Asignaci�n de Moviles --//
			if(isset($_POST['cmbMovilesAsignados'])){
				require_once 'clases/clsMoviles.php';
				$objMovil = new Movil($objSQLServer);
			
				$arrAltaAsignacion = $_POST['cmbMovilesAsignados'];	
				$objMovil->asignarMovilesUsuarios($idUsuario,$arrAltaAsignacion); 
				
				//-- Generar Log --//
				$txtMatriculas = $objMovil->obtenerMatriculas(implode(',',$arrAltaAsignacion));
				$auxTxt = $coma = NULL;
				foreach($txtMatriculas as $item){
					$auxTxt.= $coma.$item['mo_matricula'];
					$coma = ', ';	
				}
				$objUsuario->generarLog(4,$idUsuario,decode($lang->system->agregar_moviles.' '.$auxTxt));
				//-- fin. Generar Log --//
			}
			//--  --//

			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}
		else{
			$mensaje = $lang->message->error->msj_alta;
			solapaAlta($objSQLServer, $seccion, $mensaje);
		}
	}
	else{
		solapaAlta($objSQLServer, $seccion, $mensaje);
	}
}

function solapaGuardarM($objSQLServer, $seccion){
	global $lang;
	$idUsuario = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	$chkCambiarPass = (isset($_POST["chkCambiarPass"]))? 1:0;
	$resp = validarCampos($_POST, 'modificar');
	$mensaje = $resp['mensaje'];
	$coma = '';
	
	if(!$mensaje){	
		require_once 'clases/clsInterfazGenerica.php';
  		$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  		$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmUsuarios');
		
		for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
			$idCampo = $arrElementos[$i]["ig_idCampo"];
			
			if($idCampo != 'chkCambiarPass' && $idCampo != 'txtRepetirPass'){
				//VERIFICO SI ES EL CAMPO CLAVE PARA VALIDAR LA EXISTENCIA DEL REGISTRO EN LA TABLA PREVIA CARGA DE DATOS.
				if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo];
			
				if($idCampo == "txtPass"){
					if($chkCambiarPass){
						//$_POST[$idCampo] = md5($_POST[$idCampo]);
                                                $_POST[$idCampo] = hash('sha256',$_POST[$idCampo]);
						$set.= $coma.$arrElementos[$i]['ig_value']."="."''".$_POST[$idCampo]."''";
						$coma = ', ';
					}	
				}
				else{
					$set.= $coma.$arrElementos[$i]['ig_value']."="."''".$_POST[$idCampo]."''";
					$coma = ', ';
				}
			}
		}
	   
	   	$set.= $coma."us_mailAlertas = ''".$_POST['txtUsuario']."''";
                
                $set.= $coma."us_expira = ".(strtotime($_POST['txtExpiracion'])?"''".date('Y-m-d',strtotime($_POST['txtExpiracion']))."''":'NULL'); 
                
	   	require_once 'clases/clsUsuarios.php';
		$objUsuario = new Usuario($objSQLServer);
		
	   	$auxInfoActual = $objUsuario->obtenerUsuarios($idUsuario);
		$auxInfoActual = $auxInfoActual[0];
		
		$campoValidador	= !empty($campoValidador)?("us_nombreUsuario = '".$campoValidador."'"):$campoValidador;
		$cod = $objUsuario->modificarRegistro($set,$idUsuario, $campoValidador);
		
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idUsuario);
			break;
			case 1:
				//-- Generar Log --//
				$auxInfoUpdate = $objUsuario->obtenerUsuarios($idUsuario);
				$auxInfoUpdate = $auxInfoUpdate[0];
			
				$txtActual = $txtUpdate = $coma = NULL;
				if($auxInfoActual['us_cl_id'] != $auxInfoUpdate['us_cl_id']){
					$txtActual.= $coma.$lang->system->empresa.'['.$auxInfoActual['cl_razonSocial'].']';	
					$txtUpdate.= $coma.$lang->system->empresa.'['.$auxInfoUpdate['cl_razonSocial'].']';
					$coma = ', ';	
				}
				if($auxInfoActual['us_pe_id'] != $auxInfoUpdate['us_pe_id']){
					$txtActual.= $coma.$lang->system->perfil.'['.$auxInfoActual['pe_nombre'].']';	
					$txtUpdate.= $coma.$lang->system->perfil.'['.$auxInfoUpdate['pe_nombre'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['us_nombre'] != $auxInfoUpdate['us_nombre']){
					$txtActual.= $coma.$lang->system->nombre.'['.$auxInfoActual['us_nombre'].']';	
					$txtUpdate.= $coma.$lang->system->nombre.'['.$auxInfoUpdate['us_nombre'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['us_apellido'] != $auxInfoUpdate['us_apellido']){
					$txtActual.= $coma.$lang->system->apellido.'['.$auxInfoActual['us_apellido'].']';	
					$txtUpdate.= $coma.$lang->system->apellido.'['.$auxInfoUpdate['us_apellido'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['us_nombreUsuario'] != $auxInfoUpdate['us_nombreUsuario']){
					$txtActual.= $coma.$lang->system->usuario.'['.$auxInfoActual['us_nombreUsuario'].']';	
					$txtUpdate.= $coma.$lang->system->usuario.'['.$auxInfoUpdate['us_nombreUsuario'].']';	
					$coma = ', ';
				}
				if($auxInfoActual['us_pass'] != $auxInfoUpdate['us_pass']){
					$txtActual.= $coma.$lang->system->password.'[****]';	
					$txtUpdate.= $coma.$lang->system->password.'[****]';	
					$coma = ', ';
				}	
				if($auxInfoActual['us_ipAutorizada'] != $auxInfoUpdate['us_ipAutorizada']){
					$txtActual.= $coma.$lang->system->ip_autorizada.'['.$auxInfoActual['us_ipAutorizada'].']';	
					$txtUpdate.= $coma.$lang->system->ip_autorizada.'['.$auxInfoUpdate['us_ipAutorizada'].']';
					$coma = ', ';	
				}	
				
				if(!empty($txtUpdate)){
					$objUsuario->generarLog(4,$idUsuario,decode(str_replace('[DATOS_EDITADOS]',$txtUpdate,str_replace('[DATOS_ACTUALES]',$txtActual,$lang->system->edicion_usuario))));
				}
				//-- Fin. Log --//
				
				//-- Asignaci�n de Moviles --//
				require_once 'clases/clsMoviles.php';
				$objMovil = new Movil($objSQLServer);
				
				$arrMovilesAsignados = $objMovil->obtenerMovilesGrupo(-1,$idUsuario);
				$arrAltaAsignacion = $_POST['cmbMovilesAsignados'];	
				$arrBajaAsignacion = array();
				foreach($arrMovilesAsignados as $movil){
					if(!in_array($movil['id'], $arrAltaAsignacion)){
						array_push($arrBajaAsignacion,$movil['id']);
					}
					else{
						if(($key = array_search($movil['id'], $arrAltaAsignacion)) !== false) {
							unset($arrAltaAsignacion[$key]);
						}	
					}
				}
				
				if($arrAltaAsignacion){
					$txtMatriculas = $objMovil->obtenerMatriculas(implode(',',$arrAltaAsignacion));
					$auxTxt = $coma = NULL;
					foreach($txtMatriculas as $item){
						$auxTxt.= $coma.$item['mo_matricula'];
						$coma = ', ';	
					}
					$objUsuario->generarLog(4,$idUsuario,decode($lang->system->agregar_moviles.' '.$auxTxt));
				}
				
				if($arrBajaAsignacion){
					$txtMatriculas = $objMovil->obtenerMatriculas(implode(',',$arrBajaAsignacion));
					$auxTxt = $coma = NULL;
					foreach($txtMatriculas as $item){
						$auxTxt.= $coma.$item['mo_matricula'];
						$coma = ', ';	
					}
					$objUsuario->generarLog(4,$idUsuario,decode($lang->system->quitar_moviles.' '.$auxTxt));
				}
				
				$objMovil->asignarMovilesUsuarios($idUsuario,$arrAltaAsignacion,$arrBajaAsignacion); 
				//--  --//
				
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idUsuario);
			break;
		}
	}//--fin mensaje --//
	else{
		solapaModificar($objSQLServer, $seccion, $mensaje,$idUsuario);
	}
}

function asignarMoviles($id){
	global $objSQLServer;
	
	//-- Asignacion de M�viles --//
	$IdEmpresa = ($_SESSION["idTipoEmpresa"] <= 2) ? $_SESSION["idEmpresa"] : 0;
	
	require_once 'clases/clsMoviles.php';
	$objMovil = new Movil($objSQLServer);
	
	switch ($_SESSION['idTipoEmpresa']){
		case 1: //agente
			$arrClientes = obtenerDatosCombo('pa_obtenerClienteCombo 0,0,'.(int)$_SESSION["idEmpresa"].',1',3);
			//$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]);
		break;
		case 2: //clientes
			//$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]); 
		break;
		case 3: //Localizar-t
			//$arrMoviles = $objMovil->obtenerMovilesUsuarioCombo($_SESSION["idUsuario"]);
			//var_dump($arrMoviles);exit;
		break;
	}


	$arrMovilesUsuario = $objMovil->obtenerMovilesUsuarioCombo($_POST['hidId']);
	$arrAuxIdMovilUsuario = array();

	for($i=0; $i < count($arrMovilesUsuario); $i++){
		$arrAuxIdMovilUsuario[$i]=$arrMovilesUsuario[$i]['id'];
	}
	
	$j=0;
	for($i=0;$i < count($arrMoviles) && $arrMoviles; $i++){
		if(!in_array($arrMoviles[$i]['id'],$arrAuxIdMovilUsuario)){
			$arrAuxMoviles[$j]=$arrMoviles[$i];
			$j++;
		}
	}
	$arrMoviles = $arrAuxMoviles;
	//-- --//
	
	$arr['arrClientes'] = $arrClientes;
	$arr['arrMoviles'] = $arrMoviles;
	$arr['arrMovilesUsuario'] = $arrMovilesUsuario;

	return $arr;
}


function solapaBaja($objSQLServer, $seccion){
	global $lang;
	$idUsuario = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$error = false;
	if($idUsuario){
		
		
		$auxItemBaja = $objUsuario->obtenerUsuarios($idUsuario);
		$auxItemBaja = $auxItemBaja[0];
				
		$msj = "";
		$errAlertas = '';
		$errMoviles = '';
		
		// Verificar si el usuario a eliminar tiene Moviles asignados 
		$sql = " SELECT DISTINCT(mo_matricula) ";
		$sql.= " FROM tbl_usuarios_moviles  ";
		$sql.= " INNER JOIN tbl_moviles ON um_mo_id = mo_id  ";
		$sql.= " WHERE um_us_id = ".(int)$idUsuario." AND mo_borrado = 0 ";
		$objMoviles = $objSQLServer->dbQuery($sql);
		$arrObjRow = $objSQLServer->dbGetAllRows($objMoviles, 3);
		if($arrObjRow){	
			$coma = "";
			foreach($arrObjRow as $objRow){
				$errMoviles.= $coma.$objRow['mo_matricula']; 
				$coma = ", ";
			}
		}
			
		// Verificar si el usuario a eliminar tiene Alertas 
		$sql = " SELECT DISTINCT(al_nombre) as alerta ";
		$sql.= " FROM tbl_alertas ";
		$sql.= " INNER JOIN tbl_alertas_usuarios ON al_id = au_al_id ";
		$sql.= " WHERE au_us_id = ".(int)$idUsuario." AND al_borrado = 0 ";
		$objAlertas = $objSQLServer->dbQuery($sql);
		$arrObjRow = $objSQLServer->dbGetAllRows($objAlertas, 3);
		if ($arrObjRow) { 
			$coma = "";
			foreach($arrObjRow as $objRow){
				$errAlertas.= $coma.$objRow['alerta']; 
				$coma = ", ";
			}
		}
			
		if(empty($errAlertas) && empty($errMoviles)){
			$objUsuario->eliminarRegistro((int)$idUsuario);
		}
	}
		
	if(!empty($errMoviles)){
		$msj.= "<strong>".$lang->message->msj_baja_usuarios_moviles."</strong>";
		$msj.= "<br>- ".$lang->system->asociado_a.": <i>".$errMoviles."</i>";	
		$error = true;	
	}
		
	if(!empty($errAlertas)){
		if($msj != ""){$msj.="<br><br>";}
		$msj.= "<strong>".$lang->message->msj_baja_usuarios_alertas."</strong>";
		$msj.= "<br>- ".$lang->system->asociado_a.": <i>".$errAlertas."</i>";	
		$error = true;	
	}
		
	if ($error == true) {
		if(strlen($msj) > 503){
			$msj = substr($msj,0,500)."..."; 	
		}
	}
	else {
		$objUsuario->generarLog(4,$idUsuario,decode($lang->system->baja_usuario.' '.trim($auxItemBaja['us_nombre'].' '.$auxItemBaja['us_apellido'])));
		$msj = $lang->message->ok->msj_baja;
	}	
	
	index($objSQLServer, $seccion, $msj);
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsUsuarios.php';
	$objUsuario = new Usuario($objSQLServer);
    
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	
	$arrEntidades = obtenerListado($objSQLServer, 'list', $txtFiltro);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->abmUsuarios)
		->setSubject($lang->menu->abmUsuarios)
		->setDescription($lang->menu->abmUsuarios)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->nombre)
		->setCellValue('B1',$lang->system->apellido)
		->setCellValue('C1',$lang->system->usuario)
		->setCellValue('D1',$lang->system->perfil)
		->setCellValue('E1',$lang->system->cliente)
		->setCellValue('F1',$lang->system->tipo_empresa);
		
	$arralCol = array('A','B','C','D','E','F');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('D','F');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','C','E');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['us_nombre']))
			->setCellValue('B'.$i, encode($row['us_apellido']))
			->setCellValue('C'.$i, encode($row['us_nombreUsuario']))
			->setCellValue('D'.$i, encode($row['pe_nombre']))
			->setCellValue('E'.$i, encode($row['cl_razonSocial']))
			->setCellValue('F'.$i, ($row['cl_tipo'] == 3)?'Localizar-T':(($row['cl_tipo']==1)?'Agente':(($row['cl_tipo']==2)?'Cliente':$lang->system->otro)));
		$i++;	
	}
	
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->abmUsuarios);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->abmUsuarios).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
	header('Pragma: private');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

/*
function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}
*/
function obtenerListado($objSQLServer, $tipo, $filtro=0){
	    $objUsuario = new Usuario($objSQLServer);

		if(empty($filtro) && $tipo != 'list'){
			$filtro = 'getAllReg';
		}	
		
		if($_SESSION["idTipoEmpresa"] == 2){ //CLIENTE
			//$arrEntidades = $objUsuario->obtenerUsuariosSP($_SESSION['idUsuario'], $filtro);
		}
		elseif ($_SESSION["idTipoEmpresa"] == 1){ //AGENTE
			$datos = array('idEmpresa' => $_SESSION['idEmpresa'], 'filtro' => $filtro, 'exclirUsuario' => $_SESSION['idUsuario']);
			$arrEntidades = $objUsuario->obtenerUsuariosListado($datos);
		}
		elseif ($_SESSION["idTipoEmpresa"] == 3) {
			$datos = array('idTipoEmpresaExcluyente' => 4, 'filtro' => $filtro, 'exclirUsuario' => $_SESSION['idUsuario']/*, 'criterioOrden' => $criterioOrden, 'orden' => $orden*/);
			$arrEntidades = $objUsuario->obtenerUsuariosListado($datos);
		}
		else {
			$arrEntidades = $objUsuario->obtenerUsuarios(0,$filtro,"");
		}
	return $arrEntidades;
}

function validarCampos($post, $operacion){
	global $lang;
	global $objSQLServer;
	
	$chkCambiarPass = (isset($post["chkCambiarPass"]))? 1:0;
	require_once 'clases/clsInterfazGenerica.php';
   	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmUsuarios');
   	
	$mensaje = "";
	$set = "";
	$pass = "";
	$passRepetido = "";
	
	for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
	
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($idCampo != "chkCambiarPass"){
			$msjError = "";
		
			if($arrElementos[$i]["ig_tipoDato"]){
				switch ($arrElementos[$i]["ig_tipoDato"]){
					case 4:
						//PASS
						if($chkCambiarPass){
							if($pass){
								$error = checkString($post[$idCampo], $arrElementos[$i]["ig_min"], $arrElementos[$i]["ig_max"],$campo_error,$arrElementos[$i]["ig_requerido"]);
								if($error){
									$msjError.= $error;
									$mensaje.="* ".$msjError."<br/>";
								}
								else{
									$passRepetido = $post[$idCampo];
									if($passRepetido != $pass){
										$msjError.= $lang->message->password_distintos->__toString();
										$mensaje.="* ".$msjError."<br/>";
									
									}
								}
							}else{
								$error = checkString($post[$idCampo], $arrElementos[$i]["ig_min"], $arrElementos[$i]["ig_max"],$campo_error,$arrElementos[$i]["ig_requerido"]);
								if($error){
									$msjError.= $error;
									$mensaje.="* ".$msjError."<br/>";
								}else{
									$pass = $post[$idCampo];
								}
								
							}
						}
					break;
					default:
						$msjError = checkAll($arrElementos[$i], $post);
						if(!$msjError){
							$arrElementos[$i]["ig_value"] = escapear_string($arrElementos[$i]["ig_value"]);
						}
						else{
							$mensaje.="* ".$msjError."<br/> ";
						}
					break;
				}
			}
		}
   }
   
   //validar el formato del IP
	$msjError = '';			
	if(@trim($post["txtIpAutorizada"])!=""){ 
		$arr_ip = explode(',',trim($post["txtIpAutorizada"]));
		foreach($arr_ip as $ips){
			if(!preg_match('/^\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$/', $ips)){
				$msjError = $lang->message->msj_ip_autorizada->__toString();
			} 
		}
	}
	if($msjError){ $mensaje.="* ".$msjError."<br/> "; }
	
	$resp = array('mensaje' => $mensaje);
	return $resp;
}
?>