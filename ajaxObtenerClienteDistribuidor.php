<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: private");
	header("Pragma: no-cache");

	@session_start();
	include "includes/validarSesion.php";
	include "includes/funciones.php";
	include "includes/conn.php";
	include "includes/validarUsuario.php";
	include "includes/caja_negra.php";

	$return = "";
	$idDistribuidor = $_GET['idDistribuidor'] * 1;
	caja_negra( $_GET['idDistribuidor'],'clientes',1,$objSQLServer);
	
	$idTipoExcluyente=(int)$_GET['p'];
	if ($idDistribuidor > 0) {
		$arrClientes = obtenerDatosCombo("pa_obtenerClienteCombo 0, 0, {$idDistribuidor},{$idTipoExcluyente}");
		$noptions = 1;
		for($i=0;$i < count($arrClientes) && $arrClientes;$i++){
			$return .= "cmb.options[" . $noptions . "]=new Option('" .($arrClientes[$i]["dato"]). "', '" .$arrClientes[$i]["id"]. "');";
			$noptions++;
		}
	}
	die( trim( "var cmb=document.getElementById('cmbClientes');" . $return ) );
?>