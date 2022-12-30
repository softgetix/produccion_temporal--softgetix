<?php
function index($objSQLServer, $seccion){
	global $lang;
	$action = isset($_GET['action'])?$_GET['action']:'';	
	$idMovil = (int)$_GET['idMovil'];
	$idRef = (int)$_GET['idRef'];
	
	require_once('clases/clsRastreo.php');
	$objRastreo = new Rastreo($objSQLServer);
	
	require_once('clases/clsReferencias.php');
	$objReferencia = new Referencia($objSQLServer);
	
	$arrReporte = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], $idMovil,false,false,true);
	$arrReporte = $arrReporte[0];
	
	if($idRef){
		$arrRef = $objReferencia->getReferencia($idRef);
		$estadoViaje['latitud'] = $arrRef[0]['rc_latitud'];
		$estadoViaje['longitud'] = $arrRef[0]['rc_longitud'];
		$estadoViaje['radio'] = $arrRef[0]['re_radioIngreso'];
	}
	
	require_once $rel.'includes/tipomovil.inc.php';
	$arrDataMovil = getDataMovil($arrReporte);

	if (!isset($arrReporte["ubicacion"])) {
		include $rel."clases/clsNomenclador.php";
		$objNomenclador = new Nomenclador($objSQLServer);
    	$geocodificacion = $objNomenclador->obtenerNomenclados($arrReporte["sh_latitud"], $arrReporte["sh_longitud"], $arrReporte["movil"]);
    	$arrReporte["ubicacion"] = $geocodificacion;
	}
	
	require_once 'clases/clsModeloEquipos.php';
	$objModeloEquipo = new ModeloEquipo($objSQLServer);
			
	$arrModeloEquipos = $objModeloEquipo->getBitMotor($arrViaje['vi_mo_id']);
			
	require("secciones/abmViajesMapa.php");
}
?>