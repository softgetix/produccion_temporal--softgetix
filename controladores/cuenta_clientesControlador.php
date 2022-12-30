<?php
//$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function listado($objSQLServer, $seccion, $mensaje=""){
	
	$filtro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
   	
   	$txtFiltro = $filtro;
	if($_GET['viewAll']){
		$txtFiltro = 'getAllReg';
		$filtro = '';
	}
	
	global $arrEntidades;
	$arrEntidades = obtenerListadoClientes($objCliente, 'list', 0, $txtFiltro);
	
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
	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmClientes');
	
   	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	
   	if(in_array($_SESSION['idTipoEmpresa'],array(1,2))){//distribuidor/agente
		$arrEntidades[0]['cl_tipo']=2;
		$arrEntidades[0]['cl_id_distribuidor']=$_SESSION['idEmpresa'];
	}
	else if ($_SESSION['idTipoEmpresa'] == 3) {
		$arrEntidades[0]['cl_tipo']=1;
		$arrEntidades[0]['cl_id_distribuidor']=$_SESSION['idEmpresa'];
	}

	//-- Definidos tipos de Clientes --//
	if(!tienePerfil(19)){
		foreach($arrElementos as $k => $items){
			if($items['ig_value'] == 'cl_tipo_cliente'){
				$arrElementos[$k]['ic_store'] = 'SELECT tc_id AS id, tc_nombre AS dato FROM tbl_tipo_cliente WHERE tc_borrado = 0 AND tc_id = 2 ';
				break;
			}	
		}
	}
	//-- --//
	
   	//-- Validar Cant. de Dadores Permitidos --//
	/*if($_SESSION['idTipoEmpresa'] == 1){
		$arrCliente = obtenerListadoClientes($objCliente, NULL, $_SESSION['idEmpresa'], NULL);
		$cantDadores = $objCliente->getCantDadores();
		if((int)$cantDadores >= (int)$arrCliente[0]['cl_cant_dadores']){
			$restringirAltaDador = true;//NO PERMITIR EL ALTA DE DADORES.... PARA ESO SE PODRIA RESTRINGIR CON LA SQL DEL IC_STORE..
		}
	}*/
	//-- --//
	
	if(tienePerfil(19)){
		$arrGrupoPerfil = $objPerfil->obtenerGrupoPerfiles();
		$arrIdiomas = getIdiomas();
		
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
		require_once("clases/clsDefinicionReportes.php");
		$objEventos = new DefinicionReporte($objSQLServer);
		$arrEventos = $objEventos->obtenerEventosCombo();
		
		$arrMovilesUsuario = array();
		foreach($arrEventos as $k => $item){
			$dato = 'evento_'.(int)$item['id'];
			$dato = $lang->system->evento.' '.$item['id'].': '.($eventos->$dato->__toString()?$eventos->$dato->__toString():$eventos->default->__toString());
							
			if($item['id'] == 14 || $item['id'] == 15){
				array_push($arrMovilesUsuario,array('id'=>$item['id'],'dato'=>$dato));
				unset($arrEventos[$k]);
			}
			else{
				$arrEventos[$k]['dato'] = $dato;
			}
		}
	}
	
	$extraJS[]='js/boxes.js';
	$operacion = 'alta';
   	$tipoBotonera='AM';	
   	require("includes/template.php");
}

