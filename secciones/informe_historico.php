<script type="text/javascript">
	arrLang['mapa'] = '<?=$lang->system->mapa?>';
	arrLang['detalle'] = '<?=$lang->system->detalle?>';
	
	arrLang['agregar_geozona'] = '<?=$lang->botonera->agregar_geozona?>';
	arrLang['cerrar'] = '<?=$lang->botonera->cerrar?>';
	arrLang['ver_video'] = '<?=$lang->botonera->ver_video?>';
	arrLang['reproduciendo_video'] = '<?=$lang->botonera->reproduciendo_video?>';
	
	arrLang['movil'] = '<?=$lang->system->movil?>';
	arrLang['motor'] = '<?=$lang->system->motor?>';
	arrLang['velocidad'] = '<?=$lang->system->velocidad?>';
	arrLang['evento'] = '<?=$lang->system->evento?>';
	arrLang['rumbo'] = '<?=$lang->system->sentido?>';
	arrLang['consumo'] = "Consumo";
	arrLang['odometro'] = '<?=$lang->system->odometro?>';
	arrLang['ubicacion'] = '<?=$lang->system->ubicacion?>';
	arrLang['encendido'] = '<?=$lang->system->motor_encendido?>';
	arrLang['apagado'] = '<?=$lang->system->motor_apagado?>';
</script>

<ul id="btnExportar" class="float_r" style="display:none;">
<?php if(!tienePerfil(16)){?>
	<li id="export-menu" class="button">
    	<a href="javascript:;"><?=$lang->botonera->exportar?></a>
        <ul>
            <li><a href="javascript:;" onclick="javascript:generarInforme('export_historico_xls')">XLS</a></li>
            <li><a href="javascript:;" onclick="javascript:generarInforme('export_historico_kml')">KML</a></li>
        </ul>
	</li>
<?php }
else{?>
	<a class="button" href="javascript:;" onclick="javascript:generarInforme('export_historico_xls')"><?=$lang->botonera->exportar?></a>
<?php }?>    
</ul>

<a id="btnDetalleMapa" class="button float_r" style="margin-right:10px; display:none" href="javascript:historicoView()"><?=$lang->system->mapa?></a>
<a href="javascript:playVideo(0)" class="button float_r" id="btnPlayVideo" style="width:120px; display:none; margin-right:10px;"><?=$lang->botonera->ver_video?></a>
<div style="height:100%" class="contenido clear"> 
	
    <!---->
    <div id="infoPtos" class="claseInfoPunto" style="display:none;"></div>
	<div id="mapa-historico" style="display:none;"></div>
    <!---->
    
    <div id="detalle-historico">
    	<table width="100%" height="100%">
			<thead>
				<?php if(tienePerfil(16)){?>
				<tr>
                   	<td width="200">
                       	<span class="campo1"><?=$lang->system->movil?></span>
                	</td>
					<td>
                    	<span class="campo2"><?=$lang->system->evento?></span>
                        <br>
                        <span class="campo1"><?=$lang->system->ubicacion?></span>
                        <span class="campo2 float_r"><?=$lang->system->fecha?></span>
					</td>
					<td width="30" class="td-last">&nbsp;</td>
				</tr>
				<?php }
				else{?>
                <tr>
                	<td>
                    	<span class="campo1"><?=$lang->system->movil?></span>
                        <br>
                        <span class="campo2"><?=$lang->system->sentido?> - <?=$lang->system->velocidad?></span>
					</td>
					<td>
                    	<span class="campo1"><?=$lang->system->ubicacion?></span>
                        <br>
                        <span class="campo2"><?=$lang->system->evento?></span>
                        <span class="campo2 float_r"><?=$lang->system->fecha?></span>
					</td>
					<td class="td-last">&nbsp;</td>
				</tr>
                <?php }?>
			</thead>
			<tbody id="resultado">
               	<tr class="tr-last"><td colspan="4" class="td-last" style="text-align:center"><?=$lang->message->msj_utilice_buscador?></td></tr>
            </tbody>
		</table>
	</div><!-- fin. #detalle-historico -->
</div><!-- fin. contenido -->