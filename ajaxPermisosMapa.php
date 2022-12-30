<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

session_start();
set_time_limit(300);
include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$result = array();

//$result['viewMapControls'] = false;
$result['viewMapControls'] = true;

header('Content-type: application/json');
echo json_encode($result);