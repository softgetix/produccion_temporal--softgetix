<?php
//$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function listado($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsConductores.php';
  	$objConductor = new Conductor($objSQLServer);
   	
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
   	$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	
	global $arrEntidades;
	$arrEntidades = $objConductor->obtenerConductores(0,$txtFiltro);
   	
	/*
	$operacion = 'listar';
    $tipoBotonera = 'LI';
    require("includes/template.php");
    */
}

function solapaAlta($objSQLServer, $seccion, $mensaje=""){
   	global $solapa;
	global $lang;
		
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmConductores');
	
	  $operacion = 'alta';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function solapaModificar($objSQLServer, $seccion="", $mensaje="", $id=0){
	global $solapa;
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmConductores');
	
	require_once 'clases/clsConductores.php';
	$objConductor = new Conductor($objSQLServer);

   	$arrEntidades = $objConductor->obtenerConductores($id);
   	
	//-------Ver combo Moviles -------//
	if(tienePerfil(array(9,10,13,19)) ){
		require_once 'clases/clsMoviles.php';
		$objMovil = new Movil($objSQLServer);
		$arrMovilesCombo = $objMovil->obtenerRegistros(0, 'getAllReg',NULL,NULL,FALSE,$arrEntidades[0]['co_cl_id']);
		$movilAsoc = $objMovil->obtenerMovilAsignadoAlConductor($id);		
		$movilAsoc = $movilAsoc['mo_id'];
	}
	//--------------------------------//		
	
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function solapaBaja($objSQLServer, $seccion){
	global $lang;
	$idConductor = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	if($idConductor){
   		require_once 'clases/clsConductores.php';
   		$objConductor = new Conductor($objSQLServer);
	
		if($objConductor->eliminarRegistro($idConductor)){
   			$mensaje = $lang->message->ok->msj_baja;		
   		}else{
   			$mensaje = $lang->message->error->msj_baja;
   		}
   	}
   	index($objSQLServer, $seccion, $mensaje);
}


function solapaGuardarA($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmConductores');
   	$campos="";
   	$valorCampos="";
   	$mensaje="";
   	for ($i=0; $i < count($arrElementos) && $arrElementos; $i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];

		//--Ini. Quitar caracteres especiales
		$_POST[$idCampo] = sanear_string(encode($_POST[$idCampo]), false);
		//--Fin. Quitar caracteres especiales

		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = "{$arrElementos[$i]['ig_value']} = '{$_POST[$idCampo]}' ";
		
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);

		if(!$msjError && $idCampo == 'txtTelefono'){
			$nombreCampo = $lang->system->$arrElementos[$i]["ig_nombre"]?$lang->system->$arrElementos[$i]["ig_nombre"]:'**'.$arrElementos[$i]["ig_nombre"];
			$validPhone = validarTelefono('txtTelefono', $nombreCampo);
			if($validPhone){
				$mensaje.= $validPhone;
			}
		}
		elseif(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$campos.= $arrElementos[$i]["ig_value"].","; 			
		$valorCampos.= "''".$_POST[$idCampo]."'',";	
		//--
   	}
	
   	$campos = substr($campos,0,(strlen($campos)-1));
   	$valorCampos = substr($valorCampos,0,(strlen($valorCampos)-1));
	
	//-------Ver combo Moviles -------//
	if(tienePerfil(array(9,10,13,19)) ){
		if(empty($_POST['cmbMovilAsignado'])){
			$mensaje = "* ".$lang->message->msj_asociar_movil;
		}
	}
   //-------------------------------//
   
   
   //FIN FRAGMENTO
	if(!$mensaje){
		require_once 'clases/clsConductores.php';
		$objConductor = new Conductor($objSQLServer);
		if($objConductor->insertarRegistro($campos,$valorCampos, $campoValidador)){
			//------------//
			if(tienePerfil(array(9,10,13,19))  && !empty($_POST['cmbMovilAsignado'])){   
				require_once 'clases/clsMoviles.php';
				$objMovil = new Movil($objSQLServer);			
				$objMovil->guardarConductorEmpresa($_POST['cmbMovilAsignado'], $objConductor->obtenerUltimoId(), $_POST['cmbCliente']);
			}
			//------------//
			$mensaje = $lang->message->ok->msj_alta;
			index($objSQLServer, $seccion, $mensaje);
		}
		else{
			//$mensaje = $lang->message->error->msj_alta;
			$mensaje = 'El DNI ingresado está asociado a otro conductor ingresado previamente.';
			solapaAlta($objSQLServer, $seccion, $mensaje);
		}
	}
	else{
		solapaAlta($objSQLServer, $seccion, $mensaje);
	}
}

function solapaGuardarM($objSQLServer, $seccion){
 	global $lang;
	$idConductor = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmConductores');
   	$mensaje="";
   	$set="";
   	for($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];

		//--Ini. Quitar caracteres especiales
		$_POST[$idCampo] = sanear_string(encode($_POST[$idCampo]), false);
		//--Fin. Quitar caracteres especiales

		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = "{$arrElementos[$i]['ig_value']} = '{$_POST[$idCampo]}' ";
		
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		
		if(!$msjError && $idCampo == 'txtTelefono'){
			$nombreCampo = $lang->system->$arrElementos[$i]["ig_nombre"]?$lang->system->$arrElementos[$i]["ig_nombre"]:'**'.$arrElementos[$i]["ig_nombre"];
			$validPhone = validarTelefono('txtTelefono', $nombreCampo);
			if($validPhone){
				$mensaje.= $validPhone;
			}
		}
		elseif(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$set.= $arrElementos[$i]["ig_value"]."="."''".$_POST[$idCampo]."'',"; 			
		//--
   	}
   	$set = substr($set,0,(strlen($set)-1));	

	//-------Ver combo Moviles -------//
	if(tienePerfil(array(9,10,13,19)) ){
		if(empty($_POST['cmbMovilAsignado'])){
			$mensaje = "* ".$lang->message->msj_asociar_movil;
		}
	}
   	//------------------------------//
	
   	//FIN FRAGMENTO
   	if(!$mensaje){
		require_once 'clases/clsConductores.php';
		$objConductor = new Conductor($objSQLServer);
		$cod = $objConductor->modificarRegistro($set, $idConductor, $campoValidador);
		switch($cod){
			case 0:
				//$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				$mensaje = 'El DNI ingresado está asociado a otro conductor ingresado previamente.';
				solapaModificar($objSQLServer, $seccion, $mensaje,$idConductor);	
			break;
			case 1:
				//------------//			
				if(tienePerfil(array(9,10,13,19))  && !empty($_POST['cmbMovilAsignado'])){
					require_once 'clases/clsMoviles.php';
					$objMovil = new Movil($objSQLServer);				
					$objMovil->limpiarConductorEmpresa($_POST['hidId']);
					$objMovil->guardarConductorEmpresa($_POST['cmbMovilAsignado'], $_POST['hidId'], $_POST['cmbCliente']);
				}
				//------------//
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idConductor);	
			break;
		}
	}
	else{
		solapaModificar($objSQLServer, $seccion, $mensaje,$idConductor);
	}
}

