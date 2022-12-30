<div id="main"  style="margin-left: 5px; margin-right: 5px;">
   <div class="mainBoxLICabezera">
   <h1><?=$lang->system->title_probador_panico?></h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<?php
	switch ($operacion) {
		case 'listar':
?>
			<script type="text/javascript" src="js/panicoFunciones.js"></script>
			<script type="text/javascript" src="js/jquery/jquery.placeholder.js"></script>
			<script type="text/javascript" src="js/jquery/jquery-ui-1.8.14.autocomplete.min.js"></script>
			<script type="text/javascript">
				var idCliente = 0;
				var nombresZonas = [];
				var nombresMoviles = [];
				var cronoUltimasPruebasPanico = false;
				var cronoDisponibilidadPruebasPanico = false;
				var movilEnPrueba, zonaEnPrueba;
				var codigoPruebaEnCurso = 0;
				var arrLang = [];
				arrLang['sin_probar'] = '<?=$lang->system->sin_probar?>';
				arrLang['msg_desvincular'] = '<?=$lang->system->probador_panico_txt15?>';
				arrLang['msg_sin_disponibilidad'] = '<?=$lang->system->probador_panico_txt14?>';
				arrLang['desvincular'] = '<?=$lang->system->desvincular?>';
				
				<?php
					$opciones = array();
					if (count($arrClientes) > 0) {
						foreach ($arrClientes as $cliente) {
							$opciones[] = '"' . encode("({$cliente["cl_id"]}) {$cliente["cl_razonSocial"]} ({$cliente["cl_email"]})") . '"';
							if ($cliente["cl_id"] == $cliente_seleccionado) {
								$cliente_seleccionado = $cliente["cl_id"];
								$nombre_cliente_seleccionado = $opciones[count($opciones) - 1];
								$email_seleccionado = $cliente["cl_email"];
							}
						}
					}
					$opciones = implode(",", $opciones);									
				?>
				$(function() {
					var clientes = [<?= $opciones ?>];
					$("#buscador").autocomplete({
						source: clientes,
						select: function() {
							try {
								clearInterval(cronoUltimasPruebasPanico);
							}
							catch (ex) {}
							try {
								clearInterval(cronoDisponibilidadPruebasPanico);
							}
							catch (ex) {}
							panicoElegirClienteCompletar(this.value);
						}
					});
				});
			</script>
			<script type="text/javascript" src="js/jquery/colorbox/jquery.colorbox-min.js"></script>
			<link rel=stylesheet type="text/css" href="js/jquery/colorbox/colorbox.css" />
			<script type="text/javascript">
				var segundosCuentaRegresiva = 20, cronoRegresivo = false, cronoRevisionPrueba = false;
				var probando = false;
				
				$(document).ready(function(){
					$("#consulta_inicio_prueba").colorbox({inline:true, width:"50%"});
					$("#consulta_conteo_regresivo").colorbox({inline:true, width:"50%", overlayClose: false, escKey: false, closeButton: false});
					$("#consulta_prueba_exitosa").colorbox({inline:true, width:"50%"});
					$("#consulta_fuera_de_zona").colorbox({inline:true, width:"50%"});
					$("#consulta_sin_disponibilidad").colorbox({inline:true, width:"50%"});
					$("#consulta_panico_no_recibido").colorbox({inline:true, width:"50%"});
					$(document).bind('cbox_closed',
						function() {
							try {
								clearInterval(cronoRegresivo);
							}
							catch (ex) {}
							if (probando) {
								probando = false;
							}
							segundosCuentaRegresiva = 20;
							document.getElementById('contador_segundos').innerHTML = segundosCuentaRegresiva;
						}
					);
				});
			</script>
			
			<div class="panico_global">
				<div class="panico_global_buscar_cliente">
					<p class="panico_global_buscar_cliente">
						<span class="panico_global_buscar_cliente"><?=$lang->system->filtro_buscador?></span>
					</p>
				</div>
				<div class="panico_global_buscador">
					<div class="panico_global_buscador_input">
						<input onkeypress="if (event.keyCode == 13) return false;" onkeydown="if (event.keyCode == 13) return false;" id=buscador class="panico_global_buscador" type=text placeholder="<?=$lang->system->filtro_buscador?>" />
					</div>
					<div class="panico_global_buscador_lupa">
						<img style="cursor: pointer;" class="panico_global_buscador_lupa" src="imagenes/lupa.png" />
					</div>				
				</div>
				<div style="float:right;">
					<a href="boot.php?c=abmClientes&hidFiltro=<?=encode($email_seleccionado)?>" id="botonVolver" style="margin-top:4px; width:auto;" class="button colorin"><?=$lang->system->volver_abm_clientes?></a>
				</div>
				
                <!-- Listado secciones -->
				<div id="div_panico_global_cliente_mail" class="panico_global_cliente_mail">
					<div class="panico_global_cliente_mail_numero">
						<p class="panico_global_cliente_mail">
							<span class="panico_global_cliente_mail"><?=$lang->system->nro_entidad?>:&nbsp;&nbsp;&nbsp;&nbsp;<span class="panico_global_cliente_mail" id="num_cliente"></span></span>
						</p>
					</div>
					<div class="panico_global_cliente_mail_cuenta">
						<p class="panico_global_cliente_mail">
							<span class="panico_global_cliente_mail"><?=$lang->system->email_cuenta?>:&nbsp;&nbsp;&nbsp;<span class="panico_global_cliente_mail" id="mail_cuenta"></span></span>
						</p>
					</div>
				</div>
				<div id="tabla_moviles" class="panico_global_tabla">
				</div>
			</div>
			
			<!-- Lanzamiento de prueba: -->
			<a id='consulta_inicio_prueba' href="#inicio_prueba" style="display: none;">x1</a>
			<div style="display: none;">
				<div id='inicio_prueba'>
					<div>
						<p class="inicio_prueba_titulo_mayor">
							<span class="inicio_prueba_titulo_mayor"><?=$lang->system->atencion?></span>
						</p>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?=str_replace(']','>',str_replace('[','<',$lang->system->probador_panico_txt1))?>															
							</span>
						</p>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-ZONA]", "<span id=inicio_prueba_nombre_zona class=inicio_prueba_descripcion_destacado></span>", str_replace("[SPAN-MOVIL]", "<span id=inicio_prueba_nombre_movil class=inicio_prueba_descripcion_destacado></span>", $lang->system->probador_panico_txt2)) ?>
							</span>
						</p>
					</div>
					<div class="inicio_prueba_aceptacion">
						<input onclick="iniciarPruebaPanico();" class="inicio_prueba_aceptacion_si" type=button value="<?=$lang->system->si?>" />
                        <input class="inicio_prueba_aceptacion_mas_tarde" type=button value="<?=$lang->system->mas_tarde?>" onclick="$.colorbox.close();" />
					</div>
				</div>
			</div>
			
			<!-- Conteo regresivo: -->
			<a id='consulta_conteo_regresivo' href="#conteo_regresivo" style="display: none;">x2</a>
			<div style="display: none;">
				<div id='conteo_regresivo'>
					<div>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-MOVIL]", '<span id="probando_movil" class="inicio_prueba_descripcion_destacado"></span>', str_replace("[SPAN-ZONA]", '<span id="probando_zona" class="inicio_prueba_descripcion_destacado"></span>', $lang->system->probador_panico_txt3)) ?>
                       		</span>
						</p>
						<p class="inicio_prueba_esperando_panico">
							<span class="inicio_prueba_esperando_panico"><?=$lang->system->esperando_panico?></span>
						</p>
						<p class="inicio_prueba_esperando_panico_conteo">
							<span class="inicio_prueba_esperando_panico"><span class="inicio_prueba_esperando_panico_segundos" id="contador_segundos">&nbsp;</span> <?=$lang->system->segundos?></span>
						</p>
					</div>
				</div>
			</div>
			
			<!-- Prueba exitosa: -->
			<a id='consulta_prueba_exitosa' href="#prueba_exitosa" style="display: none;">x3</a>
			<div style="display: none;">
				<div id='prueba_exitosa'>
					<div>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-MOVIL]", '<span id="exito_movil" class="inicio_prueba_descripcion_destacado"></span>', str_replace("[SPAN-ZONA]", '<span id="exito_zona" class="inicio_prueba_descripcion_destacado"></span>', $lang->system->probador_panico_txt3)) ?>
                            </span>
						</p>
						<p class="inicio_prueba_esperando_panico_exitoso">
							<span class="inicio_prueba_esperando_panico_exitoso"><?=$lang->system->prueba_panico_exitosa?></span>
						</p>
						<p class="inicio_prueba_esperando_panico_exitoso_detalle">
							<span class="inicio_prueba_esperando_panico_exitoso_detalle">
								<?=$lang->system->probador_panico_txt4?>
							</span>
						</p>
					</div>
				</div>
			</div>
			
			<!-- Fuera de zona: -->
			<a id='consulta_fuera_de_zona' href="#fuera_de_zona" style="display: none;">x4</a>
			<div style="display: none;">
				<div id='fuera_de_zona'>
					<div>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-MOVIL]", '<span id="fuera_zona_movil" class="inicio_prueba_descripcion_destacado"></span>', str_replace("[SPAN-ZONA]", '<span id="fuera_zona_zona" class="inicio_prueba_descripcion_destacado"></span>', $lang->system->probador_panico_txt3)) ?>
                            </span>
						</p>
						<p class="inicio_prueba_esperando_panico_fuera_zona">
							<span class="inicio_prueba_esperando_panico_fuera_zona">
								<?=str_replace(']','>',str_replace('[','<',$lang->system->probador_panico_txt5))?>			
							</span>
						</p>
					</div>
				</div>
			</div>
			
			<!-- Sin disponibilidad técnica: -->
			<a id='consulta_sin_disponibilidad' href="#sin_disponibilidad" style="display: none;">x5</a>
			<div style="display: none;">
				<div id='sin_disponibilidad'>
					<div>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-MOVIL]", '<span id="sin_disponibilidad_movil" class="inicio_prueba_descripcion_destacado"></span>', str_replace("[SPAN-ZONA]", '<span id="sin_disponibilidad_zona" class="inicio_prueba_descripcion_destacado"></span>', $lang->system->probador_panico_txt3)) ?>
                            </span>
						</p>
						<p class="inicio_prueba_esperando_panico_no_disponibilidad_tecnica">
							<span class="inicio_prueba_esperando_panico_no_disponibilidad_tecnica">
								<?=str_replace(']','>',str_replace('[','<',$lang->system->probador_panico_txt6))?>			
							</span>
						</p>
					</div>
				</div>
			</div>
			
			<!-- Pánico no recibido: -->
			<a id='consulta_panico_no_recibido' href="#panico_no_recibido" style="display: none;">x6</a>
			<div style="display: none;">
				<div id='panico_no_recibido'>
					<div>
						<p class="inicio_prueba_descripcion">
							<span class="inicio_prueba_descripcion">
								<?= str_replace("[SPAN-MOVIL]", '<span id="panico_no_recibido_movil" class="inicio_prueba_descripcion_destacado"></span>', str_replace("[SPAN-ZONA]", '<span id="panico_no_recibido_zona" class="inicio_prueba_descripcion_destacado"></span>', $lang->system->probador_panico_txt3)) ?>
                            </span>
						</p>
						<p class="inicio_prueba_esperando_panico_no_recibido">
							<span class="inicio_prueba_esperando_panico_no_recibido"><?=$lang->system->probador_panico_txt8?></span>
						</p>
						<p class="inicio_prueba_esperando_panico_no_recibido_detalle">
							<span class="inicio_prueba_esperando_panico_no_recibido_detalle">
								<?=$lang->system->probador_panico_txt7?>
							</span>
						</p>
					</div>
				</div>
			</div>
			<?php
				if ($nombre_cliente_seleccionado != "") {
			?>
					<script type="text/javascript">
						document.getElementById("buscador").value = <?= $nombre_cliente_seleccionado ?>;
						panicoElegirClienteCompletar(<?= $nombre_cliente_seleccionado ?>);
					</script>
			<?php
				}
			?>
<?php
	   	break;
	}
?>
	</form>
</div>