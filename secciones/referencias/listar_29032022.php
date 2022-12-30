
<div id="buscador" class="no_padding margin_l">
	<div class="buscar_general_abm_tit">
    	<span><?=$lang->system->filtro_buscador?></span>
    </div>
	<input type="text" name="txtFiltro" id="txtFiltro" class="buscar" onkeypress="if(capturarEnter(event)) enviar('index');" style="width: 295px;" value="<?=$filtro?>" />
</div>
<a id="botonGuardar" class="button extra-wide colorin float_r" onclick="enviar('alta');" style="margin-bottom:10px;" href="javascript:;">
	<?=$lang->botonera->agregar_referencias?$lang->botonera->agregar_referencias:$lang->botonera->agregar_nuevo?>
</a>

<?php if(function_exists('export_xls') && !tieneperfil(array(16,9,10,11,12))){?>
<a id="botonGuardar" class="button button_xls exp_excel extra-wide float_r" onclick="javascript:enviar('export_xls');" style="margin:0 4px 10px 4px;" href="javascript:;">
   	<?=$lang->botonera->exportar_excel?>
</a>
<?php }?>

<?php if(tieneperfil(array(19,5))){?>
<a id="botonGuardar" class="button button_xls exp_excel extra-wide float_r" onclick="javascript:mostrarPopup('boot.php?c=<?=$seccion?>&action=importarExcel',480,230);" style="margin-bottom:10px;" href="javascript:;">
	<?=$lang->botonera->importar_excel?>
</a>
<?php }?>
		
<table width="100%" height="100%">
	<thead>
		<tr>
		<?php if(tienePerfil(27)){ ?>
			<td><span class="campo1"><?=$lang->system->nombre_referencia?> (<?=$lang->system->num_boca?>)</span></td>
			<td><span class="campo1"><?=$lang->system->direccion?></span></td>
			<td><span class="campo1">Stock</span></td>
			<td><span class="campo1">Verificado</span></td>
		<?php }else{?>	
        	<td><span class="campo1"><?=$lang->system->nombre_referencia?></span></td>
			<td><span class="campo1"><?=$lang->system->categoria?></span></td>
			<td><span class="campo1"><?=$lang->system->direccion?></span></td>
			<?php if(tienePerfil(array(5,8,12,19))){ ?>
            <td><span class="campo1"><?=$lang->system->num_boca?></span></td>
            <?php }?>
		<?php }?>
		<td class="td-last"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
		</tr>
		</thead>
	<tbody>
	<?php if($arrEntidades){
		foreach($arrEntidades as $i => $item){
			$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
			<tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
			<?php if(tienePerfil(27)){ ?>
				<td>
                	<input type="hidden" name="chkId[]" id="chk_<?=$item['re_id']?>" value="<?=$item['re_id']?>"/>
					<a style="text-decoration:underline" href="javascript:enviarModificacion('modificar',<?=$item['re_id']?>)"><?=$item['re_nombre']?> (<?=$item['re_numboca']?>)</a>
				</td>
				<td><?=(strlen($item['re_ubicacion']) > 35)?substr($item['re_ubicacion'],0,33).'...':$item['re_ubicacion']?></td>
				<td>
					<?php if(1 == 1 ){
						if($item['stock_cliente'] < 0){
							echo 'Transacción en curso';
						}
						else{?>
						<a title="Ver Stock" class="float_l" href="javascript:mostrarPopup('boot.php?c=abmReferencias&action=stock&idRef=<?=$item['re_id']?>',900,450)">
							<?=$item['stock_cliente']?>
                        </a>
					<?php }}?>	
				</td>
				<td><span class="<?=($item['re_Verificado'] == 1) ? 'bg_verde' : 'bg_rojo'?> clear">&nbsp;</span></td>
			<?php }else{?>
				<td>
                	<?php if(!tienePerfil(array(7,11)) && $item['tr_id'] == 1){?>
                        <input type="hidden" name="chkId[]" id="chk_<?=$item['re_id']?>" value="<?=$item['re_id']?>"/>
						<a style="text-decoration:underline" href="javascript:enviarModificacion('modificar',<?=$item['re_id']?>)"><?=$item['re_nombre']?></a>
					<?php } else{ echo $item['re_nombre'];}?>
                </td>
				<td><?=$item['rg_nombre']?></td>
				<td><?=(strlen($item['re_ubicacion']) > 35)?substr($item['re_ubicacion'],0,33).'...':$item['re_ubicacion']?></td>
				<?php if(tienePerfil(array(5,8,12,19))){ ?>
				<td><center><?=$item['re_numboca']?></center></td>
				<?php } ?>
			<?php }?>
            <td class="no_padding td-last">
				<?php if(!tienePerfil(array(7,11)) && $item['tr_id'] == 1){?>
					<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['re_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
				<?php }?>
			</td>
			</tr>
		<?php } 
			include('secciones/footer_LI.php');
		}
		else{?>
			<tr class="tr-last">
				<td class="td-last" colspan="5"><center><?=$lang->message->sin_resultados?></center></td>
			</tr>
		<?php }?>
	</tbody>
</table>