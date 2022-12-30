<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$return = "";
$latitud = isset($_GET["latitud"]) ? $_GET["latitud"] : 0;
$longitud = isset($_GET["longitud"]) ? $_GET["longitud"] : 0;
$idMovil = isset($_GET["idMovil"]) ? $_GET["idMovil"] : 0;
$flagAjustarZoom = isset($_GET["ajustarZoom"]) ? $_GET["ajustarZoom"] : 0;
if($idMovil > 0){
	$nameVar="rastreo_".$_SESSION["idUsuario"];
	for($i=0;$i < count($_SESSION[$nameVar]) && $_SESSION[$nameVar]; $i++){
		if($_SESSION[$nameVar][$i]["mo_id"]	== $idMovil){
			$latitud = $_SESSION[$nameVar][$i]["sh_latitud"];	
			$longitud = $_SESSION[$nameVar][$i]["sh_longitud"];
			$icono = $_SESSION[$nameVar][$i]["sh_longitud"];
			if($flagAjustarZoom){
				if(isset($_SESSION[$nameVar][$i]["nivelUbicacion"])){
					$idZoom = $_SESSION[$nameVar][$i]["nivelUbicacion"];
				}else{
					require_once ("clases/clsNomenclador.php");
					$objNomenclador = new Nomenclador($objSQLServer);
					$geocodificacion = $objNomenclador->obtenerNomenclados($_SESSION[$nameVar][$i]["sh_latitud"], $_SESSION[$nameVar][$i]["sh_longitud"], $_SESSION[$nameVar][$i]["movil"]);
					$_SESSION[$nameVar][$i]["ubicacion"] = $geocodificacion;
					$_SESSION[$nameVar][$i]["nivelUbicacion"] = 1;
					$idZoom = $_SESSION[$nameVar][$i]["nivelUbicacion"];
				}
				if($idZoom == 1 || $idZoom == 2){
					//NIVEL DE CALLE
					$nivelZoom = 15;	
				}else{
					//NIVEL DE RUTA
					$nivelZoom = 10;	
				}
			}else{
				$nivelZoom="map.getZoom()";	
			}
		}
	}
}
$return .= "habilitarCheck();";
$return .= "habilitarRad();";
$return .= "resaltarIcono(".$idMovil.",null,null);";
$return .= "map.setCenter(new google.maps.LatLng(".$latitud.",".$longitud."),".$nivelZoom.");";
die(trim($return));

?>
