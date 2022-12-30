<!--
<script type='text/javascript' src='js/defaultMap_Google.js'></script>
-->
<script type="text/javascript">
	var polyPoints = new Array();
</script>

<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
		<h1>Log Gateway</h1>
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI">
	<br />
    <table cellpadding="0" cellspacing="0" border="0" class="widefat">
		<tr>
			<td valign="middle" height="20" align="right">IMEI&nbsp;&nbsp;</td>
            <td><input type="text" name="imei" id="imei" style="width:250px;"/></td>
        </tr>
        <input type="hidden" name="equipo" id="equipo" value="bb50025" />
        <!--
        <tr>
			<td valign="middle" height="20" align="right">Equipo&nbsp;&nbsp;</td>
            <td>
            	<select name="equipo" id="equipo" style="width:250px;" />
            		<option value="bb50025">Black Berry / Android</option>
                    <option value="Nokia">Nokia</option>
                </select>
            </td>
        </tr>
        -->	
        <tr>
			<td valign="middle" height="20" align="right">Fecha&nbsp;&nbsp;</td>
            <td><input type="text" name="fecha" id="fecha" class="date" value="<?=date('d-m-Y')?>" style="width:90px;" /></td>
        </tr>	
        <tr>
			<td valign="middle" height="20">&nbsp;</td>
            <td><a class="button extra-wide colorin" style="margin:0 0 10px 135px; width:90px;" href="javascript:getLogGateway()">Buscar</a></td>
        </tr>    
	</table>
    <!-- Inicio. solapas -->
    <div class="solapas gum clear">
		<a href="javascript:setSolapas('mapa')" class="izquierda float_l active" id="solapa-mapa">Mapa</a>
        <a href="javascript:setSolapas('log')" class="izquierda float_l" id="solapa-log">Log</a>
        <div class="float_l" style=" line-height:30px; margin-left:20px;" id="datos-movil"></div> 
        <div class="contenido clear" style="height:100%">
            <div id="listado-log" class="contenido-solapa" style="display:none;">
            	<!-- -->
                <div id="ResultadoLog"><span class="filaPar" style="text-align:center">No se encontraton resultados</span></div>    
                <!-- -->
            </div>
            <div id="listado-mapa" class="contenido-solapa">
            	<span id="rueda-carga"></span>
            	<!-- -->
                <style>
					fieldset{border:none; margin-bottom:10px;}
					fieldset.stats span{display:inline-block; width:12px; height:12px; margin:0 4px 0 30px;}
					fieldset.stats span.gps{background:#FF0000;}
					fieldset.stats span.antena{background:#F5A7EF;}
					fieldset.stats span.historico{background:#605FF6;}
					fieldset.stats span.gps-historico{background:#66019A;}
				</style>
                <br />
				<fieldset class="stats">
				<span class="gps"></span>Log - GPS&nbsp;<label id="cant-gps"></label>
				<span class="antena"></span>Log - Cobertura Celular&nbsp;<label id="cant-antenas"></label>
				<span class="historico"></span>Base - Historico&nbsp;<label id="cant-historico"></label>
				
				<span class="gps-historico"></span>Coincidencias: GPS e Historico
				</fieldset>
				<div id="mapa" align="center" style="width:100%; height:500px; margin-left:5px;"></div>
            	<!-- -->
            </div>   
        </div>
	</div>
    <!-- Fin. solapas -->    
	</div>
</form>