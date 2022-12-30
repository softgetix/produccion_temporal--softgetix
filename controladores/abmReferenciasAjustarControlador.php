<?php
$operacion = (isset($_POST["hidOperacion"])) ? $_POST["hidOperacion"] : "";

function index($objSQLServer, $seccion, $mensaje = "") {
	$filtro = NULL;
	if($_POST['txtFiltro']){
		$filtro = $_POST['txtFiltro'];
	}
	
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$ajustar = $objReferencia->getReferenciasAjustar(NULL, $filtro);
	
	$operacion = 'listar';
	$tipoBotonera='LI';
	require("includes/template.php");
}

function ajustar($objSQLServer, $seccion = ""){
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$id = (int)$_POST['hidId'];
	$ajustar = $objReferencia->getReferenciasAjustar($id);
	$coord = $objReferencia->obtenerCoordenadas($ajustar['iar_re_id']);
	$coord = $coord[0];
	
	$extraJS[] = 'js/openLayers/OpenLayers.js';
	$extraJS[] = 'js/defaultMap.js';
	$operacion = 'ajustar';
	require("includes/template.php");
}

function ajustarRecomendacion($objSQLServer, $seccion = ""){
	global $lang;
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$id = (int)$_POST['hidId'];
	if($objReferencia->setReferenciasAjustar($id, 1)){
		$mensaje = $lang->message->ok->msj_modificar->__toString();	
	}
	else{
		$mensaje = $lang->message->error->msj_modificar->__toString();
	}
	index($objSQLServer, $seccion, $mensaje);
}

function noRecomendar($objSQLServer, $seccion = ""){
	global $lang;
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$id = (int)$_POST['hidId'];
	if($objReferencia->setReferenciasAjustar($id, 2)){
		$mensaje = $lang->message->ok->msj_modificar->__toString();	
	}
	else{
		$mensaje = $lang->message->error->msj_modificar->__toString();
	}
	index($objSQLServer, $seccion, $mensaje);
}

function ignorarRecomendacion($objSQLServer, $seccion = ""){
	global $lang;
	require_once 'clases/clsReferencias.php';
	$objReferencia = new Referencia($objSQLServer);
	
	$id = (int)$_POST['hidId'];
	if($objReferencia->setReferenciasAjustar($id, 3)){
		$mensaje = $lang->message->ok->msj_modificar->__toString();	
	}
	else{
		$mensaje = $lang->message->error->msj_modificar->__toString();
	}
	index($objSQLServer, $seccion, $mensaje);
}

function volver($objSQLServer, $seccion){
	index($objSQLServer, $seccion);
}
