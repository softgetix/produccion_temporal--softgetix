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
$idDistribuidor = $_GET['idDistribuidor'];
caja_negra($_GET['idDistribuidor'],'clientes',1,$objSQLServer);
$idPerfil = isset($_GET['sel']) ? $_GET['sel'] : 0;
if($idDistribuidor){
	include ('clases/clsIdiomas.php');
	$objIdioma = new Idioma();
	$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
			
	require_once 'clases/clsPerfiles.php';
	$objPerfil = new Perfil($objSQLServer);
	
	$arrPaquete = $objPerfil->obtenerPaquetePorAgente($idDistribuidor);
	$idPaquete = (int)$arrPaquete['pe_id'];
	$idTipoEmpresa = (int)$arrPaquete['cl_tipo'];
	$arrPerfiles = $objPerfil->obtenerPerfilesHijos($idPaquete, $idTipoEmpresa, $idPerfil);
	$noptions = 1;
	foreach($arrPerfiles as $item){
		$return .= "cmb.options[".$noptions."]=new Option('".($lang->perfiles->$item['pe_nombre']?$lang->perfiles->$item['pe_nombre']:encode($item['pe_nombre']))."','".$item['pe_id']."');";
		if ($idPerfil == $item['pe_id']) $return .= "cmb.options[".$noptions."].selected=true;";
		$noptions++;
	}
	die( trim( "$('select#cmbPerfil option').not(':eq(0)').remove(); var cmb=document.getElementById('cmbPerfil');" . $return ) );
}
?>