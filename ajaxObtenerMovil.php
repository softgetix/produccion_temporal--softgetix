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
include "includes/caja_negra.php";



$return = array();

//$idMovil= $_GET['i'];
$matricula = $_GET['m'];
$idCliente = $_GET['c'];
$matricula = (!empty($matricula))?escapear_string($matricula):'';
global $objSQLServer;
caja_negra($_GET['m'],'moviles',1,$objSQLServer);
$arrMovil=obtenerDatosCombo("pa_obtenerMoviles 0,'','{$matricula}',0,{$_SESSION['idUsuario']}");
if($arrMovil){
		$objRes=$objSQLServer->dbQuery('select ui_id from tbl_unidad_instalaciones where ui_actual=1 and ui_mo_id='.$arrMovil[0]['mo_id']);
		$instalacion= $objSQLServer->dbNumRows($objRes);
		$res['c']=($instalacion)?1:2;
}else{
	$res['c']=0;
}
echo json_encode($res);