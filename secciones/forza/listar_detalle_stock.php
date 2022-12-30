<div id="botonesABM">
	<span id="botonVolver" onclick="javascript:window.parent.cerrarPopup();"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
</div>
<span class="clear"></span>
<br>
<table width="100%" height="450">
	<thead>
		<tr>
			<td><span class="campo1">Cliente</span></td>
			<td><span class="campo1">Ubicación</span></td>
			<td><span class="campo1">Stock Entregado</span></td>
			<td><span class="campo1">Stock Pendiente de Retiro</span></td>
			<td><span class="campo1">Stock Retirado</span></td>
			<td><span class="campo1">Fecha de Entrega/Retiro</span></td>
			<td><span class="campo1">Código de transacción</span></td>
			<td><span class="campo1">Código de Viaje</span></td>
			<td><span class="campo1">Fabricante Encargado del Viaje</span></td>
			<td><span class="campo1">Viaje Vinculado</span></td>
			<td><span class="campo1">Fabricante Dueño</span></td>
			<td><span class="campo1 td-last">Tipo Viaje</span></td>
		</tr>
		</thead>
	<tbody>
	<?php if($arrListado){
		foreach($arrListado as $i => $item){
			$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
			<tr class="<?=$class?> <?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
				<td><?=$item[0]?></td>
				<td><?=$item[1]?></td>
				<td><center><?=$item[2]?></center></td>
				<td><center><?=$item[3]?></center></td>
				<td><center><?=$item[4]?></center></td>
				<td><center><?=formatearFecha($item[5])?></center></td>
				<td><center><?=$item[6]?></center></td>
				<td><?=$item[7]?></td>
				<td><?=$item[8]?></td>
				<td><?=$item[9]?></td>
				<td><?=$item[10]?></td>
				<td class="td-last"><center><?=$item[11]?></center></td>
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