<?php
 //ini_set('display_errors', 1);
 //ini_set('display_startup_errors', 1);
 //error_reporting(E_ALL);

function index($objSQLServer, $seccion, $mensaje = ""){
	//echo '<pre>';print_r($_SESSION);
	$us_id = $_SESSION['idUsuario']; 
	//echo $us_id;
	require("includes/template.php");
	require_once("secciones/whatsApp.php");
}
?>