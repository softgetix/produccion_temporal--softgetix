<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 
$moviles = array();
$cliente_seleccionado = (isset($_REQUEST["identificador_cliente"]) ? $_REQUEST["identificador_cliente"] * 1 : 0);
$nombre_cliente_seleccionado = "";

function index($objSQLServer, $seccion, $mensaje=""){
	global $cliente_seleccionado;
	require_once 'clases/clsProbadorDePanico.php';
	$objPanico = new ProbadorDePanico($objSQLServer);
	
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
	
	$arrClientes = $objPanico->obtenerClientes($_SESSION["idEmpresa"]);
   	
	$extraCSS[] = 'css/estilosProbadorDePanico.css';
   	
	$operacion = 'listar';
	$tipoBotonera='LI';
	require("includes/template.php");
}
?>