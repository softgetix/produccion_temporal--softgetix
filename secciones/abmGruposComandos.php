<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>
<div id="main">
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
        <h1><?=$lang->system->abm_comandos_grupos?></h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch ($operacion){
			case 'listar':
			require_once 'includes/botoneraABMs.php';
		?>
    </div><!-- fin.  mainBoxLICabezera --> 
		<div id="mainBoxLI">
			<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
				<tr class="titulo">
					<td width="4%">&nbsp;</td>
					<td width="40%" align="center"><?=$lang->system->nombre?></td>
					<td width="56%" align="center"><?=$lang->system->comando?></td>
				</tr>
                <?php $filtros = array();
					if($arrEntidades){
						for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
							$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
							if (!in_array($arrEntidades[$i]['gr_nombre'],$filtros)) $filtros[] = $arrEntidades[$i]['gr_nombre'];?>
							<tr class="<?=$class?>">
								<td align="center">
                                	<input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['gr_id']?>" value="<?=$arrEntidades[$i]['gr_id']?>"/>
                                </td>
								<td align="center" class="nombre">
                                	<a style="text-decoration:underline" href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['gr_id']?>)"><?=$arrEntidades[$i]['gr_nombre']?></a>
                                </td>
								<td align="center">
									<?php if (strlen($arrEntidades[$i]['comandos'])>100) {
										echo "<span style='cursor:help;' title='".$arrEntidades[$i]['comandos']."'>".substr($arrEntidades[$i]['comandos'], 0, 100)."...</span>";
									}
									else{echo $arrEntidades[$i]['comandos'];}
									?>
								</td>
							</tr>
						<?php }
					}
					else{?>
					   	<tr class="filaPar">
					    	<td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
					    </tr>
					<?php }?>
			</table>
		</div><!-- fin. #mainBoxLI -->
		<?php
	   	break;
		case 'alta':
		case 'modificar':
			require_once 'includes/botoneraABMs.php';
		?>
	</div><!-- fin.  mainBoxLICabezera --> 
    <hr/>
		<div id="mainBoxAM">
			<input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
			<input name="hidSerializado" id="hidSerializado" type="hidden" value="<?=@$serializado?>" />
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" />
			<fieldset>
				<legend><?=$lang->system->general?></legend><br />
				<table width="100%">
					<tr>
						<td class="td_label">&nbsp;&nbsp;&nbsp;<label for="txtNombre"><?=$lang->system->nombre?></label>&nbsp;&nbsp;&nbsp;<input type="text" name="txtNombre" id="txtNombre" maxlength="50" value="<?=@$arrEntidades[0]['gr_nombre']?>" /> *</td>
						<td class="td_campo" colspan="2">&nbsp;</td>
					</tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr>
						<td class="td_label" style="text-align:center">
                        	<label for="lstDisponibles"><?=$lang->system->comandos_disponibles?></label>
							<select id="lstDisponibles" multiple="multiple" size="10" style="width:570px;">
<?php								foreach ($arrBox1 as $comando){?>
								<option value="<?php echo $comando['co_id']?>"><?=decode($comando['co_nombre'].' - '.$comando['co_codigo'])?></option>
<?								}?>
							</select>
						</td>
						<td id="td_pasaje" valign="middle">
							<br><br><br><br>
							<button type="button" id="btnDerT">&gt;&gt;</button>
							<button type="button" id="btnDer">&gt;</button>
							<button type="button" id="btnIzq">&lt;</button>
							<button type="button" id="btnIzqT">&lt;&lt;</button>
						</td>
						<td class="td_campo" style="text-align:center !important">
                        	<label for="lstAsignados"><?=$lang->system->comandos_asignados?></label>
							<select id="lstAsignados" multiple="multiple" size="10" style="width:570px;">
								<?php if(isset($arrBox2)){
								foreach ($arrBox2 as $comando){?>
									<option value="<?=$comando['co_id']?>"><?=decode($comando['co_nombre'].' - '.$comando['co_codigo'])?></option>
								<?php }
								}?>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
		</div><!-- fin. #mainBoxAM -->
		<?php
		break;
	}?>
	</form>
</div>