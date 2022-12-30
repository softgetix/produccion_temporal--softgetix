<div id="main" class="sinColIzq">
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<input type="hidden" name="idEquipo" value="<?=$idEquipo?>"/>
    <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />  
    <div class="mainBoxLICabezera">
		<h1>Configuraci&oacute;n de Equipos</h1>
		<?php require_once 'includes/botoneraABMs.php';?>  
    </div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxAM">
        <br />
        <label><?=$lang->system->equipo?>: </label>
        <strong><?=$arrEqquipo['un_mostrarComo']?></strong>
        <table width="100" cellpadding="0" cellspacing="0" class="widefat" style="margin-top:10px;">
			<thead>
				<tr class="titulo">
					<td align="center">Nro</td>
					<td align="center">Min</td>
					<td align="center">Max</td>
					<td align="center">Operaci&oacute;n</td>
					<td align="center">Factor</td>
					<td align="center">Unidad</td>
					<td align="center">Visible</td>
				</tr>
			</thead>
			<tbody>
            <!--<tr><td colspan="7">&nbsp;</td></tr>-->
			<?php for($i=1; $i<=$cantSensores; $i++){?>
            	<tr>
					<td style="padding-top:10px;" align="center">#<?=$i?> <input type="hidden" name="hidIdUT[]" value="<?=$i?>" /></td>
					<td align="center"><input type="text" name="txtMin[<?=$i?>]" style="width:50px;" maxlength="3" value="<?=$arrTelemetria[$i]['ut_min']?>"/></td>
					<td align="center"><input type="text" name="txtMax[<?=$i?>]" style="width:50px;" maxlength="3" value="<?=$arrTelemetria[$i]['ut_max']?>"/></td>
					<td align="center">
                    	<select name="cmbOp[<?=$i?>]">
                        	<option value="">&nbsp;</option>
                            <?php foreach($arrOperaciones as $item){?>
								<option value="<?=$item['op_id']?>" <?php if($item['op_id'] == $arrTelemetria[$i]['ut_op_id']){?>selected="selected"<?php }?>><?=$item['op_simbolo']?></option>
							<?php }?>
                        </select>
                    </td>
					<td align="center"><input type="text" name="txtFactor[<?=$i?>]" style="width:50px;" value="<?=$arrTelemetria[$i]['ut_factor']?>"/></td>
					<td align="center"><input type="text" name="txtUnidad[<?=$i?>]" maxlength="15" value="<?=$arrTelemetria[$i]['ut_unidad']?>"/></td>
					<td align="center"><input type="checkbox" name="chkVisible[<?=$i?>]" value="1" <?php if($arrTelemetria[$i]['ut_visible']){?>checked="checked"<?php }?>/></td>
				</tr>
			<?php }?>
			</tbody>
		</table>
    </div>
</form>
</div>