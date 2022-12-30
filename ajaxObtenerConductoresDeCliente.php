<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "includes/validarSesion.php";
include "includes/caja_negra.php";

require_once("clases/clsConductores.php");
$objConductor = new Conductor($objSQLServer);
		
$return = "";
$cliente = $_GET['cliente'];
caja_negra($_GET['cliente'],'clientes',1,$objSQLServer);
$conductor = $_GET['conductor'];
$arrConductores = $objConductor->obtenerConductoresPorEmpresa($cliente);

$noptions = 1;

for($i=0;$i < count($arrConductores) && $arrConductores;$i++){
	$return .= "document.getElementById('cmbConductor').options[".$noptions."]=new Option('".($arrConductores[$i]['co_nombre']).' '.$arrConductores[$i]['co_apellido']."', '".$arrConductores[$i]['co_id']."');\n";
	
	if($arrConductores[$i]['co_id'] == $conductor){
		$return .= "document.getElementById('cmbConductor').options[" . $noptions . "].setAttribute('selected', 'selected');\n";
	}
	
	$noptions++;
}
die($return);
?>