<div id="mainBoxAM" style="margin:-10px 0 0 -4px;">
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"/>
<!-- -->
<!--[if IE]>
<style>
input{height:18px  !important; line-height:18px;}
input.mitad_largo {width: 180px !important;  height:18px  !important; line-height:18px;}
.EstadoOnlineOnOf input.no_margin {width: 170px !important;  height:18px  !important; line-height:18px;}
.EstadoOnlineOnOf select.no_margin {width: 170px !important;}
input.no_margin.date.float_l{width:70px;}
label.placeholder.txt_ie{line-height:14px; display:block !important;}
</style>	
<!-- -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".date").live("focusin", function() { 
		   $(this).datepicker({
				onSelect: function(objDatepicker){
					/*var fecha = $(this).val().replace('/','-');
					var fecha = fecha.replace('/','-');
					$(this).val(fecha);*/
					
					var $aux =  $(this).attr('id').split('_');
					onChanges($aux[1], 'comboFecha');
				}
			});
		});
		
		$(".date2").live("focusin", function() { 
		    $(this).datepicker({});
			$(this).removeClass('hasDatepicker');
        });
        
        validarPallets();
        
	});
	
	arrLang['ver_campos_opcionales'] = '<?=$lang->system->ver_campos_opcionales?>';
	arrLang['ocultar_campos_opcionales'] = '<?=$lang->system->ocultar_campos_opcionales?>';
	
	arrLang['msj_viaje_reset_ingreso'] = '<?=$lang->message->msj_viaje_reset_ingreso?>';
	arrLang['msj_viaje_reset_egreso'] = '<?=$lang->message->msj_viaje_reset_egreso?>';
	arrLang['msj_viaje_cargando'] = '<?=$lang->message->msj_viaje_cargando?>';
	arrLang['msj_viaje_fecha_anterior'] = '<?=$lang->message->msj_viaje_fecha_anterior?>';
	arrLang['msj_viaje_fecha_programada'] = '<?=$lang->message->msj_viaje_fecha_programada?>';
	arrLang['msj_viajes_not_save'] = '<?=$lang->message->msj_viajes_not_save?>';
	arrLang['msj_viajes_movil_asignado'] = '<?=$lang->message->msj_viajes_movil_asignado?>';
	arrLang['msj_viajes_existe_codigo'] = '<?=$lang->message->msj_viajes_existe_codigo?>';
	arrLang['msj_viajes_motivo_cambio'] = '<?=$lang->system->motivos_cambio?>';
	arrLang['solicitud_enviada'] = '<?=$lang->message->error->solicitud_enviada?>';
	arrLang['procesar_datos_ok'] = '<?=$lang->message->ok->procesar_datos?>';
	arrLang['procesar_datos_error'] = '<?=$lang->message->error->procesar_datos?>';
</script>

