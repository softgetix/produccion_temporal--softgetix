<script anguage="javascript" type="text/javascript">
$(document).ready(function(){
    function refreshPage() {
        location.reload();
    }
    setInterval(refreshPage, 60000);
});
</script>


<?php if(isset($popup) && $popup = true){?>
    <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post"  enctype="multipart/form-data">
	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" />

    <input name="hidMessage" id="hidMessage" type="hidden" />
    <input name="hidNumber" id="hidNumber" type="hidden" />
    <input name="hidTitle" id="hidTitle" type="hidden" />
    <input name="hidPath" id="hidPath" type="hidden" />
    <input name="hidIdViaje" id="hidIdViaje" type="hidden" />
    <input name="hidAdicional" id="hidAdicional" type="hidden" />

    <div class="solapas gum clear">
        <a title="Cerrar" class="float_r" href="javascript:window.parent.cerrarPopup();">
            <span class="sprite eliminar"></span>
        </a>
        <span class="clear" style="margin-bottom:4px;"></span>
         <div class="contenido clear" style="max-height:310px; overflow-x:auto;">
            <?php  if($vista == 'sendtosms'){?>
                <center>
                    <div style="padding:20px 0" <?=(!$status) ?'class="error"':''?> ><?=$message?></div>
                </center>
            <?php } elseif($vista == 'attachfile'){?>
                <input name="idviaje" type="hidden" value="<?=$_GET['idviaje']?>" />
                <input name="iddestino" type="hidden" value="<?=$_GET['iddestino']?>" />
                <div style="padding:10px" <?=(!$status) ?'class="error"':''?> ><?=$message?></div>
                <?php if($status != true){?>
                <table>
                <tbody>
					<tr><td><input type="file" name="attach" value="Adjuntar Archivo" style="width:100%;"></td></tr>
                </tdbody>
                </table>
                <br/>
                <center><a href="javascript:enviar('popupPostAttachFile');" class="button colorin">Enviar</a></center>
                <?php }?>
            <?php }else{?>
            <table class="listado-viajes" width="100%" height="100%">
                <thead>
                    <tr>
                     	<td>
                           	<span class="campo1">Mensajes</span>
                        </td>
						<td class="td-last">
                            <span class="campo1"><center><? if($vista == 'viewhistory'){?>Fecha de Envío<?php }?></center></span>
                        </td>
                    </tr>
                </thead>
                <tbody>
				<?php 
				foreach($results as $k => $row){
					$fila = ($fila == 'filaImpar')?'filaPar':'filaImpar';
				?>	
				<tr class="<?=$fila?>">
                    <td>
					    <span class="campo2">
                        <?php switch($vista){
                            case 'viewhistory':
                                echo $row['mensaje'];
                            break;
                            case 'viewinfoaditional':
                                echo $row['type'];
                            break;
                            default:
                                echo $row['message'];
                                if(isset($row['adicional'])){
                                    if($row['adicional'] == 1){
                                        echo '<input type="text" name="txtadicional_'.$k.'" id="txtadicional_'.$k.'" />';
                                    }
                                }
                            break;
                        }?>
                    </td>
                    <td class="td-last">
                        <center>
                        <?php switch($vista){
                            case 'viewhistory':
                                echo $row['fechaenviado'];
                            break;
                            case 'viewinfoaditional':
                                echo $row['value'];
                            break;
                            default:
                                echo '<a href="javascript:enviar(\'popupSendSMS\');" class="button colorin pv_send_sms"
                                attrmessage="'.encode($row['message']).'" 
                                attrnumber="'.$row['number'].'"
                                attrtitle="'.$row['title'].'"
                                attrurl="'.$row['url'].'"
                                attridviaje="'.$idviaje.'"
                                attrkey="'.$k.'"
                                >Enviar</a>';
                            break;
                        }?>
                        </center> 
                    </td>   
                </tr>
                <?php }?>
                <?php if(!count($results)){?>
				<tr class="tr-last">
					<td class="td-last" colspan="2" ><center><?=$lang->message->sin_resultados?></center></td>
				</tr>
				<?php }
				else{?>
					<tr class="tr-last">
					    <td></td>
                        <td class="td-last"></td>
                    </tr>
                <?php }?>
			    </tbody>
            </table>
            <?php }?>
        </div>
    </div>
    </form>          
<?php }
else{?>
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
                <input type="hidden" id="id_viaje" />
            </div>
            <!-- Inicio. listado de viajes -->
            <div class="solapas gum clear">
            	<div class="contenido clear" style="height:100%">
                    
                    <?php foreach($solapaValues as $k => $item){?>
                    <a href="boot.php?c=<?=$_GET['c']?>&solapa=<?=$k?>" class="solapa_table_no_padding izquierda float_l <?=($solapa==$k)?'active':''?>"><?=$item?></a>
                    <?php }?>
                    
                    <table class="listado-viajes" width="100%" height="100%">
                    <thead>
                        <tr>
                        	<td>
			  <?php if(!tienePerfil(8)){?>
                            	<?=$objFiltroCol->label($col['transportista'],$lang->system->transportista,'campo1')?>

			   <?php } else {?> <center><span class="campo1">Transporte</span></center>
				 <?php } ?>
							</td>
							<td colspan="2">
			<?php if(!tienePerfil(8)){?>

                              	<?=$objFiltroCol->label($col['movil'],$lang->system->conductor,'campo1')?>
                        
			 <?php } else {?> <center><span class="campo1">Chofer</span></center>
				 <?php } ?>


			    </td>
							<td>
                              	<span class="campo1">Ingreso reportado por conductor</span>
                            </td>
							<td width="220">

				<?php if(!tienePerfil(8)){?>

                                <?=$objFiltroCol->label($col['arribo'],$lang->system->arribos,'campo1',true)?>
				
				<?php } else {?> <center><span class="campo1">Arribo</span></center>
				 <?php } ?>		


							</td>
                            <td>
                                <? //=$objFiltroCol->label($col['numerodt'],'Número DT','campo1',true)?>
                                <center><span class="campo1">Número DT</span></center>
							</td>
                            <?php if(!tienePerfil(8)){?>
                            <td>
                                <center><span class="campo1">Verificado en EHS</span></center>
							</td>
                            <td width="65">


                                <center><span class="campo1">Mensajes</span></center>
                            </td>
                            <?php }?>
                            <td width="34">
                                <center><span class="campo1">Info</span></center>
							</td>
                            <td class="td-last" width="34">
                                <center><span class="campo1"></span></center>
							</td>
                        </tr>
                    </thead>
                    <tbody>
						<?php 
						foreach($arrViajes as $k => $row){
							$totalRegistros++;
                            $fila = ($fila == 'filaImpar')?'filaPar':'filaImpar';
                            $colorcelda = $row['colorcelda'] ? "background:{$row['colorcelda']}":'';
						?>	
							<tr class="<?=$fila?>">
                                <td style="<?=$colorcelda?>">
								    <span class="campo1"><?=encode($row['transportista'])?></span>
                                </td>
                                <td style="<?=$colorcelda?>">
                                	<?php if($row['id_movil']){
										if($objViaje->tieneMovilAsignado($row['id_movil'])){?>
                                        <a title="<?=$lang->botonera->ver_mapa?>" class="float_l viewCarOnMap <?='vi_'.$row['vi_id']?> <?=($row['vdd_id'])?'vdd_'.$row['vdd_id']:'origen'?>" <?=(!$row['id_movil'])?'style="display:none"':''?> href="javascript:;" attrIdMovil="<?=$row['id_movil']?>" attrIdRef="<?=$row['re_id']?>">
                                            <span class="sprite mapa no_margin"></span>
                                        </a>
                                    	<?php }
									}?>
                                </td>   
                                <td style="<?=$colorcelda?>">
                                    <span class="campo1"><?=encode($row['co_conductor'])?></span>
                                    <span class="campo2 block"><?=encode($row['co_telefono'])?></span>
                                    <br />
                                    <span class="campo1"><?=$row['vi_movil']?></span>
                                </td>
                                <td style="<?=$colorcelda?>">
                                    <center>
                                    <?php if($solapa == 'all'){?>
                                        <span class="campo2"><?=encode($row['re_nombre'])?></span><br />
                                    <?php }?>
									<span class="campo1"><?=formatearFecha($row['fecha_ini'])?></span>
                                    </center>
                                </td>
                                <td style="<?=$colorcelda?>">
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
                                <td style="<?=$colorcelda?>">
                                    <center>    
                                        <?=encode($row['observaciones'])?>
                                    </center>
                                </td>
                                <?php if(!tienePerfil(8)){?>
                                <td style="<?=$colorcelda?>">
                                    <center>    
                                        <a id="a-playavirtual-verificado-<?=$k?>" class="iconRedGreen <?=$row['vi_verificado']?'iconOn':'iconOff'?>" href="javascript:$('#id_viaje').val(<?=$row['vi_id']?>); OnOff('playavirtual-verificado',true,null,'<?=$k?>');" style="margin-top:5px !important;"></a>
                                    </center>
                                </td>
                                <td style="<?=$colorcelda?>">
                                    <a title="Enviar Mensaje" class="float_l pv_send_message margin_l" href="javascript:;" attrIdViaje="<?=$row['vi_id']?>" style="margin-top:4px;">
                                        <span class="sprite sendmessage no_margin"></span>
                                    </a>
                                    <a title="Ver Enviados" class="float_l pv_view_history margin_l" href="javascript:;" attrIdViaje="<?=$row['vi_id']?>" style="margin-top:4px;">
                                        <span class="sprite viewhistory no_margin"></span>
                                    </a>
                                </td>
                                <?php }?>
                                <td style="<?=$colorcelda?>">
                                    <a title="Información adicional" class="float_l pv_view_info_aditional margin_l" href="javascript:;" attrIdViaje="<?=$row['vi_id']?>" style="margin-top:4px;">
                                        <span class="sprite viewinfoaditional no_margin"></span>
                                    </a>
                                </td>
                                <td style="<?=$colorcelda?>" class="td-last">
                                    <?php 
                                    if(!tienePerfil(8)){
                                        $archivo = existeArchivo($row['vi_id'], $row['re_id'], true);
                                        if($archivo){?>
                                        <a href="https://<?=$_SERVER['HTTP_HOST'].'/'.$archivo?>" target="_blanck" style="margin-top:4px;">
                                            <span class="sprite document no_margin"></span>
                                        </a>
                                    <?php } else{?>
                                        <a title="Adjuntar archivo" class="float_l pv_attach_file margin_l" href="javascript:;" attrIdViaje="<?=$row['vi_id']?>" attrIdDestino="<?=$row['re_id']?>" style="margin-top:4px;">
                                            <span class="sprite attachfile no_margin"></span>
                                        </a>
                                    <?php }?>
                                    <?php }?>        
                                </td>
                            </tr>
                            <?php }?>
                            <?php if(!count($arrViajes)){?>
							<tr class="tr-last">
								<td class="td-last" colspan="10" ><center><?=$lang->message->sin_resultados?></center></td>
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
                                <?php if(!tienePerfil(8)){?>
                                <td></td>
                                <td></td>
                                <?php }?>
                                <td class="td-last"></td>
                            </tr>
                            <tr>
								<td class="none-border paginador" colspan="7">
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
<?php }?>