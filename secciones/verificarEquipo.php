<div id="nomenclado" style="position:absolute; left:0px;display:<?php if(isset($_GET['popup'])) echo "none"; else echo "block"?>;width:240px;background-color:#eaeaea">
	<h1>&Uacute;ltimo reporte</h1>
	<div style="padding-left:10px;">
		<p><span class="title">Evento: </span><span id="nom_evento">&nbsp;</span></p>
		<p><span class="title">Fecha generaci&oacute;n: </span><span id="nom_fecha">&nbsp;</span></p>
		<p><span class="title">Fecha recepci&oacute;n: </span><span id="nom_fecha_r">&nbsp;</span></p>
		<p><span class="title">Ubicaci&oacute;n equipo: </span><span id="nom_nomenclado">&nbsp;</span></p>
		<p style="display:none"><span class="title">DNI: </span><span id="nom_dni">&nbsp;</span></p>
		<p><span class="title">Lat/Lon: </span><span id="nom_latlon">&nbsp;</span></p>
		<p><span class="title">Rumbo: </span><span id="nom_rumbo">&nbsp;</span></p>
		<p><span class="title"  style="display:none">Velocidad: </span><span id="nom_velgps">&nbsp;</span></p>
		<p><span class="title">Odometro: </span><span id="nom_odometro">&nbsp;</span></p>
		<p><span class="title">Velocidad: </span><span id="nom_velocidad">&nbsp;</span></p>
		<p><span class="title"  style="display:none">Gasoil consumido: </span><span id="nom_gasoil">&nbsp;</span></p>
		<p><span class="title">Entradas: </span></p>
		<div>
            <ul>
                <li>Entrada 1 <span id="nom_entrada0" class="entrada">&nbsp;</span></li>
                <li>Entrada 2 <span id="nom_entrada1" class="entrada">&nbsp;</span></li>
                <li>Entrada 3 <span id="nom_entrada2" class="entrada">&nbsp;</span></li>
                <li>Entrada 4 <span id="nom_entrada3" class="entrada">&nbsp;</span></li>
                <li>Entrada 5 <span id="nom_entrada4" class="entrada">&nbsp;</span></li>
                <li>Entrada 6 <span id="nom_entrada5" class="entrada">&nbsp;</span></li>
                <li>Entrada 7 <span id="nom_entrada6" class="entrada">&nbsp;</span></li>
                <li>Entrada 8 <span id="nom_entrada7" class="entrada">&nbsp;</span></li>
            </ul>
        </div>
	</div>
</div>    
<div id="main" >
	<form name="frm_<?=$seccion;?>" id="frm_<?=$seccion;?>" action="?c=<?=$seccion;?>" method="post">
	<div class="mainBoxLICabecera" >
		<h1>Verificaci&oacute;n de Equipos</h1>
	</div><!-- fin. mainBoxLICabecera -->		
    <div id="mainBoxAM">
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<fieldset style="padding: 10px;">
			<legend><?=$lang->system->general?></legend>
            <table width="auto">
				<tr>
					<td width="160" height="24"><label for="txtEquipo"><?=$lang->system->equipo?></label></td>
					<td class="td_campo">
						<?php if (count($arrEquipos)===1){?>
                        	<input type="text" value="<?=$arrEquipos[0]['un_mostrarComo'] . ((strlen($arrEquipos[0]['mo_identificador']) > 5) ? ' '.$arrEquipos[0]['mo_identificador'] : '');?>" disabled />
                            <input type="hidden" name="txtEquipo" id="txtEquipo"  value="<?=$arrEquipos[0]['un_id'];?>"/>
                            <script type="text/javascript">
                            	actualizarNomenclado( $("#txtEquipo").val() );
                            </script>
						<?php }
						else{?>
                            <select name="txtEquipo" id="txtEquipo" class="combobox">
                            	<option value="0"></option>
    							<?php foreach($arrEquipos as $equipo){ ?>
                                	<option value="<?=$equipo['un_id'];?>">
										<?=$equipo['un_mostrarComo'].((strlen($equipo['mo_identificador']) > 5)?' '.$equipo['mo_identificador'] :'')?>
                                        </option>
    							<?php } ?>
							</select>
						<?php }?>
					</td>
				</tr>
				<tr>
					<td class="td_label" height="24"><label for="cmbGrupos">Grupo de Comandos</label></td>
					<td class="td_campo">
						<select name="cmbGrupos" id="cmbGrupos" style="width: 304px" >
							<option value="0"><?=$lang->system->seleccione?></option>
							<?php foreach($arrGruposComandos as $arrFila){ ?>
                            	<option value="<?=$arrFila['gr_id']?>"><?=$arrFila['gr_nombre'];?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_label" style="vertical-align:top"><label for="cmbGrupos">Comandos</label></td>
					<td class="td_campo"><textarea name="txtArchivo" id="txtArchivo" ></textarea></td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td class="td_campo">
						<input type="button" name="btnArchivo" id="btnArchivo" value="Agregar" width="50px" style="margin-top:10px;"/>
					</td>
				</tr>
                <tr>
                	<td height="50" colspan="2">&nbsp;</td>
                </tr>
                <tr>
                <td>
                	<a href="javascript:ejecutarAllComandos()" id="ejecutarAllComandos" style="display:none">Ejecutar Comandos</a>
                </td>
				<td class="td_label" align="center" style="width:310px">
					<table>
						<tbody id="tbl_pruebas">
							<tr><td colspan="3">&nbsp;</td></tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	    <div id="botonesABM">
			<span style="cursor:default;color:gray">
            	<img src="imagenes/botonGuardarGrayed.png" alt="" />
				Equipo Verificado
            </span>
		</div><!-- fin. botonesABM -->
        </fieldset>
    </div><!-- fin. botonesABM -->
	</form>
</div>