<input type="hidden" name="id_viaje" id="id_viaje" value="<?=$idViaje?>" />
<input type="hidden" name="id_motivo_cambio" id="id_motivo_cambio"/>
<input type="hidden" name="idperfil" id="idperfil" value="<?=$_SESSION['idPerfil']?>"/>
<table width="100%" height="100%">
	<tbody>
    	<tr>
        	<td style=" padding:10px;">
            	<?php if($tieneMovilAsignado){?>
                	<a href="javascript:enviar('<?=$idViaje?'guardarM':'guardarA'?>')" id="btn-guardar" class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
					<?php if($idViaje && !tienePerfil(28)){ ?>									
						<a href="javascript:;" onclick="javascript:deleteViaje()" class="button colorRed gum" style="width:173px; margin-top:4px;"><?=$lang->botonera->eliminar?></a>
					
					<?php }
                 }
				else{?>
					<span class="advertencia gum block"><?=$lang->message->msj_viaje_asignado?>.</span>
				<?php }?>
            	
                <input type="hidden" name="tipo_viaje" id="tipo_viaje" value="<?=!empty($arrViaje['vt_id']) ? $arrViaje['vt_id'] : 29 ?>" />
                <input type="hidden" name="dador" value="<?=$dador[0]['da_id']?>" />
                <input type="hidden" name="transportista" value="<?=$arrViaje['vi_transportista']?>" />
                <fieldset class="gum_top2 none">
                	<select name="transportista" id="transportista" class="no_margin float_l mitad_largo" onchange="javascript:onChanges(this, 'comboTransportista')">
                        <option value="" class="placeholder"><?=$lang->system->flota?></option>
                    </select>
                    <span id="tr-reload"></span>
                    <span class="obligatorio">*</span>
                    <input type="hidden" id="temp_transportista" value="<?=$arrViaje['vi_transportista']?>" />
                </fieldset>
                <fieldset class="gum_top2 none">
                	<select name="movil_tipo" id="movil_tipo" class="no_margin float_l mitad_largo" onchange="javascript:onChanges(this, 'comboVehiculo')" >
                        <option value="" class="placeholder"><?=$lang->system->tipo_movil?></option>
                    </select>
                    <span id="tipo-mo-reload"></span>
                    <span class="clear block"></span>
                    <input type="hidden" id="temp_movil_tipo" value="<?=$arrViaje['mo_id_tipo_movil']?>" />
                </fieldset>
                
                <fieldset class="gum_top2 none">
					<!-- Input para filtro en combo -->
                    <!--<input type="text" id="buscador-movil"  onKeyUp="filtrarCombo(this.value,'movil')" autocomplete="off" >
    				<select id="select-movil" class="no_margin mitad_largo" multiple="multiple"></select>    
                    <!-- -->
                    
                    <select name="movil" id="movil"  class="no_margin float_l mitad_largo" onchange="javascript:onChanges(this,'validarMovil')">
                        <option value="" class="placeholder"><?=$lang->system->movil?></option>
                    </select>
                    <span id="mo-reload"></span>
                    <input type="hidden" id="temp_movil" value="<?=$arrViaje['vi_mo_id']?>" />
                </fieldset>

                <fieldset class="gum_top2 none">
                	<!-- Input para filtro en combo 
                    <input type="text" id="buscador-conductor"  onKeyUp="filtrarCombo(this.value,'conductor')" autocomplete="off" >
    				<select id="select-conductor" class="no_margin mitad_largo" multiple="multiple"></select>
                    <!-- -->
                    
                    <select name="conductor" id="conductor" class="no_margin float_l mitad_largo" onchange="javascript:onChanges(null,null)">
                        <option value="" class="placeholder"><?=$lang->system->conductor?></option>
                    </select>
                    <span id="co-reload"></span>
                    <input type="hidden" id="temp_conductor" value="<?=$arrViaje['vi_co_id']?>" />
                </fieldset>
                

                <fieldset class="both gum_top2 EstadoOnlineOnOf" style="width:176px;" >
                <span class="float_l link " style="width:100%">
                	<a href="javascript:viewCamposOpcionales()" id="viewCamposOpcionales" class="icon colapsar off float_l" style="position:relative;top:4px;" title="<?=$lang->system->ver_campos_opcionales?>" ></a>
                    <a href="javascript:viewCamposOpcionales()" class="float_l link no_hover no_decoration gum"><?=$lang->system->campos_opcionales?></a>
                </span>
                
                <div id="DatosCamposOpcionales" class="clear" style="display:none;">
                    <fieldset class="gum_top2" id="fieldValidarCodigo">
                        <label class="float_l placeholder"><?=$lang->system->nro_tarea?></label>
                        <?php if(tienePerfil(28)){?>
                        <input type="text"  value="<?=$arrViaje['vi_codigo']?>" class="no_margin float_l mitad_largo" disabled="true">
                        <?php }else{?>
                        <input type="text" name="cod_viaje" id="cod_viaje" value="<?=$arrViaje['vi_codigo']?>" class="no_margin float_l mitad_largo" placeholder="<?=$lang->system->nro_tarea?>" onkeyup="javascript:onChanges(null,null)" onblur="javascript:validarCodigoViaje(this.id)">
                        <?php }?>    
                    </fieldset>
                    <fieldset class="gum_top2">
                        <label class="float_l placeholder"><?=$lang->system->observaciones?></label>
                        <textarea name="observaciones" id="observaciones" class="no_margin" rows="5" style="width:160px;" onkeyup="javascript:onChanges(null,null)"><?=trim($arrViaje['vi_observaciones'])?></textarea>
                    </fieldset>
					
                    <fieldset class="gum_top2">
                        <span class="float_l" style="line-height:22px;"><?=$lang->system->tarea_finalizada?></span>
                        <!--<input type="checkbox" name="vi_finalizado" value="1"  style="width:20px;" class="float_r" <?php /*if($arrViaje['vi_finalizado']){?>checked="checked"<?php }*/?> onchange="javascript:onChanges(null,null)"> -->
                    </fieldset>
                </div>  
            </fieldset>    
               
                <fieldset>
                	<span class="obligatorio">* <?=$lang->system->campos_obligatorios?></span>
                </fieldset>       
        	</td>
            <td width="100%">
            	<div class="solapas gum clear" style="overflow:hidden;"><!-- height:92%;-->
                	<a href="javascript:getContenido('listado');" class="izquierda float_l tipo-listado active"><?=$lang->system->referencia_ruteo?></a>
                    <?php if($historial[0] && tienePerfil(27)){?>
                    <a href="javascript:getContenido('historico')" class="izquierda float_l tipo-historico"><?=$lang->system->historial?></a>
                    <?php }?>
                    <?php if($type == 'retiros'){?>
                    <!--<a href="javascript:getContenido('cotizaciones')" class="izquierda float_l tipo-cotizaciones">Cotizaciones</a>-->
                    <?php }?>
                    <a href="javascript:getContenido('pod')" class="izquierda float_l tipo-pod"><?=$lang->system->pod?></a>

                    <div id="botonesABM" class="float_r margin_r" style="width:120px;">
                    	<span id="botonVolver" onclick="javascript:validarVolver();" >
                        	<img src="imagenes/botonVolver.png" alt="" /> 
							<?=$lang->botonera->volver?> 
                        </span>
                    </div>
                    
                    <div id="viajes-listado" class="contenido clear" style="height:100%">
                        <!-- inicio. Rows Ruteo -->
                        <fieldset class="gum_top">
                        	<label class="muy_corto float_l"><?=$lang->system->referencia?></label>
                            <input type="text" name="geozona" id="geozona" class="float_l mitad_largo no_margin" onkeyup="javascript:setBuscarGeozona(event);">
                            <!--<a href="javascript:<? //=$objPerfil->validarSeccion('abmReferencias')?"mostrarPopup('boot.php?c=abmReferencias&action=popup&ref=abmViajes')":"return false"?>;" class="float_l" style="position:relative; left:-26px; top:5px"><span class="sprite rueda <?=$objPerfil->validarSeccion('abmReferencias')?"":"inactivo"?>"></span></a>-->
                            <a href="javascript:setRuteo($('#geozona').val())" class="float_l margin_l button colorin gum" style="position:relative; left:0px;" id="btn-add-geozona" onClick=""><?=$lang->botonera->agregar?></a>
                            <!--
							<span style="line-height:20px; margin:5px 0 0 50px;" class="float_l margin_r"><?=$lang->system->ruteo_automatico?></span>
                            <a id="a-ruteo-automatico" class="iconOnOff float_l iconOff" href="javascript:OnOff('ruteo-automatico'); modoRuteo()" style="margin-top:5px !important;"></a>
                            <input type="hidden" name="ruteo-automatico" id="ruteo-automatico" value="" />
                            
                            <div class="float_r margin_r">
                            	<div class="float_l"><strong><? //=$lang->system->distancia_acumulada?>: </strong><span id="kmTotal"><? //=formatearDistancia(0)?></span></div>
								<?php /*if($arrViaje['vi_finalizado']){
									$time = $objViaje->getTiempoViaje();
									echo '<br><div class="float_l"><strong>'.$lang->system->tiempo_programado.': </strong>'.$time['programado']."</div><br>";
									echo '<div class="float_l"><strong>'.$lang->system->tiempo_real.': </strong>'.$time['real']."</div>";
								} */?>
                            </div>-->
                        </fieldset>
                        <table class="listado-viajes bottom-rows">
                            <thead>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <span class="campo1"><?=$lang->system->referencia?></span>
                                    </td>
                                    <?php if(tienePerfil(array(27,28))){?>
                                    <td>
                                        <span class="campo1">Contacto</span>
                                    </td>
                                    <td>
                                        <span class="campo1">Pallets</span>
                                    </td>
                                    <?php }?>
                                    <td>
                                        <center>
                                        	<span class="campo1"><?=$lang->system->ingreso_programado?></span>
                                        </center>
                                    </td>
                                    <td>
                                    	<center>
                                        	<span class="campo1"><?=$lang->system->ingreso_real?></span>
                                        </center>
                                    </td>
                                    <!--<td>
                                       <center>
											<span class="campo1"><?=$lang->system->estadia?></span>
                                       </center>
                                    </td>-->
                                    <td class="td-last" width=5>&nbsp;</td>
                                 </tr>
                            </thead>
                            <tbody id="sortable">
                            	<?php if(count($arrRef) > 0){
									$idViadeDestinoMax = 0;
									require_once 'clases/clsViajes.php';
    								$viaje = new Viajes($objSQLServer);
									$ultimoIdViajeDestino = 0;
									foreach($arrRef as $item){
										$item['vi_id'] = (int)$idViaje;
										$viaje->filaRuteo($item, $movil[0]);	
										
										if(!empty($item['vd_ini_real']) || !empty($item['vd_fin_real'])){
											$ultimoIdViajeDestino = $item['vd_re_id'];
										}
									}
								}?>
                            </tbody>
                        </table>
                        <fieldset class="gum_top2" style="background:none; border:none;"> 
                            <!--
                                <div class="float_l">
                            	<span class="etiqueta-referencia color-origen  float_l"></span>
                                <span class="txt-referencia float_l"><?=$lang->system->origen_tarea?></span>
                            </div>
                            -->
                            <div class="float_r margin_r">
                            	<?php if(!empty($arrViaje['vi_fechacreado'])){?>
                                <span class="float_r "><?=str_replace('[HORA_CREACION]',formatearFecha($arrViaje['vi_fechacreado'],'time'),(str_replace('[FECHA_CREACION]',formatearFecha($arrViaje['vi_fechacreado'],'date'),$lang->message->msj_viaje_creado)))?></span>
                                <?php }?>
                            </div>
                        </fieldset>
                        <input type="hidden" name="id_geozonas" id="id_geozonas" value="<?=$arrViaje['id_geozonas']?>">
                        <?php if(count($arrRef) > 0){?>
							<script language="javascript" type="text/javascript">
								var ref = $("#id_geozonas").val().split(',');
								calcularRuteo(ref,false,false,true);
								habilidarResetFechas(<?=$ultimoIdViajeDestino?>);
								habilidarAssignFechas();
                            </script> 	
						<?php }?>
                        <div class="clear"></div>
                    </div><!-- fin. #viajes-listado -->
                    <?php if($historial[0] && tienePerfil(27)){?>
                    <div id="viajes-historico" class="contenido clear" style="height:100%; display:none">
                    	<table class="listado-viajes bottom-rows"><!-- listado-viajes-->
                            <thead>
                                <tr>
                                    <td width="150"><span class="campo1"><?=$lang->system->fecha?></span></td>
                                    <td width="200"><span class="campo1"><?=$lang->system->usuario?></span></td>
                                    <td><span class="campo1"><?=$lang->system->descripcion?></span></td>
                                 </tr>
                            </thead>
                            <tbody>
                            <?php foreach($historial as $item){?>
								<tr>
                                	<td><?=formatearFecha($item['sl_fecha_alta'])?></td>
                                    <td><?=encode($item['sl_us_nombre'])?></td>
                                    <td style="text-align:left"><?=encode($item['sl_descripcion'])?></td>
                                </tr>
							<?php }?>
                            </tbody>
                        </table>
                    </div><!-- fin. #historico-listado -->
                    <?php }?>
                    <!-- Ini. Cotizaciones -->
                    <?php if($type == 'retiros'){?>
                    <div id="viajes-cotizaciones" class="contenido clear" style="height:100%; display:none">
                        <?php if(tienePerfil(27)){//--Fabricante?>
                        <table class="listado-viajes bottom-rows">
                        <thead>
                            <tr>
                                <td width="150"><span class="campo1"><?=$lang->system->fecha?></span></td>
                                <td width="200"><span class="campo1"><?=$lang->system->transportista?></span></td>
                                <td><span class="campo1">Cotización</span></td>
                                <?php if(empty($arrViaje['vi_transportista'])){?><td></td><?php }?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            if($cotizacion){
                                foreach($cotizacion as $item){?>
                                    <tr>
                                        <td><?=formatearFecha($item['vc_fecha_cotizado'])?></td>
                                        <td><?=encode($item['cl_razonSocial'])?></td>
                                        <td style="text-align:left"><p>Cotización Recibida: $ <?=number_format($item['vc_valor'], 2, ',','.')
                                            .(($arrViaje['vi_transportista'] == $item['vc_transportista'])?'. Viaje asignado.':'')?></p></td>
                                        <?php if(empty($arrViaje['vi_transportista'])){?>
                                        <td><center><a href="javascript:asignarCotizacion(<?=$item['vc_transportista']?>, '<?=encode($item['cl_razonSocial'])?>');" class="button colorin">Asignar Viaje</a></center></td>
                                        <?php }?>    
                                    </tr>
                                <?php }?>
                            <?php }
                            else{?>
                                <tr>
                                    <td colspan="3"><center>No se registran cotizaciones</center></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                        <?php }
                        elseif(tienePerfil(28)){//--Transportista?>
                        <fieldset>
                        <br>
                        <table style="width:250px; !important;">
                            <tbody>
                                <?php if(!$cotizacion['vi_transportista']){?>
                                <tr><td><strong class="no_margin">Valor del viaje:</strong></td></tr>
                                <tr><td>
                                    <input type="text" name="cotizacion_valor" id="cotizacion_valor" value="<?=number_format($cotizacion['vc_valor'], 0,'','')?>" class="only_number no_margin float_l margin_r" style="width:120px;">
                                    <a href="javascript:saveCotizacion($('#cotizacion_valor').val(), $('#cotizacion_obs').val());" class="button colorin" style="width:84px">Cotizar</a>
                                </td></tr>
                                <tr><td>
                                    <textarea name="cotizacion_obs" id="cotizacion_obs" class="no_margin float_l" rows="5" style="width:95%"><?=$cotizacion['vc_observaciones']?></textarea>
                                </td></tr>
                                <tr><td>
                                    <p id="motivo_error" style="display:none;color:#AC0C0C; margin-top:5px;"></p>
                                    <p id="motivo_ok" style="display:none;color:#008000; margin-top:5px;"></p>
                                </td></tr>
                                <?php } else{?>
                                <tr><td>
                                    <strong class="no_margin">Valor del viaje:</strong>
                                    <strong style="font-size:14px; margin-left:4px;">$<?=number_format($cotizacion['vc_valor'], 2, '.','')?></strong>
                                </td></tr>
                                <tr><td>
                                    <p style="font-size:12px;"><?=$cotizacion['vc_observaciones']?></p>
                                </td></tr>    
                                <?php }?> 
                            </tbody>
                        </table>
                        
                        </fieldset>
                        <?php }?>
                    </div><!-- fin. #cotizaciones-listado -->
                    <?php }?>
                    <!-- Fin. Cotizaciones -->
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
                        <tr><td colspan="3"><center>No se registran datos</center></td></tr>
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

