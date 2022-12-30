<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$ids = isset($_POST["ids"]) ? $_POST["ids"] : 0;
$motivo_confirmacion = isset($_POST["motivo"]) ? $_POST["motivo"] : 0;

$return = array();
require_once 'clases/clsNotificacionAlertas.php';

$arrAlertaIDs = explode(",", $ids);
foreach ($arrAlertaIDs as $id) {
	$objNotificacion = new NotificacionAlertas($objSQLServer);
	$return['msg'] = $objNotificacion->confirmarAlerta($id, $_SESSION["idUsuario"], $motivo_confirmacion);
}

header('Content-type: application/json');
echo json_encode($return);
