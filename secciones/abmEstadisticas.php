<div id="main" class="abmEstadisticas sinColIzq">
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<input name="hidId" id="hidId" type="hidden" />
    <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" />
     
    <div class="mainBoxLICabezera">
		<h1><?=$lang->menu->abmEstadisticas?></h1>
    </div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxAM">
        <br />
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_clientes?></span>
                <strong><?=$stats['clientes']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'clientes');"><?=$lang->botonera->exportar?></a>
        </fieldset>
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_licencias_vendidas?></span>
                <strong><?=$stats['licencias_vendidas']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'licencias_vendidas');"><?=$lang->botonera->exportar?></a>
        </fieldset>
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_licencias_vendidas_cliente?></span>
                <strong><?=$stats['licencias_vendidas_prom']?></strong>
            </div>
        </fieldset>
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_licencias_activas?></span>
                <strong><?=$stats['licencias_activas']?></strong> - <strong><?=$stats['licencias_activas_prom']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'licencias_activas');"><?=$lang->botonera->exportar?></a>
        </fieldset>
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_licencias_inactivas?></span>
                <strong><?=$stats['licencias_inactivas']?></strong> - <strong><?=$stats['licencias_inactivas_prom']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'licencias_inactivas');"><?=$lang->botonera->exportar?></a>
        </fieldset>
        
        <fieldset>
        	<div>
            	<span><?=$lang->system->cant_licencias_panicos_probados?></span>
                <strong><?=$stats['panicos_probados']?></strong> - <strong><?=$stats['panicos_probados_prom']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'panicos_probados');"><?=$lang->botonera->exportar?></a>
        </fieldset>
        <!--
        <fieldset>
        	<div>
            	<span><?php //=$lang->system->movileas_sin_reportar?></span>
                <strong><?php //=$stats['moviles_sin_reportar']?></strong>
            </div>
            <a href="javascript:enviar('export_xls', 'moviles_sin_reportar');"><?php //=$lang->botonera->exportar?></a>
        </fieldset>
        -->
    </div><!-- fin. #mainBoxAM-->
</form>
</div>