<?php
//$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function listado($objSQLServer, $seccion, $mensaje = ""){
	//$method 	= (isset($_GET['method'])) ? $_GET['method'] : null;
	//$filtro = (isset($_POST["hidFiltro"]))?$_POST["hidFiltro"]:"";
	
	global $arrEntidades;
	$arrEntidades['product_code'] = 'SU'.$_SESSION['idAgente'];
	$arrEntidades['validate_code'] = generarCodigoValidacion($_SESSION['nombreUsuario']);
	
	//require("includes/template.php");
}

?>