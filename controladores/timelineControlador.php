<?php 
function index($objSQLServer, $seccion, $mensaje = "") {
	global $lang;
	
	include('clases/clsTimeline.php');
	$objTimeline = new Timeline($objSQLServer);
	
	$arrItinerario = $objTimeline->getItinerarioViajes();
	//pr($arrItinerario);exit;
	include('secciones/timeline.php');
}
?>