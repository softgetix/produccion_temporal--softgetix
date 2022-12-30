<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

session_start();
//set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
##-- validar movil --##
if($_GET["strMoviles"] > 0){
	include "includes/caja_negra.php";
	caja_negra($_GET["strMoviles"],'moviles',1,$objSQLServer);
}
##-- --##
include "includes/validarUsuario.php";

include_once ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);

$return = "";
$nameVar = "rastreo_".$_SESSION["idUsuario"];
$movilesSerialized = isset($_GET["strMoviles"]) ? $_GET["strMoviles"] : 0;

$arrMoviles = explode(",",$movilesSerialized);
sort($arrMoviles);
if($movilesSerialized != ""){
	if(count($arrMoviles) > 1){
		$latMenor=90;
		$latMayor=-90;
		$lonMenor=180;
		$lonMayor=-180;
		$movilesSinReportar=0;
		for($i=0;$i < count($_SESSION[$nameVar]) && $_SESSION[$nameVar]; $i++){
			if(in_array($_SESSION[$nameVar][$i]["mo_id"],$arrMoviles)){
				if($_SESSION[$nameVar][$i]["sh_latitud"]!=0 && $_SESSION[$nameVar][$i]["sh_longitud"]!=0){
					//CHEKEO LA LATITUD MAYOR Y MENOR
					if($_SESSION[$nameVar][$i]["sh_latitud"] < $latMenor){
						$latMenor = $_SESSION[$nameVar][$i]["sh_latitud"];	
					}
					if($_SESSION[$nameVar][$i]["sh_latitud"] > $latMayor){
						$latMayor = $_SESSION[$nameVar][$i]["sh_latitud"];	
					}
					
					//CHEKEO LA LONGITUD MAYOR Y MENOR
					if($_SESSION[$nameVar][$i]["sh_longitud"] < $lonMenor){
						$lonMenor = $_SESSION[$nameVar][$i]["sh_longitud"];	
					}
					if($_SESSION[$nameVar][$i]["sh_longitud"] > $lonMayor){
						$lonMayor = $_SESSION[$nameVar][$i]["sh_longitud"];	
					}
				}
				else{
					$movilesSinReportar++;	
				}
			}	
		}
		if($movilesSinReportar > 0){
			$return .= "mostrarAlerta('".$movilesSinReportar." ".$lang->message->msj_rastreo_posicion_invalida."');";		
		}
		if($latMenor != $latMayor && $lonMenor != $lonMayor){
			$return .= "if(g_iMovEnSeguimiento < -1){}else{verificarCentrado ('".$latMenor."','".$latMayor."','".$lonMenor."','".$lonMayor."');}";
		}
		$return.= 'newTracer.deleteReferenciaSelect();';
		//$return.= 'if (g_iMovEnSeguimiento != SIN_SEGUIR_MOVIL ){ mapSetZoom(16); resaltarIcono(g_iMovEnSeguimiento);}';
	}
	else{
		if(isset($_GET['zoom']) && $_GET['zoom'] > 2 ){
            $nivelZoom = $_GET['zoom'];} 
		else {
            $nivelZoom = 14;}
		
		$return.= 'newTracer.deleteReferenciaSelect();';
		
		if((int)$arrMoviles[0]){
			require_once('clases/clsRastreo.php');
			$objRastreo = new Rastreo($objSQLServer);
			$arrPtos = $objRastreo->getPosicionPto($arrMoviles[0]);
			
			$return.= 'mapSetZoom('.$nivelZoom.');';
			$return.= 'mapSetCenter('.$arrPtos['sh_latitud'].','.$arrPtos['sh_longitud'].');';
			$return.= 'if (g_iMovEnSeguimiento != SIN_SEGUIR_MOVIL ){resaltarIcono(g_iMovEnSeguimiento,'.$arrPtos['sh_latitud'].','.$arrPtos['sh_longitud'].');}';
		}
		else{
			$nivelZoom = 4;	
			$return.= 'mapSetZoom('.$nivelZoom.');';
			$return.= 'if (g_iMovEnSeguimiento != SIN_SEGUIR_MOVIL ){resaltarIcono(g_iMovEnSeguimiento,null,null);}';
		}
	}
}
die(trim($return));
?>