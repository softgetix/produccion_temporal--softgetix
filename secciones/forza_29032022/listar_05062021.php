<!--[if IE]>
	<link href="css/abmViajes_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<style>
	input.date{ margin:4px 0 0 8px; width:85px; height:25px;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	timeout = setInterval(function () {
    if (google.visualization != undefined) {

		var titulo = new Array();
		var ide = new Array();
		var cant = new Array();
		var i = 0;
		
		<?php if(count($data1)){?>	
		i++;
		titulo[i] = '<?=$lang->system->title_tareas_grafico_1?>'; 
		ide[i] = 'grafico1';
		cant[i] = <?=count($data1)?>;
		var data1 = google.visualization.arrayToDataTable([<?=$data1?>]);
		drawChartTorta(data1,titulo[i],ide[i]);
		<?php }?>
		
		<?php if(count($data2)){?>				
		i++;
		titulo[i] = '<?=$lang->system->title_tareas_grafico_2?>'; 
		ide[i] = 'grafico2';
		cant[i] = <?=count($data2)?>;
		var data2 = google.visualization.arrayToDataTable([<?=$data2?>]);
		drawChartBarras(data2,titulo[i],ide[i]);
		<?php }?>
		
		<?php if(count($data3)){?>
		i++;
		titulo[i] = '<?=$lang->system->title_tareas_grafico_3?>';
		ide[i] = 'grafico3';
		cant[i] = <?=count($data3)?>;
		var data3 = google.visualization.arrayToDataTable([<?=$data3?>]);
		drawChartLineas(data3,titulo[i],ide[i]);
		<?php }?>
		clearInterval(timeout);
        }
    }, 300);	
}); 
</script>

