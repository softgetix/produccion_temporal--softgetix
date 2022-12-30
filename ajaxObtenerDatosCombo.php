<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

require_once 'clases/clsInterfazGenerica.php';
$objInterfazGenerica = new InterfazGenerica($objSQLServer);
$arrDatos = $objInterfazGenerica->obtenerDatosCombo('pa_obtener'.$_GET['t'],3);

limpiarArray($arrDatos);

echo json_encode($arrDatos);