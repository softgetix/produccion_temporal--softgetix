<? require_once 'includes/botoneraABMs.php';?>
<style>.ref{color:#F00; font-size:11px;}</style>
	<div id="mainBoxLI">
		<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
			<tr class="titulo">
				<td width="4%"></td>
				<td width="22%" align="left"><?=$lang->system->nombre?></td>
				<? if(!tienePerfil(16)){?>
					<td width="20%" align="left"><?=$lang->system->usuario_creador?></td>
					<td width="18%" align="center"><?=$lang->system->alerta_por_geocercas?></td>
					<td width="18%" align="center"><?=$lang->system->alerta_por_eventos?></td>					
					<td width="18%" align="center"><?=$lang->system->requiere_confirmacion?></td>
				<? } else{?>
					<td width="74%" align="left"><?=$lang->system->descripcion?></td>
				<? }?>
			</tr>
            <? $filtros = array();
				if($arrEntidades){
					$arrSiNo[0] = $lang->system->no;
					$arrSiNo[1] = $lang->system->si;
					for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
						$pos = ($i % 2 == 0)? 'r' : 'b';
						if (!in_array($arrEntidades[$i]['al_nombre'],$filtros)) $filtros[] = $arrEntidades[$i]['al_nombre'];
						?>
					<tr class="<?=$class?>">
						<? if($arrEntidades[$i]['accion']){?>
                        <td >
                        	<input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['al_id']?>" value="<?=$arrEntidades[$i]['al_id']?>"/>
                        </td>
						<td align="left" class="nombre">
                        	<a style="text-decoration:underline" href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['al_id']?>)" >
								<?=$arrEntidades[$i]['al_nombre']?>
                            </a>
                        </td>
                        <? }
						 else{?>
							<td>&nbsp;</td>
							<td align="left" class="nombre"><?=$arrEntidades[$i]['al_nombre']?>  <span class="ref" title="Creado por <?=$arrEntidades[$i]['usuario']?>">**</span></td>
						<? } ?>
						<? if(!tienePerfil(16)){?>
                        <td align="left"><?=$arrEntidades[$i]['usuario']?></td>
						<td align="center"><?=$arrSiNo[$arrEntidades[$i]['al_referencia']]?></td>
						<td align="center"><?=$arrSiNo[$arrEntidades[$i]['al_evento']]?></td>						
						<td align="center"><?=$arrSiNo[$arrEntidades[$i]['al_confirmacion']]?></td>
						<? } else{?>
						<td align="left"><?=$alertas->traduccionDescripcionAlerta($arrEntidades[$i]['al_id'])?></td>
						<? }?>
					</tr>
					<? }
						include('secciones/footer_LI.php');
					}
					else{?>
					   	<tr class="filaPar">
					         <td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
					    </tr>
					<? }?>
        </table>
	 </div> <!-- fin. #mainBoxLI -->
     <? if(!tienePerfil(16)){?>
		<span class="ref" style="line-height:20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**  <?=$lang->system->alertas_creadas_1?>.</span>
        <br /><span style="font-size:11px; line-height:20px; color:#666;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang->system->alertas_creadas_2?>.</span>
    <? }?>
	<br /><br />

