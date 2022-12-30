<?
	
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-type: application/text');

set_time_limit(300);
error_reporting(0);

include "includes/validarSesion.php";
include "includes/funciones.php";
include "includes/conn.php";
include "includes/validarUsuario.php";

require_once 'clases/clsReferencias.php';
$objReferencias = new Referencia($objSQLServer);
$arrGeocercas= $objReferencias->obtenerReferenciasEmpresa($_SESSION["idEmpresa"], $_GET['tipoAlerta']);

//---------------------

$contenidoListado = '';
$tempGeocercas='';
foreach ($arrGeocercas as $fila){
	if (isset($arrGeocercasUsadas2) && in_array($fila['re_id'], $arrGeocercasUsadas2)){
		$arrGeocercasElegidas[]=$fila;
		$tempGeocercas.=$fila['re_id'].',';
		continue;
	}
	$class="";
	if (isset($fila['re_rg_id']) && $fila['re_rg_id']){
		$class=" class='rg_".$fila['re_rg_id']."'";
	}

	$contenidoListado .= "<option value='".$fila['re_id']."'".$class.">".str_replace ('"',"'",decode($fila['re_nombre']))."</option>";
}

echo $contenidoListado;

