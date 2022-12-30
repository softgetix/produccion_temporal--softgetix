<?php
require_once 'clases/clsPerfiles.php';
require_once 'clases/clsUsuarios.php';
require_once 'includes/navbar_permisos.php';
global $objSQLServer;

$objPerfil = new Perfil($objSQLServer);
$validacion = $objPerfil->validarSeccion($seccion);

$objUsuario = new Usuario($objSQLServer);
$validacionClienteHabilitado = $objUsuario->verificarClienteHabilitado($_SESSION["idUsuario"]);

$_SESSION["faltaPago"] = false;

//si no tiene permisos, lo redirecciona a la pagina de acceso denegado
if (!$validacion || $validacionClienteHabilitado == 0){
	rompioSession($_GET, 'no permisos');
}
elseif($validacionClienteHabilitado == 2){
	$_SESSION["faltaPago"] = true;
}