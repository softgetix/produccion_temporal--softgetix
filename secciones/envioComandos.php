<script type='text/javascript' src='js/boxes.js'></script>
<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>

<div id="main">
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
	<div class="mainBoxLICabezera">
		<h1>Env&iacute;o de comandos</h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
        <input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <input name="hidMovilesSerializados" id="hidMovilesSerializados" type="hidden" />
        <input name="hidUsuariosSerializados" id="hidUsuariosSerializados" type="hidden" />
	</div><!-- fin mainBoxLICabezera -->	
	<?php
	switch ($operacion){
	   case 'listar':
	?>
		<div id="mainBoxAM">
			<fieldset>
				<legend><?=$lang->menu->equipos?></legend>
				<table>
					<tr>
						<td style="text-align:left !important;"><b>Lista de Equipos</b></td>
						<td>&nbsp;</td>
						<td style="text-align:left !important;"><b>Equipos Asignados</b></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="txtFiltroUsuario" id="txtFiltroUsuario" value="" onkeyup='javascript: filtrarUsuarios();' style="width: 142px;">
							<input type="button" value="<?=$lang->botonera->buscar?>" onclick='javascript: filtrarUsuarios();' style="width: 60px;">
						</td>
						<td rowspan="2">
						   	<input name="B1" class="texto" onclick="JavaScript: Move(document.getElementById('cmbEquipos'), document.getElementById('cmbEquiposAsignados'));" value=">>" type="button"><br><br>
		            		<input name="B2" class="texto" onclick="JavaScript: Move(document.getElementById('cmbEquiposAsignados'), document.getElementById('cmbEquipos'));" value="<<" type="button">
			        	</td>
			        	<td rowspan="2">
			        		<select size="14" name="cmbEquiposAsignados[]" multiple="multiple" id="cmbEquiposAsignados" style="width: 210px;"></select>
						</td>
					</tr>
		    		<tr>
		    			<td>
		    			   	<select size="10" name="cmbEquipos" multiple="multiple" id="cmbEquipos"  style="width: 210px;">
								<?php for($i=0;$i < count($arrEquiposUsuarios) && $arrEquiposUsuarios;$i++){?>
									<option value="<?=$arrEquiposUsuarios[$i]["un_id"]?>"><?=$arrEquiposUsuarios[$i]["un_mostrarComo"]?></option>
								<?php }?>
		    			    </select>
		    		   </td>
					</tr>
				</table>
			</fieldset>
			<br/>
			<fieldset>
				<legend><?=$lang->menu->comandos?></legend>
				<table width="350">
					<tr>
						<td><?=$lang->system->comando?></td>
						<td>
							<input type="text" name="txtComando" id="txtComando" value="" style="width: 200px;">
							<input type="button" value="<?=$lang->botonera->agregar?>" onclick='javascript: agregarComando("Debe ingresar un comando para agregar.");' style="width: 30px;">
							<input type="button" value="<?=$lang->botonera->quitar?>" onclick='javascript: quitarComando("Debe seleccionar un comando para quitar.");' style="width: 30px;">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<select size="3" name="cmbComandos[]" id="cmbComandos" style="width:335px;"></select>
						<td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="button" value="<?=$lang->botonera->enviar?>" onclick="javascript: enviarComandos('Debe seleccionar almenos un equipo e ingresar un comando.');" style="width: 335px;">
						<td>
					</tr>
				</table>
			</fieldset>
			<br/>
			<fieldset>
				<legend>Panel de control</legend>
				<table style="width:100% !important;">
					<tr class="titulo">
			    		<td width="80px"><?=$lang->system->equipo?></td>
			         	<td width="80px"><?=$lang->system->comando?></td>
				        <td width="70px">Envio</td>
				        <td width="100px"><?=$lang->system->estado?></td>
				        <td width="70px">Recepci&oacute;n</td>
				        <td width="">Respuesta</td>
					</tr>
				</table>
				<div class="divTablaEstado" id="divTablaEstado"></div>
			</fieldset>
		</div><!-- fin. mainBoxAM -->
		<?php
	   	break;
	}?>
	</form>
</div>