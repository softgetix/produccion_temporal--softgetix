<?php if(isset($popup) && $popup == true){ ?>
    
    <form name="frm_cuenta" id="frm_cuenta" action="?c=cuenta&solapa=moviles" method="post">
	<input name="hidSeccion" id="hidSeccion" type="hidden" value="cuenta" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" />

    <input name="hidMessage" id="hidMessage" type="hidden" />
    <input name="hidNumber" id="hidNumber" type="hidden" />
    <input name="hidTitle" id="hidTitle" type="hidden" />
    <input name="hidPath" id="hidPath" type="hidden" />
    <input name="hidMessagetype" id="hidMessagetype" type="hidden" />
    <input name="hidIdViaje" id="hidIdViaje" type="hidden" />
    <input name="hidAdicional" id="hidAdicional" type="hidden" />

    <input name="hidId" type="hidden" value="<?=$id?>" />
    <input name="hidAction"type="hidden" value="popupPostChangeUser"  />

    <div class="solapas gum clear">
        <a title="Cerrar" class="float_r" href="javascript: window.parent.cerrarPopup();">
            <span class="sprite eliminar"></span>
        </a>
        <span class="clear" style="margin-bottom:4px;"></span>
        <div class="contenido clear" style="max-height:310px; overflow-x:auto;">
            <?php  if($vista == 'sendtosms'){?>
                <center>
                    <div style="padding:20px 0" <?=(!$status) ?'class="error"':''?> ><?=$message?></div>
                </center>
            <?php } elseif($vista == 'viewhistory' || $vista == 'sendmessage'){ ?>
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
                            default:
                                echo $row['message'];
                                if(isset($row['adicional'])){
                                    if($row['adicional'] == 1){
                                        echo '<input type="text" name="txtadicional_'.$k.'" id="txtadicional_'.$k.'" style="width:96%" />';
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
                            default:
                                echo '<a href="javascript:enviar(\'popupSendSMS\');" class="button colorin pv_send_sms"
                                attrmessage="'.encode($row['message']).'" 
                                attrnumber="'.$row['number'].'"
                                attrtitle="'.$row['title'].'"
                                attrurl="'.$row['url'].'"
                                attrtype="'.$row['messagetype'].'"
                                attridmovil="'.$idmovil.'"
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
            <?php }else{?>
                <center><div style="padding:10px" <?=(!$status) ?'class="error"':'class="ok"'?> ><?=$message?></div></center>
                
                <?php if(isset($status) && $status == true){
                    echo '<script>parent.window.location.reload(true);</script>';
                    exit;    
                }
                else{?>
                <center>
                <label>Nombre:&nbsp</label>
                <input type="text" name="nombre" value="<?=$nombre?>" />
                <center>
                <br/><br/>
                <center><a href="javascript:enviar('popup');" class="button colorin">Enviar</a></center>
                <?php }?>
            <?php }?>    
        </div>
    </div>
    </form>          
<?php }
else{
switch($operacion){
	case 'modificar':?>
    	<div id="botonesABM" style="width:100%;">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
		<?php require_once 'includes/interfazGraficaABMs.php';?>
		<?php if(tienePerfil(19)){?>
        <fieldset>
			<legend>Configuraci&oacute;n Equipo</legend>
			<table width="100%" >
				<tr>
					<td align="right" height="20"><?=$lang->system->equipo_instalado?>&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="equipo_instalado" id="equipo_instalado" style="width:304px;">
                           	<option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($equipos as $item){?>
                            	<option value="<?=$item['un_id']?>" <?php if($item['un_mo_id'] == $id){?>selected="selected"<?php }?>><?=$item['equipo']?></option>
							<?php }?>
                        </select>
						<input type="hidden" name="equipo_viejo" id="equipo_viejo" />
                    </td>
				</tr>
			</table>
		</fieldset>
		<?php }?>
        <!-- -->
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript: enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
       	</center>
        <br />
    <?php break; 
	default:?>
    <?php 
	$tipoBotonera = 'LI-Export';
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
                <td><span class="campo1"><?=$lang->system->matricula?></span></td>
                <?php if(!tienePerfil(array(9,10,11,12))){?>
                <td><center><span class="campo1"><?=$lang->system->marca?>/<?=$lang->system->modelo?></span></center></td>
                <?php } elseif(tienePerfil(12)){?>
                <td><span class="campo1"><?=$lang->system->nombre?></span></td>
                <td><span class="campo1">Documento o Legajo</span></td>
                <td><span class="campo1">Tag</span></td>
                <?php } elseif(tienePerfil(9)){?>
                <td><center><span class="campo1">Distancia Social</span></center></td>
                <?php }?>
                <!--<td><center><span class="campo1"><? //=$lang->system->tipo_movil?></span></center></td>-->
                <td><span class="campo1"><?=$lang->system->cliente?></span></td>
                <td><center><span class="campo1"><?=$lang->system->estado?></center></span></td>
                <td><center><span class="campo1"><?=$lang->system->ultimo_reporte_recibido?></span></center></td>
                <td class="td-last"><center><span class="campo1"><? //=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>  <?=$item['mo_borrado']?'row_inactivo':''?>">
 					<td>
						<?php if(tienePerfil(array(5,9,13,19)) && !$item['mo_borrado']){?>
                            <input type="hidden" name="chkId[]" id="chk_<?=$item['mo_id']?>" value="<?=$item['mo_id']?>"/>
                            <a href="javascript:enviarModificacion('modificar',<?=$item['mo_id']?>)"><?=$item['mo_matricula']?></a>
                        <?php }
                        else{echo $item['mo_matricula'];}?>
                    </td>
                    <?php if(!tienePerfil(array(9,10,11,12))){?>
                    <td>
                    	<center>
						<?=(trim($item['mo_marca']) == true && trim($item['mo_modelo']) == true)?trim($item['mo_marca'].' / '.$item['mo_modelo']):trim($item['mo_marca'].' '.$item['mo_modelo'])?>
                        </center>
					</td>
                    <?php } elseif(tienePerfil(12)){?>
                    <td>
                        <span><?=$item['mo_otros']?></span>
                        <a href="javascript:mostrarPopup('boot.php?c=cuenta&solapa=moviles&action=changeUser&id=<?=$item['id']?>',380,150);" class="float_r">Cambiar</a>
                    </td>
                    <td><?=$item['mo_identificador']?></td>
                    <td><?=$item['tag']?></td>
                    <?php } elseif(tienePerfil(9)){?>
                    <td><center><a href="javascript:;" onclick="javascript:changeDistance(this.id, <?=$item['id']?>);" id="changeDistance<?=$item['id']?>" class="iconDistancia <?=($item['un_tiempo'] == 60) ? 'dist1m' : 'dist25m'?>"></a></center></td>
                    <?php }?>
                    <!--<td><center><? //=$lang->system->$item['tv_nombre']?$lang->system->$item['tv_nombre']->__toString():$item['tv_nombre']?></center></td>-->
                    <td><?=$item['cl_razonSocial']?><?=tienePerfil(12) ? $item['tipo_licencia'] : ''?> </td>
                    <td><center><?=($item['un_push_id_estado'] === '1') ? 'Instalado' : (($item['un_push_id_estado'] === '0') ? 'Desinstalado' : 'Pendiente')?></center></td>
                    <td><center><?=formatearFecha($arrEntidades[$i]['sh_fechaRecepcion'])?></center></td>
                    <td class="no_padding td-last">
                    	<center>
                        	<?php if($item['mo_borrado']){?>
                            	<span><?=$lang->system->movil_inactivo?></span>	
                            <?php }elseif(tienePerfil(array(5,9,13,19))){?>
                            <a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=(int)$item['mo_id']?>)"><img src="imagenes/cerrar.png" /></a>
                            <?php }elseif(tienePerfil(12)){?>
                                <a title="Enviar Mensaje" class="float_l pv_send_message margin_l" href="javascript:;" attrIdMovil="<?=$item['id']?>" style="margin-top:4px;">
                                    <span class="sprite sendmessage no_margin"></span>
                                </a>
                                <!--<a title="Ver Enviados" class="float_l pv_view_history margin_l" href="javascript:;" attrIdMovil="<? //=$item['id']?>" style="margin-top:4px;">
                                    <span class="sprite viewhistory no_margin"></span>
                                </a>-->
                            <?php }?>
						</center>
					</td>
                </tr>
            <?php } 
				$cantRegistros = count($arrEntidades);
				include('secciones/footer_LI.php');
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
	<?php break;
}}?>