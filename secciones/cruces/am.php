<div id="botoneraABM">
    <div id="botonesABM">
        <span id="botonVolver" onclick="enviar('volver');"> <img src="imagenes/botonVolver.png" alt="" /> <?=$lang->botonera->volver?> </span>
    </div>
</div>

<div id="mainBoxAM">
    <input name="hidMensaje" id="hidMensaje" type="hidden" value="<?php echo $mensaje; ?>" />
    <input name="hidId" id="hidId" type="hidden" value="<?php echo $id; ?>" />
    <fieldset id="fieldA">
        <legend><?=$lang->system->general?></legend>
        <div class="fld_wrapper" id="fieldA">
            <table class="widefat">
                <tbody>
                    <tr>
                    <input type="hidden" name="idEmpresa" id="idEmpresa" value="<?php echo $_SESSION['idEmpresa'] ?>" />
                    <input type="hidden" name="popup_ready" id="popup_ready" value="<?php echo (isset($popup) && $popup) ? "1" : '0'; ?>" />
                    <input type="hidden" name="vi_id" id="vi_id" value="<?php echo ($operacion == 'modificar') ? $arrEntidades[0]['vi_id'] : 0; ?>" />
                    <td class="td_label"><label for="txtCodigo">C&oacute;digo</label></td>
                    <td class="td_campo"><input type="text" id="txtCodigo" name="txtCodigo" value="<?php echo @$arrEntidades[0]['vi_codigo']; ?>" maxlength="60"/> *</td>
                    
                    <?php if (count($arrUsuarios) > 1) { ?>
                    <td class="td_label"><label for="cmbUsuario">Usuario</label></td>
                    <td class="td_campo">
                        <select name="cmbUsuario" id="cmbUsuario" class="combobox">
                            <option value="0"></option>
                            <?php
                            if (isset($arrUsuarios) && $arrUsuarios) {
                                foreach ($arrUsuarios as $arrFila) {
                                    $selected = '';
                                    if (isset($arrEntidades)) {
                                        if ($arrFila['us_id'] == @$arrEntidades[0]['vi_us_id']) {
                                            $selected = ' selected="selected"';
                                        }
                                    }
                                    ?>
                                    <option value="<?php echo $arrFila['us_id'] ?>"<?php echo $selected; ?>><?php echo $arrFila['us_nombreUsuario']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select> *
                    </td>
                    <?php } else { //else, el controlador pone el usuario actual porque cmbUsuario no esta definido   ?>
                    <!-- <td colspan="2">&nbsp;</td>-->
                    <?php } ?>
                </tr>
                <tr>
                    <td class="td_label"><label for="cmbMovil">M&oacute;vil</label></td>
                    <td class="td_campo">
                            <?php if (count($arrMoviles) > 0): ?>
                            <select name="cmbMovil" id="cmbMovil" class="combobox clearOnError">
                                <option value="0"></option>
                                <?php
                                if (isset($arrMoviles) && $arrMoviles) {
                                    foreach ($arrMoviles as $arrFila) {
                                        $selected = '';
                                        if (isset($arrEntidades)) {
                                            if ($arrFila['id'] == @$arrEntidades[0]['vi_mo_id']) {
                                                $selected = ' selected="selected"';
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo $arrFila['id'] ?>"<?php echo $selected; ?>><?php echo $arrFila['dato']; if (!empty($arrFila['otros'])) echo " (".$arrFila['otros'].")"; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                    <?php else: ?> 
                            <!-- //uno solo, el controlador que cargue cual es -->
                            <input type="text" value="<?php //echo $arrMoviles[0]['dato']; ?>" disabled="disabled" />
                    <?php endif; ?>
                    </td>
                    <?php if ($arrConductores) { 
                        if (isset($arrConductores) && $arrConductores) {
                            foreach ($arrConductores as $arrFila) {
                                $selected = '';
                                if (isset($arrEntidades))
                                    if ($arrFila['co_id'] == @$arrEntidades[0]['vi_co_id'])
                                        $selected = ' selected="selected"';
                                ?>
                                                <option value="<?php echo $arrFila['co_id'] ?>"<?php echo $selected; ?>><?php echo $arrFila['co_nombre']; ?></option>
                            <?php
                        }
                    }
                    ?>
                                </select>
                        </td-->
                    <input type="hidden" name="cmbConductor" id="cmbConductor" value="0" />
                    <!--
                    <td class="td_label">&nbsp;</td>
                    <td class="td_campo">&nbsp;</td>-->
                    <?php } else { ?>
                    <input type="hidden" name="cmbConductor" id="cmbConductor" value="0" />
                    <!--
                    <td class="td_label">&nbsp;</td>
                    <td class="td_campo">&nbsp;</td>
                    -->
                    <?php } ?>
                </tr>
                <tr>
                    <td class="td_label">
                        <label for="txtObservaciones">Observaciones</label><br/><br/></td>
                    <td class="td_campo"><textarea id="txtObservaciones" name="txtObservaciones" cols="10" rows="5"><?php echo @$arrEntidades[0]['vi_observaciones']; ?></textarea></td>
                </tr>
                </tbody>
            </table>
            <div class="buttonera">
                <button type="button" disabled="disabled">Anterior</button><button type="button" class="btn_sig">Siguiente</button>
            </div>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Ubicaciones</legend>
        <div class="fld_wrapper hidden" id="fieldB">
            <table class="widefat">
                <tbody>
                    <tr>
                        <td class="td_label"><label for="cmbUbicacion">Ubicaci&oacute;n</label></td>
                        <td class="td_campo">
                            <select id="cmbUbicacion" class="combobox clearOnError">
                                <option value="0"></option>
                                <?php if (isset($arrUbicaciones) && $arrUbicaciones) {
                                foreach ($arrUbicaciones as $arrFila) { 
                                ?>
                                <option value="<?php echo $arrFila['re_id'] ?>"><?php echo $arrFila['re_nombre'] . ' / ' . $arrFila['re_descripcion']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <?php if (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] != 'popup')) { ?>
                                <!--img id="btnNewRef" src="imagenes/blue.png" alt="" /-->
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_label"></td>
                        <td class="td_campo">
							<label for="chkHorario">Horario</label> <input type="checkbox" id="chkHorario" value="1"/>
                            <div id="divHorarios">
                                <table class="widefat">
									<tr>
										<td>Empieza</td>
										<td><input type="text" id="txtDiaEmpieza" class="txtCal" value="<?php echo date("d/m/Y"); ?>" /> <select id="cmbHoraEmpieza"><?php echo $optionsHora; ?></select> <select id="cmbMinutEmpieza"><?php echo $optionsMinutos; ?></select></td>
									</tr>
									<tr>
										<td>T&eacute;rmina</td>
										<td><input type="text" id="txtDiaTermina" class="txtCal" value="<?php echo date("d/m/Y"); ?>" /> <select id="cmbHoraTermina"><?php echo $optionsHora; ?></select> <select id="cmbMinutTermina"><?php echo $optionsMinutos; ?></select></td>
									</tr>
                                </table>
                                
                                
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="button" id="btnAgregar">Agregar</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th>Ubicaci&oacute;n</th>
                                        <th>Empieza</th>
                                        <th>T&eacute;rmina</th>
                                        <th>Borrar</th>
                                    </tr>
                                </thead>
                                <tbody id="tblUbicaciones">
                                <?php
                                if (isset($arrDestinos)) {
                                    for ($i = 0; $i < count($arrDestinos); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $arrDestinos[$i]['re_nombre']; ?>
                                                    <input type="hidden" value="<?php echo @$arrDestinos[$i]['vd_re_id']; ?>" name="ref_id[]"><input type="hidden" value="<?php if (@$arrDestinos[$i]['vd_ini_real'] == NULL AND @$arrDestinos[$i]['vd_fin_real'] == NULL) {
                                        echo "1";
                                    } else {
                                        echo "3";
                                    } ?>" name="es_antiguo[]">
                                                </td>
                                                <td><?php if (@$arrDestinos[$i]['vd_ini'] != NULL and @$arrDestinos[$i]['vd_ini'] != '---') echo date('d/m/Y H:i', strtotime(@$arrDestinos[$i]['vd_ini'])); else echo ' --- '; ?><input type="hidden" value="<?php if (@$arrDestinos[$i]['vd_ini'] != NULL and @$arrDestinos[$i]['vd_ini'] != '---') echo date('d/m/Y H:i', strtotime(@$arrDestinos[$i]['vd_ini'])); else echo ' --- '; ?>" name="ini[]"> 
                                                </td>
                                                <td><?php if (@$arrDestinos[$i]['vd_fin'] != NULL and @$arrDestinos[$i]['vd_fin'] != '---') echo date('d/m/Y H:i', strtotime(@$arrDestinos[$i]['vd_fin'])); else echo ' --- '; ?><input type="hidden" value="<?php if (@$arrDestinos[$i]['vd_fin'] != NULL and @$arrDestinos[$i]['vd_fin'] != '---') echo date('d/m/Y H:i', strtotime(@$arrDestinos[$i]['vd_fin'])); else echo ' --- '; ?>" name="fin[]">
                                                </td>
                                                        <td><?php if (@$arrDestinos[$i]['vd_ini_real'] == NULL AND @$arrDestinos[$i]['vd_fin_real'] == NULL) { ?><img alt="" class="btnDel" src="imagenes/cruz.png"> <!--<img alt="" class="btnUp" src="imagenes/mover_u.png"> <img alt="" class="btnDown" src="imagenes/mover_d.png">--> <?php } ?>
                                                </td>
                                            </tr>
        <?php
    }
}
?>
                                </tbody>
                            </table>

                        </td>
                    </tr>
                </tbody>
            </table>

<?php if ($operacion != 'alta' || 1) { ?>
    <!--<input type="checkbox" name="rep_mod" id="rep_mod" value="1"> Repetir Modificacion a Viajes Futuros.-->
                <div class="buttonera">
                    <button type="button" class="btn_ant">Anterior</button>
                    <!--<button type="button" disabled="disabled">Siguiente</button>-->
                    <button type="button" class="btn_fin">Finalizar</button>
                </div>
<?php } else { ?>
                <div class="buttonera">
                    <button type="button" class="btn_ant">Anterior</button>
                    <button type="button" class="btn_sig">Siguiente</button>
                </div>
<?php } ?>

        </div>
    </fieldset>


<?php if ($operacion == 'alta' && 0) { ?>
        <fieldset>
            <legend>Repetir</legend>
            <div class="fld_wrapper hidden">
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td class="td_label">
                                <ul>
                                    <li><label>Lunes <input type="checkbox" name="chkRepLunes" <?php if (in_array(1, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_lunes'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>Martes <input type="checkbox" name="chkRepMartes" <?php if (in_array(2, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_martes'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>Mi&eacute;rcoles <input type="checkbox" name="chkRepMiercoles" <?php if (in_array(3, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_miercoles'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>Jueves <input type="checkbox" name="chkRepJueves" <?php if (in_array(4, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_jueves'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>Viernes <input type="checkbox" name="chkRepViernes" <?php if (in_array(5, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_viernes'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>S&aacute;bado <input type="checkbox" name="chkRepSabado" <?php if (in_array(6, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_sabado'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                    <li><label>Domingo <input type="checkbox" name="chkRepDomingo" <?php if (in_array(7, $arrRepeticiones) or @$arrEntidades[0]['vi_rep_domingo'] == 1) echo 'checked'; ?> value="1" /></label></li>
                                </ul>
                            </td>
                            <td class="td_campo">
                                    <p>Empieza <input type="text" name="txtRepEmpieza" id="txtRepEmpieza" class="txtCal" maxlength="8" value="<?php if ($operacion == 'alta') echo @$_POST['txtRepEmpieza']; ?>" /> <!-- <img src="imagenes/calendario.png" alt="" /> --></p>
                                    <p>T&eacute;rmina <input type="text" name="txtRepTermina" id="txtRepTermina" class="txtCal" maxlength="8" value="<?php if ($operacion == 'alta') echo @$_POST['txtRepTermina']; ?>"/> <!-- <img src="imagenes/calendario.png" alt="" /> --></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="buttonera">

                    <button type="button" class="btn_ant">Anterior</button>
                    <!--<button type="button" disabled="disabled">Siguiente</button>-->							
                    <button type="button" class="btn_fin">Finalizar</button>
                </div>
            </div>
        </fieldset>
	<?php } ?>


</div>
