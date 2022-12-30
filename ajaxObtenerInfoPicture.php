<?php
session_start();
header("Cache-Control: private");
header("Pragma: no-cache");
set_time_limit(300);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";
include "includes/tipomovil.inc.php";


require_once("clases/clsHistorico.php");
$objHistorico = new Historico($objSQLServer);

require_once("clases/clsRastreo.php");
$objRastreo = new Rastreo($objSQLServer);

$return = "";
$strMoviles = $_GET['idMovil'];
if($strMoviles){
	
	include ('clases/clsIdiomas.php');
	$objIdioma = new Idioma();
	$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
	
	$arrMoviles = $objRastreo->getDataPicture($strMoviles);
	$hoy = getFechaServer('Y-m-d H:i:s');
	
	foreach($arrMoviles as $movil){
	       
	$datos = "";
	$datos.= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"3\">";
	$datos.= "<tr style=\"background-color: black; color: white;\">";
    $datos.= "<td class=\"tdTitInfoPicture\" width=\"70\"><b>".$lang->system->fecha."</b></td>";
    $datos.= "<td class=\"tdTitInfoPicture\" width=\"150\"><b>".$lang->system->evento."</b></td>";
    $datos.= "<td><b>".$lang->system->velocidad."</b></td>";
    $datos.= "</tr>";
    $datos.= "<tr>";
	$datos.= "<td class=\"tdInfoPicture\">".formatearFecha($movil["sh_fechaRecepcion"],'short')."</td>";
    $datos.= "<td class=\"tdInfoPicture\">".$movil["tr_descripcion"]."</td>";
    $datos.= "<td style=\"color:#000000;\">".formatearVelocidad($movil["dg_velocidad"])."</td>";
    $datos.= "</tr>";
	if(isset($movil["ubicacion"])){
		$ubicacion = $movil["ubicacion"];
    }
    else{
		require_once("clases/clsNomenclador.php");
    	$objNomenclador = new Nomenclador($objSQLServer);
        $geocodificacion = $objNomenclador->obtenerNomenclados($movil["sh_latitud"], $movil["sh_longitud"], $movil['mo_id']);
        $ubicacion = $geocodificacion;
	}
	$datos.= "<tr style=\"background-color: #DDD; color: gray;\">";
    $datos.= "<td colspan=\"3\" class=\"tdInfoPicture\">".$ubicacion."</td>";
    $datos.= "</tr>";
	$datos.= "</table>";
	
	$return .= "if(document.getElementById('infoPicture".$movil['mo_id']."')) { ";
	$return .= "document.getElementById('infoPicture".$movil['mo_id']."').innerHTML='".$datos."'; ";
	$return .= "if (pipMarker[".$movil['mo_id']."]) { ";
    $return .= "    deleteMap(pipMarker[".$movil['mo_id']."]);";
    $return .= "    pipMarker[".$movil['mo_id']."] = null;";
    $return .= "} ";
	
	$bEncendido = getEstadoMotor($movil);
	$arr = getDataMovil($movil);// en includes/tipomovil.inc.php
	$bEncendido = $arr['bEncendido']; 
	$iconImage = $arr['img'];
	$flagEnvioMailGrupo = $arr['flagEnvioMailGrupo'];
	$mostrarIconoMail = $arr['mostrarIconoMail'];
	$iconFolder = $arr['carpetaImagen'];
	$dirUno = ($iconFolder == 'misc')?'':'1/';
	$urlImage = "getImage.php?pathmode=rel&file=".$dirUno.$iconFolder."/".$iconImage."&caption=".urlencode($movil['mo_matricula']);
			
	$lat = $movil['sh_latitud'];
	$lng = $movil['sh_longitud'];
	
	if(is_array($arrHistorico)){
		$ultimoHistorico = null;
		$return .= "var points = Array();";
		foreach($arrHistorico as $historico){
        	$ultimoHistorico = $historico;
			$return .= "var LonLat = mapLatLng(".$historico[9].",".$historico[10].");";
			$return .= "points.push(new OpenLayers.Geometry.Point(LonLat.lon,LonLat.lat));";
		}
		
		$return .= "var stylePolyLine = OpenLayers.Util.extend({
			fillColor:'#0082d8'
			,fillOpacity:2
			,strokeColor:'#0082d8'
			,strokeWidth:2
			});";
		$return .= " var objetoGeometrico = new OpenLayers.Layer.Vector('Line Layer ".$movil['mo_id']."',{style: stylePolyLine});";
		$return .= " var line = new OpenLayers.Geometry.LineString(points); ";
		$return .= " var ObjetoOpenlayer = new OpenLayers.Feature.Vector(line); ";
		$return .= " objetoGeometrico.addFeatures([ObjetoOpenlayer]);";
		$return .= "pipMap[".$movil['mo_id']."].addLayer(objetoGeometrico);";
		
		$lat = $ultimoHistorico[9];
		$lng = $ultimoHistorico[10];
	}
	
	
	$return .= "var arr = []; arr['lat'] = ".$lat."; arr['lng'] = ".$lng."; arr['icono'] = '".$urlImage."';";
	$return .= "pipMarker[".$movil['mo_id']."] = new OpenLayers.Layer.Markers('Markers');";
	$return .= "pipMap[".$movil['mo_id']."].addLayer(pipMarker[".$movil['mo_id']."]);";
	$return .= "pipMarker[".$movil['mo_id']."].addMarker(mapMarker(arr));";
	$return .= "pipMap[".$movil['mo_id']."].zoomTo(14);";
	$return .= "pipMap[".$movil['mo_id']."].setCenter(mapLatLng(".$lat.",".$lng."));";
	$return .= "}";
	}
	die(trim($return));
}
?>
