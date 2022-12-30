<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");

set_time_limit(300);
error_reporting(0);

//TIEMPO (EXPRESADO EN SEGUNDOS) QUE ESPERA UNA RESPUESTA DEL EQUIPO, SI SUPERA ESE TIEMPO Y NO TIENE RESPUESTA, SE COLOCA EL ESTADO COMO EXPIRADO. 
define("TiempoDeExpire",90);
//--------------------------

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

$return = "";
	
require_once 'clases/clsEnvioComandos.php';
$objEnvioComando = new EnvioComando($objSQLServer);
$arrEnvios = $objEnvioComando->obtenerEnvioComando();

if(isset($_SESSION["enviosComandos"])){
	$arrEnviosGuardados = $_SESSION["enviosComandos"];
	for($i=0; $i < count($arrEnvios) && $arrEnvios; $i++){
		$flagExiste = 0;
		for($j=0; $j < count($arrEnviosGuardados) && $arrEnviosGuardados; $j++){
			if($arrEnvios[$i]["ce_id"] == $arrEnviosGuardados[$j]["ce_id"]){
				$flagExiste = 1;
				$arrEnviosGuardados[$j]["ce_fechaEnviado"] = $arrEnvios[$i]["ce_fechaEnviado"];
				$arrEnviosGuardados[$j]["ce_fechaRespuesta"] = $arrEnvios[$i]["ce_fechaRespuesta"];
				$arrEnviosGuardados[$j]["ce_respuesta"] = $arrEnvios[$i]["ce_respuesta"];
				break;
			}
		}
		if($flagExiste == 0){
			if(isset($arrEnviosGuardados[0])){
				$arrEnviosGuardados[count($arrEnviosGuardados)] = $arrEnvios[$i];
			}else{
				$arrEnviosGuardados[0] = $arrEnvios[$i];
			}
		}
	}
	$arrEnvios = $arrEnviosGuardados;
	$_SESSION["enviosComandos"] = $arrEnvios;
	unset($arrEnviosGuardados);
}

$diaHoy= date("d");
$mesHoy= date("m");
$anioHoy= date("Y");
$horaHoy= date("G");
$minutoHoy= date("i");
$segundoHoy= date("s");
$fechaHoy = mktime($horaHoy,$minutoHoy,$segundoHoy,$mesHoy,$diaHoy,$anioHoy);

$datosTabla='<table style="width:100% !important;">';
if($arrEnvios){
	for($i=0; $i < count($arrEnvios) && $arrEnvios; $i++){
		$textoExpirado = "";
		if(isset($arrEnvios[$i]['ce_respuesta'])){
				$strImg = "ok.png";
				$respuesta = $arrEnvios[$i]['ce_respuesta'];
		}else{
			$strImg = "cruz2.png";
			$respuesta = "-";
		
			if(strlen($arrEnvios[$i]['ce_fechaEnviado']) > 3){
				$mes = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],3,2));
				$dia = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],0,2));
				$anio = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],6,4));
				$hora = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],11,2));
				$minuto = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],14,2));
				$segundo = (int)(substr($arrEnvios[$i]['ce_fechaEnviado'],17,2));
				
				$fechaEnvio = mktime($hora,$minuto,$segundo,$mes,$dia,$anio);		
				$duracion = $fechaHoy - $fechaEnvio;
				if($duracion > TiempoDeExpire || $duracion < 0){
					$textoExpirado = 'Tiempo expirado';	
				}
			}	
		}
		
		$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
						
		$datosTabla.='<tr class="'.$class.'" height=30>';
      $datosTabla.='<td width="80px">'.$arrEnvios[$i]['ug_identificador'].'</td>';
      $datosTabla.='<td width="80px">'.$arrEnvios[$i]['ce_comando'].'</td>';
      $datosTabla.='<td width="70px">'.$arrEnvios[$i]['ce_fechaEnviado'].'</td>';
      $datosTabla.='<td width="100px"><img src="imagenes/'.$strImg.'"> '.$textoExpirado.'</td>';
      $datosTabla.='<td width="70px">'.$arrEnvios[$i]['ce_fechaRespuesta'].'</td>';
      $datosTabla.='<td>'.$respuesta.'</td>';
      $datosTabla.='</tr>';
	}
}else{
	$datosTabla.='<tr class="filaPar">';
	$datosTabla.='<td colspan="4">'.$lang->message->sin_resultados.'</td>';
	$datosTabla.='</tr>';
}
$datosTabla.='</table>';
$return.="document.getElementById('divTablaEstado').innerHTML='".$datosTabla."';";
$return.="scrollear();";
$return.="myTime = setTimeout('actualizarTablaEnvios();',15000);";

die(trim($return));
?>
