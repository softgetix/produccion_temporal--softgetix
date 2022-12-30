<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>
<div id="main">
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
    <div class="mainBoxLICabezera">
	<h1><?=$lang->system->abm_comandos?></h1>
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<input name="hidMovilesSerializados" id="hidMovilesSerializados" type="hidden"/>
		<?php
		switch ($operacion){
			case 'listar':
				require_once 'includes/botoneraABMs.php';
		?>
	</div><!-- fin. mainBoxLICabezera -->
		<div id="mainBoxLI">
			<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
				<tr class="titulo">
					<td width="4%"></td>
					<td width="24%" align="center"><?=$lang->system->nombre?></td>
					<td width="24%" align="center"><?=$lang->system->tipo_comando?></td>
					<td width="24%" align="center"><?=$lang->system->comando?></td>
					<td width="24%" align="center"><?=$lang->system->instrucciones?></td>
				</tr>
                <?php $filtros = array();
					if($arrEntidades){
						for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
							$opciones = '<img src="imagenes/espera.png" width="14" height="14"/>';
							$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
							
							if ($arrEntidades[$i]["co_tipo"] == 1) $opciones = '<img src="imagenes/envia_espera.png" width="14" height="14"/>';
							?>
                            <tr class="<?=$class?>">
								<td align="center">
                                	<input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['co_id']?>" value="<?=$arrEntidades[$i]['co_id']?>"/>
                                </td>
								<td align="center" class="nombre">
                                	<a style="text-decoration:underline" href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['co_id']?>)"><?=$arrEntidades[$i]['co_nombre']?></a>
                                </td>
                                <td align="center"><?=$opciones?></td>
                                <td align="center"><?=$arrEntidades[$i]['co_codigo']?></td>
                                <td align="center"><?=$arrEntidades[$i]['co_instrucciones']?></td>
							</tr>
						<?php }
					}
					else{?>
					   	<tr class="filaPar">
					         <td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
					    </tr>
					<?php }?>
                </table>
			</div><!-- fin. mainBoxLI -->
		<?php
		break;
		case 'alta':
		case 'modificar':
			require_once 'includes/botoneraABMs.php';
		?>
		</div><!-- fin. mainBoxLICabezera -->
        <hr/>
		<div id="mainBoxAM">
			<input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=@$mensaje;?>" />
			<fieldset>
				<legend><?=$lang->system->general?></legend>
				<table style="margin-left:30px;">
					<tr height="27">
						<td class="td_label">
                        	<label for="txtNombre"><?=$lang->system->nombre?></label>
                        </td>
						<td class="td_campo">
                        	<input type="text" name="txtNombre" id="txtNombre" maxlength="50" value="<?=@$arrEntidades[0]['co_nombre']?>" /> *
                        </td>
					</tr>
					<tr height="27">
						<td class="td_label">
                        	<label for="txtCodigo"><?=$lang->system->comando?></label>
                        </td>
						<td class="td_campo">
                        	<input type="text" id="txtCodigo" name="txtCodigo" maxlenght="50" value="<?=@$arrEntidades[0]['co_codigo']?>" /> *
                        </td>
					</tr>
					<tr height="27">
						<td class="td_label">
                        	<label for="txtInstrucciones"><?=$lang->system->instrucciones?></label>
                        </td>
						<td class="td_campo">
                        	<input type="text" id="txtInstrucciones" name="txtInstrucciones" value="<?=@$arrEntidades[0]['co_instrucciones'];?>" maxlength="255"/>
                        </td>
					</tr>
					<tr height="27">
						<td class="td_label">
                        	<label for="txtResOK"><?=$lang->system->respuesta_correcta?></label>
                        </td>
						<td class="td_campo">
                        	<input type="text" id="txtResOK" name="txtResOK" value="<?=@$arrEntidades[0]['co_respuesta_ok'];?>" />
                        </td>
					</tr>
                    <tr height="27">
						<td class="td_label"><label for="cmbFavorito"><?=$lang->system->favorito?></label></td>
						<td class="td_campo">
                           	<select name="cmbFavorito">
                               	<option value="0" <?=(!@$arrEntidades[0]['co_favorito'])?'selected="selected"':''?>><?=$lang->system->no?></option>
                                   <option value="1" <?=(@$arrEntidades[0]['co_favorito'])?'selected="selected"':''?>><?=$lang->system->si?></option>
                            </select>
                        </td>
					</tr>
                    <tr height="27">
						<td class="td_label"><label for="cmbModeloEquipo"><?=$lang->system->modelo_de_equipo?></label></td>
						<td class="td_campo">
                        	<select name="cmbModeloEquipo">
                            	<option value=""><?=$lang->system->seleccione?></option>
                                <?php foreach($cmbModeloEquipo as $item){?>
                                	<option value="<?=$item['mo_id']?>" <?=(@$arrEntidades[0]['co_mo_id'] == $item['mo_id'])?'selected="selected"':''?>><?=$item['mo_nombre']?></option>
                                <?php }?>
							</select> *
                        </td>
					</tr>
					<tr height="27">
						<td class="td_label"><label><?=$lang->system->tipo_comando?></label></td>
						<td class="td_campo">
                        	<label><input type="radio" name="radTipo" value="0"<?=(@$arrEntidades[0]['co_tipo'] == 0)?' checked="checked"':'';?>/><?=$lang->system->espera_evento?></label>
                            <label><input type="radio" name="radTipo" value="1"<?=(@$arrEntidades[0]['co_tipo'] == 1)?' checked="checked"':'';?>/><?=$lang->system->envia_comando_espera?></label>
                        </td>
					</tr>
				</table>
			</fieldset>
		</div><!-- fin. mainBoxAM -->
	<?php
    break;
	}?>
	</form>
</div>