function validarTelefono($idCampo, $nombreCampo){
	$mensaje = null;
	if(!empty($_POST[$idCampo])){
		switch($_SESSION['idPais']){
			case 1:
				require_once 'clases/clsPhoneNumberAr.php';
				$objPhone = new PhoneNumberAr($_POST[$idCampo], $_POST[$idCampo]);
				$validPhone = $objPhone->validateGuestNumber();
				if(empty($validPhone['guest_number_e164'])){
					$mensaje ="* El valor ingresado en {$nombreCampo} no es válido para Argentina.<br> Verifique si el número contiene un Número de área válido.<br>";
				}
				else{
					$_POST[$idCampo] = $validPhone['guest_number_e164'];
				}
			break;
			case 4:
				require_once 'clases/clsPhoneNumberUy.php';
				$objPhone = new PhoneNumberUy($_POST[$idCampo], $_POST[$idCampo]);
				$validPhone = $objPhone->validateGuestNumber();
				if(empty($validPhone['guest_number_e164'])){
					$mensaje ="* El valor ingresado en {$nombreCampo} no es válido para Uruguay<br/> ";
				}
				else{
					$_POST[$idCampo] = $validPhone['guest_number_e164'];
				}	
			break;
		}
	}
	
	return $mensaje;
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsConductores.php';
  	$objConductor = new Conductor($objSQLServer);
    
	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	
	$arrEntidades = $objConductor->obtenerConductores(0,$txtFiltro);
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->abmConductores)
		->setSubject($lang->menu->abmConductores)
		->setDescription($lang->menu->abmConductores)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
        $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->nombre)
		->setCellValue('B1',$lang->system->apellido)
		->setCellValue('C1',$lang->system->telefono)
		->setCellValue('D1',$lang->system->empresa)
		->setCellValue('E1',$lang->system->moviles_asignados)
                ->setCellValue('F1',$lang->system->estado);
		
	$arralCol = array('A','B','C','D','E','F');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('C','E','F');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','B','D');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['co_nombre']))
			->setCellValue('B'.$i, encode($row['co_apellido']))
			->setCellValue('C'.$i, encode($row['co_telefono']))
			->setCellValue('D'.$i, encode($row['razon_social']))
			->setCellValue('E'.$i, encode($row['movil_1'].((!empty($row['movil_1']) && !empty($row['movil_2']))?', ':'').$row['movil_2']))
                        ->setCellValue('F'.$i, ($row['co_borrado']?$lang->system->registro_baja:$lang->system->registro_activo));
		$i++;	
	}
        
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->abmConductores);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->abmConductores).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
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
?>
