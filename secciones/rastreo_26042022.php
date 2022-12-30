<?php //require_once("includes/google.v3.ini");?>
<?php $arrAlertasPermisos = array(5,7,9,11,19);?>
<script type="text/javascript">
	var idPerfil = <?=(int)$_SESSION['idPerfil']?>;
	
    var permisos = []; 
	permisos['alertas'] = <?=tienePerfil($arrAlertasPermisos)?'true':'false'; ?>; 
	permisos['conReferencias'] = <?=tienePerfil(16)?'true':'false'; ?>; 
	
	arrLang['modificar_grupo'] = '<?=$lang->system->modificar_grupo?>';
	arrLang['asignar_grupo'] = '<?=$lang->system->asignar_grupo?>';
	arrLang['bateria'] = '<?=$lang->system->bateria?>';
	arrLang['bateria_baja'] = '<?=$lang->system->bateria_baja?>';
	arrLang['sin_cobertura'] = '<?=$lang->system->sin_cobertura?>';
	arrLang['cobertura_mala'] = '<?=$lang->system->cobertura_mala?>';
	arrLang['cobertura_regular'] = '<?=$lang->system->cobertura_regular?>';
	arrLang['cobertura_buena'] = '<?=$lang->system->cobertura_buena?>';
	arrLang['cobertura_muy_buena'] = '<?=$lang->system->cobertura_muy_buena?>';
	arrLang['wifi_apagado'] = '<?=$lang->system->wifi_apagado?>';
	arrLang['gps_apagado'] = '<?=$lang->system->gps_apagado?>';
	arrLang['gps_encendido'] = '<?=$lang->system->gps_encendido?>';
	arrLang['sin_nombre'] = '<?=$lang->system->sin_nombre?>';
	arrLang['datos_gps'] = '<?=$lang->system->datos_gps?>';
	arrLang['velocidad'] = '<?=$lang->system->velocidad?>';
	arrLang['sentido'] = '<?=$lang->system->sentido?>';
	arrLang['no_mostrar'] = '<?=$lang->system->no_mostrar?>';
	arrLang['sugerencia_inteligencia'] = '<?=$lang->system->sugerencia_inteligencia?>';
	arrLang['matricula'] = '<?=$lang->system->matricula?>';
	arrLang['movil'] = '<?=$lang->system->movil?>';
	arrLang['fecha'] = '<?=$lang->system->fecha?>';
	arrLang['evento'] = '<?=$lang->system->evento?>';
	arrLang['ubicacion'] = '<?=$lang->system->ubicacion?>';
	arrLang['si'] = '<?=$lang->system->agregar_punto?>';
	arrLang['confirmar'] = '<?=$lang->botonera->confirmar?>';
	arrLang['cancelar'] = '<?=$lang->botonera->cancelar?>';
	arrLang['agregar_geocerca'] = '<?=$lang->system->agregar_geocerca?>';
	arrLang['dir_no_encontrada'] = '<?=$lang->system->direccion_no_encontrada?>';
	arrLang['rastreo_sin_moviles'] = '<?=$lang->system->rastreo_sin_moviles?>';
	
</script>
<script type='text/javascript' src='js/defaultMap.js'></script>
<script type="text/javascript" src="js/newtracer.js"></script>
<script type="text/javascript" src="js/rastreoFunciones.js" ></script>
<script type="text/javascript" src="js/comandos_favoritos.js"></script>
<script type="text/javascript" src="js/jquery/jquery.placeholder.js"></script>

<!--
<div id="play-alarma-ie"></div><!-- necesario para reproducir alarmas en IE -->

<?php require_once('includes/newtracer/newtracer.inc.php');?>
    <script type="text/javascript">
       var g_dlgEstadoComandos = null;
        var g_dlgReferencias = null;
		var g_dlgTrafico = null;
		
		 $(document).bind("ready", function(){
            var arrStartUpButtons = [
                "mnu_MoveGPSPanel" ,    // Panel GPS a la derecha
                "mnu_FilterMovOnly",    // Solo filtrar moviles (no seguir)
                "mnu_OrderByClient"     // Ordenar moviles por cliente
            ];

            for(i in arrStartUpButtons){
                $("#" + arrStartUpButtons[i]).click();
            }

            g_bResetGroupInfoWhenCriteriaChanges = true;

			/*
            g_dlgReferencias = $("#dlgReferencias");
            g_dlgReferencias.dialog({
                "autoOpen"  : false,
                "width"     : 210,
                "minWidth"  : 210,
                "height"    : 345,
                "minHeight" : 345
            });
			/**/
			<?php /*if(tienePerfil(19)){?>
				g_dlgTrafico = $("#dlgTrafico");
				g_dlgTrafico.dialog({
					"autoOpen"  : false,
					"width"     : 280,
					"minWidth"  : 210,
					"height"    : 345,
					"minHeight" : 345
				});
			<?php  }*/?>

			<?php /*if(tienePerfil(19)){?>
				var $btnDlgTrafico = $("#btnDlgTrafico");
				$btnDlgTrafico.bind("click", function(ev){
					if ( g_dlgTrafico.dialog("isOpen")){
						g_dlgTrafico.dialog("close");
					}
					else{
						g_dlgTrafico.dialog("open");
					}
				});			
			<?php }*/?>
			
			
			$("#rastreo_colIzqTabs").tabs();
			$("#rastreo-tabs").tabs({
				select: function(event, ui) {
					if (ui.index == 3) {
						mostrarPopup("boot.php?c=tableroMilkrun");
						return false;
					}
					if (ui.index == 2) {
						return false;
					}
					if (ui.index == 1) {
						return false;
					}
					return true;
				}
			});
			
			$('#rastreo-tabs').bind('tabsshow', function(event, ui) {
				if (ui.panel.id == "mapa") {
					//resizeMap();
				}
			});
			
			$( "#rastreo-tabs" ).css('z-index', 0);
			$( "#rastreo-tabs" ).css('overflow', "hidden");
			$( "#rastreo-tabs" ).css('position', "relative");
			
        });
    </script>

