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

        <?php if(count($data5)){?>				
		i++;
		titulo[i] = 'Confirmación de entregas'; 
		ide[i] = 'grafico5';
		cant[i] = <?=count($data5)?>;
		var data5 = google.visualization.arrayToDataTable([<?=$data5?>]);
		drawChartTorta(data5,titulo[i],ide[i]);
		<?php }?>
	}); 
</script>
<center>
    <?php if(count($data1)){?><div id="grafico1" style="width: 18%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data2)){?><div id="grafico2" style="width: <?=(!count($data1) && count($data4))?26:35?>%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data4)){?><div id="grafico4" style="width: 40%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data3)){?><div id="grafico3" style="width: 25%; height:259px; display:inline-block"></div><?php }?>
    <?php if(count($data5)){?><div id="grafico5" style="width: 18%; height:259px; display:inline-block"></div><?php }?>
</center>
<span class="clear" style="height:<?=(count($data1) || count($data2) || count($data3) || count($data4))?'20px':'0px'?>;"></span>
<?php if(!tienePerfil(array(8,12))){?>
<center>
	<div class="stats">
       
        <span>Detecci&oacute;n de entregas por GPS <strong><?='('.$stats['en_destino']['detectados'].'/'.$stats['en_destino']['total'].') '.$stats['en_destino']['promedio'].'%'?></strong></span>
        <span>Moviles asociados a entregas Reportando <strong><?='('.$stats['moviles']['reportando'].'/'.$stats['moviles']['total'].') '.$stats['moviles']['promedio'].'%'?></strong></span>
    </div>
</center>
<?php }?>

<input type="hidden" name="viaje_instancia" id="viaje_instancia" value="" />                  
<table width="100%" height="100%">
	<tbody>
    	<tr>
        	</td>
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
                        <input type="text" name="buscar" id="buscador_viaje" class="buscar float_l" onkeyup="javascript:getBuscar(event, this.value)" title="<?=$lang->system->enter_buscar?>"  placeholder="<?=$lang->system->buscar_delivery?>" value="<?=$_POST['buscar']?>" autocomplete="off"/>
                        <input type="text"  class="date fdesde float_l" name="fdesde" placeholder="<?=$lang->system->fecha_inicio?>" value="<?=$_POST['fdesde']?>">
                        <input type="text"  class="date fhasta float_l" name="fhasta" placeholder="<?=$lang->system->fecha_fin?>" value="<?=$_POST['fhasta']?>">
                        <a class="float_l margin_l button colorin" style="margin-top:5px;" href="javascript:enviar('index');"><?=$lang->botonera->buscar?></a>

                        <ul id="btnExportar" class="float_r" style="margin-top:5px;" >
                            <li id="export-menu" class="button">
                                <a href="javascript:;" onclick="enviar('export_delivery');"><?=$lang->botonera->exportar?></a>
                            </li>
                        </ul>
                        <?php 
                        if($procesarViajes){?>
                            <?php if($arrProcesarViajes['vp_id']){?>
                            <div class="margin_r" id="procesarViajes">An&aacute;lisis retroactivo solicitado para los d&iacute;as <?=date('d-m-Y',strtotime($arrProcesarViajes['vp_ini']))?> al <?=date('d-m-Y',strtotime($arrProcesarViajes['vp_fin']))?></div>
                            <?php }
                            else{?>
                            <a class="float_r margin_r button colorin" style="margin-top:5px;" href="javascript:mostrarPopup('boot.php?c=abmViajesDeliveryAltaMasiva&action=procesar_viajes',500,310);">Solicitar An&aacute;lisis Retroactivo</a>   
                            <?php }?>
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
                                    <br />
                                    <br />
                                </td>
                                <td>
                                    <span class="campo1"><?=$lang->system->delivery?></span>
                                    <br />
                                    <span class="campo2"><?=$lang->system->pedidos?></span>
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
                                	<center><?=$objFiltroCol->label($col['iniReal'],$lang->system->ingreso_real,'campo1')?></center>
                                </td>
								<td <?=($_SESSION['idAgente'] != 4835)?'class="td-last"':''?>>
									<center><?=$objFiltroCol->label($col['finReal'],$lang->system->estadia,'campo1')?></center>
                                    <br />
                                </td>
                                <?php if($_SESSION['idAgente'] == 4835){?>
                                <td class="td-last">
									<center><?=$objFiltroCol->label($col['pod'],'POD','campo1')?></center>
                                    <br />
                                </td>
                                <?php }?>
                            </tr>
						</thead>
                        <tbody>
						<?php 
						foreach($arrViajes as $k => $row){
                            $verContenido = ($viBan != $row['vi_id'])?1:0;
                            $verSubContenido = ($vdBan != $row['vd_id'] && !$verContenido)?1:0;
                            
                            $viBan = $row['vi_id'];
                            $vdBan = $verContenido?$vdBan:$row['vd_id'];
                            
                            if($verContenido){
								$totalRegistros++;
								$fila = ($fila == 'filaImpar')?'filaPar':'filaImpar'; 
							}?>	
                            <tr class="<?=$fila?> <?=($verContenido && !$verSubContenido)?'':((!$verContenido && $verSubContenido)?'destinos_'.$row['vi_id'].' row_viajes':(!$verSubContenido?'entrega_'.$row['vd_id'].' destinoEntrega_'.$row['vi_id']:''))?> <?=($row['vi_finalizado'])?'viaje-finalizado':''?>" <?=(!$verContenido /*|| !$verSubContenido*/)?'style="display:none"':''?>>
                                <td align="center" width="10">
                                <?php if(($verContenido && $arrViajes[$k+1]['vi_id'] == $row['vi_id'])){?>
                                    <a href="javascript:viewOptions('destinos','<?=$row['vi_id']?>','destinoEntrega_<?=$row['vi_id']?>')" class="icon colapsar off no_margin no_padding" title="<?=$lang->system->ver?> <?=$destinos[$row['vi_id']]?> <?=($destinos[$row['vi_id']] > 1)?$lang->system->destinos:$lang->system->destino?>" id="link_<?=$row['vi_id']?>" ></a>
                                <?php }elseif($verSubContenido && $arrViajes[$k+1]['vd_id'] == $row['vd_id']){?>
                                	<a href="javascript:viewOptions('entrega','<?=$row['vd_id']?>',null)" class="icon colapsar colapsarV2 off no_margin no_padding" title="<?=$lang->system->ver?>" id="link_<?=$row['vd_id']?>" ></a>
                                <?php }?>
                                </td>
                                <td>
									<?php if($verContenido){?>
                                    	<a href="javascript:;" class="link float_l" onclick="javascript:enviar('verDetalle',<?=$row['vi_id']?>)">
                                            <?=encode($row['vi_codigo'])?>
                                        </a>
                                     <?php }?>
								</td>
                                <td>
									<span class="campo1"><?=encode($row['vdd_delivery'])?></span>
                                	<br />
									<?php if($row['vdd_id']){?>
                                    <span class="campo2"><?=$objViaje->getCodigoPedidos($row['vdd_id'],'<br>')?></span>
                                    <?php }?>
                                </td>
                                <td>
									<span class="campo1 transportista <?='vi_'.$row['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>"><?=encode($row['transportista'])?></span>
                                	<br />
									<span class="campo2"><?=encode($row['dador'])?></span>
                                </td>
                                <td>
                                	<?php if($row['id_movil']){
										if($objViaje->tieneMovilAsignado($row['id_movil'])){?>
                                        <a title="<?=$lang->botonera->ver_mapa?>" class="float_l viewCarOnMap <?='vi_'.$row['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>" <?=(!$row['id_movil'])?'style="display:none"':''?> href="javascript:;" attrIdMovil="<?=$row['id_movil']?>" attrIdRef="<?=$row['re_id']?>">
                                            <span class="sprite mapa no_margin"></span>
                                        </a>
                                    	<?php }
									}?>
                                </td>    
								<td>
                                	<a href="javascript:;" class="float_r setNewcar <?=$verContenido?'firts':''?>" title="<?=$lang->system->asignar_conductor_movil?>"
                                        attr_vdd_id="<?=$row['vdd_id']?>" attr_vi_id="<?=$row['vi_id']?>" attr_crossdocking="<?=$row['vi_crossdocking']?>"
                                        attr_cl_id="<?=($row['vdd_id'])?(int)$row['vdd_cl_id']:(int)$row['vi_transportista']?>"
                                        attr_co_id="<?=($row['vdd_id'])?(int)$row['vdd_co_id']:(int)$row['vi_co_id']?>"
                                        attr_mo_id="<?=(int)$row['id_movil']?>"
                                        style="display:<?=$row['vi_crossdocking']?'block':($verContenido?'block':'none')?>">
                                        <span class="sprite editar"></span>
                                    </a>
                                    <span class="block_info_movil <?='vi_'.$row['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>"> 
                                        <span class="campo1"><?=$row['vi_movil']?></span>
                                        <br />
                                        <span class="campo2"><?=encode($row['co_conductor'])?></span>
                                        <span class="campo2 block"><?=encode($row['co_telefono'])?></span>
                                        
                                        <?php if(!$row['id_movil'] && $row['disponibilidad']){?>
										<center style="width:150px;"><span style="color:#09F; font-style:italic;"><?=$lang->system->disponibilidad_transportista?></span></center>
										<?php }?>
                                    </span>
                                </td>
                                <td>
									<span class="campo1"><?=encode($row['re_nombre'])?></span>
                                    <br />
                                    <span class="campo2"><?=formatearFecha($row['fecha_ini'])?></span>
                                </td>
                                <td style="vertical-align:middle">
                                    <center>
                                    <span class="campo1"><?=formatearFecha($row['fecha_ini_real'])?></span>
                                    <span class="campo3 clear"><?=$row['retroactivo']?'Actualizado mediante Proceso Retroactivo':''?></span>
                                </td>
                                <td <?=($_SESSION['idAgente'] != 4835)?'class="td-last"':''?> style="vertical-align:middle">
				                <center>
                                <?php if(trim($row['fecha_ini_real']) == true && trim($row['fecha_fin_real']) == true){?>
                                        <span class="campo1"><?=$objViaje->getTiempoHM(strtotime($row['fecha_fin_real']) - strtotime($row['fecha_ini_real']))?></span>
                                    <?php }
                                    elseif(!empty($row['paso_instancia']) && $row['fecha_ini_real']){
					                    $aux = explode('#',$row['paso_instancia']);?>
                                        <a href="javascript:$('#hidId').val(<?=$row['vi_id']?>); $('#viaje_instancia').val(<?=$aux[0]?>); enviar('modificarInstancia');">
                                        <div class="estado-viaje" style="float:none;"><?=encode($aux[1])?></div>
                                        </a>
                                    <?php }?>
                                    </center>
                                </td>
                                <?php if($_SESSION['idAgente'] == 4835){?>
                                <td class="td-last">
                                    <center><span class="campo1"><?=($row['vd_estado'] === 1)?'Si':'No'?></span></center>
                                </td>
                                <?php }?>
							</tr>
                            <?php }?>
                            <?php if(!$arrViajes[0]){?>
							<tr class="tr-last">
								<td class="td-last" colspan="10"><center><?=$lang->message->sin_resultados?></center></td>
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
                                <td></td>
                                <?=($_SESSION['idAgente'] == 4835)?'<td></td>':''?>
                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="9">
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