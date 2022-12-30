<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

include 'clases/clsHardKey.php';
$objHK=new HardKey();

$return = array();
$return['r']='NO';

// post[m] method, post[s] string, post[c] command
switch ($_POST['m']){
	//chequear si hay un hardkey y devuelve el id
	case 'checkBuscarLlave':
		$temp=$objHK->recibirBuscarLlave($_POST['s']);
		if ($temp['errCode']===HardKey::ERR_NO_ERROR){
			$return['r']='OK';
			$return['key']=$temp['numSerie'];
			$_SESSION['hkey']=$temp['numSerie'];
		}
		break;
	case 'getCommand':
		switch($_POST['c']){
			case '0':
				$return['r']='OK';
				$return['c']=$objHK->enviarBuscarLlave();
				break;
		}
		break;
	//si la llave no coincide o no podemos leerla, timeout a 0 para que cualquier otra accion considere sesion expirada
	case 'checkConnected':
		$temp=$objHK->recibirBuscarLlave($_POST['s']);
		if ($temp['errCode']==HardKey::ERR_NO_ERROR){
			if (isset($_SESSION['hkey_check']) && $temp['numSerie']==$_SESSION['hkey_check']){
				$_SESSION['hkey']=$_SESSION['hkey_check'];
				$return['r']='OK';
			}else{
				session_destroy();
				$return['r']='KICK';
			}
		}else{
			session_destroy();
			$return['r']='KICK';
		}
		break;
}
include 'includes/json.php';
//$a=new Services_JSON();
header('content-type: application/json');
//echo $a->encode($return);
echo json_encode($return);