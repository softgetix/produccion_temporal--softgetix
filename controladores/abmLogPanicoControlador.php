<?php
$operacion = (isset($_POST["hidOperacion"]))? $_POST["hidOperacion"]:"";

function index($objSQLServer, $seccion, $mensaje=""){
	$method 	= (isset($_GET['method'])) ? $_GET['method'] : NULL;
	
	if($_POST){
		include('clases/clsLogPanico.php');
		$objPanico = new LogPanico($objSQLServer);
		$arr_log = $objPanico->getLogPanico($_POST);
		$numRows = ($objPanico->cantReg + 1);
	}
	else{
		//$_POST['refresh'] = 1; //Inicializo como automatico//	
	}
	require("includes/template.php");
}

?>