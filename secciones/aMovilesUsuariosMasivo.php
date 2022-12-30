<?php if (!$popup) { ?>
	<div id="colIzq">
		<?php require_once('includes/datosColIzqAbm.php')?>
	</div> 
<?php }?>
<div id="main" <?php if (isset($popup)) { if ($popup>0) { $botonera_old = "_old"; ?> class="sinColIzq" <?php } } ?>>
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
    <div class="mainBoxLICabecera" style="height:100%">
		<h1><?=$lang->system->abm_asignacion_moviles_usuarios?></h1>
		<div class="esp">
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch ($operacion){
		case 'alta':
		case 'modificar':
			if ($popup) {
				require_once 'includes/botoneraABMs.php';
			}
	?>
	</div><!-- fin. mainBoxLICabecera --> 
	<hr/>
		<script>
			var grupos = new Array();
			<?php $i = 0;
			foreach ($Grupos as $gid => $grupo) {
				$moviles = array();
				foreach ($grupo as $mid => $nombre) {
					$moviles[] = $mid;
				}
				echo "grupos[".$gid."] = [".implode(",", $moviles)."];\n";
				$i++;
			}?>
			function marcarGrupo(gid) {
				var select = document.getElementById('lstMoviles');
				for(var i=select.options.length-1;i>=0;i--){select.remove(i);}
				var select2 = document.getElementById('lstMovilesFull');
				
				for (var i = 0; i < select2.length; i++){
					if (gid == 0){
						var optn = document.createElement("OPTION");
						optn.text = select2.options[i].text;
						optn.value = select2.options[i].value;
						select.options.add(optn);
					} 
					else{
						for (var j = 0; j < grupos[gid].length; j++) {
							if (select2.options[i].value == grupos[gid][j]) {
								var optn = document.createElement("OPTION");
								optn.text = select2.options[i].text;
								optn.value = select2.options[i].value;
								select.options.add(optn);
								select.options[select.options.length-1].selected = true;
							}
						}
					}
				}
			}		
		</script>
		<div id="mainBoxAM">
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=@$mensaje;?>" />
			<input name="hidId" id="hidId" type="hidden" value="<?=@$id;?>" />
			<input name="ag" type="hidden" value="<?=@$_GET['ag']?>"/>
			<fieldset>
				<legend><?=$lang->system->moviles?></legend>
				<table width="100%">
					<tbody>
						<tr>
							<td class="td_label"  height="20" align="right">
                            	<label for="txtFiltroMoviles"><?=$lang->system->filtro?></label>&nbsp;&nbsp;
                            </td>
							<td class="td_campo">
                            	<input type="text" id="txtFiltroMoviles" class="txtFiltroTransfer" maxlength="50"/>
                            </td>
						</tr>
						<tr>
							<td class="td_label"  height="20" align="right">
                            	<label for="txtGrupos"><?=$lang->system->grupo?></label>&nbsp;&nbsp;
                            </td>
							<td class="td_campo">
								<select id="txtGrupos" class="txtGrupos" onchange="marcarGrupo(this.value);">
									<option value="0"> - <?=$lang->system->todos?> - </option>
									<?php if (isset($arrGrupos2)) { ?>
										<?php foreach ($arrGrupos2 as $id => $nombre): ?>
										<option <?php if ($idGrupo == $id):?>selected="selected"<?php endif; ?> value="<?php echo $id; ?>"><?php echo $nombre; ?></option>
										<?php endforeach; ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="td_transfer_box">
								<table>
								<tbody>
									<tr>
										<td class="td_campo">
                                        	<label for="lstMoviles" style="display:block;margin:20px 0 5px 0;"><?=$lang->system->moviles_disponibles?></label>
											<select id="lstMoviles" multiple="multiple" size="11" class="lstIzq">
												<?php foreach ($arrMoviles as $fila){?>
													<option value="<?=$fila['id']?>"><?=$fila['dato']?></option>
												<?php }?>
											</select>
											<select class="hidden" id="lstMovilesFull" multiple="multiple" size="5" class="lstIzq">
												<?php foreach ($arrMoviles as $fila){?>
													<option value="<?=$fila['id']?>"><?=$fila['dato']?></option>
												<?php }?>
											</select>
										</td>
										<td class="td_pasaje" style="vertical-align: middle">
											<button type="button" class="btnDerT">&gt;&gt;</button>
											<button type="button" class="btnDer">&gt;</button>
											<button type="button" class="btnIzq">&lt;</button>
											<button type="button" class="btnIzqT">&lt;&lt;</button>
										</td>
										<td class="td_campo">
                                        	<label for="lstMovilesAsig" style="display:block;margin:20px 0 5px 0;"><?=$lang->system->moviles_asignados?></label>
											<select id="lstMovilesAsig" multiple="multiple" size="11" class="lstDer">
												<?php if(isset($arrMovilesAsig)){
												foreach ($arrMovilesAsig as $fila){?>
													<option value="<?=$fila['mo_id']?>"><?=$fila['mo_matricula']?></option>
												<?php }}?>
											</select>
										</td>
									</tr>
								</tbody>
								</table>
							<div><input type="hidden" id="hid_lstMovilesAsig" name="hid_lstMovilesAsig" value=""/></div>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
            <br />
			<fieldset>
				<legend><?=$lang->system->usuarios?></legend>
				<table width="100%">
					<tbody>
						<tr>
							<td class="td_label"  height="20" align="right">
                            	<label for="txtFiltroUsuarios"><?=$lang->system->filtro?></label>&nbsp;&nbsp;
                            </td>
							<td class="td_campo">
                            	<input type="text" id="txtFiltroUsuarios" class="txtFiltroTransfer" maxlength="50"/>
                            </td>
						</tr>
						<tr>
							<td colspan="2" class="td_transfer_box">
								<table>
								<tbody>
									<tr>
										<td class="td_campo" >
                                        	<label for="lstUsuarios" style="display:block;margin:20px 0 5px 0;"><?=$lang->system->usuarios_disponibles?></label>
											<select id="lstUsuarios" multiple="multiple" size="11" class="lstIzq">
												<?php foreach ($arrUsuarios as $fila){?>
													<option value="<?=$fila['us_id']?>"><?=$fila['us_nombreUsuario']." - ".$fila['cl_razonSocial']?></option>
												<?php }?>
											</select>
										</td>
										<td class="td_pasaje" style="vertical-align: middle">
											<button type="button" class="btnDerT">&gt;&gt;</button>
											<button type="button" class="btnDer">&gt;</button>
											<button type="button" class="btnIzq">&lt;</button>
											<button type="button" class="btnIzqT">&lt;&lt;</button>
										</td>
										<td class="td_campo">
                                        	<label for="lstUsuariosAsig" style="display:block;margin:20px 0 5px 0;"><?=$lang->system->usuarios_asignados?></label>
											<select id="lstUsuariosAsig" multiple="multiple" size="11" class="lstDer">
												<?php if (isset($arrUsuariosAsig)){
													foreach ($arrUsuariosAsig as $fila){?>
														<option value="<?=$fila['us_id']?>">
															<?=$fila['us_nombreUsuario']. " - ".$fila['cl_razonSocial']; ?>
                                                        </option>
												<?php }}?>
											</select>
										</td>
									</tr>
								</tbody>
								</table>
								<div>
									<input type="hidden" id="hid_lstUsuariosAsig" name="hid_lstUsuariosAsig" value=""/>
								</div>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend><?=$lang->system->grupo?></legend>
					<table width="100%">
						<tbody>
                        	<tr>
                            	<td class="td_label" align="right">
                                	<label for="txtFiltroUsuarios"><?=$lang->system->crear_grupo?></label>&nbsp;&nbsp;
                                </td>
                                <td class="td_campo">
                                	<input type="text" name="txtNombreGrupo" id="txtNombreGrupo" value="<?=$Grupo['gm_nombre']; ?>" disabled="disabled" maxlength="50"/>
									<input type="checkbox" onclick="document.getElementById('txtNombreGrupo').disabled=!this.checked;" id="chkCrearNuevoGrupo" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>		
			</div><!-- fin. #mainBoxAM-->
			<script>
				marcarGrupo(<?=$idGrupo?>);
			</script>
		<?php break;
		}?>
	</form>
</div>
