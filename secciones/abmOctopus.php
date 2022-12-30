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
                <center>
                <table class="inline" >
				<tbody>
                	<tr><td><table>
                    <tr>
						<td align="right" height="20" valign="middle">Nombre&nbsp;&nbsp;</td>
						<td style="text-align:left;" width="80%"><input name="txtNombre" id="txtNombre" value="<?=$_POST['txtNombre']?>" style="width:300px;" size="50" type="text">&nbsp;*</td>
					</tr>
                    <tr>
						<td align="right" height="20" valign="middle">Agente&nbsp;&nbsp;</td>
						<td style="text-align:left;" width="80%">
                        	<select name="cmbAgente" style="width:306px;">
                            	<option value=""><?=$lang->system->seleccione?></option>
                                <?php foreach($arrAgente as $item){?>
                                    <option value="<?=$item['cl_id']?>" <?php if($item['cl_id'] == $_POST['cmbAgente']){?>selected="selected"<?php }?>><?=$item['cl_razonSocial']?></option>
                                <?php }?>
                            </select>&nbsp;*
                       	</td>
					</tr>
                    <tr>
						<td colspan="2">
                        	<table style="width:100% !important" cellpadding="0" cellspacing="0">
                        	<tr>
                            	<td align="right" height="20" valign="middle">Protocolo&nbsp;&nbsp;</td>
                                <td>
                                    <select name="cmbTipoProtocolo" style="width:160px;" onchange="javascript:message_example(this.value)">
                                        <option value=""><?=$lang->system->seleccione?></option>
                                        <?php foreach($arrProtocolo as $item){?>
                                            <option value="<?=$item?>" <?php if($item == $_POST['cmbTipoProtocolo']){?>selected="selected"<?php }?>><?=$item?></option>
                                        <?php }?>
                                    </select> *
                                </td>
                                <td align="right" style="width:40px !important" valign="middle">M&eacute;todo&nbsp;&nbsp;</td>
                                <td>
                                    <select name="cmbMetodo" style="width:160px;">
                                        <option value=""><?=$lang->system->seleccione?></option>
                                        <?php foreach($arrMetodo as $item){?>
                                            <option value="<?=$item?>" <?php if($item == $_POST['cmbMetodo']){?>selected="selected"<?php }?>><?=$item?></option>
                                        <?php }?>
                                    </select> *
                                </td>
                            </tr>
                        	</table>
                        </td>
					</tr>
                    <tr>
						<td align="right" height="20" valign="middle">URL&nbsp;&nbsp;</td>
						<td>
							<input name="txtURL" value="<?=$_POST['txtURL']?>" style="width:400px;" type="text">&nbsp;*
						</td>
					</tr>
                    <tr>
						<td colspan="2">
                        	<fieldset class="box-container-example">
                            	<legend>Header</legend>
                                <textarea name="txtHeader"><?=$_POST['txtHeader']?></textarea>
                                <div class="box-example">
                                	<p>Ingresar con separador de comas, ej: <a href="javascript:;" class="a-click-example"><i>Authorization:sso-key CLIENT:PASSWORD</i>, <i>Content-length:length</i></a></p>
                                </div>
                            </fieldset>    
                        </td>
                    </tr>
                    <tr>
						<td colspan="2">
                        	<fieldset class="box-container-example">
                            	<legend>Datos</legend>
                                <textarea name="txtDatos" class="with100 ws-message"><?=$_POST['txtDatos']?></textarea>
                            </fieldset>    
                        </td>
                    </tr>
                    <tr>
						<td colspan="2">
                        	<fieldset class="box-container-example">
                            	<legend>CURL SETOPT Adicionales</legend>
                                <textarea name="txtCurl"><?=$_POST['txtCurl']?></textarea>
                                <div class="box-example">
                                	<p>Ingresar con separador de comas, ej: <a href="javascript:;" class="a-click-example"><i>CURLOPT_USERPWD=User:Pass</i>, <i>CURLOPT_HTTPGET=true</i></a></p>
                                	<p><a href="http://php.net/manual/es/function.curl-setopt.php" target="_blank">Ver mas opciones</a></p>
                                </div>
                            </fieldset>    
                        </td>
                    </tr>
                    </table></td></tr>
                </tbody>
                </table>
                <table class="inline">
				<tbody>
                	<tr><td><table>
                 	<tr>
                    	<td>
                        	<fieldset class="box-container-example">
                            	<legend>Asignaci&oacute;n Par&aacute;metros de Respuesta</legend>
                                
                                <fieldset class="campos">
                                <label>Ruta Principal</label>
                                <input name="valRuta" value="<?=$_POST['valRuta']?>" style="width:400px;" type="text">
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Matricula</label>
                                <input name="valMatricula" value="<?=$_POST['valMatricula']?>" style="width:400px;" type="text">&nbsp;*
                                </fieldset>
                                
                                <fieldset class="campos bg_color_group">
                                <label>Evento</label>
                                <input name="valEvento" value="<?=$_POST['valEvento']?>" style="width:400px;" type="text">&nbsp;&nbsp;&nbsp;
                                	
                                    <fieldset>
                                    <label>Reporte GPS Programado (01)</label>
                                	<input name="valOptEvento01" value="<?=$_POST['valOptEvento01']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Falta de reporte (75)</label>
                                	<input name="valOptEvento75" value="<?=$_POST['valOptEvento75']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Falta de reporte + de 24 hs (76)</label>
                                	<input name="valOptEvento76" value="<?=$_POST['valOptEvento76']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Bot&oacute;n de P&aacute;nico (06)</label>
                                	<input name="valOptEvento06" value="<?=$_POST['valOptEvento06']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Sabotaje de Equipo (09)</label>
                                	<input name="valOptEvento09" value="<?=$_POST['valOptEvento09']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Reconexión Sabotaje (08)</label>
                                	<input name="valOptEvento08" value="<?=$_POST['valOptEvento08']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Exceso de Velocidad (13)</label>
                                	<input name="valOptEvento13" value="<?=$_POST['valOptEvento13']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Apertura de Puerta (36)</label>
                                	<input name="valOptEvento36" value="<?=$_POST['valOptEvento36']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Enganche acoplado (67)</label>
                                	<input name="valOptEvento67" value="<?=$_POST['valOptEvento67']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Encendido de Motor (54)</label>
                                	<input name="valOptEvento54" value="<?=$_POST['valOptEvento54']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Apagado de Motor (55)</label>
                                	<input name="valOptEvento55" value="<?=$_POST['valOptEvento55']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <p>** Para mas de una opci&oacute;n utilizar "/" (barra invertida)&nbsp;&nbsp;&nbsp;</p>
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Fecha de GPS</label>
                                <input name="valFechaGPS" value="<?=$_POST['valFechaGPS']?>" style="width:400px;" type="text">&nbsp;*
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Latitud</label>
                                <input name="valLatitud" value="<?=$_POST['valLatitud']?>" style="width:400px;" type="text">&nbsp;*
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Longitud</label>
                                <input name="valLongitud" value="<?=$_POST['valLongitud']?>" style="width:400px;" type="text">&nbsp;*
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Velocidad</label>
                                <input name="valVelocidad" value="<?=$_POST['valVelocidad']?>" style="width:400px;" type="text">&nbsp;&nbsp;&nbsp;
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Estado Motor</label>
                                <input name="valEstadoMotor" value="<?=$_POST['valEstadoMotor']?>" style="width:400px;" type="text">&nbsp;&nbsp;&nbsp;
                                	
                                    <fieldset>
                                    <label>Motor Encendido (1)</label>
                                	<input name="valOptEstadoMotorEncendido" value="<?=$_POST['valOptEstadoMotorEncendido']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <fieldset>
                                    <label>Motor Apagado (0)</label>
                                	<input name="valOptEstadoMotorApagado" value="<?=$_POST['valOptEstadoMotorApagado']?>" style="width:250px;" type="text">
                                    </fieldset>
                                    
                                    <p>** Para mas de una opci&oacute;n utilizar "/" (barra invertida)&nbsp;&nbsp;&nbsp;</p>
                                </fieldset>
                                
                                <fieldset class="campos">
                                <label>Od&oacute;metro</label>
                                <input name="valOdometro" value="<?=$_POST['valOdometro']?>" style="width:400px;" type="text">&nbsp;&nbsp;&nbsp;
                                </fieldset>
                        	</fieldset>        
                        </td>
                    </tr>
                    </table></td></tr>
                </tbody>
                </table>
                </center>
                </fieldset>
                <fieldset class="not_bg_white clear">
                    <center>
                   		<a href="javascript:;" onclick="javascript: enviar('probarServicio')"  class="button_xls exp_excel margin_r" style="width:173px; margin-top:18px;">Probar Servicio</a>
                    	<a href="javascript:;" onclick="javascript: enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px; margin-top:18px;"><?=$lang->botonera->guardar?></a>
					</center>
                </fieldset>
                
                <?php if($ws_response_msg){?>
				<fieldset class="box-container-example">
                	<legend>Respuesta</legend>
                   	<textarea class="with10012" style=" width:99%; height:291px;"><?=$ws_response_msg?></textarea>
                </fieldset>    
                <?php }?>    
				<?php if($ws_data){?>
                <br />
                <fieldset class="box-container-example">
                	<legend>Parametros Obtenidos (<?=(int)count($ws_data)?> registros) </legend>
                    <?php 
					foreach($ws_data as $ws_item){
						echo '<p>';
						foreach($ws_item as $var => $item){
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$var.':</strong> '.($item == ''?' -- ':$item);
						}
						echo '</p>';
					}?>
                </fieldset>    
                <?php }?>
                
                <?php 	
				break;
				default:
				$tipoBotonera = 'LI-NewItem'; 
				include('includes/botoneraABMs.php');
				?>
                
                <table width="100%" height="100%">
                <thead>
                	<tr>
                        <td><span class="campo1">Nombre Protocolo</span></td>
                        <td><span class="campo1">Agente</span></td>
                        <td><center><span class="campo1">Protocolo</span></center></td>
                        <td>&nbsp;</td>
                        <td><center><span class="campo1">&Uacute;lt. Conexi&oacute;n</span></center></td>
                        <td class="td-last" width="60"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
                    </tr>
				</thead>
                <tbody>
                <?php if($arrListado){
                	foreach($arrListado as $i => $item){
                    	$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
                        <td>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['oc_id']?>" value="<?=$item['oc_id']?>"/>
							<a href="javascript: enviarModificacion('modificar',<?=$item['oc_id']?>)"><?=encode($item['oc_nombre'])?></a>
                        </td>
                        <td><?=$item['cl_razonSocial']?></td>
                        <td><center><?=$item['oc_protocolo']?></center></td>
                        <td><center>
                            <a href="javascript:;" onclick="javascript:onOffOctopus(this.id, <?=(int)$item['oc_id']?>);" id="estadoInforme<?=$item['oc_id']?>" title="<?=$item['oc_estado']?'Desactivar':'Activar'?>"><img src="imagenes/<?=$item['oc_estado']?'ok':'cerrar'?>.png" /></a>
                            </center>
                        </td>
                        <td><center><?=formatearFecha($item['oc_fechaConexion'])?></center></td>
                        <td class="no_padding td-last">
							<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['oc_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                        </td>
                    </tr><?php }
					}
					else{?>
						<tr class="tr-last">
                    	<td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
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
