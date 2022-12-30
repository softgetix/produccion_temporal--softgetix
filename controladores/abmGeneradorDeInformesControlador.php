<?php
function index($objSQLServer, $seccion, $mensaje = "") {
    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    $operacion = 'listar';
	$filtro = isset($_POST['txtFiltro'])?trim($_POST['txtFiltro']):NULL;
	$filtroTipoEnvio = isset($_POST['cmbTipoEnvioList'])?trim($_POST['cmbTipoEnvioList']):NULL;
	$filtroAgente = isset($_POST['cmbAgenteList'])?trim($_POST['cmbAgenteList']):NULL;
	$tipoBotonera = 'LI';
	
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
	require_once 'clases/clsClientes.php';
    $objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();
	
	$arrFiltros = array();
	if($_POST){
		$arrFiltros['txtBuscador'] = $filtro;
		$arrFiltros['cmbTipoEnvio'] = $filtroTipoEnvio;
		$arrFiltros['cmbAgente'] = $filtroAgente;
	}	
	$arrInformes = $objGeneradorDeInformes->obtenerRegistros($arrFiltros);
	$arrTipoEnvio = $objGeneradorDeInformes->getTipoEnvio();
	
	require("includes/template.php");
}


function alta($objSQLServer, $seccion, $mensaje = "", $popup = false) {
  	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	$arrTipoEnvio = $objGeneradorDeInformes->getTipoEnvio();
	
	require_once 'clases/clsClientes.php';
    $objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();
	
	$extraJS[] = 'js/ckeditor-3.1/ckeditor.js';
	$operacion = 'alta';
	$tipoBotonera = 'AM';
	require("includes/template.php");
}


function modificar($objSQLServer, $seccion, $mensaje = "", $id = 0) {
  	$id = (isset($_POST['hidId']))?$_POST['hidId']:(($id)?$id:0);
	
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	$arrTipoEnvio = $objGeneradorDeInformes->getTipoEnvio();
	
	
	require_once 'clases/clsClientes.php';
    $objCliente = new Cliente($objSQLServer);
	
	$strSQL = " SELECT *, CONVERT(TEXT,in_consulta) AS in_consulta FROM tbl_informes WHERE in_id = ".(int)$id; 
	$objRes = $objSQLServer->dbQuery($strSQL);
	$informe = $objSQLServer->dbGetRow($objRes, 0, 3);
	
	$_POST['txtNombreInforme'] = $informe['in_nombre'];
	$_POST['cmbAgente'] = $informe['in_cl_id_agente'];
	$_POST['cmbCliente'] = $informe['in_cl_id_cliente'];
	$_POST['txtConsulta'] = $informe['in_consulta'];
	$_POST['cmbTipoEnvio'] = $informe['in_ite_id'];
	$_POST['cmbHoraEnvio'] = $informe['in_hora_envio'];
	$_POST['txtSubject'] = $informe['in_subject'];
	$_POST['txtMensaje'] = $informe['in_mensaje'];
	$_POST['txtEnviarA'] = $informe['in_enviar_a_txt'];
	$_POST['checkEnviarA'] = $informe['in_enviar_a_us_id'];
	$_POST['txtEnviarCopiaA'] = $informe['in_enviar_copia_a'];
	$_POST['checkEnviarAdjunto'] = $informe['in_adjunto'];
	$_POST['checkGuardarCopia'] = $informe['in_guardar_copia'];
	
	$arrAgente = $objCliente->obtenerAgentes2();
	
	$extraJS[] = 'js/ckeditor-3.1/ckeditor.js';
	$operacion = 'modificar';
    $tipoBotonera = 'AM';
    require("includes/template.php");
}

function generarAdjunto($objSQLServer, $seccion){
	if(!empty($_POST['txtConsulta'])){
		require_once 'clases/clsGeneradorDeInformes.php';
    	$objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
		$objGeneradorDeInformes->generarAdjunto($_POST['txtConsulta']);
		exit;
	}
	else{
		$mensaje = 'Debe definir la consulta para obtener un archivo adjunto.';	
	}
	
	require_once 'clases/clsClientes.php';
    $objCliente = new Cliente($objSQLServer);
	$arrAgente = $objCliente->obtenerAgentes2();
	
	
	$noError = true;
	$extraJS[] = 'js/ckeditor-3.1/ckeditor.js';
	$operacion = $_POST['hidId']?'alta':'modificar';
    $tipoBotonera = 'AM';
    require("includes/template.php");
}

function duplicarInforme($objSQLServer, $seccion){
	$id = (isset($_POST['hidId']))?(int)$_POST['hidId']:0;
	
	require_once 'clases/clsGeneradorDeInformes.php';
    $objGeneradorDeInformes = new GeneradorDeInformes($objSQLServer);
	
	if($objGeneradorDeInformes->duplicarRegistros($id)){
		$mensaje = 'El informe se duplico con éxito!';		
	}
	else{
		$mensaje = 'Algo fallo, y el informe no se pudo duplicar.';	
	}
	index($objSQLServer, $seccion, $mensaje);
}