<style>
fieldset.grupo{border: 2px solid #D9D9D9; padding:6px; background:#FFF;}
fieldset.grupo h2{font-weight:bold; font-size:17px; color:#616161; margin-bottom:4px; line-height:24px; }
fieldset.grupo h2, fieldset.grupo p{margin-left:10px;}
input#buscador_viaje{left:10px;}
a#deleteBusqueda{margin-left:-45px;}
</style>
<?php if(($type == 'entregas' || $_GET['cargamanualok'] == 1) && tienePerfil(27)){?>
<fieldset class="grupo" style="margin-bottom:10px;">
	<h2><?=$lang->system->titulo_tareas?></h2>
	<p><?=$lang->system->subtitulo_tareas?></p>
    <br />
    <center><a href="javascript:;" onclick="enviar('alta');" class="button colorin" style="margin-top:5px; padding:8px 20px;"><?=strtoupper($lang->botonera->nueva_tarea)?></a></center>
	<br />
</fieldset>
<fieldset class="grupo" style=" margin:10px 0;">
<?php } else{?>
<fieldset class="grupo">
<?php }?>
	<h2><?=$lang->system->$type->titulo_monitoreo?></h2>
	<p><?=$lang->system->$type->subtitulo_monitoreo_1?></p>

<? if (tienePerfil(27)) { ?>

    <p><?=$lang->system->$type->subtitulo_monitoreo_2?></p>

<? } else { ?>

 	<p><?=$lang->system->$type->subtitulo_monitoreo_3?></p>
<? } ?>

    <br />
<center>
    <?php if(count($data1)){?><div id="grafico1" style="width: 26%; height:259px; display:inline-block;"></div><?php }?>
    <?php if(count($data2)){?><div id="grafico2" style="width: 40%; height:259px; display:inline-block;"></div><?php }?>
    <?php if(count($data3)){?><div id="grafico3" style="width: 30%; height:259px; display:inline-block;"></div><?php }?>
</center>
<span class="clear" style="height:<?=(count($data1) || count($data2) || count($data3) || count($data4))?'20px':'0px'?>;"></span>
<table width="100%" height="100%">
	<tbody>
    	<tr>
        	<td width="100%">
            	<!-- Inicio. listado de viajes -->
                <div class="solapas clear">
                	<span class="float_l tipo-listado gum" style="height:20px; width:0px;">&nbsp;</span>
                    <!--
                    <a href="javascript:getContenido('listado')" class="izquierda float_l tipo-listado active"><?//=$lang->system->informe?></a>
                    <a href="javascript:getContenido('grafico')" class="izquierda float_l tipo-grafico"><?//=$lang->system->estadistica?></a>
                    -->
                    <input type="text" name="buscar" id="buscador_viaje" class="buscar float_l" onkeyup="javascript:getBuscar(event, this.value)" title="<?=$lang->system->enter_buscar?>"  placeholder="<?=$lang->system->buscar_tarea?>" value="<?=$_POST['buscar']?>" autocomplete="off"/>
                    <a class="float_l margin_l button <?=($diffFechas==30 || $_POST['hidId'] == 'ultimo_mes')?'colorin':''?>" style="margin-top:5px;" onclick="javascript:enviar('filtroFecha','ultimo_mes');" href="javascript:;"><?=$lang->system->reporte_ultimo_mes?></a>
                    <a class="float_l margin_l button <?=($diffFechas==0 || ($diffFechas!=30 && $_POST['hidId'] != 'ultimo_mes'))?'colorin':''?>" style="margin-top:5px;" onclick="javascript:enviar('filtroFecha','hoy');" href="javascript:;"><?=$lang->system->reporte_tiempo_real?></a>
                    
                    <input type="hidden" class="date float_l" name="fdesde" id="fhasta" value="<?=$_POST['fdesde']?>" >
                    <input type="hidden"  class="date float_l" name="fhasta" id="fhasta" value="<?=$_POST['fhasta']?>" >
                    
                    <!--
					<ul id="btnExportar" class="float_r" style="margin-top:5px;">
                    	<li id="export-menu" class="button">
                        	<a href="javascript:;" onclick="enviar('export_estadias');"><?=$lang->botonera->exportar?></a>
                        </li>
					</ul>
                    -->
                 
                    <div class="contenido clear">
                    <table width="100%" height="100%">
						<thead>
							<tr>
                            	<td>&nbsp;</td>
								<td>
                                	<span class="campo1"><?=$lang->system->nro_orden?></span>
                                </td>
                                <td colspan="2">
                                	<?php //if($type == 'entregas'){
										echo $objFiltroCol->label($col['transportista'],$lang->system->asignar_tarea,'campo1');?>
										<!--<span class="campo2"><? //=$lang->system->movil?></span>-->
									<?php
									/*}
									else{?>
										<span class="campo1"><?=$lang->system->asignar_tarea?></span>
                                    	<?=$objFiltroCol->label($col['movil'],$lang->system->movil,'campo2');
									}*/?>
								</td>
								<td class="td-last">
                                	<?=$objFiltroCol->label($col['referencia'],$lang->system->referencia,'campo1')?>
                                    <span class="campo2"><?=$lang->system->ingreso_programado?></span>
                                </td>

							<? if ($type != 'entregas') { ?>
							
								<td class="td-last">
                                	<center>
										<?=$objFiltroCol->label($col['estado_check'],'Estado','campo1')?>
                                    </center>
                                    <br />
								</td>

							<?  }   ?>

		
                                
								
                            </tr>
						</thead>
                        <tbody>
						<?php
						foreach($arrViajes as $k => $row){
							$verContenido = ($codViaje != $row['vi_id'])?1:0;
							$codViaje = $row['vi_id'];
							if($verContenido){
								$totalRegistros++;
								$fila = ($fila == 'filaImpar')?'filaPar':'filaImpar';
							}?>	
							<tr class="<?=$fila?> <?=(!$verContenido)?'destinos_'.$row['vi_id'].' row_viajes':''?> <?=($row['vi_finalizado'])?'viaje-finalizado':''?>" <?php if(!$verContenido){?>style="display:none" <?php }?>>
                                <td align="center" width="10">
                                <?php if($verContenido && $arrViajes[$k+1]['vi_id'] == $row['vi_id']){?>
                                	<a href="javascript:viewDestinos('<?=$row['vi_id']?>')" class="icon colapsar off no_margin no_padding" id="link_<?=$row['vi_id']?>" ></a>
                                <?php }?>
                                </td>
                                <td>
									<?php if($verContenido){?>
                                    	<a href="javascript:;" class="link float_l" onclick="javascript:enviarModificacion('modificar',<?=$row['vi_id']?>)">
                                            <?=encode($row['vi_codigo'])?>
                                        </a>
                                        <input type="checkbox" id="chk_<?=$row['vi_id']?>" name="chkId[]" value="<?=$row['vi_id']?>" style="display:none">
                                     <?php }?>
								</td>
								<?php if(!tieneperfil(array(27,28))){?>
                                <td>
                                	<?php if($verContenido){?>
									<?php if($row['id_movil']){
										if($objViaje->tieneMovilAsignado($row['id_movil'])){?>
                                        <a title="<?=$lang->botonera->ver_mapa?>" class="float_l" href="javascript:mostrarPopup('boot.php?c=abmViajesDeliveryMapa&action=popup&idMovil=<?=$row['id_movil']?>&idRef=<?=$row['re_id']?>',740,450)">
                                            <span class="sprite mapa no_margin"></span>
                                        </a>
                                    	<?php }
									}?>
                                    <?php }?>
								</td>
								<?php }?>    
                                <td <?=tieneperfil(array(27,28)) ? 'colspan=2':''?> >
                                	<?php if($verContenido){?>
                                	<span id="condMovil_<?=$row['vi_id']?>"> 
									<span class="campo1"><?=encode($row['co_conductor'])?></span>
									<br />
									<span class="campo2"><?=encode($row['transportista'])?></span>
									<!--<br /><span class="campo2"><?//=$row['vi_movil']?></span>-->									
									<?php }?>
                                </td>
                                <td>
									<span class="campo1"><?=encode($row['re_nombre'])?></span>
                                    <br />
									<span class="campo2"><?=formatearFecha($row['vd_ini'])?></span>

									<?php if($row['vd_stock'] > 0){?>
									<br />
                                    <span class="campo1">Pallets: <?=$row['vd_stock']?></span>
									<?php }?>	
                                </td>

					
					<? if ($type != 'entregas') { ?>



								<td class="td-last">
									<center><span class="campo1"><?=($row['vd_checked'] == 1) ? 'Confirmado' : ($row['vd_checked'] === 0 ? 'Rechazado' : 'Pendiente')?></span></center>
                                </td>

					<? } ?>

                                
								
							</tr>
                            <?php }?>
                            <?php if(!$arrViajes[0]){?>
							<tr class="tr-last">
								<td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
							</tr>
							<?php }
							else{?>
							<tr class="tr-last">
                                <td></td>
								<td></td>
								<td></td>
                                <td></td>
				<? if ($type != 'entregas') { ?>

                                <td></td>
				<? } ?>

                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="7">
									<strong>Cantidad de viajes: <?=$totalRegistros?></strong>
                                	<span class="float_r"><?=$objViaje->getRefInfo($filtros)?></span>
                                </td>
							</tr>
							<?php }?>
						</tbody>
                    </table>
                    </div><!-- fin. contenido -->
                    <!--<fieldset class="gum_top2" style="width:100%"> 
                    	<span class="etiqueta-referencia color-fiajes-finalizados  float_l"></span>
                        <span class="txt-referencia float_l"><?=$lang->system->tarea_finalizada?></span>
					</fieldset>-->
				</div>
                <!-- Fin. listado de viajes -->
			</td>
		</tr>
	</tbody>
</table>
</fieldset>