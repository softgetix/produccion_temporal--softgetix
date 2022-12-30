<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$filtro = (isset($_REQUEST["hidFiltro"]))?$_REQUEST["hidFiltro"]:"";
   	$filtro = trim($filtro);
   
	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
   	
   $perfilAllInOne = false;
   if(tienePerfil(17)){
	   	$perfilAllInOne = true;
   		$arrEntidades = ($filtro)? obtenerListadoClientes($objCliente, 'list', 0, $filtro) : array();
	}
	else{
		$txtFiltro = $filtro;
		if($_GET['viewAll']){
			$txtFiltro = 'getAllReg';
			$filtro = '';
		}
		$arrEntidades = obtenerListadoClientes($objCliente, 'list', 0, $txtFiltro);
		$cantRegistros = count($arrEntidades);
	}
	
   $extraCSS[]='css/estilosPopup.css';
   $extraJS[] ='js/popupHostFunciones.js';
   if($perfilAllInOne){// Vista Operador ADT //
		$extraJS[] ='js/abmClientesFunciones.js';
		$seccion = 'abmClientesAllInOne';  
   }
   $operacion = 'listar';
   $tipoBotonera='LI';
   require("includes/template.php");
}

function alta($objSQLServer, $seccion, $mensaje=""){
   	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
	
   	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	
   	if(in_array($_SESSION['idTipoEmpresa'],array(1,2))){//distribuidor/agente
		$agente=true;
		$arrEntidades[0]['cl_tipo']=2;
		$arrEntidades[0]['cl_id_distribuidor']=$_SESSION['idEmpresa'];
	}
	else if ($_SESSION['idTipoEmpresa'] == 3) {
		$agente=true;
		if (!isset($_GET['ag'])){
			$arrEntidades[0]['cl_tipo']=1;
			$arrEntidades[0]['cl_id_distribuidor']=$_SESSION['idEmpresa'];
		}else{
			$arrEntidades[0]['cl_tipo']=2;
			$arrEntidades[0]['cl_id_distribuidor']=$_GET['ag'];
		}
	}

   	$tipoEmpresa = intval($_SESSION['idTipoEmpresa']);
   	$arrDistribuidores = $objInterfazGenerica->obtenerDatosCombo('pa_obtenerDistribuidoresCombo '.$tipoEmpresa.'');
   	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   
   	$arrGrupoPerfil = $objPerfil->obtenerGrupoPerfiles();
	
	//-- Validar Cant. de Dadores Permitidos --//
	if($_SESSION['idTipoEmpresa'] == 1){
		$arrCliente = obtenerListadoClientes($objCliente, NULL, $_SESSION['idEmpresa'], NULL);
		$cantDadores = $objCliente->getCantDadores();
		if((int)$cantDadores >= (int)$arrCliente[0]['cl_cant_dadores']){
			$restringirAltaDador = true;
		}
	}
	//-- --//
	if(tienePerfil(19)){
		$arrIdiomas = getIdiomas();
	}
	
	$operacion = 'alta';
   	$tipoBotonera='AM';	
   	require("includes/template.php");
}

