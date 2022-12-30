<div id="mainBoxAM">
	<?php if($popup){?>
    	<script>var recargar = true;</script>
	<?php }?>
	<fieldset>		
		<legend><?=$lang->menu->equipos?></legend>
		<table>
			<tr>
				<td colspan="3">
                	<strong style="padding:0 10px 0 25px;"><?=$lang->system->grupo?></strong>
                	<input type="text" name="txtGrupo" id="txtGrupo" value="<?=($arrEntidades[0]["gm_nombre"])?$arrEntidades[0]["gm_nombre"]:"";?>">
                </td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
            <tr>
				<td style="height:25px; line-height:25px; text-align:center"><strong>M&oacuteviles</strong></td>
				<td></td>
				<td style="text-align:center"><strong>M&oacuteviles asociados</strong></td>
			</tr>
			<tr>
				<td align="center" style="vertical-align:top; width:210px;">
                	<select name="cmbGrupos" id="cmbGrupos" style="width: 400px; margin:0px 0px 2px 0px; clear:both;" onChange='simple_ajax("ajaxObtenerMovilesGrupos.php?idGrupo=" + this.value + "&p=0");'>
						<option value="-1"><?=$lang->system->filtrar?></option>
						<option value="0"><?=$lang->system->sin_grupo?></option>
						<?php for ($i=0; $i<count($arrGrupos) && $arrGrupos;$i++){ ?>
							<option value="<?=$arrGrupos[$i]["gm_id"]?>"><?=$arrGrupos[$i]["gm_nombre"]?></option>
						<?php } ?>	    			   		
					</select>
                    <select size="10" name="cmbMoviles" multiple="multiple" id="cmbMoviles"  style="width: 400px;">
					</select>
				</td>
				<td  style="vertical-align:top;">
                	<div id="botones" style="margin-top:70px;" >
						<input name="B1" class="texto" onclick="JavaScript: Move(document.getElementById('cmbMoviles'), document.getElementById('cmbMovilesAsignados'));" value=">>" type="button"><br/>
						<input name="B2" class="texto" onclick="JavaScript: Move(document.getElementById('cmbMovilesAsignados'), document.getElementById('cmbMoviles'));" value="<<" type="button">  				
					</div>
				</td>
				<td align="center" style="width:210px;">
					<select name="cmbMovilesAsignados[]" multiple="multiple" id="cmbMovilesAsignados" style="width: 400px; height:186px;">
						<?php for($i=0; $i < count($arrMovilesAsignados) && $arrMovilesAsignados; $i++): ?>
							<option value="<?=$arrMovilesAsignados[$i]["id"]?>"><?=$arrMovilesAsignados[$i]["dato"]?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
</div><!-- fin. #mainBoxAM -->