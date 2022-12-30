<!--
<script type='text/javascript' src='js/boxes.js'></script> 
-->
<?php
function selectHorarioDesde($name, $id = null, $default = false) {
    return selectHorario($name, 'desde', $id, $default);
}

function selectHorarioHasta($name, $id = null, $default = false) {
    return selectHorario($name, 'hasta', $id, $default);
}

function selectHorario($name, $tipo, $id = null, $default = false) {
    if ($id == null) {
        $id = $name;
    }
    
    if ($default !== false) {
        $mins_default = substr($default, -2);
        $hora_default = substr($default, 0, -2);
        if (strlen($hora_default) == 1) {
            $hora_default = "0".$hora_default;
        }
    }
    
    $html = "<select id='{$id}' name='{$name}'>";
    for ($i = 0; $i < 24; $i++)
    {
        if ($i < 10) $i = "0" . $i; 
        if ($tipo == 'desde' && $i == 24) {
            
        } else {
            $html .= "<option value='{$i}:00'";
            if ($hora_default == $i && $mins_default == "00") {
                $html .= " selected";
            }
            $html .= ">{$i}:00</option>";
        }
        
        $html .= "<option value='{$i}:30'";
        if ($hora_default == $i && $mins_default == "30") {
            $html .= " selected";
        }                
        $html .= ">{$i}:30</option>";
        

        if ($i == 23 && $tipo == 'hasta') {
                $html .= "<option value='{$i}:59'";
                if ($hora_default == 23 && $mins_default == "59") {
                    $html .= " selected";
                }
                $html .= ">{$i}:59</option>";            
        }        
    }
    $html .= "</select>";
    return $html;
}
?>
<?php /*if ($operacion=="modificarAsignacion") { ?>
  <div id="colIzq">
  <?php require_once('includes/datosColIzqAbm.php')?>
  </div> 
<?php } */?>
<div id="main" class="sinColIzq" >
	<form name="frm_<?= $seccion ?>" id="frm_<?= $seccion ?>" action="?c=<?= $seccion ?>" method="POST">
    <div class="mainBoxLICabezera">
        <h1><?=$lang->botonera->configurar_horarios?></h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidId" id="hidId" type="hidden" value="<?php if (isset($id)) { echo $id; } ?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?= $seccion; ?>" />
        <input name="hidEquiposSerializados" id="hidEquiposSerializados" type="hidden" />
        <input name="hidIdEquipoPrimario" id="hidIdEquipoPrimario" type="hidden" />
        <?php
        switch ($operacion) {
        	/*case 'listar':
				$tituloFiltroBuscador = $arrPalabras["movil"];
                require_once 'includes/botoneraABMs.php';
        ?>
	</div><!-- fin. mainBoxLICabezera -->
    	<div id="mainBoxLI">
			<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
				<tr class="titulo">
					<td width="5%">&nbsp;</td>
					<td width="15%" align="center"><?= $arrPalabras["movil"] ?></td>
					<td width="15%" align="center"><?= $arrPalabras["matricula"] ?></td>
					<td width="15%" align="center"><?= $arrPalabras["cliente"] ?></td>
					<td width="15%" align="center"><?= $arrPalabras["distribuidor"] ?></td>
					<td width="15%" align="center"><?= $arrPalabras["principal"] ?></td>
				</tr>
                <?php $filtros = array();
					if($arrEntidades){
						for ($i = 0; $i < count($arrEntidades) && $arrEntidades; $i++) {
							$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
							if (!in_array($arrEntidades[$i]['movil'], $filtros))
								$filtros[] = $arrEntidades[$i]['movil'];
							if (!in_array($arrEntidades[$i]['mo_matricula'], $filtros))
								$filtros[] = $arrEntidades[$i]['mo_matricula'];
							if (!in_array($arrEntidades[$i]['cl_razonSocial'], $filtros))
								$filtros[] = $arrEntidades[$i]['cl_razonSocial'];
							if (!in_array($arrEntidades[$i]['us_nombreUsuario'], $filtros))
								$filtros[] = $arrEntidades[$i]['us_nombreUsuario'];
							if (!in_array($arrEntidades[$i]['un_mostrarComo'], $filtros))
								$filtros[] = $arrEntidades[$i]['un_mostrarComo'];
							?>
							<tr class="<?=$class?>">
								<td>
                                	<input type="checkbox" name="chkId[]" id="chk_<?= $arrEntidades[$i]['mo_id'] ?>" value="<?= $arrEntidades[$i]['mo_id'] ?>"/>
                                </td>
								<td align="center">
                                	<a href="javascript: enviarModificacion('modificar',<?= $arrEntidades[$i]['mo_id'] ?>)">
										<?=$arrEntidades[$i]['movil'] ?>
                                    </a>
                                </td>
								<td align="center"><?= $arrEntidades[$i]['mo_matricula'] ?></td>
								<td align="center"><?= $arrEntidades[$i]['cl_razonSocial'] ?></td>
								<td align="center"><?= $arrEntidades[$i]['us_nombreUsuario'] ?></td>
								<td align="center"><?= $arrEntidades[$i]['un_mostrarComo'] ?></td>
							</tr>
						<?php }
							include('secciones/footer_LI.php');
						}
						else{?>
					   		<tr class="filaPar">
					        	<td colspan="6" align="center"><i><?=$arrPalabras["sin resultados"]?></i></td>
					      	</tr>
						<?php }?>
                </table>
			</div><!-- fin. mainBoxLI -->
        <?php
        break; */
    	case 'alta':
        	require_once 'includes/botoneraABMs.php';
        ?>			
	</div><!-- fin. mainBoxLICabezera -->        
    <hr />
        <div id="mainBoxAM">
	        <input name="hidMensaje" id="hidMensaje" type="hidden" value="<?= ($mensaje) ? $mensaje : ""; ?>" >
        </div><!-- fin. #mainBoxAM -->     
        <?php
        break;
    	/*case 'modificarAsignacion':
        	require_once 'includes/botoneraABMs.php';
        ?>			
	</div><!-- fin. mainBoxLICabezera -->        
    <hr />     
        <div id="mainBoxAM">
            <table width="100%">
                <tr><td><b><u><?=$lang->system->vehiculo.": " . $arrEntidades[0]["movil"] ?></u></b></td></tr>
            </table>
            <br />
            <fieldset>		
                <legend><?= $arrPalabras["equipos"] ?></legend>
                <br />
                <table style="margin-left:20px;">
                    <tr>
                        <td style="text-align:left !important;" height="20"><?= $arrPalabras["lista de equipos"] ?></td>
                        <td>&nbsp;</td>
                        <td style="text-align:left !important;"><?= $arrPalabras["equipos asignados"] ?></td>
                    </tr>
                    <tr>
                        <td>
                            <select size="10" name="cmbEquipos" multiple="multiple" id="cmbEquipos"  style="width: 350px;">
							<?php for ($i = 0; $i < count($arrEquipos) && $arrEquipos; $i++) { ?>
								<option value="<?= $arrEquipos[$i]["id"] ?>"><?= $arrEquipos[$i]["dato"] ?></option>
							<?php }?>	    			   		
                            </select>
                        </td>
                        <td class="vtop" style="vertical-align: middle;" align="center" width="70">
                            <input name="B1" class="texto" onclick="JavaScript: Move(document.getElementById('cmbEquipos'), document.getElementById('cmbEquiposAsignados'));" value=">>" type="button"><br><br>
                            <input name="B2" class="texto" onclick="JavaScript: Move(document.getElementById('cmbEquiposAsignados'), document.getElementById('cmbEquipos'));" value="<<" type="button">  
                        </td>
                        <td>
                            <select size="10" name="cmbEquiposAsignados[]" multiple="multiple" id="cmbEquiposAsignados" style="width: 350px;">
                                <?php
                                for ($i = 0; $i < count($arrEquiposMovil) && $arrEquiposMovil; $i++) {
                                    if ($arrEquiposMovil[$i]["un_esPrimaria"] == 1)
                                        $equipoPrimario = $arrEquiposMovil[$i]["un_mostrarComo"];
                                    ?>
                                    <option value="<?= $arrEquiposMovil[$i]["un_id"] ?>"><?= $arrEquiposMovil[$i]["un_mostrarComo"] ?></option>
        						<?php }?>
                            </select>
                        </td>
                        <td valign="top">
                            <a href='javascript: asignarEquipoPrimario();' title='<?= $arrPalabras["msjEstrella"] ?>'>
                                <img src="imagenes/estrella.png"/>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>	
                        <td><?= $arrPalabras["equipo primario"]; ?>: <b><span id="equipoPrimario" name="equipoPrimario"><?= (isset($equipoPrimario)) ? $equipoPrimario : $arrPalabras["no asignado"]; ?></span></b></td>	

                    </tr>
                </table>
            </fieldset>
        </div><!-- fin. mainBoxAM -->
        <?php
        break;*/
    	case 'modificarHorarios':
        	require_once 'includes/botoneraABMs.php';
        ?>			
	</div><!-- fin. mainBoxLICabezera -->        
    <hr />   
        <style>
            .configuracion-horarios dd, .configuracion-horarios dt {float: left;}
            .configuracion-horarios dd {padding: 3px 0;padding-right: 25px;}
            h3, h2 {padding: 5px 0;font-weight: bold;font-size: 14px;background: white;color: black;}
            h2{font-size: 16px;}
            #precision, #frecuencia, #precisionlav, #frecuencialav, #precisionfds, #frecuenciafds, table.widethin {width: 400px;}
        </style>
        <div id="mainBoxAM">
            <h2><?=$lang->system->movil.': '.$arrEntidades[0]["movil"]; ?></h2>
            <div id="tabHorarios">
                <ul>
                    <li><a href="#simple">Configuraci&oacute;n Simple</a></li>
                    <li><a href="#avanzado">Configuraci&oacute;n Avanzada</a></li>
                </ul>
                <div id="simple">
                    <h3>Precisi&oacute;n</h3>
                    <div id="precision"></div>
                    <table class="widefat widethin">
                        <tr><td width="33%" align="left">Baja</td><td width="34%" align="center">Media</td><td width="33%" align="right">Alta</td></tr>
                        <tr><td width="33%" align="left">(Antena)</td><td width="34%" align="center">(Antena o GPS)</td><td width="33%" align="right">(GPS)</td></tr>
                    </table>
                    <br/>
                    <h3>Frecuencia</h3>
                    <div id="frecuencia"></div>
                    <table class="widefat widethin">
                        <tr>
                            <td width="20%" align="left">Tiempo real<br/>(1 min)</td>
                            <td width="20%" align="center">3 min.&nbsp;&nbsp;&nbsp;</td>
                            <td width="20%" align="center">5 min.&nbsp;&nbsp;&nbsp;</td>
                            <td width="20%" align="center">&nbsp;&nbsp;&nbsp;10 min.</td>
                            <td width="20%" align="right">30 min.</td>
                        </tr>
                    </table>
                    <p style="text-align:right;">
						<button class="button big" type="button" onclick="javascript:insertarHorarioSimple(<?php echo $arrEntidades[0]["un_id"] ?>, 1);">Guardar &amp; Cerrar</button>
                        <button class="button big" type="button" onclick="javascript:insertarHorarioSimple(<?php echo $arrEntidades[0]["un_id"] ?>, 0)">Guardar</button>
                    </p>
                </div>
                <div id="avanzado">
                    <h3>De Lunes a Viernes <span style="float:right;"><input class="activo" name="activolav" id="activolav" <?php if (isset($lav)) echo "checked"; ?> type="checkbox" value="1" /> Activo</span></h3>
                    <table class="widefat">
                        <tr><td width="20%">Reportar entre las</td><td width="400px">
                        <?php echo selectHorarioDesde('lavdesde', null, @$lav['Inicio']); ?>
                        y las <?php echo selectHorarioHasta('lavhasta', null, @$lav['Fin']); ?>
                        </td><td width="80%"></td></tr>
                        <tr><td>Precisi&oacute;n</td><td><div id="precisionlav"></div></td><td id="precisionlav-label"><?php echo @$lav['Tipo']; ?></td></tr>
                        <tr><td>Frecuencia</td><td><div id="frecuencialav"></div></td><td id="frecuencialav-label"><?php echo @$lav['Tiempo']; ?></td></tr>
                    </table>
                    <h3>Fin de Semana <span style="float:right;"><input class="activo" name="activofds" id="activofds" <?php if (isset($fds)) echo "checked"; ?> type="checkbox" value="1" /> Activo</span></h3>
                    <table class="widefat">
                        <tr><td width="20%">Reportar entre las </td><td width="400px">
                        <?php echo selectHorarioDesde('fdsdesde', null, @$fds['Inicio']); ?>
                        y las <?php echo selectHorarioHasta('fdshasta', null, @$fds['Fin']); ?>
                        </td><td width="80%"></td></tr>
                        <tr><td>Precisi&oacute;n</td><td><div id="precisionfds"></div></td><td id="precisionfds-label"><?php echo @$fds['Tipo']; ?></td></tr>
                        <tr><td>Frecuencia</td><td><div id="frecuenciafds"></div></td><td id="frecuenciafds-label"><?php echo @$fds['Tiempo']; ?></td></tr>
                    </table>
                    
                    <fieldset style="display:none;">
                        <legend><?php echo "horarios asignados al equipo"; ?></legend>
                        <table class="widefat">
                            <tr><td><div  align="left"><b>Dia</b></div></td><td>
                                    <dl class="configuracion-horarios">
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[1]" value="1"></dt>
                                        <dd>Lunes</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[2]" value="2"></dt>
                                        <dd>Martes</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[3]" value="3"></dt>
                                        <dd>Miercoles</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[4]" value="4"></dt>
                                        <dd>Jueves</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[5]" value="5"></dt>
                                        <dd>Viernes</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[6]" value="6"></dt>
                                        <dd>S&aacute;bado</dd>
                                        <dt><input type='checkbox' class="diaDeLaSemana" name="dia[7]" value="7"></dt>
                                        <dd>Domingo</dd>
                                    </dl>
                                    <!--<select name="dia" id="dia">
                                    <option value="1" select="select">Lunes</option>
                                    <option value="2">Martes</option>
                                    <option value="3">Miercoles</option>
                                    <option value="4">Jueves</option>
                                    <option value="5">Viernes</option>
                                    <option value="6">Sabado</option>
                                    <option value="7">Domingo</option>
                                    </select>
                                    -->
                                </td></tr>
                            <tr><td><div  align="left">Horario desde:</div></td><td><select name="h_d_h" id="h_d_h">
                                        <?php for ($i = 0; $i < 10; $i++) {
                                            ?>
                                            <option value="0<?= $i ?>">0<?= $i ?></option>
                                        <?php } for ($i = 10; $i < 24; $i++) {
                                            ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php } ?>
                                    </select>Hs<select name="h_d_m" id="h_d_m">
                                        <?php for ($i = 0; $i < 10; $i++) {
                                            ?>
                                            <option value="0<?= $i ?>">0<?= $i ?></option>
                                        <?php } for ($i = 10; $i < 60; $i++) {
                                            ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php } ?>
                                    </select>min</td></tr><tr><td><div  align="left">Horario hasta:</div></td><td><select name="h_h_h" id="h_h_h">
                                        <?php for ($i = 0; $i < 10; $i++) {
                                            ?>
                                            <option value="0<?= $i ?>">0<?= $i ?></option>
                                        <?php } for ($i = 10; $i < 24; $i++) {
                                            ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php } ?>
                                    </select>Hs<select name="h_h_m" id="h_h_m">
                                        <?php for ($i = 0; $i < 10; $i++) {
                                            ?>
                                            <option value="0<?= $i ?>">0<?= $i ?></option>
        <?php } for ($i = 10; $i < 60; $i++) {
            ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
        <?php } ?>
                                    </select>min</td></tr>
                            <tr><td><div  align="left">Tiempo</div></td><td><select name="tiempo" id="tiempo">
                                        <option value="10">10 seg</option>
                                        <option value="20">20 seg</option>
                                        <option value="30">30 seg</option>
                                        <option value="60">1 min</option>
                                        <option value="180">3 min</option>
                                        <option value="300">5 min</option>	
                                        <option value="3600">1 h</option>										
                                    </select></td></tr><tr><td><div  align="left">Tipo de localizacion</div></td><td><select name="tipo" id="tipo">
                                        <option value="antena">Antena</option>
                                        <option value="gps">GPS</option>
                                        <option value="gps / antena">GPS / Antena</option>
                                    </select></td></tr>
                            <tr>
                                <td></td>
                                <td>
                                    <a href="javascript:insertarHorarioMultipleMovil(<?= $arrEntidades[0]["un_id"] ?>)"><b>+</b> Agregar</a>
                                </td>
                            </tr>
                        </table>
                        <!-- insertarHorarioMovil -->
                    </fieldset>
                    
                    <!--
                    <a href="javascript:insertarHorarioAvanzado(<?php echo $arrEntidades[0]["un_id"] ?>)"><b>+</b> Agregar</a>
                    -->
                    <p style="text-align:right;">
						<button class="button big" type="button" onclick="javascript:insertarHorarioAvanzado(<?php echo $arrEntidades[0]["un_id"] ?>, 1);">Guardar &amp; Cerrar</button>
						<button class="button big" type="button" onclick="javascript:insertarHorarioAvanzado(<?php echo $arrEntidades[0]["un_id"] ?>, 0)">Guardar</button>
					</p>
                </div>

                <table id="tablaHorarios" class="widefat" style="display:none;">
                    <tr class="titulo" >
                        <td width="20px">Dia</td>
                        <td width="150px">Inicio</td>
                        <td width="150px">Fin</td>
                        <td width="150px">Tiempo</td>
                        <td width="200px">Tipo</td>
                        <td width="100px"></td>
                    </tr>
                    <?php
                    $iFila = 0;
                    if ($arrHorarios) {
                        for ($i = 0; $i < count($arrHorarios) && $arrHorarios; $i++) {
                            $class = ($i % 2 == 0) ? 'filaPar' : 'filaImpar';
                            $iFila = $i;
                            ?>
                            <tr class="<?= $class ?>" id="tr<?= $arrHorarios[$i]['Id'] ?>">
                                <td width="20px"><?php
                    switch ($arrHorarios[$i]['Dia']) {
                        case 1: echo "Lunes";
                            break;
                        case 2: echo "Martes";
                            break;
                        case 3: echo "Miercoles";
                            break;
                        case 4: echo "Jueves";
                            break;
                        case 5: echo "Viernes";
                            break;
                        case 6: echo "Sabado";
                            break;
                        case 7: echo "Domingo";
                            break;
                        default: echo "ERROR";
                            break;
                    }
                            ?></td>
                                <td width="150px"><?php
                    echo number_format(($arrHorarios[$i]['Inicio'] / 100), 2, ':', '');
                    ?></td>
                                <td width="150px"><?php echo number_format(($arrHorarios[$i]['Fin'] / 100), 2, ':', ''); ?></td>
                                <td width="150px"><?= $arrHorarios[$i]['Tiempo'] ?></td>
                                <td width="200px"><?= $arrHorarios[$i]['Tipo'] ?></td>
                                <td><a href="javascript:borrarHorario(<?= $arrHorarios[$i]['Id'] ?>)"><img src="imagenes/cerrar.png"> </a></td>
                            </tr>
                <?php
            }
        }
        ?>
                    <input type="hidden" id="classFila" value="<?= $iFila ?>" />
                </table>
		</div><!-- fin. mainBoxAM -->
        <?php $freq_val = 1;?>
        <script>
            $(document).ready(function(){
                $("#tabHorarios").tabs();
            });

            function insertarHorarioMultipleMovil(idMovil){
                $('.diaDeLaSemana').each(function(index, elem) {
                    if (elem.checked == true) {
                        insertarHorarioMovil(idMovil, elem.value);
                    }
                });
            }
            
            $("#precision" ).slider({
                value: <?php echo $precision; ?>, min: 1, max: 3, step: 1,
                slide: function( event, ui ) {
                    $("#precision").val(ui.value);
                }
            });
            $("#precision").val($("#precision").slider("value"));
            
            $("#frecuencia" ).slider({
                value: <?php echo $frecuencia; ?>, min: 1, max: 5, step: 1,
                slide: function(event, ui) {
                    $( "#frecuencia" ).val(ui.value);
                }
            });
            $("#frecuencia").val($("#frecuencia").slider("value"));
            
            var slabel;
            $("#precisionlav" ).slider({
                value: <?php echo $lav_precision; ?>, min: 1, max: 3, step: 1,
                slide: function( event, ui ) {
                    $("#precisionlav").val(ui.value);
                    switch (ui.value) {
                        case 1: {
                                slabel = ' Baja (Antena)'; break;
                        }
                        case 2: {
                                slabel = ' Media (Antena o GPS)'; break;
                        }
                        case 3: {
                                slabel = ' Alta (GPS)'; break;
                        }
                    }
                    $("#precisionlav-label").html("&nbsp;&nbsp;" + slabel);
                }
            });
            $("#precisionlav").val($("#precisionlav").slider("value"));
            
            $("#frecuencialav" ).slider({
                value: <?php echo $lav_frecuencia; ?>, min: 1, max: 5, step: 1,
                slide: function(event, ui) {
                    $("#frecuencialav").val(ui.value);
                    switch (ui.value) {
                        case 1: {
                                slabel = ' 1 min.';
                                break;
                        }
                        case 2: {
                                slabel = ' 3 min.';
                                break;
                        }
                        case 3: {
                                slabel = ' 5 min.';
                                break;
                        }
                        case 4: {
                                slabel = ' 10 min.';
                                break;
                        }
                        case 5: {
                                slabel = ' 30 min.';
                                break;
                        }
                    }
                    $("#frecuencialav-label").html("&nbsp;&nbsp;" + slabel);
                }
            });
            $("#frecuencialav").val($("#frecuencialav").slider("value"));
            
            $("#precisionfds" ).slider({
                value: <?php echo $fds_precision; ?>, min: 1, max: 3, step: 1,
                slide: function( event, ui ) {
                    $("#precisionfds").val(ui.value);
                    switch (ui.value) {
                        case 1: {
                                slabel = ' Baja (Antena)'; break;
                        }
                        case 2: {
                                slabel = ' Media (Antena o GPS)'; break;
                        }
                        case 3: {
                                slabel = ' Alta (GPS)'; break;
                        }
                    }
                    $("#precisionfds-label").html("&nbsp;&nbsp;" + slabel);
                }
            });
            $("#precisionfds").val($("#precisionfds").slider("value"));
            
            $("#frecuenciafds" ).slider({
                value: <?php echo $fds_frecuencia; ?>, min: 1, max: 5, step: 1,
                slide: function(event, ui) {
                    $("#frecuenciafds").val(ui.value);
                    switch (ui.value) {
                        case 1: {
                                slabel = ' 1 min.';
                                break;
                        }
                        case 2: {
                                slabel = ' 3 min.';
                                break;
                        }
                        case 3: {
                                slabel = ' 5 min.';
                                break;
                        }
                        case 4: {
                                slabel = ' 10 min.';
                                break;
                        }
                        case 5: {
                                slabel = ' 30 min.';
                                break;
                        }
                    }
                    $("#frecuenciafds-label").html("&nbsp;&nbsp;" + slabel);
                }
            });
            $("#frecuenciafds").val($("#frecuenciafds").slider("value"));
        </script>
       <?php
        break;
    	case 'ErrorEquipoAsociado':
        require_once 'includes/botoneraABMs.php';
        ?>
	</div><!-- fin. mainBoxLICabezera -->        
    <hr />         
        <div id="contenedorIngresarComo" style="text-align:center;padding:0 3px 0 3px;border:3px solid #FFFF4A;background-color:#FFFFAA;font-size:11px;line-height:13px;position:absolute;bottom:150px;left:35%;width:30%;">
            <a href="javascript: cerrarMensaje();">
                <img id="imgCerrarMensaje" src="imagenes/cerrar.png" />
            </a>
            <br/>
            <span style='color:#000000;'><br/><?= $mensaje; ?><br/></span><br/>
        <?php
        break;
	}?>
</form>
</div>