<input type="hidden" name="HidPopup" id="HidPopup" value="<?php if (isset($_GET["popupRastreo"])) echo '1'; else echo '0';?>" />
<?php require_once 'rastreo.colIzq.php'; ?>
<div id="main" onclick="ocultarAlertaVisual();" >
	<div id="rastreo-tabs">
    	<div class="contenedor-vinietas">
            <div class="vinieta" id="imgPanel">
                <img onclick="javascript:ocultarPanel();" src="imagenes/raster/black/flecha16i.png" />
            </div>
            <?php /*if(!tienePerfil(16)){?>
            <div class="vinieta" id="btnDlgReferencias">
                <img src="imagenes/raster/black/pin_16x16.png"  />
            </div>
            <?php }*/?>
            <?php /*<div class="vinieta" id="btnPtosReferencias">
                <?php if(tienePerfil(16)){?>
                	<img src="imagenes/raster/black/map_pin_stroke_10x16.png" />
                <?php } else{?>
					<img src="imagenes/raster/black/map_pin_stroke_10x16_off.png" />
				<?php }?>    
           	</div>
			<?php /*if(tienePerfil(19)){?>
			<div class="vinieta" id="btnDlgTrafico">
                <img src="imagenes/raster/black/steering_wheel_16x16.png"  />
            </div>
			<?php }*/?> 
            
            <?php if(!tienePerfil(16)){?>
            <div class="vinieta" id="btnPtosReferencias">
                <img src="imagenes/raster/black/map_pin_stroke_10x16_off.png" />
           	</div>
            <?php }?> 
        </div> 
         
		<div id="rastreo_mapa" class="tab">
        	<div id="loading_logo" style="position: absolute; right: 0px; top: 0px; z-index: 5; display: none;">
            	<img src="imagenes/ajax-loader.gif" />
			</div>
            <div id="mapa"></div>
            <div id="divHoverLatLng" class="hoverLatLng" style="display: none">
            	<div>Latitud : <span id="hoverLat"></span></div>
                <div>Longitud: <span id="hoverLng"></span></div>
			</div>
		</div>
	</div>
</div>

<!-- Ventana Flotante Izq -->
<div id="infogps" style="display: none;" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
    <div class="close">
        <a href="javascript:;" onclick="newTracer.cerrarPanelGPS();"><?=$lang->botonera->cerrar?></a>
    </div>
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#datosgps"><?=$lang->system->datos_gps?></a></li>
    </ul>
    <div class="contenido ui-tabs-panel ui-widget-content ui-corner-bottom" id="infogps-contenido"></div>
</div>
<!-- -->

<?php include "includes/extrasMapa.php" ?>

<?php /*if(tienePerfil($arrAlertasPermisos)){?>
<div id="alertas">
    <p style="background: #555; font-weight: bold; color: #fff; margin: 0px; padding: 10px;">
        <?=$lang->system->alertas?> (<span id="cantidad_de_alertas">-</span>)
        <!-- <span id="ultimo_id_alerta"></span> -->
        <span style="float: right;">
            <a href="javascript:;" id="btnMostrarAlertas" onclick="newTracer.mostrarVisorAlertas();">
                <img src="imagenes/agrandarPanel.png" />
            </a>
            <a href="javascript:;" id="btnOcultarAlertas" style="display: none;" onclick="newTracer.ocultarVisorAlertas();">
                <img src="imagenes/achicarPanel.png" />
            </a>
        </span>
        <span style="float:right; margin-right: 20px;">
             <?=$lang->system->alertas_restantes?>: (<span id="total_restante_alertas">-</span>)
        </span>
    </p>

    <div id="alertas-contenido2">
        
        <table class="widefat">
            <thead>
                <tr>
                    <td width="10%"><?=$lang->system->movil?></td>
                    <td width="5%" ><?=$lang->system->sentido?></td>
                    <td width="5%" ><?=$lang->system->velocidad?></td>
                    <td width="10%"><?=$lang->system->recibido?></td>
                    <td width="10%"><?=$lang->system->generado?></td>
                    <td width="25%"><?=$lang->system->alertas?></td>
                    <td width="5%" ><?=$lang->system->ocurrencias?>&nbsp;&nbsp;</td>
                    <td width="20%"><?=$lang->system->ubicacion?></td>
                    <td width="5%" >-</td>
                    <td width="10%"></td>
                </tr>
            </thead>
        </table>

        <div id="alertas-contenido" style="height: 170px; overflow-y: auto; padding: 0; display:none;">
            <table class="widefat">
                <tbody id="alertas_body">
                    <!-- Rellenado automaticamente -->
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<div id="dlgConfirmarAlerta" title="<?=$lang->system->confirmar_alerta?>" style="display:none">
    <table>
        <tr>
            <td style="white-space: nowrap;">
                <?=$lang->message->msj_rastreo_alertas?>:
            </td>
            <td>
                <select id="cmbMotivoConfirmacion"><?php
                    foreach( $arrMotivosConfirmacion as $motivo ){
                        echo '<option value="'.$motivo['mc_id'].'">'.$motivo['mc_descripcion'].'</option>';
                    }?>
                </select>
            </td>
        </tr>
    </table>
    
    <input type="hidden" id="alerta_id" value="" />
    <input type="hidden" id="alerta_ids" value="" />
</div>
<?php }*/?>
