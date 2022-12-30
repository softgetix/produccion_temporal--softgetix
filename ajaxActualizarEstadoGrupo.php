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

$idGrupo = ( $_POST['idGrupo'] ) ? $_POST['idGrupo'] : "";
$estado = ( $_POST['estado'] ) ? $_POST['estado'] : 0;

if( $idGrupo ){
	// ACTUALIZO LA VARIABLE DE SESION PQ SINO NO SE VEN REFLEJADOS LOS CAMBIOS
	// HASTA QUE ESTA NO SEA ACTUALIZADA EN EL ARCHIVO ajaxActualizarArray.php
	$nameVar = "rastreo_".$_SESSION["idUsuario"];
	$nameVarConf = $nameVar.'_conf';
	
	
	$_SESSION[$nameVarConf]['groups'][$idGrupo]['expanded'] = $estado;
	
	if($estado == 1){
		$_SESSION[$nameVarConf]['expanded_group_ids'][] = $idGrupo;	
	}
	else{
		for($i=0; $i < count($_SESSION[$nameVarConf]['expanded_group_ids']); $i++){
			if($_SESSION[$nameVarConf]['expanded_group_ids'][$i] == $idGrupo){
				unset($_SESSION[$nameVarConf]['expanded_group_ids'][$i]);
				$_SESSION[$nameVarConf]['expanded_group_ids'] = array_values($_SESSION[$nameVarConf]['expanded_group_ids']);
				$i = count($_SESSION[$nameVarConf]['expanded_group_ids']);
			}
		}	
	}
}
