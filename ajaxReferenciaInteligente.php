<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

session_start();
set_time_limit(300);
include "includes/funciones.php";
include "includes/conn.php";

switch($_POST['accion']){
	case 'get-baja-referencia-recomendada':
		require_once("clases/clsReferencias.php");
		$objReferencia = new Referencia($objSQLServer);
		$objReferencia->setCancelInteligencia($_POST['id_referencia']);
	break;	
}

$objSQLServer->dbDisconnect();
