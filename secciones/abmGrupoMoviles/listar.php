<div id="mainBoxLI">
	<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
		<tr class="titulo">
			<td width="20px"></td>
			<td width=""><?=$lang->system->grupo?></td>
		</tr>
        <?php $filtros = array();
			if($arrEntidades){
				for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
					$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
					if (!in_array($arrEntidades[$i]['gm_nombre'],$filtros)) $filtros[] = $arrEntidades[$i]['gm_nombre'];?>
		<tr class="<?=$class?>">
			<td width="20px">
            	<input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['gm_id']?>" value="<?=$arrEntidades[$i]['gm_id']?>"/>
			</td>
			<td width="">
            	<a href="javascript: enviarModificacion('modificarAsignacion',<?=$arrEntidades[$i]['gm_id']?>)"><?=$arrEntidades[$i]['gm_nombre']?></a>
			</td>
		</tr>
		<?php }
		}else{?>
			<tr class="filaPar">
				<td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
			</tr>
		<?php }?>
	</table>
</div> <!-- fin. #mainBoxLI -->   
<div style="display:none">
	<select name="cmbFiltro" id="cmbFiltro" class="combobox">
		<option value="0"></option>
		<?php sort($filtros); foreach($filtros as $f) { ?>
		<option value="<?=$f?>"><?=$f;?></option>
		<?php } ?>
	</select>
</div>
