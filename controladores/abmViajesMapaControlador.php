<?php
function index($objSQLServer, $seccion){
	global $lang;
	$action = isset($_GET['action'])?$_GET['action']:'';	
	$id_viaje = (int)$_GET['idViaje'];
	
	require_once('clases/clsViajes.php');
	$objViaje = new Viajes($objSQLServer, $id_viaje);
	
	require_once('clases/clsRastreo.php');
	$objRastreo = new Rastreo($objSQLServer);
	
	require_once("clases/clsHistorico.php");
	$objHistorico = new Historico($objSQLServer);
			
	$arrViaje = $objViaje->get_viajes();
	$arrViaje = $arrViaje[0];
	
	$estadoViaje = $objViaje->getEstadoViaje($id_viaje);
	
	$txtEstadoViaje = '';
	if($estadoViaje['pendiente_inicio']){
		$txtEstadoViaje = $lang->system->pendiente_inicio_en.' ';	
	}
	elseif($estadoViaje['en_cliente'] || $estadoViaje['en_origen']){
		$txtEstadoViaje = $lang->system->en.' ';
	}
	elseif($estadoViaje['en_transito']){
		$txtEstadoViaje = $lang->system->en_transito_hacia.' ';
	}
	
	$arrReporte = $objRastreo->obtenerReportesMovilesUsuario($_SESSION["idUsuario"], $arrViaje['vi_mo_id'],false,false,true);
	$arrReporte = $arrReporte[0];
	
	require_once $rel.'includes/tipomovil.inc.php';
	$arrDataMovil = getDataMovil($arrReporte);

	if (!isset($arrReporte["ubicacion"])) {
		include $rel."clases/clsNomenclador.php";
		$objNomenclador = new Nomenclador($objSQLServer);
    	$geocodificacion = $objNomenclador->obtenerNomenclados($arrReporte["sh_latitud"], $arrReporte["sh_longitud"], $arrReporte["movil"]);
    	$arrReporte["ubicacion"] = $geocodificacion;
	}
	
	
	## historico del viajes ##
	$arrHistorico = array();
	/* SE OCULTA PORQ SE DEMORA EN TRAER EL HISTORICO
	$fechas = $objViaje->getFechaDesdeHastaHistoricoViaje();
	$desde = date('Y-m-d H:i:s',strtotime($fechas['desde']));
	$hasta = date('Y-m-d H:i:s',strtotime($fechas['hasta']));
	$objHistorico->tipoMovil = 'vehiculo';
				
	$arrHistorico = $objHistorico->llenarTablaTemporal($desde, $hasta, $arrViaje['vi_mo_id'], $_SESSION["idUsuario"]);
	if(!is_array($arrHistorico)){//-- Error 408: Supero tiempo de espera --//
		$arrHistorico = array();	
	}
	else{
		$arrHistorico = $objHistorico->agruparHistorico($arrHistorico, $desde, $hasta);
		$arrHistorico = $objHistorico->historicoVista($arrHistorico);
	
		// Si no hay rumbo lo calculamos segun los puntos
		if (is_array($arrHistorico)){
			calcularRumbo2($arrHistorico);
		}
			
		$arrHistorico = $objHistorico->historicoDistancia($arrHistorico);
	}
	*/
	require_once 'clases/clsModeloEquipos.php';
	$objModeloEquipo = new ModeloEquipo($objSQLServer);
			
	$arrModeloEquipos = $objModeloEquipo->getBitMotor($arrViaje['vi_mo_id']);
			
	require("secciones/abmViajesMapa.php");
}
?>