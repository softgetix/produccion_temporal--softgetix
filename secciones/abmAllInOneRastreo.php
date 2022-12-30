<?php
session_start();
$rel = '../';
chdir($rel);
$idMovil = $_GET['idMovil'];
require_once("includes/funciones.php");
require_once("clases/clsSqlServer.php");
$objSQLServer = new SqlServer();
//$objSQLServer->rel = $rel;
//$objSQLServer->dirConfig = $_SESSION['DIRCONFIG'];
$objSQLServer->dbConnect();

include ('clases/clsIdiomas.php');
$objIdioma = new Idioma();
//$objIdioma->rel = $rel;
$lang = $objIdioma->getIdiomas($_SESSION['idioma']);
		
require_once("clases/clsRastreo.php");
$obRastreo = new Rastreo($objSQLServer);
$arrReportes = $obRastreo->obtenerReportesMovilesUsuario(0, $idMovil);
$movil = $arrReportes[0];

require_once 'includes/tipomovil.inc.php';
$arrDataMovil = getDataMovil($movil);

$movil['entradas'] = (int)$movil['entradas'];
$txt_bateria = $lang->system->bateria_baja;
$class_bateria = 'bateria_1';
if($movil['entradas']){
	if($movil['entradas'] <= 25){
		$class_bateria = 'bateria_2';
	}
	elseif($movil['entradas'] > 25 && $movil['entradas'] <= 50){
		$class_bateria = 'bateria_3';
	}
	elseif($movil['entradas'] > 50 && $movil['entradas'] <= 75){
		$class_bateria = 'bateria_4';
	}
	else{
		$class_bateria = 'bateria_5';
	}
	$txt_bateria = $lang->system->bateria.' '.$movil['entradas'].'%';
}

$txt_senial = $lang->system->sin_cobertura;
$class_senial = 'signal_1';
if($movil['sh_senial']){
	if($movil['sh_senial'] < 0){
		$movil['sh_senial'] = $movil['sh_senial']*-1;
	}
	if($movil['sh_senial'] > 0 && $movil['sh_senial'] <= 25){
		$txt_senial = $lang->system->cobertura_mala;
		$class_senial = 'signal_2';
	}
	elseif($movil['sh_senial'] > 25 && $movil['sh_senial'] <= 50){
		$txt_senial = $lang->system->cobertura_regular;
		$class_senial = 'signal_3';
	}
	elseif($movil['sh_senial'] > 50 && $movil['sh_senial'] <= 75){
		$txt_senial = $lang->system->cobertura_buena;
		$class_senial = 'signal_4';
	}
	else{
		$txt_senial = $lang->system->cobertura_muy_buena;
		$class_senial = 'signal_5';
	}
}

$txt_wifi = $movil['sh_estado_wifi']?$movil['sh_wifi_name']:$lang->system->wifi_apagado;
$class_wifi = $movil['sh_estado_wifi']?'wifi_on':'wifi_off';

$txt_gps = $movil['sh_estado_gps']?$lang->system->gps_encendido:$lang->system->gps_apagado;
$class_gps = $movil['sh_estado_gps']?'gps_on':'gps_off';

if (!isset($movil["ubicacion"]) && !empty($movil["movil"])) {
	include "clases/clsNomenclador.php";
	$objNomenclador = new Nomenclador($objSQLServer);
    $geocodificacion = $objNomenclador->obtenerNomenclados($movil["sh_latitud"], $movil["sh_longitud"], $movil["movil"]);
    $movil["ubicacion"] = $geocodificacion;
}
$objSQLServer->dbDisconnect();
?>
<script type="text/javascript" src="<?=$rel?>js/jquery.tools.js"></script>
<script type='text/javascript' src='<?=$rel?>js/funciones.js'></script>

<script type='text/javascript' src='<?=$rel?>js/jquery.1.7.1.min.js'></script>
<script type='text/javascript' src='<?=$rel?>js/openLayers/OpenLayers.js'></script>
<script type='text/javascript' src='<?=$rel?>js/defaultMap.js'></script>

<link href="<?=$rel?>css/estilosDefault.css" rel="stylesheet" type="text/css">
<link href="<?=$rel?>css/estilosABMDefault.css" rel="stylesheet" type="text/css">
<link href="<?=$rel?>css/estilosRastreo.css" rel="stylesheet" type="text/css">
<style>
#mapa{height:450px; width:100%; float:left;}
#infoMovil{border:1px solid #AAAAAA; padding:10px; max-width: 274px; *width: 274px;  float:left; position:absolute; right:10px; top:4px; z-index:999; background:#FFF;}
a{color:#09F;}
</style>
<div id="mapa"></div>
<div id="infoMovil">
    <table id="tbl_infodatogps">
        <tbody>
        <tr>
            <td colspan="2">
                <ul class="f-left">
                    <li class="f-right i-status <?=$class_bateria?>" title="<?=$txt_bateria?>"></li>
                    <li class="f-right i-status <?=$class_senial?>" title="<?=$txt_senial?>"></li>
                    <li class="f-right i-status <?=$class_wifi?>" title="<?=$txt_wifi?>"></li>
                    <li class="f-right i-status <?=$class_gps?>" title="<?=$txt_gps?>"></li>
                </ul>
                <ul>
                	<a href="javascript:cerrarPopup();" class="f-right">&laquo;&nbsp;<?=$lang->botonera->volver?></a>
                </ul>
            </td>
        </tr>
        <tr>
            <td><?=$lang->system->movil?></td>
            <td><?=decode($movil['movil'])?></td>
        </tr>
        <tr>
            <td><?=$lang->system->ultima_conexion?></td>
            <td><?=formatearFecha($movil["sh_fechaRecepcion"],'short')?></td>
        </tr>
        <tr>
            <td><?=$lang->system->ubicacion?></td>
            <td>
                <a href="javascript:mapSetCenter(<?=$movil['sh_latitud']?>,<?=$movil['sh_longitud']?>)">
                    <span id="infoNomenclado_<?=$movil['mo_id']?>" title="(<?=$movil['sh_latitud']?>,<?=$movil['sh_longitud']?>)"><?=($movil["ubicacion"]?str_replace("'",'',$movil["ubicacion"]):'--')?></span>
                </a>
            </td>
        </tr>
        <tr>
            <td><?=$lang->system->coordenadas?></td>
            <td><span><?=substr($movil['sh_latitud'],0,8)?>,<?=substr($movil['sh_longitud'],0,9)?></span></td>
        </tr>
        <?php if(!tienePerfil(array(16,17,18))){?>
        <tr>
            <td><?=$lang->system->evento?></td>
            <td><?=$movil['tr_descripcion']?></td>
        </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<script language="javascript">
var lat= <?=$movil['sh_latitud']?$movil['sh_latitud']:0?>;
var lng = <?=$movil['sh_longitud']?$movil['sh_longitud']:0?>;
var zoom = 16;
$( document ).ready(function() {
	CrearMapa('mapa');
	mapSetZoom(zoom);
	mapSetCenter(lat,lng);
	
	var srcIcono = '1/<?=$arrDataMovil['carpetaImagen']?>/<?=$arrDataMovil['img']?>';
	var arr = [];
	arr['lat'] = lat;
	arr['lng'] = lng;
	arr['icono'] = '../getImage.php?pathmode=rel&file='+srcIcono+'&caption=';
	var marker = mapMarker(arr);
	setMap(marker,false);
});

function cerrarPopup(){
	<?php if($_GET['referer']){?>
		location.href = '<?=$_SERVER['HTTP_REFERER']?>';
	<?php }
	else{?>	
		window.parent.cerrarPopup();
	<?php }?>
}
</script>
