<?php
@session_start();
set_time_limit(300);
error_reporting(E_ERROR);
header_remove('X-Powered-By');


##-- Validar fijación de sessiones --##
if(isset($_SESSION['mark']) === false){
	session_regenerate_id(true);
	$_SESSION['mark'] = true;
}
##-- --##
//if(!isset($rel)){$rel = "";}
//require_once($rel."check.php");
require_once("check.php");

$esSQL = false;
if(array_key_exists('txtConsulta',$_POST) && $_GET['c'] == 'abmGeneradorDeInformes'){
	$esSQL = true;
}

if(array_key_exists('txtCodigo',$_POST) && $_GET['c'] == 'abmComandos'){
	$_POST = escapear_db_array($_POST);	
	//NO realizo limpieza del POST porq los comandos tienen caracteres especiales y no se los puede limpiar, escapeo solo para la DB.
}
else{
	$_POST = escapear_array($_POST);	
}
$_GET = escapear_array($_GET);
$_SERVER = escapear_array($_SERVER);


require_once($rel."clases/clsSqlServer.php");
$objSQLServer = new SqlServer();

$rel = "";
$arrSelf = explode('/',$_SERVER['PHP_SELF']);
$rutaSelf = @$arrSelf[2].'/'. @$arrSelf[3];
if ($rutaSelf  == 'controladores/abmViajesControlador.php'
	|| $rutaSelf  == 'controladores/abmLogGatewayControlador.php'
) { $rel = "../"; }

require_once "mobile/detectorDispositivo.php";
define('ES_MOBILE',esDispositivo());

$objSQLServer->rel = $rel;
$objSQLServer->dbConnect();
?>