function baja($objSQLServer, $seccion) {
	global $lang;
	$id = (isset($_POST['hidId']))?$_POST['hidId']:(($id)?$id:0);
	
	$arr['in_borrado'] = 1;
	if ($objSQLServer->dbQueryUpdate($arr, 'tbl_informes', 'in_id in('.$id.')')){
		$mensaje = $lang->message->ok->msj_baja;
	}
	else{
		$mensaje = $lang->message->error->msj_baja;
	}
		
	index($objSQLServer, $seccion, $mensaje);	
}

function guardarA($objSQLServer, $seccion) {
	global $lang;
	
	$resp = validarCampos('alta');
	$mensaje = $resp['mensaje'];
	$arr  = $resp['arr'];
		
	if(!$mensaje){
        if ($id = $objSQLServer->dbQueryInsert($arr, 'tbl_informes')){
			$mensaje = $lang->message->ok->msj_alta;
		}
		else{
			$mensaje = $lang->message->error->msj_alta;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		alta($objSQLServer, $seccion, $mensaje);
	}
}

function guardarM($objSQLServer, $seccion) { 
   global $lang;
	$id = (isset($_POST["hidId"])) ? $_POST["hidId"] : "";
	$resp = validarCampos('alta');
	$mensaje = $resp['mensaje'];
	$arr  = $resp['arr'];
	
	if(!$mensaje){
        if ($objSQLServer->dbQueryUpdate($arr, 'tbl_informes', 'in_id = '.(int)$id)){
			$mensaje = $lang->message->ok->msj_modificar;
		}
		else{
			$mensaje = $lang->message->error->msj_modificar;
		}
		index($objSQLServer, $seccion, $mensaje);
	}
	else{
		modificar($objSQLServer, $seccion, $mensaje, $id);
	}
}

function volver($objSQLServer, $seccion) {
    index($objSQLServer, $seccion);
}

function validarCampos($operacion){
	global $lang;
	$mensaje = '';
	$arr = array();
	
	$arr['in_nombre'] = trim($_POST['txtNombreInforme']);	
	$msjError = checkString($_POST['txtNombreInforme'], 0, 50 ,'Nombre del Informe',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_cl_id_agente'] = trim($_POST['cmbAgente']);
	$msjError = checkCombo(trim($_POST['cmbAgente']), 'Agente', 1, 0);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_cl_id_cliente'] = trim($_POST['cmbCliente'])?trim($_POST['cmbCliente']):NULL;
	
	$arr['in_consulta'] = trim($_POST['txtConsulta']);	
	$msjError = checkString($_POST['txtConsulta'], 0, 9999999 ,'Consulta SQL',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_ite_id'] =(int)$_POST['cmbTipoEnvio'];
	$msjError = checkCombo(trim($_POST['cmbTipoEnvio']), 'Tipo de Envio', 1, 0);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_hora_envio'] = trim($_POST['cmbHoraEnvio']);
	$msjError = checkCombo(strlen(trim($_POST['cmbHoraEnvio'])), 'Hora de Envio', 1, 0);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_subject'] = trim($_POST['txtSubject']);	
	$msjError = checkString($_POST['txtSubject'], 0, 100 ,'Asunto del E-Mail a enviar',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_mensaje'] = trim($_POST['txtMensaje']);	
	$msjError = checkString($_POST['txtMensaje'], 0, 9999999 ,'Mensaje del E-Mail a enviar',1);
	if($msjError){ 
		$mensaje.="* ".$msjError."<br/> ";
	}
	
	$arr['in_enviar_a_txt'] = !empty($_POST['txtEnviarA'])?str_replace(';',',',$_POST['txtEnviarA']):NULL;
	
	if(!is_array($_POST['checkEnviarA'])){
		$msjError = checkString($_POST['txtEnviarA'], 0, 9999999 ,'Enviar a / Destinatarios',1);
		if($msjError){ 
			$mensaje.="* ".$msjError."<br/> ";
		}	
	}
	
	$arr['in_enviar_a_us_id'] = is_array($_POST['checkEnviarA'])?implode(',',$_POST['checkEnviarA']):NULL;
	
	$arr['in_enviar_copia_a'] = !empty($_POST['txtEnviarCopiaA'])?str_replace(';',',',$_POST['txtEnviarCopiaA']):NULL;
	
	$arr['in_adjunto'] = $_POST['checkEnviarAdjunto']?1:0;
	
	$arr['in_adjunto_name'] = $arr['in_adjunto']?getNombreAdjunto($_POST):NULL;
	
	$arr['in_guardar_copia'] = $_POST['checkGuardarCopia']?1:0;
	
	$resp = array('mensaje' => $mensaje, 'arr' => $arr);
	return $resp;
}

function getNombreAdjunto($post){
	$texto =  substr(trim(strtolower($post['txtSubject'])),0,25).'GUIONMEDIO'.'A'.$post['cmbAgente'].($post['cmbCliente']?'C'.$post['cmbCliente']:'').'GUIONMEDIO'.$post['cmbTipoEnvio'];
	$texto =  ereg_replace("[^A-Za-z0-9]", "", $texto); //preg_replace('([^A-Za-z0-9])', '', $texto);
	$texto =  str_replace('GUIONMEDIO','-',$texto);
	return $texto;
}
