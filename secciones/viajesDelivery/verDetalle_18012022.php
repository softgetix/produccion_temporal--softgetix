<script type="text/javascript">
	$(document).ready(function() {
		/*$(".date").live("focusin", function() { 
		   $(this).datepicker({
				onSelect: function(objDatepicker){
					var $aux =  $(this).attr('id').split('_');
					onChanges($aux[1], 'comboFecha');
				}
			});
		});*/
		
		$(".date2").live("focusin", function() {
            $(this).datepicker({});
			//$(this).removeClass('hasDatepicker');
		});
    });
    
    arrLang['msj_viaje_reset_ingreso'] = '<?=$lang->message->msj_viaje_reset_ingreso?>';
	arrLang['msj_viaje_reset_egreso'] = '<?=$lang->message->msj_viaje_reset_egreso?>';
	arrLang['solicitud_enviada'] = '<?=$lang->message->error->solicitud_enviada?>';
</script>

<div id="mainBoxAM" style="margin:-10px 0 0 0;">
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"/>
<input type="hidden" id="id_viaje" value="<?=$id_viaje?>" />
<table width="100%" height="100%">
<tbody>
	<tr>
    	<td width="100%">
        <div class="solapas gum clear" style="overflow:hidden;"><!-- height:92%;-->
            <a href="javascript:getContenido('listado');" class="izquierda float_l tipo-listado <?=(empty($_POST['hidSolapa']) || $_POST['hidSolapa']=='listado')?'active':''?>"><?=$lang->system->referencia_ruteo?></a>
            <?php if(!tienePerfil(8)){?>
            <a href="javascript:getContenido('observaciones')" class="izquierda float_l tipo-observaciones <?=($_POST['hidSolapa']=='observaciones')?'active':''?>"><?=$lang->system->observaciones?></a>
            <a href="javascript:getContenido('historico')" class="izquierda float_l tipo-historico"><?=$lang->system->historial?></a>
            <?php }?>
            <a href="javascript:getContenido('pod')" class="izquierda float_l tipo-pod">Documentos (remitos y firma)</a>
            
            <div id="botonesABM" class="float_r margin_r" style="width:120px;">
            	<span id="botonVolver" onclick="javascript:enviar('volver');" >
                	<img src="imagenes/botonVolver.png" alt="" /> 
					<?=$lang->botonera->volver?> 
				</span>
			</div>
            
            <div id="viajes-listado" class="contenido clear" style="height:100%; <?=(!empty($_POST['hidSolapa']) && $_POST['hidSolapa']!='listado')?'display:none':''?>">
            	<fieldset class="float_l">
                    <fieldset class="float_l clear margin_l">
                        <p><strong><?=$lang->system->codigo_viaje?>: </strong><span><?=encode($datosViaje['vi_codigo'])?></span></p>
                        <?php if(!tienePerfil(8)){?>
                            <p><strong><?=$lang->system->tipo_viaje?>: </strong><span><?=encode($datosViaje['vt_nombre'])?></span></p>
                            <p><strong><?=$lang->system->distancia_acumulada?>: </strong><span><?=$distViaje?></span></p>
                            <?php if(isset($tiempoViaje['programado'])){?>
                            <p><strong><?=$lang->system->tiempo_programado?>: </strong><span><?=$tiempoViaje['programado']?></span></p>
                            <p><strong><?=$lang->system->tiempo_real?>: </strong><span><?=$tiempoViaje['real']?></span></p>
                            <?php }?>
                        <?php }?>
                    </fieldset>
                </fieldset>
                
                
                <fieldset class="float_r">
                	<?php $pasoActual = NULL;
					foreach($instancias as $item){
						if(empty($item['vin_fecha']) && $pasoActual == NULL && $arrViaje[0]['fecha_ini_real']){
							$pasoActual = $item['vie_id'];?>
							<a href="javascript:enviar('modificarInstancia');">
                            	<div class="estado-viaje online-3">
                                	<?=encode($item['vie_descripcion'])?>
                                    <span class="clear" style="font-style:italic">- Sin Ejecutar -</span>
                            	</div>
                            </a>
                        <?php }
						else{?>
                        	<div class="estado-viaje">
                               	<?=encode($item['vie_descripcion'])?>
                                <span class="clear"><?=empty($item['vin_fecha'])?'--':formatearFecha($item['vin_fecha'])?></span>
                            </div>
                        <?php }?>    
					<?php }?>
                    <input type="hidden" name="paso_instancia" value="<?=$pasoActual?>" />
                </fieldset>
                
                <?php /*if(!tienePerfil(8)){?>
                <fieldset class="float_r margin_r margin_t">
                    <span style="line-height:20px; margin:5px 0 0 50px;" class="float_l margin_r">Cross docking</span>
                    <a id="a-cross-docking" class="iconOnOff float_l <?=$datosViaje['vi_crossdocking']?'iconOn':'iconOff'?>" href="javascript:OnOff('cross-docking',true,crossDocking());" style="margin-top:5px !important;"></a>
                    
                    <span class="clear"></span>
                </fieldset>
                <?php //}*/?>
                <input type="hidden" name="cross-docking" id="cross-docking" value="<?=$datosViaje['vi_crossdocking']?>" />
                <span class="clear"></span>
                <table class="listado-viajes bottom-rows">
                <thead>
                	<tr>
                        <td width="10"></td>
                    	<td>
                        	<span class="campo1"><?=$lang->system->delivery?></span>
                        	<br />
                            <span class="campo2"><?=$lang->system->pedidos?></span>
                        </td>
                      	<td>
                        	<span class="campo1"><?=$lang->system->transportista?></span>
                        	<br /> 
                            <span class="campo2"><?=$lang->system->dador?></span>
                        </td>
                        <td colspan="2">
                        	<span class="campo1"><?=$lang->system->movil?></span>
                            <br />
                            <span class="campo2"><?=$lang->system->conductor?></span>
                        </td>
                        <td>
                        	<span class="campo1"><?=$lang->system->referencia?></span>
                            <br />
                            <span class="campo2"><?=$lang->system->ingreso_programado?></span>
                            
                        </td>
                        <td>
                            <center><span class="campo1"><?=$lang->system->ingreso_real?></span></center>
                            <!--<center><span class="campo1 color_rojo">POD SMS</span></center>-->
                            <center><span class="campo2">POD</span></center>
                        </td>
                        <td>
							<center><span class="campo1"><?=$lang->system->estadia?></span></center>
                            <center><span class="campo2">Sobre Estad&iacute;a</span></center>
						</td>
                        <td class="td-last">&nbsp;</td>
					</tr>
				</thead>
                <tbody>
         			<?php 
					$first = true;
					$box_date = NULL;
					foreach($arrViaje as $k => $row){
                        $verContenido = ($codViaje != $row['vd_id'])?1:0;
						$codViaje = $row['vd_id'];
						
						$movil = $id_movil = NULL;
						if($row['vdd_id']){
							$id_movil = $row['vdd_mo_id']?$row['vdd_mo_id']:NULL;
						}
						else{
							$id_movil = $datosViaje['vi_mo_id']?$datosViaje['vi_mo_id']:NULL;
						}
						
						if($id_movil){
							$arr['id'] = $datosViaje['vi_mo_id'];
							$arr['id_usuario'] = $_SESSION['idUsuario'];
							$movil = $objViaje->getMovil($arr);
						}
					?>
                    
                    <tr <?=empty($row['vdd_delivery'])?'class="refencia-origen"':''?> class="<?=(!$verContenido)?'destinos_'.$row['vd_id'].' row_viajes':''?>" <?php if(!$verContenido){?>style="display:none" <?php }?>>
                        <td align="center" width="10">
                            <?php if($verContenido && $arrViaje[$k+1]['vd_id'] == $row['vd_id']){?>
                                <a href="javascript:viewDestinos('<?=$row['vd_id']?>')" class="icon colapsar colapsarV2 off no_margin no_padding" title="<?=$lang->system->ver?>" id="link_<?=$row['vd_id']?>" ></a>
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
							<span class="campo1 transportista <?='vi_'.$datosViaje['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>"><?=encode($row['transportista'])?></span>
                            <br />
							<span class="campo2"><?=encode($row['dador'])?></span>
                        </td>
                        <td>
                        	<?php if($id_movil){
								if($objViaje->tieneMovilAsignado($id_movil)){?>
                                	<a title="<?=$lang->botonera->ver_mapa?>" class="float_l viewCarOnMap <?='vi_'.$datosViaje['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>" <?=(!$id_movil)?'style="display:none"':''?> href="javascript:;" attrIdMovil="<?=$id_movil?>" attrIdRef="<?=$row['re_id']?>">
                                        <span class="sprite mapa no_margin"></span>
                                    </a>
								<?php }
							}?>
                        </td>
                        <td>
                        	<a href="javascript:;" class="float_r setNewcar <?=$first?'firts':''?>" title="<?=$lang->system->asignar_conductor_movil?>"
                           		attr_vdd_id="<?=$row['vdd_id']?>" attr_vi_id="<?=$datosViaje['vi_id']?>" 
                                attr_cl_id="<?=($row['vdd_id'])?(int)$row['vdd_cl_id']:(int)$datosViaje['vi_transportista']?>"
                                attr_co_id="<?=($row['vdd_id'])?(int)$row['vdd_co_id']:(int)$datosViaje['vi_co_id']?>"
                                attr_mo_id="<?=($row['vdd_id'])?(int)$row['vdd_mo_id']:(int)$datosViaje['vi_mo_id']?>"
                                style="display:<?=$datosViaje['vi_crossdocking']?'block':($first?'block':'none')?>">
                                <span class="sprite editar"></span>
                            </a>
                            <?php $first=false;?>
                            <span class="block_info_movil <?='vi_'.$datosViaje['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>"> 
                            	<span class="campo1"><?=($row['vdd_id'])?$row['vi_movil']:$datosViaje['vi_movil']?></span>
								<br />
								<span class="campo2"><?=encode(($row['vdd_id'])?$row['co_conductor']:$datosViaje['co_conductor'])?></span>
                                <span class="campo2 block"><?=encode(($row['vdd_id'])?$row['co_telefono']:$datosViaje['co_telefono'])?></span>
							</span>
                        </td>
                        <td>
							<span class="campo1"><?=encode($row['re_nombre'])?></span>
                        	<br />
                            <span class="campo2"><?=!empty($row['fecha_ini'])?(formatearFecha($row['fecha_ini'])):''?></span>    
                        </td>
                        <td>
                            <center>
                            <span class="campo1">
                                <div id="estado-fecha-ingreso-<?=(int)$row['vd_id'].(($box_date != $id_movil.$row['vd_re_id'])?(int)$row['vdd_id']:'equals')?>" <?php /*=!empty($row['sms_fecha'])?'class="tooltip POD_SMS"':''*/?> >
                                
                                <? if(!empty($row['fecha_ini_real'])){?>    
                                <div class="box_reset_datetime reset-ingreso-<?=$row['vd_id']?>">
                                <span class="campo1"><?=formatearFecha($row['fecha_ini_real'])?></span>
                                    <? if($box_date != $id_movil.$row['vd_re_id'] && !tienePerfil(8)){?>
                                    <a href="javascript:resetIngresoDelivery(<?=$row['vd_id']?>);" class="resetDates" style="display:block">
                                        <span class="sprite restart no_margin"></span>
                                    </a>
                                    <? }?>
                                </span>	
                                </div>
                                <? }?>   
                                    
                                <? /*=!empty($row['fecha_ini_real'])?(formatearFecha($row['fecha_ini_real'])):''*/?>
                                    
                                    <?php /*if(!empty($row['sms_fecha'])){?>
                                        <span class="tooltiptext"><?=formatearFecha($row['sms_fecha'])?><br><?=$row['sms_respuesta']?></span>
                                    <?php }*/?>
                                </div>
                                <?php if($id_movil && empty($row['fecha_ini_real']) && $row['re_id'] > 0 && $row['re_id'] != 6464 && $box_date != $id_movil.$row['vd_re_id']){
                                    if($objViaje->esFaltaDeReporte(NULL, $movil[0]['sh_rd_id']) && !tienePerfil(array(8,12))){?>
                                    <div id="assign-datetime-ingreso-<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="box_assign_datetime reset-ingreso-<?=$row['vd_id']?>">
                                        <input type="text" id="assign_fecha_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="no_margin date2 float_l" value="<?=getFechaServer('d-m-Y')?>">
                                        <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
                                        <select class="float_l no_margin" id="assign_hora_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->hora as $item){?>
                                                <option value="<?=$item?>" <?=($item==getFechaServer('H'))?'selected="selected"':''?> ><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <span class="float_l" style="margin:0 2px 0 2px">:</span>
                                        <select class="float_l no_margin" id="assign_min_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->min as $item){?>
                                                <option value="<?=$item?>" <?php if(substr(getFechaServer('i'),0,1) == substr($item,0,1)){?>selected="selected"<?php }?>><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <a href="javascript:assignIngresoDelivery(<?=(int)$row['vd_id']?>,<?=(int)$row['vdd_id']?>);" title="Asignar Fecha y Hora de Ingreso" class="float_l">
                                            <span class="sprite guardar no_margin"></span>
                                        </a>
                                        <span class="clear"></span>
                                    </div>
                                    <?php }?>
                                <?php }?>
                            </span>
                            <span class="campo3 clear"><?=$row['retroactivo']?'Actualizado mediante Proceso Retroactivo':''?></span>
                            <span class="campo2">
                                <?=!empty($row['vd_pod_manual'])?(formatearFecha($row['vd_pod_manual'])):''?>
                                 <!-- Ini. Ingreso Fecha POD --> 
                                <?php if($row['vd_orden'] > 0 && $id_movil && (!empty($row['fecha_ini_real']) || (empty($row['fecha_ini_real']) && strtotime($row['fecha_ini']) < strtotime(getFechaServer()))) && $row['re_id'] > 0 && $row['re_id'] != 6464 && $box_date != $id_movil.$row['vd_re_id'] && empty($row['vd_pod_manual'])){?>
                                    <div id="pod-datetime-ingreso-<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="box_assign_datetime box_pod reset-ingreso-<?=$row['vd_id']?>">
                                        <label class="float_l"><strong>POD</strong>&nbsp</label>
                                        <input type="text" id="pod_fecha_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="no_margin date2 float_l" value="<?=getFechaServer('d-m-Y')?>">
                                        <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
                                        <select class="float_l no_margin" id="pod_hora_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->hora as $item){?>
                                                <option value="<?=$item?>" <?=($item==getFechaServer('H'))?'selected="selected"':''?> ><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <span class="float_l" style="margin:0 2px 0 2px">:</span>
                                        <select class="float_l no_margin" id="pod_min_ingreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->min as $item){?>
                                                <option value="<?=$item?>" <?php if(substr(getFechaServer('i'),0,1) == substr($item,0,1)){?>selected="selected"<?php }?>><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <a href="javascript:podIngresoDelivery(<?=(int)$row['vd_id']?>,<?=(int)$row['vdd_id']?>);" title="Asignar Fecha y Hora de POD" class="float_l">
                                            <span class="sprite guardar no_margin"></span>
                                        </a>
                                        <span class="clear"></span>
                                    </div>
                                <?php }?>    
                                <!-- Fin. Ingreso Fecha POD --> 
                            </span> 
                            </center>           
                        </td>
                        <td>
                        	<center>
                        	<span class="campo1">
                            	<div id="estado-fecha-egreso-<?=(int)$row['vd_id'].(($box_date != $id_movil.$row['vd_re_id'])?(int)$row['vdd_id']:'equals')?>">
								<? if(!empty($row['fecha_fin_real'])){?>    
                                    <div class="box_reset_datetime reset-egreso-<?=$row['vd_id']?>">
                                    <span class="campo1"><?=$objViaje->getTiempoHM(strtotime($row['fecha_fin_real']) - strtotime($row['fecha_ini_real']))?></span>
                                    <? if(!empty($row['vdd_delivery']) && $box_date != $id_movil.$row['vd_re_id']){?>
                                        <br />
                                        <a class="button colorin clear <?=(!$row['fecha_fin_real'])?'disabled':''?> margin_t" href="javascript:<?=(!$row['fecha_fin_real'])?'':'rechazarDelivery('.(int)$datosViaje['vi_id'].','.(int)$row['vd_id'].','.(int)$row['vdd_id'].')'?>;" style="width:55px; " id="button_rechazado_<?=$row['vdd_id']?>">Rechazar</a>
                                    <?php }

                                    if($box_date != $id_movil.$row['vd_re_id'] && !tienePerfil(8)){?>
                                        <a href="javascript:resetEgresoDelivery(<?=$row['vd_id']?>);" class="resetDates" style="display:block">
                                            <span class="sprite restart no_margin"></span>
                                        </a>
                                    <? }?>
                                    </span>	
                                    </div>
                                <? }?>   

                                <? /*
                                <?php if(!empty($row['fecha_fin_real'])){
									echo $objViaje->getTiempoHM(strtotime($row['fecha_fin_real']) - strtotime($row['fecha_ini_real']));
									if(!empty($row['vdd_delivery'])){?>
                                    	<br />
                                		<a class="button colorin clear <?=(!$row['fecha_fin_real'])?'disabled':''?> margin_t" href="javascript:<?=(!$row['fecha_fin_real'])?'':'rechazarDelivery('.(int)$datosViaje['vi_id'].','.(int)$row['vd_id'].','.(int)$row['vdd_id'].')'?>;" style="width:55px; " id="button_rechazado_<?=$row['vdd_id']?>">Rechazar</a>
                                	<?php }
								}?>
                                </div>*/?>
                                
                                <?php if($id_movil && empty($row['fecha_fin_real']) && $row['re_id'] > 0 && $row['re_id'] != 6464){
                                   if($objViaje->esFaltaDeReporte(NULL, $movil[0]['sh_rd_id']) && !tienePerfil(array(8,12)) && $box_date != $id_movil.$row['vd_re_id']){?>
                                    <div id="assign-datetime-egreso-<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="box_assign_datetime reset-egreso-<?=$row['vd_id']?>" <?=empty($row['fecha_ini_real'])?'style="display:none"':''?> >
                                        <input type="text" id="assign_fecha_egreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" class="no_margin date2 float_l" value="<?=getFechaServer('d-m-Y')?>">
                                        <span class="float_l" style="margin:0 2px 0 2px">&nbsp;</span>
                                        <select class="float_l no_margin" id="assign_hora_egreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->hora as $item){?>
                                                <option value="<?=$item?>" <?=($item==getFechaServer('H'))?'selected="selected"':''?> ><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <span class="float_l" style="margin:0 2px 0 2px">:</span>
                                        <select class="float_l no_margin" id="assign_min_egreso_<?=(int)$row['vd_id'].(int)$row['vdd_id']?>" style="width:42px;">
                                            <?php foreach($objViaje->min as $item){?>
                                                <option value="<?=$item?>" <?php if(substr(getFechaServer('i'),0,1) == substr($item,0,1)){?>selected="selected"<?php }?>><?=$item?></option>
                                            <?php }?>
                                        </select>
                                        <a href="javascript:assignEgresoDelivery(<?=(int)$row['vd_id']?>,<?=(int)$row['vdd_id']?>);" title="Asignar Fecha y Hora de Egreso" class="float_l">
                                            <span class="sprite guardar no_margin"></span>
                                        </a>
                                        <span class="clear"></span>
                                    </div>
                                    <?php }?>
                                <?php }?>
                            </span>
                            
                            <?php if(strtotime($row['fecha_fin_real']) > strtotime($row['fecha_ini_real'])){?>
                            <span class="campo2 reset-egreso-<?=$row['vd_id']?>"><?=$objViaje->getTiempoHM(strtotime($row['fecha_fin_real']) - strtotime($row['fecha_ini_real']))?></span>
                            <?php }?>
                            
                            </center>
                        </td>
                        <td class="td-last">
                            <center>
                                <?php if($datos['vie_descripcion'] == 'entregado'){?>
                                    <a href="javascript:mostrarPopup('boot.php?c=<?=$seccion?>&action=proof_delivery&status=<?=$datos['vie_descripcion']?>&name=<?=$datos['vi_id'].'_'.$datos['vd_id']?>&date=<?=$datos['vin_fecha']?>',370,380);"><span class="sprite_hand like"></span></a>
                                <?php }
                                elseif($datos['vie_descripcion'] == 'rechazado'){?>
                                    <a href="javascript:mostrarPopup('boot.php?c=agendaGPS&action=proof_delivery&status=<?=$datos['vie_descripcion']?>&name=<?=$datos['vi_id'].'_'.$datos['vd_id']?>&date=<?=$datos['vin_fecha']?>',370,380);"><span class="sprite_hand unlike"></span></a>
                                <?php }?>
                            </center>
                        </td>
                    </tr>
                    <?php 
                        		$box_date = ($box_date != $id_movil.$row['vd_re_id'])?$id_movil.$row['vd_re_id']:$box_date;
					}?>
         		</tbody>
                </table>
                <fieldset class="gum_top2"> 
                	<div class="float_l">
                    	<span class="etiqueta-referencia color-origen  float_l"></span>
                        <span class="txt-referencia float_l"><?=$lang->system->origen_viaje?></span>
					</div>
                    <div class="float_r margin_r">
                   		<?php if(!empty($datosViaje['vi_fechacreado'])){?>
                        <span class="float_r "><?=str_replace('[HORA_CREACION]',formatearFecha($datosViaje['vi_fechacreado'],'time'),(str_replace('[FECHA_CREACION]',formatearFecha($datosViaje['vi_fechacreado'],'date'),$lang->message->msj_viaje_creado)))?></span>
                       	<?php }?>
					</div>
				</fieldset>
                <div class="clear"></div>
			</div><!-- fin. #viajes-listado -->
            <div id="viajes-observaciones" class="contenido clear" style="height:100%; <?=($_POST['hidSolapa']!='observaciones')?'display:none':''?>">
            	<fieldset class="gum_top2 float_l not_bg_white" style="width:310px">
                	<label class="float_l placeholder"><?=$lang->system->observaciones?></label>
                    <textarea name="observaciones" id="observaciones" class="clear" rows="5"><?=trim($datosViaje['vi_observaciones'])?></textarea>

                    <label class="float_l placeholder">Nota Interna</label>
                    <textarea name="observaciones_2" id="observaciones" class="clear" rows="5"><?=trim($datosViaje['vi_observaciones_2'])?></textarea>
				</fieldset>
                <fieldset class="float_l not_bg_white" style="width:310px; margin:30px 0 0 40px;">
                    <fieldset class="gum_top2 not_bg_white">
                        <span class="float_l" style="line-height:22px;"><?=$lang->system->viaje_finalizado?></span>
                        <input type="checkbox" name="vi_finalizado" value="1"  style="width:20px;" class="float_l" <?php if($datosViaje['vi_finalizado']){?>checked="checked"<?php }?>> 
                    </fieldset>
                	<a style="width:163px;" class="button colorin clear gum" onclick="enviar('guardarObservaciones');" href="javascript:;"><?=$lang->botonera->guardar?></a>
                </fieldset>
                <span class="clear"></span>
			</div><!-- fin. #viajes-observaciones -->
           	<?php if($historial[0]){?>
            <div id="viajes-historico" class="contenido clear" style="height:100%; display:none">
				<table class="listado-viajes bottom-rows">
                <thead>
                	<tr>
                    	<td width="150"><span class="campo1"><center><?=$lang->system->fecha?></center></span></td>
                        <td width="200"><span class="campo1"><?=$lang->system->usuario?></span></td>
						<td><span class="campo1"><?=$lang->system->descripcion?></span></td>
					</tr>
				</thead>
                <tbody>
                <?php foreach($historial as $item){?>
					<tr>
                    	<td><center><?=formatearFecha($item['sl_fecha_alta'])?></center></td>
                        <td><?=encode($item['sl_us_nombre'])?></td>
                        <td style="text-align:left"><?=encode($item['sl_descripcion'])?></td>
					</tr>
				<?php }?>
				</tbody>
                </table>
			</div><!-- fin. #viajes-historico -->
            <?php }?>
            <!-- Ini. POD -->
            <div id="viajes-pod" class="contenido clear" style="height:100%; display:none">
				<table class="listado-viajes bottom-rows">
                <thead>
                	<tr>
                    	<td width="150"><span class="campo1"><center><?=$lang->system->fecha?></span></center></td>
                        <td width="200"><span class="campo1"><center><?=$lang->system->usuario?></span></center></td>
						<td><span class="campo1"><?=$lang->system->descripcion?></span></td>
					</tr>
				</thead>
                <tbody>
                <?php if($POD){
                    foreach($POD as $item){?>
					<tr>
                        <td><center><?=formatearFecha($item['sl_fecha_alta'])?></center></td>
                        <td><?=encode($item['sl_us_nombre'])?></td>
                        <td style="text-align:left"><?=encode($item['sl_descripcion'])?></td>
					</tr>
                <?php }
                }else{?>
                <tr><td colspan="3"><center>No se registran datos POD</center></td></tr>
                <?php }?>
				</tbody>
                </table>
			</div>
            <!-- Fin. POD -->
        </div>
        </td>
	</tr>
</tbody>
</table>
<!-- -->
</div> 	
