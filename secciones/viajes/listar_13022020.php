<!--[if IE]>
	<link href="css/abmViajes_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<style>
	input.date{ margin:4px 0 0 8px; width:85px; height:25px;}
</style>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('visualization', '1', {packages: ['corechart']});</script>		
<script type="text/javascript">
 	$(document).ready(function() {
		var titulo = new Array();
		var ide = new Array();
		var cant = new Array();
		var i = 0;
		
		<?php if(count($data1)){?>	
		i++;
		titulo[i] = 'Distribución total de viajes'; 
		ide[i] = 'grafico1';
		cant[i] = <?=count($data1)?>;
		var data1 = google.visualization.arrayToDataTable([<?=$data1?>]);
		drawChartTorta(data1,titulo[i],ide[i]);
		<?php }?>
		
		<?php if(count($data2)){?>				
		i++;
		titulo[i] = 'Distribución por transportista'; 
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
		
		<?php if(count($data4)){?>
		i++;
		titulo[i] = '<?=$lang->system->title_tareas_grafico_4?>';
		ide[i] = 'grafico4';
		cant[i] = <?=count($data4)?>;
		var data4 = google.visualization.arrayToDataTable([<?=$data4?>]);
		drawChartBarras(data4,titulo[i],ide[i]);
		<?php }?>
	}); 
</script>
<center>
    <?php if(count($data1)){?><div id="grafico1" style="width: 26%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data2)){?><div id="grafico2" style="width: <?=(!count($data1) && count($data4))?26:40?>%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data4)){?><div id="grafico4" style="width: 40%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data3)){?><div id="grafico3" style="width: 30%; height:259px; display:inline-block"></div><?php }?>
</center>
<span class="clear" style="height:<?=(count($data1) || count($data2) || count($data3) || count($data4))?'20px':'0px'?>;"></span>
<?php if(!tienePerfil(array(8,12))){?>
<center>
	<div class="stats">
        <span>Detecciones en Origen <strong><?='('.$stats['en_origen']['detectados'].'/'.$stats['en_origen']['total'].') '.$stats['en_origen']['promedio'].'%'?> </strong></span>
        <span>Detecciones en Destino <strong><?='('.$stats['en_destino']['detectados'].'/'.$stats['en_destino']['total'].') '.$stats['en_destino']['promedio'].'%'?></strong></span>
        <span>Moviles Reportando <strong><?='('.$stats['moviles']['reportando'].'/'.$stats['moviles']['total'].') '.$stats['moviles']['promedio'].'%'?></strong></span>
    </div>
</center>
<?php }?>
<table width="100%" height="100%">
	<tbody>
    	<tr>
        	<td width="100%">
            	<!-- Inicio. listado de viajes -->
                <div class="solapas gum clear">
					<?php include('includes/navbarSolapas.php');?>
					<!--
                	<a href="javascript:getContenido('listado')" class="izquierda float_l tipo-listado active"><?=$lang->system->informe?></a>
                    <a href="javascript:getContenido('grafico')" class="izquierda float_l tipo-grafico"><?=$lang->system->estadistica?></a>
                    -->

					<div class="contenido clear" style="height:100%">
						<!-- -->
						<div class="box_buscador">
						<input type="text" name="buscar" id="buscador_viaje" class="buscar float_l" onkeyup="javascript:getBuscar(event, this.value)" title="<?=$lang->system->enter_buscar?>"  placeholder="<?=$lang->system->buscar_viaje?>" value="<?=$_POST['buscar']?>" autocomplete="off"/>
						<input type="text"  class="date float_l" name="fdesde" placeholder="<?=$lang->system->fecha_inicio?>" value="<?=$_POST['fdesde']?>" >
						<input type="text"  class="date float_l" name="fhasta" placeholder="<?=$lang->system->fecha_fin?>" value="<?=$_POST['fhasta']?>" >
						<a class="float_l margin_l button colorin" style="margin-top:5px;" href="javascript:enviar('index');"><?=$lang->botonera->buscar?></a>
						
						<ul id="btnExportar" class="float_r">
							<li id="export-menu" class="button">
								<a href="javascript:;"><?=$lang->botonera->exportar?></a>
								<ul>
									<li><a href="javascript:;" onclick="enviar('export_arribos');"><?=$lang->botonera->exportar_arribos?></a></li>
									<li><a href="javascript:;" onclick="enviar('export_partidas')"><?=$lang->botonera->exportar_partidas?></a></li>
									<li><a href="javascript:;" onclick="enviar('export_estadias')"><?=$lang->botonera->exportar_estadias?></a></li>
								</ul>
							</li>
						</ul>
						<?php if(tienePerfil(array(5,6,7,9,10,11,19))){?>
						<a href="javascript:;" onclick="enviar('alta');" class="button colorin float_r margin_r"><?=$lang->botonera->crear_viajes?></a>
						<?php }?>
						<span class="clear"></span>
						</div>
						<!-- -->
					
                    <table class="listado-viajes" width="100%" height="100%">
						<thead>
							<tr>
                            	<td>&nbsp;</td>
								<td>
                                	<span class="campo1"><?=$lang->system->codigo_viaje?></span>
                                </td>
                                <td>
                                    <?=$objFiltroCol->label($col['transportista'],$lang->system->transportista,'campo1')?>
                                    <span class="campo2"><?=$lang->system->dador?></span>
                                </td>
								<td colspan="2">
                                	<?=$objFiltroCol->label($col['movil'],$lang->system->movil,'campo1')?>
                                    <span class="campo2"><?=$lang->system->conductor?></span>
                                </td>
								<td>
                                	<?=$objFiltroCol->label($col['referencia'],$lang->system->referencia,'campo1')?>
                                    <span class="campo2"><?=$lang->system->ingreso_programado?></span>
                                </td>
								<td>
                                	<center>
                                    	<?=$objFiltroCol->label($col['iniReal'],$lang->system->ingreso_real,'campo1')?>
                                    </center>
                                    <br />
								</td>
                                <td class="td-last">
									<center>
                                    	<?=$objFiltroCol->label($col['finReal'],$lang->system->estadia,'campo1')?>
                                    </center>
                                    <br />
                                </td>
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
                                	<a href="javascript:viewDestinos('<?=$row['vi_id']?>')" class="icon colapsar off no_margin no_padding" title="<?=$lang->system->ver?> <?=$destinos[$row['vi_id']]?> <?=($destinos[$row['vi_id']] > 1)?$lang->system->destinos:$lang->system->destino?>" id="link_<?=$row['vi_id']?>" ></a>
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
                                <td>
									<?php if($verContenido){?>
                                    <span class="campo1"><?=encode($row['transportista'])?></span>
                                	<br />
									<span class="campo2"><?=encode($row['dador'])?></span>
                                    <?php }?>
                                </td>
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
                                <td <?=(!tienePerfil(array(5,6,7,8,19)))?'colspan="2"':''?> >
                                	<?php if($verContenido){?>
                                	<a href="javascript:agregarConductor(<?=$row['vi_id']?>, <?=(int)$row['id_transportista']?>, <?=(int)$row['id_conductor']?>, <?=(int)$row['id_movil']?>);" class="float_r" title="<?=$lang->system->asignar_conductor_movil?>"><span class="sprite editar"></span></a>
                                    <span id="condMovil_<?=$row['vi_id']?>"> 
                                    <span class="campo1"><?=$row['vi_movil']?></span>
                                    <br />
                                    <span class="campo2"><?=encode($row['co_conductor'])?></span>
                                    <span class="campo2 block"><?=encode($row['co_telefono'])?></span>
                                    <?php }?>
                                </td>
                                <td>
									<span class="campo1">(<?=!empty($row['re_numboca'])?$row['re_numboca']:$row['re_numboca']?>) <?=encode($row['re_nombre'])?></span>
                                    <br />
                                    <span class="campo2"><?=formatearFecha($row['vd_ini'])?></span>
                                </td>
                                <td style="vertical-align:middle">
                                	<center>
                                    <span class="campo1">
                                        <?=formatearFecha($row['vd_ini_real'])?>
								    </span>
                                    </center>
								</td>
                                <td class="td-last" style="vertical-align:middle">
									<center>
                                   	<?php 
									if(trim($row['vd_ini_real']) == true && trim($row['vd_fin_real']) == true){?>	
										 <span class="campo1"><?=$objViaje->getTiempoHM(strtotime($row['vd_fin_real']) - strtotime($row['vd_ini_real']))?></span>
									<?php }?>
                                    </center>
                                </td>
							</tr>
                            <?php }?>
                            <?php if(!$arrViajes[0]){?>
							<tr class="tr-last">
								<td class="td-last" colspan="8"><center><?=$lang->message->sin_resultados?></center></td>
							</tr>
							<?php }
							else{?>
							<tr class="tr-last">
								<td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="8">
									<strong><?=$totalRegistros?> <?=($totalRegistros > 1)?$lang->system->viajes:$lang->system->viaje?></strong>
                                	<span class="float_r"><?=$objViaje->getRefInfo($filtros)?></span>
                                </td>
							</tr>
							<?php }?>
						</tbody>
                    </table>
                    </div><!-- fin. contenido -->
                    <fieldset class="gum_top2" style="width:100%"> 
                    	<span class="etiqueta-referencia color-fiajes-finalizados  float_l"></span>
                        <span class="txt-referencia float_l"><?=$lang->system->viaje_finalizado?></span>
					</fieldset>
				</div>
                <!-- Fin. listado de viajes -->
			</td>
		</tr>
	</tbody>
</table>