function solapaModificar($objSQLServer, $seccion="", $mensaje="", $id=0){
	global $solapa;
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:($id?$id:0);
	
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
   	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
	
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

	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmClientes');
	
	//-- Definidos tipos de Clientes --//
	if(!tienePerfil(19)){
		foreach($arrElementos as $k => $items){
			if($items['ig_value'] == 'cl_tipo_cliente'){
				$arrElementos[$k]['ic_store'] = 'SELECT tc_id AS id, tc_nombre AS dato FROM tbl_tipo_cliente WHERE tc_borrado = 0 AND tc_id = 2 ';
				break;
			}	
		}
	}
	//-- --//
	
	$arrEntidades = $objCliente->obtenerClientes($id);
	
	//-- Validar Cant. de Dadores Permitidos --//
	/*if($_SESSION['idTipoEmpresa'] == 1){
		$cantDadores = $objCliente->getCantDadores();
		$arrCliente = obtenerListadoClientes($objCliente, NULL, $_SESSION['idEmpresa'], NULL);
		if((int)$cantDadores >= (int)$arrCliente[0]['cl_cant_dadores']){
			if($arrEntidades[0]['cl_tipo_cliente'] != 1){
				$restringirAltaDador = true; //NO PERMITIR EL ALTA DE DADORES.... PARA ESO SE PODRIA RESTRINGIR CON LA SQL DEL IC_STORE..
			}		
		}
	}*/
	//-- --//
	
	if(tienePerfil(19)){
		$arrGrupoPerfil = $objPerfil->obtenerGrupoPerfiles();
		$arrIdiomas = getIdiomas();
		
		require_once 'clases/clsIdiomas.php';
		$objIdioma = new Idioma();
		$eventos = $objIdioma->getEventos($_SESSION['idioma']);
		
		require_once("clases/clsDefinicionReportes.php");
		$objEventos = new DefinicionReporte($objSQLServer);
		$arrEventos = $objEventos->obtenerEventosCombo();
		
		$arrMovilesUsuario = $objEventos->obtenerEventosAsignados($id);
		foreach($arrEventos as $k => $item){
			$dato = 'evento_'.(int)$item['id'];
			$dato = $lang->system->evento.' '.$item['id'].': '.($eventos->$dato->__toString()?$eventos->$dato->__toString():$eventos->default->__toString());

			$arrEventos[$k]['dato'] = $dato;			
			foreach($arrMovilesUsuario as $km => $item_active){
				if($item['id'] == $item_active['id']){
					$arrMovilesUsuario[$km]['dato'] = $dato;
					unset($arrEventos[$k]);
				}	
			}
		}
	}
	
	$extraJS[]='js/boxes.js';
	$operacion = 'modificar';
   	$tipoBotonera='AM';
   	require("includes/template.php");
}

