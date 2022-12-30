<script type="text/javascript" language="javascript">
	var solapa = '<?=$solapa?>';
</script>
<div id="main" class="sinColIzq">
    <div class="mainBoxLICabecera">
        <form name="frm_<?=$seccion ?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
            <div class="esp">
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input name="solapa" type="hidden" value="<?=$solapa?>" />
                <input name="hidId" id="hidId" type="hidden" value=""/>
            </div>
            <!-- Inicio. listado de viajes -->
            <div class="solapas gum clear">
            	<a href="boot.php?c=<?=$seccion?>&solapa=arribos" class="izquierda float_l <?=($solapa=='arribos')?'active':''?>"><?=$lang->system->arribos?></a>
                <a href="boot.php?c=<?=$seccion?>&solapa=partidas" class="izquierda float_l <?=($solapa=='partidas')?'active':''?>"><?=$lang->system->partidas?></a>
                  
                <a class="button colorin float_l margin_l" onclick="javascript:vistaArribos('<?=$solapa?>');" href="javascript:;">Refrescar</a>
                 
                <div class="contenido clear" style="height:100%">
                	<table class="listado-viajes" width="100%" height="100%">
                    <thead>
                        <tr>
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
                                <span class="campo2"><?=($solapa == 'partidas')?$lang->system->egreso_programado:$lang->system->ingreso_programado?></span>
                            </td>
							<td width="220">
                            	<center>
                                	<?php if($solapa == 'partidas'){
                                	 	echo $objFiltroCol->label($col['partida'],$lang->system->partidas,'campo1');
									}
									else{
										echo $objFiltroCol->label($col['arribo'],$lang->system->arribos,'campo1');
									}?>	
                                </center>
                                <br />
							</td>
                            <td class="td-last">
                            	<?=$objFiltroCol->label($col['facturado'],$lang->system->facturado,'campo1')?>
                                <span class="campo2"><?=$lang->system->observaciones?></span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
						<?php 
						foreach($arrViajes as $k => $row){
							$totalRegistros++;
							$fila = ($fila == 'filaImpar')?'filaPar':'filaImpar';
						?>	
							<tr class="<?=$fila?>">
                                <td>
								   	<a href="?c=abmViajes&idViaje=<?=$row['vi_id']?>" class="link float_l" target="_blank">
                                    	<?=encode($row['vi_codigo'])?>
                                    </a>
                                </td>
                                <td>
								    <span class="campo1"><?=encode($row['transportista'])?></span>
                                	<br />
									<span class="campo2"><?=encode($row['dador'])?></span>
                                </td>
                                <td>
                                    <?php if($row['id_movil']){
                                        if($objViaje->tieneMovilAsignado($row['id_movil'])){?>
                                            <a title="<?=$lang->botonera->ver_mapa?>" class="float_l" href="javascript:mostrarPopup('boot.php?c=abmViajesDeliveryMapa&action=popup&idMovil=<?=$row['id_movil']?>&idRef=<?=$row['re_id']?>',740,450)">
                                                <span class="sprite mapa no_margin"></span>
                                            </a>
                                        <?php }?>
                                     <?php }?>
                                </td>    
                                <td>
                                	<span class="campo1"><?=$row['vi_movil']?></span>
                                    <br />
                                    <span class="campo2"><?=encode($row['co_conductor'])?></span>
                                    <span class="campo2 block"><?=encode($row['co_telefono'])?></span>
                                </td>
                                <td>
									<span class="campo1"><?=encode($row['re_nombre'])?></span>
                                    <br />
                                    <span class="campo2"><?=($solapa == 'partidas')?formatearFecha($row['vd_fin']):formatearFecha($row['vd_ini'])?></span>
                                </td>
                                <td style="vertical-align:middle">
                                	<?php 
									$aux = $fecha = '';
									if($solapa == 'arribos'){ //-- arribos
										if($row['vd_ini_real']){
											$fecha = formatearFecha($row['vd_ini_real']);
											if($row['diferenciaIngreso'] > 0){
												$estado = $lang->system->arribo_atrasado;
												$estado.= ' ('.$objViaje->getTiempoHM(strtotime($row['vd_ini_real']) - strtotime($row['vd_ini'])).')';	
												$classEstado = 'color_rojo';
											}
											else{
												$estado = $lang->system->arribo_en_tiempo;
												$classEstado = 'color_verde';
											}
										}
										elseif($row['sh_rd_id'] == 75 || $row['sh_rd_id'] == 76){
											$estado = $lang->system->movil_sin_reportar;
											$classEstado = 'color_rojo bold';
										}
										else{
											$arribo = $objViaje->getTrayectoEstimado($row);
											if(strtotime($row['vd_ini']) < strtotime($arribo['fecha'])){
												$estado = $lang->system->atrasado;
												$classEstado = 'color_rojo';
											}
											else{
												$estado = $lang->system->en_tiempo;
												$classEstado = 'color_verde';
											}
											$aux = $lang->system->arribo_aprox.': '.$objViaje->getTiempoHM($arribo['segundos']).' (dist. '.formatearDistancia($arribo['km']).')';	
										}
									
									
									}
									else{//-- partidas
										if($row['vd_fin']){
											if($row['vd_fin_real']){
												$fecha = formatearFecha($row['vd_fin_real']);	
												if($row['diferenciaEgreso'] > 0){
													$estado = $lang->system->partio_atrasado;
													$estado.= ' ('.$objViaje->getTiempoHM(strtotime($row['vd_fin_real']) - strtotime($row['vd_fin'])).')';	
													$classEstado = 'color_rojo';
												}
												else{
													$estado = $lang->system->partio_en_tiempo;
													$classEstado = 'color_verde';
												}
											}
											elseif($row['sh_rd_id'] == 75 || $row['sh_rd_id'] == 76){
												$estado = $lang->system->movil_sin_reportar;
												$classEstado = 'color_rojo bold';
											}
											else{
												$estado = '';
												if(strtotime($row['vd_fin']) < strtotime(getFechaServer('Y-m-d H:i'))){
													$estado = $lang->system->partira_atrasado;
													$classEstado = 'color_rojo';
												}
												else{
													$estado = $lang->system->en_tiempo;
													$classEstado = 'color_verde';
												}
											}
										}
										elseif($row['vd_fin_real']){
											$fecha = formatearFecha($row['vd_fin_real']);
											$estado = $lang->system->partio;
											$classEstado = 'color_verde';	
										}
										elseif($row['sh_rd_id'] == 75 || $row['sh_rd_id'] == 76){
											$estado = $lang->system->movil_sin_reportar;
											$classEstado = 'color_rojo bold';
										}
									}
                                    ?>   
                                    <center>
                                    	<?php if(!empty($fecha)){?>
                                        	<span class="campo1"><?=$fecha?></span>
                                            <br />
                                        <?php }?>    
                                    	<span class="campo2 <?=$classEstado?>"><?=$estado?></span> 
                                        <span class="campo2 block"><?=$aux?></span> 
                                    </center>
								</td>
                                <td class="td-last" width="100">
									<center>
                                    <?php if(!$row['vi_facturado']){?>
                                    <a href="javascript:;" onclick="javascript:$('#hidId').val(<?=$row['vi_id']?>); enviar('modificarFacturado');">
                                        <div class="estado-viaje" style="float:none;">
                                            Facturar
                                        </div>
                                    </a>
                                    <?php }else{?><span class="campo1"><?=$lang->system->facturado?></span><?php }?>
                                    </center>
                                    <br />
                                    <span class="campo2" style="font-size:9px; color:#999;"><?=encode($row['vi_observaciones'])?></span>
                                </td>
                            </tr>
                            <?php }?>
                            <?php if(!count($arrViajes)){?>
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
                                <td></td>
                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="6">
									<strong><?=$totalRegistros?> <?=($totalRegistros > 1)?$lang->system->viajes:$lang->system->viaje?></strong>
                                </td>
							</tr>
							<?php }?>
						</tbody>
                    </table>
                </div>
			</div>
            <!-- Fin. listado de viajes -->
    	</form>
    </div>
</div>