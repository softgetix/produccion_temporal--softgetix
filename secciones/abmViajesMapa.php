<script type='text/javascript' src='<?=$rel?>js/jquery.1.7.1.min.js'></script>
<script type="text/javascript" src="<?=$rel?>js/jquery.tools.js"></script>
<script type='text/javascript' src='<?=$rel?>js/funciones.js'></script>
<script type='text/javascript' src='<?=$rel?>js/openLayers/OpenLayers.js'></script>
<script type='text/javascript' src='<?=$rel?>js/defaultMap.js'></script>
<script type='text/javascript' src='<?=$rel?>js/historicoMap.js'></script>
<link href="<?=$rel?>css/estilosDefault.css" rel="stylesheet" type="text/css">
<link href="<?=$rel?>css/estilosABMDefault.css" rel="stylesheet" type="text/css">
<link href="<?=$rel?>css/estilosRastreo.css" rel="stylesheet" type="text/css">
<style>
#mapa-historico{height:450px; width:100%; *width:98%; float:left;}
#infoMovil{border:1px solid #AAAAAA;  max-width: 274px; *width: 234px; 
	position:absolute; z-index:999; background:#FFF;
	padding:10px; right:10px; top:4px; float:right; }
a{color:#09F;}
</style>
<div id="mapa-historico" style="background:url(imagenes/ajax-loader.gif) center center no-repeat;"></div>
<div id="infoMovil">
    <table id="tbl_infodatogps">
        <tbody>
        <tr>
            <td colspan="2" style="border-top:none">
                <a href="javascript:cerrarPopup();" class="f-right"><?=$lang->botonera->cerrar?></a>
            </td>
        </tr>
        <tr>
            <td><?=$lang->system->movil?></td>
            <td><?=encode($arrReporte['movil'])?></td>
        </tr>
        <tr>
            <td><?=$lang->system->ultima_conexion?></td>
            <td><?=formatearFecha($arrReporte["sh_fechaRecepcion"],'short')?></td>
        </tr>
        <tr>
            <td><?=$lang->system->ubicacion?></td>
            <td>
                <a href="javascript:mapSetCenter(<?=$arrReporte['sh_latitud']?>,<?=$arrReporte['sh_longitud']?>)">
                    <span id="infoNomenclado_<?=$arrReporte['mo_id']?>" title="(<?=$arrReporte['sh_latitud']?>,<?=$arrReporte['sh_longitud']?>)"><?=($arrReporte["ubicacion"]?str_replace("'",'',$arrReporte["ubicacion"]):'--')?></span>
                </a>
            </td>
        </tr>
        <tr>
            <td><?=$lang->system->coordenadas?></td>
            <td><span><?=substr($arrReporte['sh_latitud'],0,8)?>,<?=substr($arrReporte['sh_longitud'],0,9)?></span></td>
        </tr>
        <tr>
            <td><?=$lang->system->evento?></td>
            <td><?=encode($arrReporte['tr_descripcion'])?></td>
        </tr>
        <?php if(!empty($txtEstadoViaje)){?>
        <tr>
            <td colspan="2"><?=$txtEstadoViaje.' <a href="javascript:mapSetCenter('.$estadoViaje['latitud'].','.$estadoViaje['longitud'].')">'.encode($estadoViaje['referencia']).'</a>'?></td>
        </tr>
        <?php }?>
        <?php if(count($arrHistorico)){?>
        <tr>
            <td colspan="2" style="border-bottom:none">
                <a href="javascript:verHistorico();" class="f-left" id="verOcultarHistorico"><?=$lang->botonera->ver_historico?></a>
            </td>
        </tr>
        <?php }?>
        </tbody>
    </table>
</div>

<script language="javascript">

var arrLang = [];
arrLang['encendido'] = 'Motor Encendido';
arrLang['apagado'] = 'Motor Apagado';
arrLang['ver_historico'] = 'Ver Historico';
arrLang['ocultar_historico'] = 'Ocultar Historico';

var zoom = 14;
var movilLatLng = [];
movilLatLng['lat'] = <?=$arrReporte['sh_latitud']?$arrReporte['sh_latitud']:0?>;
movilLatLng['lng'] = <?=$arrReporte['sh_longitud']?$arrReporte['sh_longitud']:0?>;

var refLatLng = [];
refLatLng['lat'] = '<?=$estadoViaje['latitud']?$estadoViaje['latitud']:0?>';
refLatLng['lng'] = '<?=$estadoViaje['longitud']?$estadoViaje['longitud']:0?>';

var registros, registros_ordenados, arrBitMotor, celulares, token, zoomActual, zoomAnterior;
var total_registros = 0;	

$( document ).ready(function() {
	CrearMapa('mapa-historico');
	
	//-- referencia --//
	setMapObj(mapCircle(refLatLng['lat'], refLatLng['lng'], <?=$estadoViaje['radio']?$estadoViaje['radio']:0?>, '#4b5de4'));
	
	var srcIcono = '1/referencias/ref-wp.png';
	var arr = [];
	arr['lat'] = refLatLng['lat'];
	arr['lng'] = refLatLng['lng'];
	arr['icono'] = 'getImage.php?pathmode=rel&file='+srcIcono+'&caption=';
	var marker = mapMarker(arr);
	setMap(marker,false);
	
	//-- Movil --//
	var srcIcono = '1/<?=$arrDataMovil['carpetaImagen']?>/<?=$arrDataMovil['img']?>';
	var arr = [];
	arr['lat'] = movilLatLng['lat'];
	arr['lng'] = movilLatLng['lng'];
	arr['icono'] = 'getImage.php?pathmode=rel&file='+srcIcono+'&caption=';
	var marker = mapMarker(arr);
	marker.icon.size.w = 58;
	marker.icon.size.h = 78;
	setMap(marker,false);
	
	mapSetZoom(14);
	mapSetCenter(movilLatLng['lat'],movilLatLng['lng']);
	
});

function cerrarPopup(){
	<?php if($_GET['referer']){?>
		location.href = '<?=$_SERVER['HTTP_REFERER']?>';
	<?php }
	else{?>	
		window.parent.cerrarPopup();
	<?php }?>
}

var historicoStatus = '';
function verHistorico(){
	
	if(historicoStatus == ''){
		registros = <?=json_encode($arrHistorico)?>;
		arrBitMotor = <?=json_encode($arrModeloEquipos)?>;
	
		total_registros = registros.length
		crearMarcadores();
		
		map.events.register('zoomend', this, function (e){
			if(historicoStatus != 'off'){		
				zoomActual = mapGetZoom();
				cargarPuntos(-1,1);
				zoomAnterior = mapGetZoom();
			}
		});
			
		zoomActual = mapGetZoom();
		cargarPuntosInicial(-1);// Pinta el Camino en azul/violeta
		historicoStatus = 'on';
		$('a#verOcultarHistorico').html(arrLang['ocultar_historico']);
	}
	else if(historicoStatus == 'on'){
		var cant = markers.length;
		for(i=0; i<cant; i++){
			if(typeof(markerV[i].setVisibility) == 'function'){
				markerV[i].setVisibility(false);
			}
		}
		
		lineLayer.setVisibility(false);
		
		historicoStatus = 'off';
		$('a#verOcultarHistorico').html(arrLang['ver_historico']);
	}
	else if(historicoStatus == 'off'){
		zoomActual = mapGetZoom();
		cargarPuntos(-1,1);
		zoomAnterior = mapGetZoom();
		
		var cant = markers.length;
		for(i=0; i<cant; i++){
			if(typeof(markerV[i].setVisibility) == 'function'){
				if(marcadoresEnMapa[i]){
					markerV[i].setVisibility(true);
				}
			}
		}
		
		lineLayer.setVisibility(true);
				
		historicoStatus = 'on';
		$('a#verOcultarHistorico').html(arrLang['ocultar_historico']);
	}
}
</script>
