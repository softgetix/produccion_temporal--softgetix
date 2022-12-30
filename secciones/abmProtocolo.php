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
                <?php 
				$vistaReducida = true;
				if(tienePerfil(19)){
					$vistaReducida = false;
				}
				?>
				<fieldset>
					<legend>General</legend>
					<table width="100%">
					<tbody>
					<tr>
						<td align="right" height="20" valign="middle">Nombre&nbsp;&nbsp;</td>
						<td style="text-align:left;" width="80%"><input name="txtNombre" id="txtNombre" value="<?=$_POST['txtNombre']?>" style="width:300px;" size="50" type="text">&nbsp;*</td>
					</tr>
					<tr>
						<td align="right" height="20" valign="middle">Ip&nbsp;&nbsp;</td>
						<td style="text-align:left; font-weight:bolder;" width="80%">
						<?php if(!$vistaReducida){?>
							<input name="txtIp" value="<?=$_POST['txtIp']?>" style="width:300px;" type="text">&nbsp;*
						<?php }else{echo $_POST['txtIp'];}	?>
						</td>
					</tr>
					<tr>
						<td align="right" height="20" valign="middle">Puerto&nbsp;&nbsp;</td>
						<td style="text-align:left; font-weight:bolder;" width="80%">
						<?php if(!$vistaReducida){?>
							<input name="txtPuerto" value="<?=$_POST['txtPuerto']?>" style="width:300px;" size="5" type="text">&nbsp;*
						<?php }else{echo $_POST['txtPuerto'];}	?>
						</td>
					</tr>
					</tbody>
					</table>
				</fieldset>
				<fieldset style="margin-top:10px;">
					<legend>Configuraci&oacute;n</legend>
					<table width="100%">
					<tbody>
					<?php if(!$vistaReducida){?>
					<tr>
						<td align="right" height="20">Tipo Protocolo&nbsp;&nbsp;</td>
						<td width="80%" style="text-align:left;">
							<select name="cmbTipoProtocolo" style="width:300px;">
								<option value=""><?=$lang->system->seleccione?></option>
								<?php foreach($arrTipoProtocolo as $item){?>
									<option value="<?=$item['pt_id']?>" <?php if($item['pt_id'] == $_POST['cmbTipoProtocolo']){?>selected="selected"<?php }?>><?=$item['pt_nombre']?></option>
								<?php }?>
							</select> *
						</td>
					</tr>
					<tr>
						<td align="right" style="vertical-align:top; ">Consulta SQL&nbsp;&nbsp;</td>
						<td style="text-align:left;">
							<textarea id="txtConsulta" name="txtConsulta" style="width:524px; height:100px;"><?=$_POST['txtConsulta']?></textarea>
						</td>
					</tr>
					<?php }?>
					<tr>
						<td colspan="2">
						<!-- -->
						<table style="margin:10px 0 0 30px;">
						<tbody>
						<tr>
							<td style="text-align:center !important">
								<label for="lstMoviles" style="line-height:20px;"><?=$lang->system->moviles_disponibles?></label>
							</td>
							<td></td>
							<td style="text-align:center !important">
								<label for="lstMovilesAsig" style="line-height:20px;"><?=$lang->system->moviles_asignados?></label>
							</td>
						</tr>
						<tr>
							<td class="td_campo" style="text-align:center !important">
								<select id="lstMoviles" multiple="multiple"  class="lstIzq" style="height:150px !important;">
									<?php foreach ($arrMoviles as $fila){?>
										<option value="<?=$fila['id']?>"><?=$fila['dato']?></option>
									<?php	}?>
								</select>
							</td>
							<td class="td_pasaje" style="vertical-align: middle">
								<button type="button" class="btnDerT">&gt;&gt;</button>
								<button type="button" class="btnDer">&gt;</button>
								<button type="button" class="btnIzq">&lt;</button>
								<button type="button" class="btnIzqT">&lt;&lt;</button>
							</td>
							<td class="td_campo" style="text-align:center !important">
								<select id="lstMovilesAsig" multiple="multiple"  class="lstDer" style="height:150px !important;">
									<?php	if (isset($arrMovilesAsig)){
										foreach ($arrMovilesAsig as $fila){?>
											<option value="<?=$fila['id']?>"><?=$fila['dato']?></option>
										<?php }?>
									<?php }?>
								</select>
								<input type="hidden" id="hid_lstMovilesAsig" name="hid_lstMovilesAsig" value="<?=$hidArrMovilesAsignados?>"/>
							</td>
						</tr>
						</tbody>
						</table>
						<!-- -->
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
				$tipoBotonera = 'LI-NewItem'; 
				include('includes/botoneraABMs.php');
				?>
                
                <table width="100%" height="100%">
                <thead>
                	<tr>
                        <td><span class="campo1">Nombre Protocolo</span></td>
                        <td><center><span class="campo1">Tipo</span></center></td>
                        <td class="td-last" width="60"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
                    </tr>
				</thead>
                <tbody>
                <?php if($arrProtocolo){ $temp_ref = false;
                	foreach($arrProtocolo as $i => $item){
                    	$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrProtocolo) - 1)==$i)?'tr-last':''?>">
                        <td>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['pr_id']?>" value="<?=$item['pr_id']?>"/>
							<a href="javascript: enviarModificacion('modificar',<?=$item['pr_id']?>)"><?=encode($item['pr_nombre'])?></a>
                        </td>
                        <td><center><?=$item['pt_nombre']?></center></td>
                        <td class="no_padding td-last">
							<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['pr_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                        </td>
                    </tr><?php }
					}
					else{?>
						<tr class="tr-last">
                    	<td class="td-last" colspan="2"><center><?=$lang->message->sin_resultados?></center></td>
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
