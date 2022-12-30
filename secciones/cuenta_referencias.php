<input name="hidZoom" id="hidZoom" type="hidden" value="12" />
<input name="hidNombreDeteccionCircular" id="hidNombreDeteccionCircular" type="hidden" value="Radio de Ingreso" />
<input name="hidNombreDeteccionOtros" id="hidNombreDeteccionOtros" type="hidden" value="Desvio M&aacute;ximo" />
<?php
switch($operacion){
    case 'alta':
	case 'modificar':
			
		$lt = isset($_REQUEST["lt"])?$_REQUEST["lt"]:$_SESSION['lat'];
		$lg = isset($_REQUEST["lg"])?$_REQUEST["lg"]:$_SESSION['lng'];
	
		switch($operacion){
			case 'alta':
				$strPuntos = $lt.', '.$lg.';';
				
				$idMovil = isset($_POST["idMovil"])  ? $_POST["idMovil"] : "";
				$arrEntidades[0]['re_radioIngreso'] = isset($_POST['cmbRadioIngreso']) ? $_POST['cmbRadioIngreso'] : 0;
				$arrEntidades[0]['re_radioEgreso'] = 500;//= isset($_POST['cmbRadioEgreso']) ? $_POST['cmbRadioEgreso'] : 0;
				$arrEntidades[0]['re_tr_id'] = isset($_POST['cmbTipoReferencia']) ? $_POST['cmbTipoReferencia'] : 0;
				if ($lt != 0 && is_numeric($lg)) {
					$_POST["hidPuntos"] = '(' . $lt . ', ' . $lg . ');';
					$arrEntidades[0]['re_tr_id'] = 1;
				}
			break;
			case 'modificar':
				for ($i = 0;$i < count($arrPuntos);$i++){
					$strPuntos .= "(".$arrPuntos[$i]["rc_latitud"].", ".$arrPuntos[$i]["rc_longitud"].");";	
				}
			break;
		}
			
		?>
    	<input name="hidPuntos" id="hidPuntos" type="hidden" value=";<?=isset($_POST["hidPuntos"]) ? $_POST["hidPuntos"] : $strPuntos?>" />
		<script language="javascript">
            var imgW = 24;
            var imgH = 35;
        </script>
        <?php
        $disabled = ($arrEntidades[0]['re_tr_id'] == 2)?'disabled':'';
        if (!($arrEntidades[0]['re_radioIngreso'])) $arrEntidades[0]['re_radioIngreso'] = 100;
        if(tienePerfil(array(27))){
            require_once "referencias/edit_referencia_forza.php";
        }
        else{
            require_once "referencias/edit_referencia_default.php";
        }
	break;
	case 'listarstock':
		?><div id="main" class="sinColIzq" style="margin-right:0px;"><div class="solapas gum clear"><div class="contenido flaps clear" style="height:100%"><?php
		require_once "forza/listar_stock.php";
		?></div></div></div><?php
	break;
	case 'listardetallestock':
		?><div id="main" class="sinColIzq" style="margin-right:0px;"><div class="solapas gum clear"><div class="contenido flaps clear" style="height:100%"><?php
		require_once "forza/listar_detalle_stock.php";
		?></div></div></div><?php
	break;
	default:?>
    <?php 
	$tipoBotonera = 'LI-NewItem-Export';
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
            <?php if(tienePerfil(27)){ ?>
                <td><span class="campo1"><?=$lang->system->nombre_referencia?> (<?=$lang->system->num_boca?>)</span></td>
                <td><span class="campo1"><?=$lang->system->direccion?></span></td>
                <td><span class="campo1">Stock</span></td>
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
					<?php if(!empty($item['stock_cliente'])){
						if($item['stock_cliente'] < 0){
							echo 'Transacción en curso';
						}
						else{?>
						<a title="Ver Stock" class="float_l" href="javascript:mostrarPopup('boot.php?c=cuenta&solapa=referencias&action=stock&idRef=<?=$item['re_id']?>',900,450)">
							<?=$item['stock_cliente']?>
                        </a>
					<?php }}?>	
				</td>
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
            $cantRegistros = count($arrEntidades);
			include('secciones/footer_LI.php');
		}
		else{?>
			<tr class="tr-last">
				<td class="td-last" colspan="5"><center><?=$lang->message->sin_resultados?></center></td>
			</tr>
		<?php }?>
        </tbody>
    </table>
	<?php break;
}?>