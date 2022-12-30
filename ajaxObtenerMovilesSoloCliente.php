<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "includes/validarSesion.php";

$return = "";
$idDistribuidor = (!isset($_GET["idDistribuidor"]) ? 0 : $_GET["idDistribuidor"] * 1);	//Corresponde al primer combo.
$idCliente = (!isset($_GET["idCliente"]) ? 0 : $_GET["idCliente"] * 1);	//Corresponde al segundo combo.
$idUser = (!isset($_GET["idUser"]) ? 0 : $_GET["idUser"] * 1);	//Es el cliente seleccionado en la ABM.

//if ($idUser == 0) die();
//if ($idDistribuidor == 0 && $idCliente > 0) die();

if ($_SESSION["idTipoEmpresa"]==2){
	$idCliente=$_SESSION['idEmpresa'];
}

if($idCliente>-1){
	require_once 'clases/clsMoviles.php';
	$objMoviles = new Movil($objSQLServer);
	$arrDatos['idCliente'] = $idCliente;
	$arrDatos['idUsuario'] = $idUser;
	$arrDatos['idDistribuidor'] = $idDistribuidor;
	$arrMoviles = $objMoviles->getMovilesEstado($arrDatos);
	
	$noptions = 0;
	for($i=0;$i < count($arrMoviles) && $arrMoviles;$i++){
		$movil = empty($arrMoviles[$i]["estado"])?$arrMoviles[$i]["dato"].' (Sin Reportar)':$arrMoviles[$i]["dato"];
		$return .= "cmb.options[" . $noptions . "]=new Option('" .$movil. "','" .$arrMoviles[$i]["id"]. "');";
		$noptions++;
	}
	die( trim( "var cmb=document.getElementById('cmbMoviles');cmb.length=0;" . $return ) );
}