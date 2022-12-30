<?php
session_start();
set_time_limit(300);

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT"); 
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  


include('includes/config_clientes.php');
include "includes/funciones.php";
require_once "includes/conn.php";

$controlador = escapear_string($_GET['c']);

//VERIFICO QUE LA CADENA CONTENGA SOLO VALORES DE: A-Z, a-z, 0-9
if(!ctype_alnum($controlador)) $controlador="";
$seccion = $controlador;

include 'includes/verificarSeccion.php';

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

$pathControlador = 'controladores/' . $controlador . 'Controlador.php';
require_once $pathControlador;

if (isset($_POST['hidOperacion'])){
	$accion = ($_POST['hidOperacion'] != 'listar')?$_POST['hidOperacion']:'index';
}
else{
	$accion = "index";
}


//---------- ----------//
if(!isset( $_SESSION['OLVIDAR'])){
    $_SESSION['OLVIDAR'] = array('tableroMilkrun');
}
$_SESSION['OLVIDAR'] = array(
    'tableroMilkrun'
);
// Si abre un popup no deberia sobreescribir esto.
if(!isset($_GET['popup']) && @$_GET['action'] != 'popup' && !in_array( $seccion, $_SESSION['OLVIDAR'])){
    $_SESSION['accion'] = $accion;
    $_SESSION['seccion'] = $seccion;
}
//---------- ----------//

include ('clases/clsSeguridad.php');
$objSeguridad = new Seguridad($objSQLServer, $seccion);

$accion($objSQLServer, $seccion);
//$objSQLServer->dbDisconnect();
?>