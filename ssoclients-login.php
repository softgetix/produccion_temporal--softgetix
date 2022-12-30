<?php 
session_start();
set_time_limit(300);
header_remove('X-Powered-By');

include 'includes/funciones.php';
include 'includes/check.php';

//-- Decodifico contenido
$_GET['config'] =  base64_decode($_GET['0']);
$_GET['email'] =  base64_decode($_GET['1']);
$_GET['validate'] =  base64_decode($_GET['2']);
$_GET['page'] =  base64_decode($_GET['3']);
$_GET['solapa'] =  base64_decode($_GET['4']);
//-- 
		
$_GET['config'] = strrev(!empty($_GET['config'])?escapear_string($_GET['config']):'localizart');
$email = !empty($_GET['email'])?escapear_string($_GET['email']):NULL;
$password = !empty($_GET['validate'])?escapear_string($_GET['validate']):NULL;
$paginaDefecto = !empty($_GET['page'])?escapear_string($_GET['page']):'cuenta';
$solapaDefecto = !empty($_GET['solapa'])?escapear_string($_GET['solapa']):NULL;
	
if(empty($email) || empty($password)){
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: index.php"); 
	exit;		
}
	
include('includes/config_clientes.php');
require_once 'includes/conn.php';
require_once('clases/clsUsuarios.php');		
$objUsuario = new Usuario($objSQLServer);
	
//--Ini. Se implementa HASH256 y que conviva con md5 hasta que todos migren a HASH256 mediante el cambio de clave..
$arrDatos = array ("usuario" => $email,"pass" => hash('sha256',trim($password)));
$arrUsuario = $objUsuario->login($arrDatos, true);
if($arrUsuario == false){//-- si es false, verificamos si no posee hash256 (borrar cuando se desida sacar por completo md5)
	$arrDatos = array ("usuario" => $email,"pass" => md5(trim($password)));
	$arrUsuario = $objUsuario->login($arrDatos, false);
}
//--Fin.

if($arrUsuario[0]){
	$_SESSION["pass"] 				= $arrDatos["pass"];
	$_SESSION["pass_inicioSession"] = $arrDatos["pass"];
	$_SESSION["idUsuario"]			= $arrUsuario[0]["us_id"];
	$_SESSION["us_nombre"]			= $arrUsuario[0]["us_nombre"];
	$_SESSION["us_apellido"]		= $arrUsuario[0]["us_apellido"];
	$_SESSION["idPerfil"] 			= $arrUsuario[0]["us_pe_id"];
	$_SESSION["nombreUsuario"] 		= $arrUsuario[0]["us_nombreUsuario"];
	$_SESSION["idEmpresa"] 			= $arrUsuario[0]["us_cl_id"];
	$_SESSION["idAgente"] 			= $arrUsuario[0]['idAgente'];
	$_SESSION["nombreAgente"] 		= $arrUsuario[0]['nombreAgente'];
	$_SESSION["idTipoEmpresa"] 		= $arrUsuario[0]["cl_tipo"];
	$_SESSION["idPais"] 			= $arrUsuario[0]["cl_pai_id"];
	$_SESSION['idioma'] = trim($arrUsuario[0]['cl_idioma_definida']);	
	$aux = explode('_',$_SESSION['idioma']);
	$_SESSION['language'] = $aux[0];
			
	//centrado de mapas
	$_SESSION["lat"] 				= $arrUsuario[0]["pr_lat"];
	$_SESSION["lng"] 				= $arrUsuario[0]["pr_lng"];
	$_SESSION["zoom"] 				= $arrUsuario[0]["pr_zoom"];
	
	//$_SESSION["mailAlerta"] 		= $arrUsuario[0]["us_mailContacto"];
			
	$aux = explode("@",$arrUsuario[0]["us_nombreUsuario"]);
	$_SESSION["nombreUsuarioCorto"] = $aux[0];
	//$_SESSION["ultimoAcceso"] 		= $arrUsuario[0]["us_ultimo_acceso"];
		
	// Cargo los per
	$_SESSION["accesoMobile"] = $arrUsuario[0]["us_accesoMobile"];
			
	$_SESSION['paginaDefecto'] = $paginaDefecto.(!empty($solapaDefecto)?('&solapa='.$solapaDefecto):'');
	header('HTTP/1.1 301 Moved Permanently'); 
	header('Location: /'.strrev($_GET['config']).'/boot.php?c='.$_SESSION["paginaDefecto"]); 
	exit;
}
else{
	header('HTTP/1.1 301 Moved Permanently'); 
	header('Location: /'.strrev($_GET['config']).'/index.php'); 
	exit;
}
?>