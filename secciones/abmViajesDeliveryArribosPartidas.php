<script type="text/javascript" language="javascript">
	var solapa = '<?=$solapa?>';
</script>
<div id="main" class="sinColIzq">
    <div class="mainBoxLICabecera">
        <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$_GET['c']?>" method="post" style="height:100%;">
            <div class="esp">
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input name="solapa" type="hidden" value="<?=$solapa?>" />
            </div>
            <!-- Inicio. listado de viajes -->
            <div class="solapas gum clear">
            	<?php include('includes/navbarSolapas.php');?>
                <!--
                <a href="boot.php?c=<?=$_GET['c']?>&solapa=arribos" class="izquierda float_l <?=($solapa=='arribos')?'active':''?>"><?=$lang->system->arribos?></a>
                <a href="boot.php?c=<?=$_GET['c']?>&solapa=partidas" class="izquierda float_l <?=($solapa=='partidas')?'active':''?>"><?=$lang->system->partidas?></a>
                -->       
                <div class="contenido clear" style="height:100%">
                    
                    <!--
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=arribos" class="float_l button <?=($solapa=='arribos')?'colorin':''?>" style="margin:4px 10px; width: 120px;"><?=$lang->system->arribos?></a>
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=partidas" class="float_l button <?=($solapa=='partidas')?'colorin':''?>" style="margin:4px 10px; width: 120px;"><?=$lang->system->partidas?></a>
                    -->
                    <!--
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=arribos" class="float_l" style="margin: 4px 10px;">
                        <input type="radio" <?=($solapa=='arribos')?'checked="checked"':''?> class="float_l" style="width:10px; height: 10px;">
                        <label class="float_l" style="line-height:18px;"><?=$lang->system->arribos?></label>
                    </a>
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=partidas" class="float_l" style="margin: 4px 10px;">
                        <input type="radio" <?=($solapa=='partidas')?'checked="checked"':''?> class="float_l" style="width:10px; height: 10px;" />
                        <label class="float_l" style="line-height:18px;"><?=$lang->system->partidas?></label>
                    </a>
                    -->
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=arribos" class="solapa_table izquierda float_l <?=($solapa=='arribos')?'active':''?>"><?=$lang->system->arribos?></a>
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=partidas" class="solapa_table izquierda float_l <?=($solapa=='partidas')?'active':''?>"><?=$lang->system->partidas?></a>
                   
                    
                    <table class="listado-viajes" width="100%" height="100%">
                    <thead>
                        <tr>
                        	<td>
                               	<span class="campo1"><?=$lang->system->codigo_viaje?></span>
                                <br />
                                <br />
                            </td>
                            <?php if($deliveryView){?>
                            <td>
                            	<span class="campo1"><?=$lang->system->delivery?>999</span>
                                <br />
                                <span class="campo2"><?=$lang->system->pedidos?></span>
							</td>
                            <?php }?>
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
                                <span class="campo2"><?=($solapa=='arribos')?$lang->system->ingreso_programado:$lang->system->egreso_programado?></span>
                            </td>
							<td class="td-last" width="220">
                            	<center>
                                	<?php if($solapa == 'partidas'){
                                	 	echo $objFiltroCol->label($col['partida'],$lang->system->partidas,'campo1',true);
									}
									else{
										echo $objFiltroCol->label($col['arribo'],$lang->system->arribos,'campo1',true);
									}?>	
                                </center>
                                <br />
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
                                <td style="<?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
								   	<a href="?c=abmViajesDelivery&idViaje=<?=$row['vi_id']?>" class="link float_l" target="_blank">
                                    	<?=encode($row['vi_codigo'])?>
                                    </a>
                                </td>
                                <?php if($deliveryView){?>
                                <td>
									<span class="campo1"><?=$row['vdd_delivery']?></span>
                                	<br />
									<?php if($row['vdd_id']){?>
                                    <span class="campo2">
                                        <?=$objViaje->getCodigoPedidos($row['vdd_id'],'<br>')?>
                                    </span>
                                    <?php }?>
                                </td>
                                <?php }?>
                                <td style="<?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
								    <span class="campo1"><?=encode($row['transportista'])?></span>
                                	<br />
									<span class="campo2"><?=encode($row['dador'])?></span>
                                </td>
                                <td style="<?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
                                	<?php if($row['id_movil']){
										if($objViaje->tieneMovilAsignado($row['id_movil'])){?>
                                        <a title="<?=$lang->botonera->ver_mapa?>" class="float_l viewCarOnMap <?='vi_'.$row['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>" <?=(!$row['id_movil'])?'style="display:none"':''?> href="javascript:;" attrIdMovil="<?=$row['id_movil']?>" attrIdRef="<?=$row['re_id']?>">
                                            <span class="sprite mapa no_margin"></span>
                                        </a>
                                    	<?php }
									}?>
                                </td>   
                                <td style="<?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
                                	<span class="campo1"><?=$row['vi_movil']?></span>
                                    <br />
                                    <span class="campo2"><?=encode($row['co_conductor'])?></span>
                                    <span class="campo2 block"><?=encode($row['co_telefono'])?></span>
                                </td>
                                <td style="<?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
									<span class="campo1"><?=encode($row['re_nombre'])?></span>
                                    <br />
                                    <span class="campo2"><?=($solapa=='arribos')?formatearFecha($row['fecha_ini']):formatearFecha($row['fecha_fin'])?></span>
                                </td>
                                <td class="td-last" style="vertical-align:middle; <?=(($arribos_y_partidas == 'cliente' || $arribos_y_partidas == 'planta') && !empty($row['colorcelda']))?('background:'.$row['colorcelda']):''?>">
                                <?php 
                                    $classEstado = null;
                                    if(isset($row['detalleestado'])){
                                        if(strpos($row['detalleestado'], 'Demorado') !== false){
                                            $classEstado = 'color_rojo';
                                        }
                                        elseif(strpos($row['detalleestado'], 'Tiempo estimado de Arribo') !== false){
                                            $classEstado = 'color_verde';
                                        }
                                    }

                                    $aux = $fecha = '';
                                    if($solapa == 'arribos'){ //-- arribos
                                        if(isset($row['detalleestado']) || is_null($row['detalleestado'])){//--Info del nuevo procedimiento db_SegmentosViajes
                                            $fecha = formatearFecha($row['fecha_ini_real']);
                                            $estado = $row['detalleestado'];
                                        }
                                        elseif($row['fecha_ini_real']){
                                            $fecha = formatearFecha($row['fecha_ini_real']);
                                            if(strtotime($row['fecha_ini_real']) > strtotime($row['fecha_ini'])){
                                                $estado = $lang->system->arribo_atrasado;
                                                $estado.= ' ('.$objViaje->getTiempoHM(strtotime($row['fecha_ini_real']) - strtotime($row['fecha_ini'])).')';	
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
                                            if(strtotime($row['fecha_ini']) < strtotime($arribo['fecha'])){
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
                                        if(isset($row['detalleestado']) || is_null($row['detalleestado'])){//--Info del nuevo procedimiento db_SegmentosViajes
                                            $fecha = formatearFecha($row['fecha_fin_real']);
                                            $estado = $row['detalleestado'];
                                        }
                                        elseif($row['fecha_fin']){
                                            if($row['fecha_fin_real']){
						                        $fecha = formatearFecha($row['fecha_fin_real']);	
						                        if(strtotime($row['fecha_fin_real']) > strtotime($row['fecha_fin'])){
                                                    $estado = $lang->system->partio_atrasado;
                                                    $estado.= ' ('.$objViaje->getTiempoHM(strtotime($row['fecha_fin_real']) - strtotime($row['fecha_fin'])).')';	
                                                    $classEstado = 'color_rojo';
                                                }
                                                else{
                                                    $estado = $lang->system->partio_en_tiempo;
                                                    $classEstado = 'color_verde';
						                        }
                                            }
                                            else{
                                                $estado = '';
						                        if(strtotime($row['fecha_fin']) < strtotime(getFechaServer('Y-m-d H:i'))){
                                                    $estado = $lang->system->partira_atrasado;
                                                    $classEstado = 'color_rojo';
                                                }
                                                else{
                                                    $estado = $lang->system->en_tiempo;
                                                    $classEstado = 'color_verde';
						                        }
                                            }
                                        }
                                        elseif($row['fecha_fin_real']){
                                            $fecha = formatearFecha($row['fecha_fin_real']);
                                            $estado = $lang->system->partio;
                                            $classEstado = 'color_verde';	
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
                                        
                                        <?php if(isset($row['masinformacion']) && !is_null($row['masinformacion'])){?>
                                            <div class="traficoView">
                                                <img src="imagenes/trafico_icono.png" style="margin-top:4px;"/>
                                                <span class="none"><?=encode($row['masinformacion'])?></span>
                                            </div>
                                            <!--<div  class="tooltip"><img src="imagenes/trafico_icono.png" style="margin-top:4px;"/>
                                            <span class="tooltiptext"><?php //=$row['masinformacion']?></span></div>-->
                                        <?php }?>
                                    </center>
								</td>
                            </tr>
                            <?php }?>
                            <?php if(!count($arrViajes)){?>
							<tr class="tr-last">
								<td class="td-last" colspan="8" ><center><?=$lang->message->sin_resultados?></center></td>
							</tr>
							<?php }
							else{?>
							<tr class="tr-last">
								<td></td>
                                <?php if($deliveryView){?><td></td><?php }?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="8">
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


<!--Ini.Popup para visualización de Tráfico-->
<div class="topupTrafico">
    <div class="showPopup" style="display:none;">
        <div class="contenedorPopup">
            <dl><p></p></dl>
            <br />    
            <center>
                <a href="javascript:;" onclick="javascript:;" class="button cancel" style=""><?=$lang->botonera->cerrar?></a>
            </center>
        </div>
    </div>
</div>    
<!--Fin.-->