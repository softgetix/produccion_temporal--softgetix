<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:""; 

function index($objSQLServer, $seccion, $mensaje=""){
	require_once 'clases/clsEnvioComandos.php';
   	require_once 'clases/clsEquipos.php';
   	$tipoBotonera='AM';
   	$operacion = 'listar';
   	$objEquipo = new Equipo($objSQLServer);
   	$objEnvioComando = new EnvioComando($objSQLServer);
   	$_SESSION["enviosComandos"] = $objEnvioComando->obtenerEnvioComando();
   	$arrEquiposUsuarios = $objEquipo->obtenerEquiposUsuario($_SESSION["idUsuario"]);
	require("includes/template.php");
}

function volver($objSQLServer, $seccion){
   index($objSQLServer, $seccion);
}
?>
