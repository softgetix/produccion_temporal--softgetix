<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta name="robots" content="noindex,nofollow">
<link href="css/estilosTimeline.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.1.7.1.min.js"></script>
<script type="text/javascript" src="js/timelineFunciones.js"></script>


</head>
<?php 
$viajesID = array();
?>
<body onLoad="setInterval('posicionMovil()',60000); setInterval('serverHour()',1000);" onresize="javascript:alinear();alinearLogo();">
<table class="tabla-principal">
	<tr>
    	<td class="td-principal">

<table width="400" id="table-info" class="class-fixed">
    <thead id="enc-info-fixed">
    	<tr>
        <td colspan="2" rowspan="3" class="logo enc-show">
        	<img src="<?='imagenes/img-clientes/'.LOGO?>"  style="max-width:282px; max-height:101px; margin-bottom:10px;" />
            <div align="center">
            <input type="text" name="buscador" class="buscar" onkeyup="javascript:getBuscar(this.value)"/>
            </div>
        </td>
        <td class="info-table hora-show">
        	<span id="fijar-info">Fijar Info</span>
            <input type="checkbox" id="fijar_info" checked="checked" onchange="javascript:fijarInfo(this.checked)" />
        </td>
        </tr>
        <tr>
        	<td id="fecha_dia"><div>&nbsp;</div></td>
		</tr><!-- .Termina linea de tiempo en horas-->
        <tr>
        	<td id="fecha_hora"><div>&nbsp;</div></td>
		</tr><!-- .Termina linea de tiempo en minutos-->
    </thead>
    <thead>
    	<tr>
        	<td colspan="2" rowspan="3" class="logo enc-hide">&nbsp;</td>
        	<td class="info-table hora-hide">&nbsp;</td>
        </tr>
        <tr><td id="fecha_dia_hide">&nbsp;</td></tr>
        <tr><td id="fecha_hora_hide">&nbsp;</td></tr>
     </thead>
     <tbody>
     	<?php if(count($arrItinerario)){?>
			<?php foreach($arrItinerario as $k =>$viaje){?>
            <tr class="display_<?=$k?>"><td colspan="3" class="separador no_padding">&nbsp;</td></tr>
            <tr class="display_<?=$k?>">
                <td id="viaje_<?=$k?>" class="row r_viaje" style="background:<?=$arrItinerario[$k]['info']['color1']?> !important" height="32">
					<p style="font-size:16px"><?=$arrItinerario[$k]['info']['viajeNombre']?></p>
                    <?=count($arrItinerario[$k]['info']['delivery'])?'('.implode(',',$arrItinerario[$k]['info']['delivery']).')':''?>
                </td>
                <td id="chofer_<?=$k?>" class="row r_chofer" style="background:<?=$arrItinerario[$k]['info']['color1']?> !important">
					<?=$arrItinerario[$k]['info']['transportista']?><br /><?=$arrItinerario[$k]['info']['matricula']?> - <?=$arrItinerario[$k]['info']['semi']?>
                </td>
                <td id="cliente_<?=$k?>" class="row r_cliente" style="background:<?=$arrItinerario[$k]['info']['color1']?> !important">
					<?=$arrItinerario[$k]['info']['conductor']?><br /><?=$arrItinerario[$k]['info']['conductortel']?>
                </td>
            </tr><!-- .Termina historia del viaje-->
            <tr class="display_<?=$k?>">
                <td id="patente_<?=$k?>" style="background:<?=$arrItinerario[$k]['info']['color2']?> !important">
					<?=$arrItinerario[$k]['info']['ref_inicio']?> - <?=formatearFecha($arrItinerario[$k]['info']['hs_inicio'],'short')?>
                    <br /><?=$arrItinerario[$k]['info']['ref_fin']?> - <?=formatearFecha($arrItinerario[$k]['info']['hs_fin'],'short')?>
                    <br /><strong>Tiempo programado (hs)</strong>: <?=$arrItinerario[$k]['info']['horasViaje']?>
                    <input type="hidden" id="hs_programado_<?=$k?>" value="<?=$arrItinerario[$k]['info']['horasViaje']?>" />
                </td>
                <td id="actualizacion1_<?=$k?>" style="background:<?=$arrItinerario[$k]['info']['color2']?> !important"></td>
                <td id="actualizacion2_<?=$k?>" style="background:<?=$arrItinerario[$k]['info']['color2']?> !important"></td>
            </tr> <!-- .Termina estado actual del movil-->
            <?php }?>
        <?php }
		else{?>
        	<tr><td colspan="3" class="separador no_padding">&nbsp;</td></tr>
            <tr><td colspan="3">&nbsp;</td></tr>
		<?php }?>
        <tr><td colspan="3" id="not-find-busqueda" class="none" style="border:none; background:#FFF !important;">No se encontraron resultados</td></tr>
	</tbody>
