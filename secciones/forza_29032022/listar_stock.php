<div id="botonesABM">
	<span id="botonVolver" onclick="javascript:window.parent.cerrarPopup();"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
</div>
<span class="clear"></span>
<br>
<table width="100%" height="100%">
	<thead>
		<tr>
			<td><span class="campo1"><?=$lang->system->fecha?></span></td>
			<td><span class="campo1">Viaje Asociado</span></td>
			<td><span class="campo1">Tipo de Viaje</span></td>
			<td><span class="campo1">Cantidad</span></td>
			<td><span class="campo1 td-last"><?=$lang->system->pod?></span></td>
		</tr>
		</thead>
	<tbody>
	<?php if($arrListado){
		foreach($arrListado as $i => $item){
			$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
			<tr class="<?=$class?> <?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
				<td><center><?=formatearFecha($item['Fecha'])?></center></td>
				<td><?=$item['Viaje']?></td>
				<td><center><?=$item['tipo']?></center></td>
				<td><center><?=$item['Cantidad']?></center></td>
				<td class="td-last"><center><?php if(!empty($item['vale'])){?><a href="<?=$item['vale']?>" target="_blanck">Visualizar</a><?php }?></center></td>
			</tr>
		<?php } 
		}
		else{?>
			<tr class="tr-last">
				<td class="td-last" colspan="5"><center><?=$lang->message->sin_resultados?></center></td>
			</tr>
		<?php }?>
	</tbody>
</table>