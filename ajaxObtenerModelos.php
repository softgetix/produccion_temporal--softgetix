<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$return = "";
$marca = $_GET['marca'];
$modelo = isset($_GET['modelo']) ? $_GET['modelo'] : "";

if($marca > 0){
	require_once 'clases/clsEquipos.php';
	$objEquipo = new Equipo($objSQLServer);
	$arrModelos = $objEquipo->obtenerModeloEquipos($marca);
}

$noptions = 1;
for($i=0;$i < count($arrModelos) && $arrModelos;$i++){
	$return .= "document.getElementById('cmbModelo').options[" . $noptions . "]=new Option('" .($arrModelos[$i]["dato"]). "', '" .$arrModelos[$i]["id"]. "');\n";
	if ($modelo) {
		if ($modelo == $arrModelos[$i]["id"]){
				$return .= "document.getElementById('cmbModelo').selectedIndex = ".$noptions .";";
		}	
	}
	$noptions++;
}
die($return);

?>