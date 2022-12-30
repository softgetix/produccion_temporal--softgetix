<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

@session_start();
include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

require_once 'clases/clsEquipos.php';
$objEquipo = new Equipo($objSQLServer);
	
$return = "";
$filtro = $_GET['filtro'];
$arrEquiposUsuarios = $objEquipo->obtenerEquiposUsuario($_SESSION["idUsuario"], $filtro);
$noptions = 0;
for($i=0;$i < count($arrEquiposUsuarios) && $arrEquiposUsuarios;$i++){
	$return .= "document.getElementById('cmbEquipos').options[" . $noptions . "]=new Option('" .($arrEquiposUsuarios[$i]["un_mostrarComo"]). "', '" .$arrEquiposUsuarios[$i]["un_id"]. "');\n";
	$noptions++;
}
die( trim( "document.getElementById('cmbEquipos').length=0;" . $return ) );
?>