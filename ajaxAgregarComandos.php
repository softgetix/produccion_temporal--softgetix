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
include "includes/caja_negra.php";

$return = "";
$equipos = isset($_GET["equipos"]) ? $_GET["equipos"] : 0;
$comandos = isset($_GET["comandos"]) ? $_GET["comandos"] : 0;

caja_negra($_GET["equipos"],'equipos',1,$objSQLServer);
 
 if($equipos && $comandos){
	$arrEquipos = explode(",", $equipos);
	for($i=0; $i < count($arrEquipos) && $arrEquipos; $i++){
		$arrEquipos[$i]=trim($arrEquipos[$i]);
	}
	sort($arrEquipos);

	$arrComandos = explode(",", $comandos);
	for($i=0; $i < count($arrComandos) && $arrComandos; $i++){
		$arrComandos[$i]=trim($arrComandos[$i]);
	}

	if($arrEquipos && $arrComandos){
		require_once 'clases/clsEnvioComandos.php';
	   $objEnvioComando = new EnvioComando($objSQLServer);

		for($i=0; $i < count ($arrEquipos); $i++){
			for($j=0; $j < count ($arrComandos); $j++){
				$objEnvioComando->agregarComando($arrEquipos[$i],$arrComandos[$j]);
			}
		}
	}
}
$return.="actualizarTablaEnvios();";
die(trim($return));