function solapaBaja($objSQLServer, $seccion){
	global $lang;
	$idCliente = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsClientes.php';
   	$objCliente = new Cliente($objSQLServer);
   
   	/////////////////////////////////////////////////////////////////////////////////////////////
	//PROTECCIÓN CONTRA INYECCION JS en la función enviarModificación
	$arr_clientes = obtenerListadoClientes($objCliente, 'delete');
	
	$mPermitido = 0;
	foreach($arr_clientes as $item){
		if($item['cl_id'] == $idCliente){
			$mPermitido = 1;
			break;
		}
	}
	validarModificar($mPermitido,$objSQLServer);
	/////////////////////////////////////////////////////////////////////////////////////////////
	
   if($idCliente){
	   	$result = $objCliente->eliminarCliente($idCliente);
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


function solapaGuardarA($objSQLServer, $seccion){
	global $lang;
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
   	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmClientes');
	
   	$mensaje = $campos = $valorCampos = $coma = "";
	for($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo= $arrElementos[$i]['ig_idCampo'];
		if($arrElementos[$i]['ig_validacionExistencia']) $campoValidador = $_POST[$idCampo];
		
		$msjError = "";		
		$msjError = checkAll($arrElementos[$i], $_POST);
		if($msjError){
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$campos.= $coma.$arrElementos[$i]["ig_value"];
		$valorCampos.= $coma."''".$_POST[$idCampo]."''";
		$coma = ', ';
		//--
	}
	
	$campos.= ", cl_habilitado";
	$valorCampos.= ", ''1''";
	
	
	if ($_SESSION['idTipoEmpresa'] != 3) {  // SI NO ES LOCALIZAR-T
	   	$campos.= ", cl_tipo";	
   		$valorCampos.= ", ''2''";
		
		$campos.= ",cl_id_distribuidor";	
   		$valorCampos.= ",''".$_SESSION['idEmpresa']."''";
   	}
	else{
   		if(isset($_POST['cmbTipo'])) {
			if ($_POST['cmbTipo']!="") {
				$campos .= ", cl_tipo";
				$valorCampos .= ", ''".$_POST['cmbTipo']."''";	
			}
		}
		
		if($_POST['cmbDistribuidor']!="") {
			$campos .= ",cl_id_distribuidor";
			$valorCampos .= ",''".$_POST['cmbDistribuidor']."''";	
		}
		else {
			$campos .= ",cl_id_distribuidor";	
			$valorCampos .= ",''".$_SESSION['idEmpresa']."''";
		}
	}
  
	if(tienePerfil(19)){
   	
		if(isset($_POST['txtUrlAutorizada'])){
			$campos .= ",cl_urlAutorizada";	
		 	$valorCampos .= ",".(!empty($_POST['txtUrlAutorizada'])?"''".$_POST['txtUrlAutorizada']."''":'NULL');	
		}
	
		if(!empty($_POST['cmbPaquete'])){
			$campos .= ",cl_paquete";	
			$valorCampos .= ",''".(int)$_POST['cmbPaquete']."''";	
		}
		else{
			$mensaje.= '<br>Seleccione el Paquete al que pertenece el Cliente';	
		}
	
		if(!empty($_POST['txtDadores']) && is_numeric($_POST['txtDadores'])){
			$campos .= ",cl_cant_dadores";	
			$valorCampos .= ",''".$_POST['txtDadores']."''";	
		}
		else{
			$mensaje.= '<br>Ingrese la cantidad de Dadores que podr&aacute; crear el Cliente';	
		}
	
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
		
		if(empty($_POST['cmbEventosAsignados'])){
			$mensaje.= '<br>Asigne los eventos correspondientes al agente';		
		}	
	}

	
   //FIN FRAGMENTO
	if(!$mensaje){
		$campoValidador	= !empty($campoValidador)?("cl_razonSocial = '".$campoValidador."'"):$campoValidador;
   		$idAgente = $objCliente->insertarRegistro($campos,$valorCampos,$campoValidador);
		if($idAgente){
			$objCliente->insertarRelAgentesEventos($idAgente, $_POST['cmbEventosAsignados']);
			
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
	$idCliente = (isset($_POST["hidId"]))? $_POST["hidId"]:"";
	
	require_once 'clases/clsClientes.php';
	$objCliente = new Cliente($objSQLServer);
   	
	require_once 'clases/clsInterfazGenerica.php';
  	$objInterfazGenerica = new InterfazGenerica($objSQLServer);
  	$arrElementos = $objInterfazGenerica->obtenerInterfazGrafica('abmClientes');
   	
	$mensaje = $set = $coma =  "";
   	for($i=0;$i < count($arrElementos) && $arrElementos;$i++){
		$idCampo = $arrElementos[$i]['ig_idCampo'];
		if($arrElementos[$i]['ig_validacionExistencia']) $campoValidador = $_POST[$idCampo];
		
		$msjError = "";	
		$msjError = checkAll($arrElementos[$i], $_POST);
		if($msjError){
			$mensaje.="* ".$msjError."<br/> ";
		}
		
		//SERIALIZACION DE DATOS Y CAMPOS PARA ENVIAR AL STORE
		$set.= $coma.$arrElementos[$i]['ig_value'].'='.(!empty($_POST[$idCampo])?"''".$_POST[$idCampo]."''":'NULL');
		$coma = ', ';
		//--
	}
   
	if ($_SESSION['idTipoEmpresa'] == 3) {  // SI ES LOCALIZAR-T
  		$set.= ", cl_tipo = "."".$_POST['cmbTipo'];
   	}
   
	if(tienePerfil(19)){
		if(isset($_POST["radHabilitado"]) && !esVacio($_POST["radHabilitado"])){
   			$set.=", cl_habilitado = ".$_POST["radHabilitado"];
   		}
		
		$set.=", cl_urlAutorizada = ".(!empty($_POST['txtUrlAutorizada'])?"''".$_POST['txtUrlAutorizada']."''":'NULL');
		
		if(!empty($_POST['cmbPaquete'])){
			$set.=", cl_paquete = ".(int)$_POST['cmbPaquete'];
		}
		else{
			$mensaje.= '<br>Seleccione el Paquete al que pertenece el Cliente';	
		}
		
		if(!empty($_POST['txtDadores']) && is_numeric($_POST['txtDadores'])){
			$set.=", cl_cant_dadores = ".(int)$_POST['txtDadores'];
		}
		else{
			$mensaje.= '<br>Ingrese la cantidad de Dadores que podr&aacute; crear el Cliente';	
		}
	
		if(!empty($_POST['cmbRegion'])){
			if($objCliente->validarIdiomaCliente($_POST['cmbProvincia'],$_POST['cmbRegion'])){
				$_POST['cmbRegion'] = NULL;
			}
			$set.=", cl_idioma_definida = ''".$_POST['cmbRegion']."''";
		}
		else{
			$mensaje.= '<br>Seleccione el Idioma del Agente';	
		}
		
		if(empty($_POST['cmbEventosAsignados'])){
			$mensaje.= '<br>Asigne los eventos correspondientes al agente';		
		}	
	}
	
	//FIN FRAGMENTO
	if(!$mensaje){
		$campoValidador	= !empty($campoValidador)?("cl_razonSocial = '".$campoValidador."'"):$campoValidador;
		$cod = $objCliente->modificarRegistro($set,$idCliente,$campoValidador);
	
		switch($cod){
			case 0:
				$mensaje = $lang->message->interfaz_generica->msj_modificar_existe;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idCliente);
			break;
			case 1:
				$objCliente->modificarRelAgentesEventos($idCliente, $_POST['cmbEventosAsignados']);
			
				$mensaje = $lang->message->ok->msj_modificar;
				index($objSQLServer, $seccion, $mensaje);
			break;
			case 2:
				$mensaje = $lang->message->error->msj_modificar;
				solapaModificar($objSQLServer, $seccion, $mensaje,$idCliente);
			break;
		}
	}
	else{
		solapaModificar($objSQLServer, $seccion, $mensaje,$idCliente);
	}
}

function solapaExportar_xls($objSQLServer, $seccion){
	global $lang;
	$txtFiltro = trim((isset($_POST['txtFiltro']))?$_POST['txtFiltro']:NULL);
	
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
		->setTitle($lang->menu->abmClientes)
		->setSubject($lang->menu->abmClientes)
		->setDescription($lang->menu->abmClientes)
		->setKeywords("Excel Office 2007 openxml php")
		->setCategory("Localizar-t");
	
        $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',$lang->system->razon_social)
		->setCellValue('B1',$lang->system->telefono)
		->setCellValue('C1',$lang->system->direccion);
                if(!tienePerfil(array(9,10,11,12))){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1',$lang->system->habilitado);
                }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1',$lang->system->estado);
        
	$arralCol = (!tienePerfil(array(9,10,11,12)))?array('A','B','C','D','E'):array('A','B','C','E');
	$objPHPExcel->setFormatoRows($arralCol);
	$alingCenterCol = (!tienePerfil(array(9,10,11,12)))?array('B','D','E'):array('B','E');
	$objPHPExcel->alignCenter($alingCenterCol);
	$alingLeftCol = array('A','C');
	$objPHPExcel->alignLeft($alingLeftCol);
	
	$i = 2;
	foreach($arrEntidades as $row){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, encode($row['cl_razonSocial']))
			->setCellValue('B'.$i, encode($row['cl_telefono']))
			->setCellValue('C'.$i, encode(trim($row['cl_direccion'].' '.$row['cl_direccion_nro'])));
		if(!tienePerfil(array(9,10,11,12))){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, ($row['cl_habilitado']?$lang->system->si:$lang->system->no));
                }    
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, ($row['cl_borrado']?$lang->system->registro_baja:$lang->system->registro_activo));
                
		$i++;	
	}
        
	$objPHPExcel->getActiveSheet()->setTitle(''.$lang->menu->abmClientes);
	$objPHPExcel->setActiveSheetIndex(0);
	
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
	header('Content-Type: application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.strtolower($lang->menu->abmClientes).'-'.getFechaServer('d').getFechaServer('m').getFechaServer('Y').'.xlsx"');
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
function obtenerListadoClientes($objCliente, $tipo, $idCliente = 0, $filtro = NULL){
	$objCliente->allData = true;
	$idEmpresa = ($_SESSION["idTipoEmpresa"] < 3)?$_SESSION["idEmpresa"]:NULL;
	
	if($tipo == 'list'){
		$arrEntidades = $objCliente->obtenerClientes($idCliente,$filtro,$idEmpresa);
	}
	else{
		if($idCliente){
			$idEmpresa = NULL;
		}
		$arrEntidades = $objCliente->obtenerClientes($idCliente,'getAllReg',$idEmpresa);
	}
	
	return $arrEntidades;	
}
?>
