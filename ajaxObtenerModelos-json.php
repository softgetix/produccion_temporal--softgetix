<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-type: application/json');

set_time_limit(300);

include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "includes/validarSesion.php";

require_once 'clases/clsEquipos.php';
$objEquipo = new Equipo($objSQLServer);
	
$return = array();
$marca = $_GET['marca'];

$arrModelos = $objEquipo->obtenerModeloEquipos($marca);

require_once 'includes/json.php';
$jsonOBJ = new Services_JSON();
echo $jsonOBJ->encode($arrModelos);