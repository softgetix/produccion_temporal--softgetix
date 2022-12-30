<div id="main" class="sinColIzq">
	<form name="frm_<?php echo $seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?php echo $operacion;?>" />
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?php echo $seccion; ?>" />
		<div id="intermil">
			<input type="hidden" name="fechaDesde" id="fechaDesde" />
            <input type="hidden" name="fechaHasta" id="fechaHasta" />
            <!-- -->
            <ul>
                <li><a href="#intermil-historico-administracion"><span onclick="mostrar();">Informe Hist&oacute;rico</span></a></li>
                <!--li><a href="#containerCalendar" ><span onclick='setTimeout("mostrarCalendar();",500)';>Calendario</span></a></li-->
            </ul>
	
            <div id="intermill-fecha-seleccionada">
                Fecha de Ingreso Real seleccionada: 
                <strong>
                    <span id="date-selected">Hoy</span>
                </strong>
            </div>
	
			<div id="intermil-historico-administracion">
                <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="vertical-align:top">
                        <div id="buscador-calendario">
                            <div id="lblDesde" style="clear:both;display:none;">Desde</div>
                            <div id="datepicker"></div>
                            
                            <div id="avanzado">
                                <div id="buscador-avanzado" style="display:none;" class="hidden">
                                    <div id="lblHasta" style="display:none;">Hasta</div>
                                    <div id="datepicker2"></div>
                                </div>
                            </div>
                            
                            <div>
                                <!--<button type="button" class="button" id="btnAvanzado" style="float:left;" onclick="busquedaAvanzada();return false;">B&uacute;squeda Avanzada</button> -->
                                <a href="javascript:;" id="btnAvanzado" style="float:left;" onclick="busquedaAvanzada();return false;">B&uacute;squeda Avanzada</a>
                                <button disabled="disabled" type="button" id="btnBuscarAvanzado" style="display:none;float:right;margin-right:6px;" onclick="buscar();">Buscar</button>
                            </div>
                            
                        </div>
                    </td>
                    <td style="vertical-align:top">						
                        <div id="intermil-historico-administracion-listar">
                            <table id="example" width="100%">
                                <thead class="">
                                    <tr>
                                        <th nowrap width="10%">C&oacute;digo</th>
                                        <th nowrap width="10%">Veh&iacute;culo / Conductor (Transportista)</th>
                                        <th nowrap width="20%">Lugar</th>
                                        <!-- 3 <th nowrap width="10%">Ingreso Programado</th> -->
                                        <th nowrap width="10%">Ingreso Real</th>
                                        <th nowrap width="10%">Egreso Real</th>
                                        <!-- 6 <th nowrap width="10%">Egreso Programado</th> -->
                                        <th nowrap width="10%">Estad&iacute;a</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>        
                </table>
			</div>
            <!-- -->
		</div>
	</form>
</div>