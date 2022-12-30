<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" enctype="multipart/form-data">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
            
            <div style="height:100%" class="contenido clear"> 
			<?php switch($operacion){
				case 'alta':
				case 'modificar':
				?>
                
                <div id="botonesABM">
                    <a id="botonVolver" href="boot.php?c=<?=$seccion?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
                </div>
                <!-- -->
                <?php if(tienePerfil(16)){?> 
                    <input type="hidden" name="txtNombre" value="<?=isset($_POST['txtNombre'])?$_POST['txtNombre']:round(microtime(true))?>" style="width:210px;">
                <?php }else {?>    
                <fieldset class="not_bg_white">
                    <label class="etiqueta"><?=$lang->system->nombre_alerta?>: </label>
                    <input type="text" name="txtNombre" value="<?=isset($_POST['txtNombre'])?$_POST['txtNombre']:encode($arrEntidades['al_nombre'])?>" style="width:210px;">
                </fieldset>
                <br />
                <?php }?>
                <!-- Moviles -->
                <?php $ide0 = 'alert-moviles';?>
                <fieldset id="<?=$ide0?>" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_1?></label>
                    <div class="content-tags">
                        <div class="tags-input"></div>
                        <div class="search_tags">
                            <input type="text" name="add_tags" placeholder="<?=$lang->system->alertas_option_1?>" autocomplete="off" />
                            <ul>
                                <?php foreach($arrMoviles as $item){?>
                                <li style="display:none"><a href="javascript:;" class="input-add-tag" id="<?=$ide0?>@@<?=$item['mo_id']?>"><?=encode($item['mo_matricula'])?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="<?=$ide0?>" id="val-<?=$ide0?>" value="<?=implode(',',$valorActivo['moviles'])?>" />
                </fieldset>
                <!-- -->
                <!-- Referencias -->
                <?php $ide1 = 'alert-referencia';?>
                <fieldset id="<?=$ide1?>" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_2?></label>
                    <div class="content-tags">
                        <div class="tags-input"></div>
                        <div class="search_tags">
                            <input type="text" name="add_tags" placeholder="<?=$lang->system->alertas_option_2?>" autocomplete="off"/>
                            <ul>
                                <?php foreach($arrReferencias as $item){?>
                                <li style="display:none"><a href="javascript:;" class="input-add-tag" id="<?=$ide1?>@@<?=$item['re_id']?>"><?=encode($item['re_nombre']).(!empty($item['re_numboca'])?(' ('.$item['re_numboca'].')'):'')?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="<?=$ide1?>" id="val-<?=$ide1?>" value="<?=implode(',',$valorActivo['referencias'])?>" />
                </fieldset>
                <!-- -->
                <!-- Eventos -->
                <?php if($arrEventos){?>
				<?php $ide2 = 'alert-evento';?>
                <fieldset id="alert-evento" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_3?></label>
                    <div class="content-tags">
                        <div class="tags-input"></div>
                        <div class="search_tags">
                            <input type="text" name="add_tags" placeholder="<?=$lang->system->alertas_option_3?>" autocomplete="off"/>
                            <ul>
                                <?php foreach($arrEventos as $item){?>
                                <li style="display:none"><a href="javascript:;" class="input-add-tag" id="<?=$ide2?>@@<?=$item['id']?>"><?=$item['dato']?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="<?=$ide2?>" id="val-<?=$ide2?>" value="<?=implode(',',$valorActivo['eventos'])?>" />
                </fieldset>
                <!-- -->
                <?php }?>
                <!-- Dias -->
                <fieldset id="alert-dias" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_4?></label>
                    <div class="content-tags">
                        <center>
                            <a href="javascript:;" id="lunes_a_viernes" onclick="javascript:check_day(this)">
                                <span><?=$lang->system->alertas_option_4?></span>
                                <span class="check <?=$valorActivo['lunes_a_viernes']?'check_on':'check_off'?>"></span>
                            </a>    
                            
                            <a href="javascript:;" id="sabados_y_domingos" style="margin-left:20px;" onclick="javascript:check_day(this)">
                                <span><?=$lang->system->alertas_option_5?></span>
                                <span class="check <?=$valorActivo['sabados_y_domingos']?'check_on':'check_off'?>"></span>
                            </a>    
                            
                            <input type="hidden" name="lunes_a_viernes" id="val_lunes_a_viernes" value="<?=$valorActivo['lunes_a_viernes']?>"/>
                            <input type="hidden" name="sabados_y_domingos" id="val_sabados_y_domingos" value="<?=$valorActivo['sabados_y_domingos']?>"/>
                        </center>
                    </div>
                </fieldset>
                <!-- -->
                <!-- Horas -->
                <?php 
				if($_SESSION['idAgente'] == 100){	
				global $arr_hora, $arr_min;
                $valorActivo['hora']['fin'] = empty($valorActivo['hora']['fin']) ? '23' : $valorActivo['hora']['fin'];
                $valorActivo['min']['fin'] = empty($valorActivo['min']['fin']) ? '59' : $valorActivo['min']['fin'];
                ?>
                <fieldset id="alert-horas" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_6?></label>
                    <div class="content-tags">
                        <center>
                        <fieldset style="width:120px; display:inline;">	
                            <span class="clear"><?=$lang->system->alertas_option_7?></span>	
                            <select class="float_l" name="hora_ini" id="hora_ini" style="width:42px;" onchange="javascript:onChanges('ini','comboFecha')">
								<?php foreach($arr_hora as $item){?>
                                    <option value="<?=$item?>" <?php if($item == $valorActivo['hora']['ini']){?>selected="selected"<?php }?>><?=formatearFecha(date('Y-m-d').' '.$item.':00','hour')?></option>
                                <?php }?>
            				</select>
            				<span class="float_l" style="margin:0 2px 0 2px">:</span>
                            <select class="float_l" name="min_ini" id="min_ini" style="width:42px;">
                                <?php foreach($arr_min as $item){?>
                                    <option value="<?=$item?>" <?php if($item == $valorActivo['min']['ini']){?>selected="selected"<?php }?>><?=$item?></option>
                                <?php }?>
                            </select>
                            <span class="float_l" id="curso_horario_ini" style="margin:2px 0 0 2px"><?=formatearFecha(date('Y-m-d').' '.$valorActivo['hora']['ini'].':00','pref_hour')?></span>
                            <span class="clear"></span>
                        </fieldset>    
                        <fieldset style="width:120px; display:inline;">    
                            <span class="clear"><?=$lang->system->alertas_option_8?></span>	
                            <select class="float_l" name="hora_fin" id="hora_fin" style="width:42px;" onchange="javascript:onChanges('fin', 'comboFecha')">
								<?php foreach($arr_hora as $item){?>
                                    <option value="<?=$item?>" <?php if($item == $valorActivo['hora']['fin']){?>selected="selected"<?php }?>><?=formatearFecha(date('Y-m-d').' '.$item.':00','hour')?></option>
                                <?php }?>
            				</select>
            				<span class="float_l" style="margin:0 2px 0 2px">:</span>
                            <select class="float_l" name="min_fin" id="min_fin" style="width:42px;">
                                <?php foreach($arr_min as $item){?>
                                    <option value="<?=$item?>" <?=($item == $valorActivo['min']['fin']) ? 'selected="selected"' : ''?>><?=$item?></option>
                                <?php }?>
                            </select>
                            <span class="float_l" id="curso_horario_fin" style="margin:2px 0 0 2px"><?=formatearFecha(date('Y-m-d').' '.$valorActivo['hora']['fin'].':00','pref_hour')?></span>
                            <span class="clear"></span>
                        </fieldset>
                        </center>
                    </div>
                </fieldset>
                <?php }?>
                <!-- -->
                <!-- Usuarios -->
                <?php $ide3 = 'alert-usuario';?>
                <fieldset id="<?=$ide3?>" class="not_bg_white">
                    <label><?=$lang->system->alertas_label_5?></label>
                    <div class="content-tags">
                        <div class="tags-input">
                            <?php if($noBorrarUsuario){?>
                            <span class="tag" id="<?=$ide3?>@@<?=$noBorrarUsuario['us_id']?>"><span><?=encode($noBorrarUsuario['us_nombre'].' '.$noBorrarUsuario['us_apellido'].' ['.$noBorrarUsuario['us_nombreUsuario'].']')?></span></span>
                            <?php }?>
                        </div>
                        <div class="search_tags">
                            <input type="text" name="add_tags" placeholder="<?=$lang->system->alertas_option_6?>" autocomplete="off"/>
                            <ul>
                                <?php foreach($arrUsuarios as $item){?>
                                    <li style="display:none"><a href="javascript:;" class="input-add-tag" id="<?=$ide3?>@@<?=$item['us_id']?>"><?=encode($item['us_nombre'].' '.$item['us_apellido'].' ['.$item['us_nombreUsuario'].']')?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="<?=$ide3?>" id="val-<?=$ide3?>" value="<?=implode(',',$valorActivo['usuarios'])?>" />
                </fieldset>
                <!-- -->
                
                <fieldset class="not_bg_white">
                    <center>
                    	<a href="javascript:;" onclick="javascript: enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px; margin-top:18px;"><?=$lang->botonera->guardar?></a>
					</center>
                </fieldset>
                <?php 	
				break;
				default:
				?>
				<?php 
				$tipoBotonera = 'LI-NewItem'; 
				$lang->botonera->agregar_nuevo = $lang->botonera->agregar_alertas?$lang->botonera->agregar_alertas:$lang->botonera->agregar_nuevo;
				$lang->system->filtro_buscador = $lang->system->filtro_buscador_alertas?$lang->system->filtro_buscador_alertas:$lang->system->filtro_buscador;
				include('includes/botoneraABMs.php');
				?>
                <table width="100%" height="100%">
                <thead>
                	<tr>
                    <?php if(tienePerfil(16)){?>  
                    	<td><span class="campo1"><?=$lang->system->descripcion?></span></td>
                    <?php }else{?>    
                        <td><span class="campo1"><?=$lang->system->nombre_alerta?></span></td>
                        <?php if(!tienePerfil(array(16, 9, 10, 11, 12))){?>
                        <td><span class="campo1"><?=$lang->system->usuario_creador?></span></td>
						<?php } elseif(tienePerfil(array(16,9,10))){?>
                        <td><span class="campo1"><?=$lang->system->descripcion?></span></td>
                        <?php }?>
                    <?php }?>    
                    <td class="td-last" width="60"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>    
					</tr>
				</thead>
                <tbody>
                <?php if($arrEntidades){ $temp_ref = false;
                	foreach($arrEntidades as $i => $item){
                    	$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
                            <?php 
                            if(tienePerfil(16)){?>   
                                <td>
                                    <?php if($item['accion']){?>
                                    <input type="hidden" name="chkId[]" id="chk_<?=$item['al_id']?>" value="<?=$item['al_id']?>"/>
                                    <a href="javascript: enviarModificacion('modificar',<?=$item['al_id']?>)" class="no_decoration">
                                        <p style="color:#444"><?=$objAlertas->traduccionDescripcionAlertaMobile($item['al_id'])?></p>
                                    </a>
                                    <?php }?>
                                </td>
                                <td class="no_padding td-last">
                                    <?php if($item['accion']){?>
                                        <center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['al_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                                    <?php }?>
                                </td>
                            <?php }else{?>
                            <td>
								<?php if($item['accion']){?>
                                <input type="hidden" name="chkId[]" id="chk_<?=$item['al_id']?>" value="<?=$item['al_id']?>"/>
                                <a href="javascript: enviarModificacion('modificar',<?=$item['al_id']?>)">
                                    <?=$item['al_nombre']?>
                                </a>
                                <?php }
                                else{$temp_ref = true;?>
                                	<?=$item['al_nombre']?>  <span class="ref" title="Creado por <?=$item['usuario']?>">**</span>
                               	<?php }?>
                            </td>
                            <?php if(!tienePerfil(array(16, 9, 10, 11, 12))){?>
								<td><?=$item['usuario']?></td>
							<?php }
							elseif(tienePerfil(array(16,9,10))){?>
                            	<td><?=$objAlertas->traduccionDescripcionAlerta($item['al_id'])?></td>
                            <?php } ?>
							<td class="no_padding td-last">
								<?php if($item['accion']){?>
                                    <center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['al_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                                <?php }?>
                            </td>
                            <?php }?>
						</tr>
					<?php } 
					include('secciones/footer_LI.php');
				}
                else{ ?>
					<tr class="tr-last">
                    	<td class="td-last" colspan="3"><center><?=$lang->message->sin_resultados?></center></td>
					</tr>
                <?php }?>
                    </tbody>
                </table>
               	<?php if(!tienePerfil(16)){?>
                    <?php if($temp_ref){?><br /><span class="ref" style="line-height:20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**  <?=$lang->system->alertas_creadas_1?>.</span><?php }?>
                <?php }?>
				<?php 	
				break;
			}?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>

<script language="javascript">
function onChanges(ide, opcion){
	var fecha = $('#hora_'+ide).val()+':'+$('#min_'+ide).val()+':00';
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: 'ajax.php'
		,data:({accion:"get-formato-fecha",fecha:fecha,formato:'pref_hour'})
		,success:function(c){
			$('#curso_horario_'+ide).html(c);
		}
	});
}
</script>