</table>
</td>
<td class="td-principal">

<table class="class-absolute">
	<!-- ## INICIO  Encabezado DUPLICADO para dejarlo fijo y no romper toda la estructura ##-->
    <thead id="enc-datos-fixed">
    	<tr><td colspan="144" class="info-table">&nbsp;</td></tr>
        <tr>
        	<?php for($i=0; $i<24; $i++){?>
			<td colspan="6" class="align_center"><?=$i?></td>
            <?php }?>
		</tr><!-- .Termina linea de tiempo en horas-->
        <tr>
        	<?php $nro = 1;?>
            <?php for($h=0; $h<24; $h++){?>
				<?php for($i=0; $i<60; $i=$i+10){?>
                <td class="padding_time_td"><span id="celdas_show_<?=$nro++?>" class="padding_time"><?=$i?></span></td>
                <?php }?>
            <?php }?>    
		</tr><!-- .Termina linea de tiempo en minutos-->
     </thead>
     <!-- ## FIN ##-->
     <thead>
    	<tr><td colspan="144" class="info-table">&nbsp;</td></tr>
        <tr>
        	<?php for($i=0; $i<24; $i++){?>
			<td colspan="6" class="align_center"><?=$i?></td>
            <?php }?>
		</tr><!-- .Termina linea de tiempo en horas-->
        <tr>
        	<?php $nro = 1;?>
            <?php for($h=0; $h<24; $h++){?>
				<?php for($i=0; $i<60; $i=$i+10){?>
                <td id="celdas_hide_<?=$nro++?>" class="padding_time_td"><span class="padding_time"><?=$i?></span></td>
                <?php }?>
            <?php }?>    
		</tr><!-- .Termina linea de tiempo en minutos-->
     </thead>
     <tbody>
     	<?php if(count($arrItinerario)){?>
			<?php foreach($arrItinerario as $k =>$viaje){?>
            <tr class="display_<?=$k?>"><td colspan="144" class="separador no_padding">&nbsp;</td></tr>
            <tr class="display_<?=$k?>">
				<?php $filas = 1;
                foreach($viaje['destinos'] as $item){?>
                    <?php for($i = $filas; $i < $item['ti']; $i++){?>
                        <td id="vehiculo_<?=$k?>_<?=$i?>" class="row_vehiculo_<?=$k?>" height="32">&nbsp;</td>
                    <?php }?>
                    <td colspan="<?=((int)$item['tf'] - (int)$item['ti']) + 1?>" style="background:<?=$item['color']?>" height="32" class="row_vehiculo_<?=$k?>"><p style="overflow:hidden; display:block;"><?=$item['pto_visitar']?></p></td>
                    <?php //$filas = $item['tf'] + 1;
					$filas = ($item['tf'] > $item['ti'])?(($item['tf'] - $item['ti'] + 1) + $i):($item['ti'] + 1);
                }
                for($i = $filas; $i <= 144; $i++){?>
                        <td class="trayectoria_inactiva row_vehiculo_<?=$k?>" id="vehiculo_<?=$k?>_<?=$i?>" height="32">&nbsp;</td>
                <?php }?>
            </tr><!-- .Termina historia del viaje-->
            <tr class="display_<?=$k?>">
                <td colspan="144" class="guia_vehiculo row_guia_<?=$k?>">
                	<span class="auto block" id="movil_<?=$k?>" style="left:-9px; height:60px;">
                    	<img src="<?php //=$images['vehiculo']?>" class="block" />
                        <span class="etiqueta_movil"></span>
                    </span>
                </td>
            </tr> <!-- .Termina estado actual del movil--> 
			<?php array_push($viajesID,$k);?>
			<?php }?>
        <?php }
		else{?>
        	<tr><td colspan="144" class="separador no_padding">&nbsp;</td></tr>
            <tr><td colspan="144" class="align_left">No se han definido viajes</td></tr>
		<?php }?>
	</tbody>
</table>

</td></tr>
</table>
<input type="hidden" name="movilID" id="movilID" value="<?=json_encode($viajesID)?>" />
</body>
</html>
<script>
$(document).ready(function(){ 
	serverDate();
	alinearEncabezado();
	posicionMovil();
});
</script>