function modificar($objSQLServer, $seccion="", $mensaje="", $idCliente=0){
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
	
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
   	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	
	$id = (isset($_POST["chkId"]))? $_POST["chkId"][0]: (($idCliente)? $idCliente: 0);
	
    //PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$mPermitido = 0;
   	$arr_clientes = obtenerListadoClientes($objCliente, 'update');
	foreach($arr_clientes as $item){
		if($item['cl_id'] == $id){
			$mPermitido = 1;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////

	if (in_array($_SESSION['idTipoEmpresa'],array(3,1,2))){//distribuidor/agente
		$agente = true;
	}
   
    $arrDistribuidores = $objInterfazGenerica->obtenerDatosCombo('pa_obtenerDistribuidoresCombo 0');
	
	$arrEntidades = $objCliente->obtenerClientes($id);
	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   	
	$arrGrupoPerfil = $objPerfil->obtenerGrupoPerfiles();
	
	//-- Validar Cant. de Dadores Permitidos --//
	if($_SESSION['idTipoEmpresa'] == 1){
		$cantDadores = $objCliente->getCantDadores();
		$arrCliente = obtenerListadoClientes($objCliente, NULL, $_SESSION['idEmpresa'], NULL);
		if((int)$cantDadores >= (int)$arrCliente[0]['cl_cant_dadores']){
			if($arrEntidades[0]['cl_tipo_cliente'] != 1){
				$restringirAltaDador = true;
			}		
		}
	}
	//-- --//
	if(tienePerfil(19)){
		$arrIdiomas = getIdiomas();
	}
	
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function baja($objSQLServer, $seccion){
	global $lang;
	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
   
   	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_clientes = obtenerListadoClientes($objCliente, 'delete');
	
	foreach($arrCheks as $itemCheck){
		$mPermitido = 0;
		foreach($arr_clientes as $item){
			if($item['cl_id'] == $itemCheck){
				$mPermitido = 1;
			}
		}
		if($mPermitido == 0){
			break;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////
	
   $idClientes="";
   for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idClientes.=$arrCheks[$i];
		else $idClientes.=$arrCheks[$i].",";
   }
   if($idClientes){
	   	$result = $objCliente->eliminarCliente($idClientes);
	   	if($result === 'error_tiene_moviles'){
			$mensaje = $lang->message->msj_baja_clientes_moviles;
		}
		elseif($result){
   			$mensaje = $lang->message->ok->msj_baja;
   		}
		else{
   			$mensaje = $lang->message->error->msj_baja;
   		}
   }
   index($objSQLServer, $seccion, $mensaje);
}


function bajaAllInOne($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsClientes.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
 	$objCliente = new Cliente($objSQLServer);
   
   	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_clientes = obtenerListadoClientes($objCliente, 'delete');	
	foreach($arrCheks as $itemCheck){
		$mPermitido = 0;
		foreach($arr_clientes as $item){
			if($item['cl_id'] == $itemCheck){
				$mPermitido = 1;
			}
		}
		if($mPermitido == 0){
			break;
		}
	}
	/////////////////////////////////////////////////////////////////////////////////////////////
	
   $idClientes="";
   for($i=0;$i < count($arrCheks) && $arrCheks; $i++){
		if($i+1 == count($arrCheks))$idClientes.=$arrCheks[$i];
		else $idClientes.=$arrCheks[$i].",";
   }
   
   if($idClientes){
	   if($objCliente->eliminarClienteAllInOne($idClientes)){
			$mensaje = $lang->message->ok->msj_baja;
		}
		else{
			$mensaje = $lang->message->error->msj_baja;
		}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function habilitarAllInOne($objSQLServer, $seccion){
	global $lang;
	require_once 'clases/clsClientes.php';
   	$arrCheks = ($_POST["chkId"])?$_POST["chkId"]:0;
 	$objCliente = new Cliente($objSQLServer);
   	$idCliente = (int)$arrCheks[0];
	   
   if($idCliente){
		/////////////////////////////////////////////////////////////////////////////////////////////
		//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
		$arr_clientes = obtenerListadoClientes($objCliente, 'list',$idCliente);	
		foreach($arrCheks as $itemCheck){
			$mPermitido = 0;
			foreach($arr_clientes as $item){
				if($item['cl_id'] == $itemCheck){
					$mPermitido = 1;
				}
			}
			if($mPermitido == 0){
				break;
			}
		}
	
  
	   if($objCliente->habilitarClienteAllInOne($idCliente)){
			$mensaje = $lang->message->ok->procesar_datos;
		}
		else{
			$mensaje = $lang->message->error->procesar_datos;
		}
   }
   index($objSQLServer, $seccion, $mensaje);
}

function guardarA($objSQLServer, $seccion){
	global $lang;
		
	/* si, es una cabeceada lo se; tomas.- */
	if(!isset($_POST['cmbDistribuidor'])) {
		$_POST['cmbDistribuidor'] = 0;
	}

	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
   	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
	
   	$campos="";
   	$valorCampos="";
   	$mensaje="";
   for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo];
		
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
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
	if(isset($_POST["radHabilitado"])){
		if(!esVacio($_POST["radHabilitado"])){
	   	$campos.= "cl_habilitado";
	   	$valorCampos.= "''".$_POST["radHabilitado"]."''";
	   }
   }else{
   	$campos= substr($campos,0,(strlen($campos)-1));
   	$valorCampos= substr($valorCampos,0,(strlen($valorCampos)-1));
   }
   
   if ($_SESSION['idTipoEmpresa']!=3) {  // SI NO ES LOCALIZAR-T
	   	$campos .= ",cl_tipo";	
   		$valorCampos .= ",''2''";
   } else {
   		if (isset($_POST['cmbTipo'])) {
			if ($_POST['cmbTipo']!="") {
				$campos .= ",cl_tipo";
				$valorCampos .= ",''".$_POST['cmbTipo']."''";	
			}
		}
   }
   
   
   	if($_SESSION['idTipoEmpresa']!=3) {  // SI NO ES LOCALIZAR-T
	   	$campos .= ",cl_id_distribuidor";	
   		$valorCampos .= ",''".$_SESSION['idEmpresa']."''";
   	}
   	else{
   		if(isset($_POST['cmbDistribuidor'])) {
			if ($_POST['cmbDistribuidor']!="") {
				$campos .= ",cl_id_distribuidor";
				$valorCampos .= ",''".$_POST['cmbDistribuidor']."''";	
			} else {
				$campos .= ",cl_id_distribuidor";	
		   		$valorCampos .= ",''".$_SESSION['idEmpresa']."''";
			}
		} else {
			$campos .= ",cl_id_distribuidor";	
			$valorCampos .= ",''".$_SESSION['idEmpresa']."''";	
		}
   }
   
   if(isset($_POST['cmbTrasportistaFlete'])){
		 $campos .= ",cl_id_fletero";	
		 $valorCampos .= ",''".$_POST['cmbTrasportistaFlete']."''";	
	}
	
	if(isset($_POST['txtUrlAutorizada'])){
		 $campos .= ",cl_urlAutorizada";	
		 $valorCampos .= ",".(!empty($_POST['txtUrlAutorizada'])?"''".$_POST['txtUrlAutorizada']."''":'NULL');	
	}
	
	if(isset($_POST['cmbPaquete'])){
		if(!empty($_POST['cmbPaquete'])){
			$campos .= ",cl_paquete";	
			$valorCampos .= ",''".(int)$_POST['cmbPaquete']."''";	
		}
		else{
			$mensaje.= '<br>Seleccione el Paquete al que pertenece el Cliente';	
		}
	}
	
	if(isset($_POST['txtDadores'])){
		if(!empty($_POST['txtDadores']) && is_numeric($_POST['txtDadores'])){
			$campos .= ",cl_cant_dadores";	
			$valorCampos .= ",''".$_POST['txtDadores']."''";	
		}
		else{
			$mensaje.= '<br>Ingrese la cantidad de Dadores que podr&aacute; crear el Cliente';	
		}
	}
	
	if(tienePerfil(19)){
		if(!empty($_POST['cmbRegion'])){
			if($objCliente->validarIdiomaCliente($_POST['cmbProvincia'],$_POST['cmbRegion'])){
				$_POST['cmbRegion'] = NULL;
			}
			
			$campos .= ",cl_idioma_definida";	
			$valorCampos .= ",''".$_POST['cmbRegion']."''";	
		}
		else{
			$mensaje.= '<br>Seleccione el Idioma del Agente';	
		}	
	}

   //FIN FRAGMENTO
   if(!$mensaje){
   	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
	$campoValidador	= !empty($campoValidador)?("cl_razonSocial = '".$campoValidador."'"):$campoValidador;
   	if($objCliente->insertarRegistro($campos,$valorCampos,$campoValidador)){
   		$mensaje = $lang->message->ok->msj_alta;
   		index($objSQLServer, $seccion, $mensaje);
   	}else{
   		$mensaje = $lang->message->error->msj_alta;
   		alta($objSQLServer, $seccion, $mensaje);
   	}
	}else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion){
 	global $lang;
	/* si, es una cabeceada lo se; tomas.- */
	if(!isset($_POST['cmbDistribuidor'])) {
		$_POST['cmbDistribuidor'] = 0;
	}
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
   
   	$idCliente = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
   	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica($seccion);
   $mensaje="";
   $set="";
   for ($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]["ig_idCampo"];
		if($arrElementos[$i]["ig_validacionExistencia"]) $campoValidador = $_POST[$idCampo];
		
		$msjError = "";	
		$msjError = checkAll($arrElementos[$i], $_POST);
		if(!$msjError){
			$arrElementos[$i]["ig_value"] = $arrElementos[$i]["ig_value"];
		}
		else{
			$mensaje.="* ".$msjError."<br/> ";
		}
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$set.= $arrElementos[$i]["ig_value"]."=".(!empty($_POST[$idCampo])?"''".$_POST[$idCampo]."''":'NULL').",";
		//--
   }
   
   if ($_SESSION['idTipoEmpresa']==3) {  // SI ES LOCALIZAR-T
  		$set.= "cl_tipo = "."".$_POST['cmbTipo'].",";
   }
      
   if ($_SESSION['idTipoEmpresa']==3) {  // SI ES LOCALIZAR-T
  		//$set.= "cl_id_distribuidor = "."".$_POST['cmbDistribuidor'].",";
   }
   
   if(isset($_POST["radHabilitado"]) && !esVacio($_POST["radHabilitado"])){
   	$set.="cl_habilitado = ".$_POST["radHabilitado"];
   }
   else{
   	$set = substr($set,0,(strlen($set)-1));
   }
   
   $set.=", cl_id_fletero = ".(isset($_POST['cmbTrasportistaFlete'])?$_POST['cmbTrasportistaFlete']:0);
   $set.=", cl_urlAutorizada = ".(!empty($_POST['txtUrlAutorizada'])?"''".$_POST['txtUrlAutorizada']."''":'NULL');
   
   if(isset($_POST['cmbPaquete'])){
		if(!empty($_POST['cmbPaquete'])){
			$set.=", cl_paquete = ".(int)$_POST['cmbPaquete'];
		}
		else{
			$mensaje.= '<br>Seleccione el Paquete al que pertenece el Cliente';	
		}
	}
	
	if(isset($_POST['txtDadores'])){
		if(!empty($_POST['txtDadores']) && is_numeric($_POST['txtDadores'])){
			$set.=", cl_cant_dadores = ".(int)$_POST['txtDadores'];
		}
		else{
			$mensaje.= '<br>Ingrese la cantidad de Dadores que podr&aacute; crear el Cliente';	
		}
	}
	
	if(tienePerfil(19)){
		if(!empty($_POST['cmbRegion'])){
			if($objCliente->validarIdiomaCliente($_POST['cmbProvincia'],$_POST['cmbRegion'])){
				$_POST['cmbRegion'] = NULL;
			}
			$set.=", cl_idioma_definida = ''".$_POST['cmbRegion']."''";
		}
		else{
			$mensaje.= '<br>Seleccione el Idioma del Agente';	
		}	
	}
	
	//FIN FRAGMENTO
	if(!$mensaje){
		require_once 'clases/clsClientes.php';
		$objCliente = new Cliente($objSQLServer);
		$campoValidador	= !empty($campoValidador)?("cl_razonSocial = '".$campoValidador."'"):$campoValidador;
		$cod = $objCliente->modificarRegistro($set,$idCliente,$campoValidador);
	
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				modificar($objSQLServer, $seccion, $mensaje,$idCliente);
				break;
			case 1:
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
				break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				modificar($objSQLServer, $seccion, $mensaje,$idCliente);
				break;
		}
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje,$idCliente);
	}
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}

function obtenerListadoClientes($objCliente, $tipo, $idCliente = 0, $filtro = NULL){
	
	$idEmpresa = ($_SESSION["idTipoEmpresa"] < 3)?$_SESSION["idEmpresa"]:NULL;
	
	if($tipo == 'list'){
		$perfilAllInOne = false;
   		if(tienePerfil(17)){
			$perfilAllInOne = true;
		}
		$arrEntidades = $objCliente->obtenerClientes($idCliente,$filtro,$idEmpresa,$perfilAllInOne);
	}
	else{
		if($idCliente){
			$idEmpresa = NULL;
		}
		$arrEntidades = $objCliente->obtenerClientes($idCliente,'getAllReg',$idEmpresa);
	}
	
	return $arrEntidades;	
}

function export_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST["hidFiltro"]))?$_POST["hidFiltro"] : '');
	
	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
   
   	require_once 'clases/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	
	if(empty($txtFiltro)){
		$txtFiltro = 'getAllReg';
	}
	$arrEntidades = obtenerListadoClientes($objCliente, 'list', 0, $txtFiltro);
		
	
	$objPHPExcel->getProperties()
		->setCreator("Localizar-t")
		->setLastModifiedBy("Localizar-t")
		->setTitle($lang->menu->$seccion)
		->setSubject($lang->menu->$seccion)
		->setDescription($lang->menu->$seccion)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->razon_social)
		->setCellValue('B1',$lang->system->clave_tributaria)
		->setCellValue('C1',$lang->system->telefono)
		->setCellValue('D1',$lang->system->email)
		->setCellValue('E1',$lang->system->empresa)
		->setCellValue('F1',$lang->system->tipo_cliente)
		->setCellValue('G1',$lang->system->habilitado);
		
	$arralCol = array('A','B','C','D','E','F','G');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = array('B','C','E','F','G');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','D');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['cl_razonSocial']))
			->setCellValue('B'.$i, encode($row['cl_cuit']))
			->setCellValue('C'.$i, encode($row['cl_telefono']))
			->setCellValue('D'.$i, encode($row['cl_email']))
			->setCellValue('E'.$i, encode($row['distribuidor']))
			->setCellValue('F'.$i, ($row['cl_tipo'] == 3)?'Localizar-T':(($row['cl_tipo']==1)?'Agente':(($row['cl_tipo']==2)?'Cliente':$lang->system->otro)))
			->setCellValue('G'.$i, ($row['cl_habilitado'] == 1)?$lang->system->si:$lang->system->no);
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
