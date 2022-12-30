<?php 
if(empty($_REQUEST['txtFiltro']) && !$_REQUEST['viewAll'] && $cantRegistros > 29){?>
	<tr class="filaPar tr-last">
		<td class="td-last" colspan="<?=$colspan?$colspan:7?>" align="center" style="font-size:11px; height:30px; line-height:30px;font-weight:bolder !important;">
			<center><?=idiomaHTML(str_replace('[LINK]',($_SERVER['REQUEST_URI'].'&viewAll=1'),$lang->message->interfaz_generica->msj_limite_registros))?></center>
		</td>
	</tr>
<?php }?>				
				