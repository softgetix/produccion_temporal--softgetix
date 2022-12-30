<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

$perfil = (int)$_GET['perfil'];
$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : NULL;
$apellido = isset($_GET['apellido']) ? trim($_GET['apellido']) : NULL;

$return = '';
if(!$perfil || (empty($nombre) && empty($apellido))){
	exit;
}
elseif(in_array($perfil, array(34,35))){
	$email = $nombre.$apellido.'@'.$nombre.$apellido.'.com';
	$password = uniqid();

	$return = "$('#txtUsuario').val('".$email."');";
	$return.= "$('#txtPass').val('".$password."');";
	$return.= "$('#txtRepetirPass').val('".$password."');";
	
	die($return);
}
?>