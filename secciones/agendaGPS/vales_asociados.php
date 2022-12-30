<div id="botonesABM">
	<a id="botonVolver" href="javascript:;"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
</div>
<span class="clear"></span>
<br>
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
	<input type="hidden" name="idViaje" value="<?=$vi_id?>" >
	<div style="font-size: 18px"><center><span>Cantidad de Pallets: </span><strong id="total-pallets">0</strong></center></div>
	<br><br>
	<table id="abmVales" width="100%" height="100%">
		<tbody>
			<?php if(!$registros){?>
			<tr class="tr-last">
				<td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
			</tr>
			<?php }
			else{
				foreach($registros as $i => $row){
					$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
					<tr class="<?=$class?> <?=((count($registros) - 1)==$i)?'tr-last':''?>" >
						<td align="center" width="10">
							<input type="checkbox" id="chk_<?=$row['id']?>" name="chkId[]" value="<?=$row['id']?>" <?=$row['checked']?'checked':''?> onchange="javascript:changeCheckPallets(this)" >
						</td>
						<td>
							<span class="campo1"><?=encode($row['propietario'])?></span>
						</td>
						<td>
							<span class="campo1"><?=encode($row['ubicacion'])?></span>
						</td>
						<td>
							<span class="campo1"><a href="<?=$row['link']?>" target="_blank" class="link">Foto</a></span>
						</td>
						<td>
							<span class="campo1"><?=encode($row['codigoVale'])?></span>
						</td>
						<td>
							<span class="campo1"><?=encode($row['estado'])?></span>
						</td>
						<td class="td-last">
							<span id="cant_<?=$row['id']?>" class="campo1 cantidad <?=$row['checked']?'checked':''?>" ><?=$row['cantidad']?></span>
						</td>
					</tr>
				<?php }?>
				<tr class="tr-last">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="td-last"></td>
				</tr>
			<?php }?>
		</tbody>
	</table>
	<span class="clear"></span>
	<center><a href="javascript:;" onclick="enviar('guardarVales');" class="button colorin" style="margin-top:5px; padding:8px 20px;"><?=strtoupper($lang->botonera->guardar)?></a></center>
	<br>
</form>
<script language="javascript">
	$(document).ready(function(){
		sumarPallets();		
	});

	function sumarPallets(){
		var $suma = 0;
		$('#abmVales tr').each(function(){ 
			$(this).find('td').each(function(){ 
				if($(this).find('span').hasClass('cantidad') && $(this).find('span').hasClass('checked')){
					$suma = $suma + parseInt($(this).find('span').html());
				}				
			});
		});

		$('#total-pallets').html($suma);
	}

	function changeCheckPallets($this){
		if($($this).is(':checked') == false){
			$('#cant_'+$($this).val()).removeClass('checked');
		}
		else{
			$('#cant_'+$($this).val()).addClass('checked');
		}
		
		sumarPallets();
	}
</script>