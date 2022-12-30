<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>

<div id="main">
   <div class="mainBoxLICabezera">
   <h1>Administraci&oacute;n de Interfaz Generica</h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?= isset( $id ) ? $id : '' ?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<?php
	switch ($operacion){
		case 'listar':
			require_once 'includes/botoneraABMs.php';?>
			<div id="mainBoxLI">
				<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
					  <tr class="titulo">
						 <td width="4%"></td>
						 <td width="32%" align="center"><b><?=$lang->system->nombre?></b></td>
						 <td width="32%" align="center"><b>Referencias</b></td>
						 <td width="32%" align="center"><b>Secci&oacute;n</b></td>
					  </tr>
				<?php if($arrEntidades){
					for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
						<tr class="<?=$class?>">
							<td><input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['ig_id']?>" value="<?=$arrEntidades[$i]['ig_id']?>"/></td>
							<td align="center"><a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['ig_id']?>)"><?=$arrEntidades[$i]['ig_nombre']?></a></td>
							<td align="center"><?=$arrEntidades[$i]['ig_nombre']?></td>
							<td align="center"><?=$arrEntidades[$i]['ig_seccion']?></td>
						</tr>
					<?php }
					}
					else{?>
						<tr class="filaPar">
							<td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
						</tr>
					<?php }?>
			</table>
		</div>
		<?php
	   	break;
		case 'alta':
		case 'modificar':
			require_once 'includes/botoneraABMs.php';?>			
			</div>
			<hr>
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
			<?php require_once 'includes/interfazGraficaABMs.php'; ?>
            <br />
            <fieldset>
				<legend>Secci&oacute;n al que pertenece</legend>
				<table width="100%">
					<tr>
						<td align="right" valign="middle" height="20px">Secci&oacute;n&nbsp;&nbsp;</td> 
						<td style="text-align:left;" width="80%">
							<?php global $arrMenu;?>
							<select name="cmbSeccion" style="width:304px;">
                            	<option value=""><?=$lang->system->seleccione?></option>
							<?php foreach($arrMenu as $item){?>
								<option value="<?=$item?>" <?=$arrEntidades[0]['ig_seccion']==$item?'selected="selected"':''?>><?=$item?></option>
							<?php }?>
                            </select>
						</td>
					</tr>
				</table>
			</fieldset>	
		<?php
		break;
	}?>
	</form>
</div>