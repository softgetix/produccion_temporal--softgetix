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
$idGrupo = $_GET['idGrupo'];
$filtro = $_GET['filtro'];
if($idGrupo >=0){
	require_once("clases/clsMoviles.php");
	$objMovil = new Movil($objSQLServer);
    $arrMoviles = $objMovil->obtenerMovilesGrupo($idGrupo,$_SESSION["idUsuario"],$filtro);
	$noptions = 0;
	for($i=0;$i < count($arrMoviles) && $arrMoviles;$i++){
		$return .= "document.getElementById('cmbMoviles').options[" . $noptions . "]=new Option('" .($arrMoviles[$i]["dato"]). "', '" .$arrMoviles[$i]["id"]. "');\n";
		$noptions++;
	}
	die( trim( "document.getElementById('cmbMoviles').length=0;" . $return ) );
}
?>