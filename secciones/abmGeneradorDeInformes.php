<style>
.solapas .contenido fieldset  table{ width:100%; margin:auto;}
</style>
<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" enctype="multipart/form-data">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
            
            <div style="height:100%" class="contenido clear"> 
			<?php switch($operacion){
				case 'alta':
				case 'modificar':?>
                
                <div id="botonesABM">
                    <a id="botonVolver" href="boot.php?c=<?=$seccion?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
                </div>
                <span class="clear"></span>
                <!-- -->
                <fieldset>
                    <legend>Configuraci&oacute;n General</legend>
                    <table width="100%">
                    <tbody>
                    <tr>
                        <td align="right" height="20" valign="middle">Nombre del Informe&nbsp;&nbsp;</td>
                        <td style="text-align:left;" width="80%">
                            <input name="txtNombreInforme" value="<?=encode($_POST['txtNombreInforme'])?>" style="width:300px;" size="50" type="text">&nbsp;*
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="20">Agente&nbsp;&nbsp;</td>
                        <td width="80%" style="text-align:left;">
                            <select name="cmbAgente" id="cmbAgente" style="width:305px;" onchange="javascript:getClientes(this.value, <?=(int)$_POST['cmbCliente']?>); getUsuarios(this.value, false, '<?=$_POST['checkEnviarA']?>')">
                                <option value=""><?=$lang->system->seleccione?></option>
                                <?php foreach($arrAgente as $item){?>
                                    <option value="<?=$item['cl_id']?>" <?php if($item['cl_id'] == $_POST['cmbAgente']){?>selected="selected"<?php }?>><?=$item['cl_razonSocial']?></option>
                                <?php }?>
                            </select>&nbsp;*
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="20">Cliente&nbsp;&nbsp;</td>
                        <td width="80%" style="text-align:left;">
                            <select name="cmbCliente" id="cmbCliente" style="width:305px;" onchange="javascript:getUsuarios($('#cmbAgente option:selected').val(), false, '<?=$_POST['checkEnviarA']?>'); getUsuarios(this.value, true, '<?=$_POST['checkEnviarA']?>')">
                                <option value=""><?=$lang->system->seleccione?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:top; ">ACCION</td>
                        <td style="text-align:left;">
                            <textarea id="txtConsulta" name="txtConsulta" style="width:524px; height:200px; margin-bottom:4px;"><?=encode($_POST['txtConsulta'])?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                        <td style="text-align:left;height: 35px;">
                            <a href="javascript:;" onclick="javascript:enviar('generarAdjunto')" style="width:100px;" class="button_xls exp_excel">
                                EJECUTAR ACCION
                            </a>
                            
                            <a href="javascript:;" onclick="javascript:limpiarConsulta()" style="margin-left:330px;">
                                Borrar Consulta
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="20">Tipo de Envio&nbsp;&nbsp;</td>
                        <td width="80%" style="text-align:left;">
                            <select name="cmbTipoEnvio" style="width:305px;">
                                <option value=""><?=$lang->system->seleccione?></option>
                                 <?php foreach($arrTipoEnvio as $tipo){?>
                                 <option value="<?=$tipo['ite_id']?>" <?php if($tipo['ite_id'] == $_POST['cmbTipoEnvio']){?>selected="selected"<?php }?>><?=$tipo['ite_nombre']?></option>
                                 <?php }?>
                            </select>&nbsp;*
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="20">Hora de Envio&nbsp;&nbsp;</td>
                        <td width="80%" style="text-align:left;">
                            <select name="cmbHoraEnvio" style="width:305px;">
                                <option value="06:00" <?php if('06:00' == $_POST['cmbHoraEnvio']){?>selected="selected"<?php }?>>06:00</option>
                            </select>&nbsp;*
                        </td>
                    </tr>
                    </tbody>
                    </table>
                </fieldset>
                <fieldset style="margin-top:10px;">
                    <legend>Configuraci&oacute;n de Envio</legend>
                    <table width="100%">
                    <tbody>
                    <tr>
                        <td align="right" height="20" valign="middle">Asunto&nbsp;&nbsp;</td>
                        <td style="text-align:left;" width="80%">
        
                            <input name="txtSubject" value="<?=encode($_POST['txtSubject'])?>" style="width:524px;" size="50" type="text">&nbsp;*
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:top;">Mensaje&nbsp;&nbsp;</td>
                        <td style="text-align:left;">
                            <textarea id="txtMensaje" name="txtMensaje" style="margin-bottom:4px;"><?=encode($_POST['txtMensaje'])?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:top;">Enviar a&nbsp;&nbsp;</td>
                        <td style="text-align:left;">
                            <textarea id="txtEnviarA" name="txtEnviarA" style="width:524px; height:100px; margin-bottom:4px;"><?=$_POST['txtEnviarA']?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">&nbsp;</td>
                        <td id="IDEnviarA" style="text-align:left;"></td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:top;">Enviar con copia a&nbsp;&nbsp;</td>
                        <td style="text-align:left;">
                            <textarea id="txtEnviarCopiaA" name="txtEnviarCopiaA" style="width:524px; height:100px; margin-bottom:4px;"><?=$_POST['txtEnviarCopiaA']?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:middle;">Enviar adjunto&nbsp;&nbsp;</td>
                        <td style="text-align:left;">
                            <input type="checkbox" name="checkEnviarAdjunto" value="1" class="float_l" <?=(!isset($_POST['checkEnviarAdjunto']) || $_POST['checkEnviarAdjunto'])?'checked="checked"':''?> />
                            <?php 
                            if($informe['in_adjunto_name']){
                                echo '<span class="float_l" style="line-height:20px; margin-left:20px;">Adjunto: <strong>'.$informe['in_adjunto_name'].'</strong></span><span class="clear"></span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align:middle;">Guardar Copia&nbsp;&nbsp;</td>
                        <td style="text-align:left;">
                            <input type="checkbox" name="checkGuardarCopia" value="1" class="float_l" <?=(!isset($_POST['checkGuardarCopia']) || $_POST['checkGuardarCopia'])?'checked="checked"':''?> />
                        </td>
                    </tr>
                    </tbody>
                    </table>
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
				if ($_SESSION['idPerfil'] != 37) {
				$tipoBotonera = 'LI-NewItem'; 
				include('includes/botoneraABMs.php');
				}
	
				?>
            
	<?php  if ($_SESSION['idPerfil'] != 37) {?>
    
                <div class="float_l" style="margin:0 0 4px 47px;">
                    <select name="cmbTipoEnvioList" class="buscar" onchange="javascript:enviar('index');">
                        <option value="">Todos los tipos de envio</option>
                        <?php foreach($arrTipoEnvio as $tipo){?>
                        <option value="<?=$tipo['ite_id']?>" <?php if($tipo['ite_id'] == $filtroTipoEnvio){?>selected="selected"<?php }?>><?=$tipo['ite_nombre']?></option>
                        <?php }?>
                    </select>
                </div>
                


                <div class="float_l" style="margin:0 0 4px 10px;">
                    <select name="cmbAgenteList" id="cmbAgenteList" class="buscar" onchange="javascript:enviar('index');">
                        <option value="">Todos los agentes</option>
                        <?php foreach($arrAgente as $item){?>
                            <option value="<?=$item['cl_id']?>" <?php if($item['cl_id'] == $filtroAgente){?>selected="selected"<?php }?>><?=$item['cl_razonSocial']?></option>
                        <?php }?>
                    </select>
                </div>
          <?php }    ?>
	   
                <table width="100%" height="100%">
                <thead>
                	<tr>
                    	<td><span class="campo1">Nombre</span></td>
                        <td><span class="campo1">Agente / Cliente</span></td>
                        <td><center><span class="campo1">Tipo Envio</span></center></td>
                        <td><center><span class="campo1">Adjuntos</span></center></td>
                        <td><center><span class="campo1">Hora Envio</span></center></td>
                        <td><center><span class="campo1">&Uacute;ltimo Envio</span></center></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="td-last" width="60"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
                    </tr>
				</thead>
                <tbody>
                <?php if($arrInformes){ $temp_ref = false;
                	foreach($arrInformes as $i => $item){
                    	$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrInformes) - 1)==$i)?'tr-last':''?>">
                        <td>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['in_id']?>" value="<?=$item['in_id']?>"/>
							
			     
	<?php  if ($_SESSION['idPerfil'] != 37) {?>
				<a href="javascript: enviarModificacion('modificar',<?=$item['in_id']?>)"><?=encode($item['in_nombre'])?></a>
	
<?php } else {   ?>
<?=encode($item['in_nombre'])?>
<?php }   ?>

                        </td>
                        <td><?=$item['agente'].(!empty($item['cliente'])?' / '.$item['cliente']:'')?></td>
						<td><center><?=$item['in_tipo_envio']?></center></td>
                        <td><center><?=$item['in_adjunto']?'Si':'No'?></center></td>
                        <td><center><?=$item['in_hora_envio']?></center></td>
                        <td><center><?=formatearFecha($item['in_fecha_ultimo_envio'])?></center></td>
                        <td><center>
<?php  if ($_SESSION['idPerfil'] != 37) {?>

                            <a href="javascript:;" onclick="javascript:onOffInforme(this.id, <?=(int)$item['in_id']?>);" id="estadoInforme<?=$item['in_id']?>" title="Activar/Desactivar Envios"><img src="imagenes/<?=$item['in_estado']?'ok':'cerrar'?>.png" /></a>

                        	<a href="javascript:;" onclick="javascript:duplicarInforme(<?=(int)$item['in_id']?>);" title="Duplicar Informe"><span class="icon_small duplicar clear"></span></a>

<?php }   ?>
                            </center>
                        </td>
                        <td>
                        	<center>
                                <a href="javascript:;" onclick="javascript:probarEnvio(<?=(int)$item['in_id']?>)" class="button colorin">
                                    Ejecutar
                                </a>
                    		</center>
                        </td>
                        <td class="no_padding td-last">
<?php  if ($_SESSION['idPerfil'] != 37) {?>
							<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['in_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
<?php }   ?>

                        </td>
                    </tr><?php }
					}
					else{?>
						<tr class="tr-last">
                    	<td class="td-last" colspan="3"><center><?=$lang->message->sin_resultados?></center></td>
						</tr>
					<?php }
				?></table><?php
                break;
			}?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>