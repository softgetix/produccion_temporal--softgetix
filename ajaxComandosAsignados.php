<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-type: application/json');

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$grupo = $_GET['g'];

include 'clases/clsGruposComandos.php';

$objGrupos = new GrupoComandos($objSQLServer);
$objGrupos->obtenerRegistros($grupo,'','',0,false,true);
$arrComandos = $objGrupos->obtenerComandosAsignados();

limpiarArray($arrComandos);
echo json_encode($arrComandos);