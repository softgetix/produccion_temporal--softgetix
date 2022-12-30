<script type="text/javascript">
    arrLang['confirmar'] = '<?=$lang->botonera->confirmar?>';
	arrLang['cancelar'] = '<?=$lang->botonera->cancelar?>';
</script>
<div id="main" class="sinColIzq">
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
		<h1><?=$lang->system->alertas?> (<span id="cantidad_de_alertas">0</span>)</h1>
	</div><!-- fin. mainBoxLICabezera -->
   <div id="mainBoxLI">
	<br />
    	<table id="contenidoGrilla" cellpadding="0" cellspacing="0" border="0" class="widefat">
		<thead>
        	<tr class="titulo">
        		<td width="10%"><?=$lang->system->movil?></td>
                <td width="5%" ><?=$lang->system->sentido?></td>
                <td width="5%" ><?=$lang->system->velocidad?></td>
                <td width="10%"><?=$lang->system->recibido?></td>
                <td width="10%"><?=$lang->system->generado?></td>
                <td width="25%"><?=$lang->system->alertas?></td>
                <td width="5%" ><?=$lang->system->ocurrencias?>&nbsp;&nbsp;</td>
                <td width="20%"><?=$lang->system->ubicacion?></td>
                <td width="20%"><?=$lang->system->telefono?></td>
                <td width="5%" >-</td>
        	</tr>
        </thead>
        <tbody>
        	<!-- datos dinamicos -->
        </tbody>
        </table>
    </div>
</form>
</div>
<div id="play-alarma-ie"></div><!-- necesario para reproducir alarmas en IE -->

<div id="dlgConfirmarAlerta" title="Confirmar alerta" style="display:none">
    <table>
        <tr>
            <td style="white-space: nowrap;">
                <?=$lang->message->msj_rastreo_alertas?>:
            </td>
            <td>
                <select id="cmbMotivoConfirmacion"><?php
                    foreach($arrMotivosConfirmacion as $motivo ){
                        echo '<option value="'.$motivo['mc_id'].'">'.$motivo['mc_descripcion'].'</option>';
                    }?>
                </select>
            </td>
        </tr>
    </table>
    
    <input type="hidden" id="alerta_id" value="" />
    <input type="hidden" id="alerta_ids" value="" />
</div>