<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
	<div class="mainBoxLICabezera">
		<h1>Log Panico</h1>
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI" style="overflow:hidden">
        <br />
        <table cellpadding="0" cellspacing="0" border="0" class="widefat">
            <tr>
                <td valign="middle" height="20" align="right">M&oacute;vil&nbsp;&nbsp;</td>
                <td><input type="text" name="movil" style="width:250px;" value="<?=$_POST['movil']?>" /></td>
                <td>
                    <fieldset>
                        <label>SMS</label>
                        <input type="radio" name="medio" value="1" <?=($_POST['medio'] == 1)?'checked="checked"':''?>/>
                    </fieldset>
                    <fieldset>
                        <label>GPRS</label>
                        <input type="radio" name="medio" value="0" <?=($_POST['medio'] == 0)?'checked="checked"':''?>/>
                    </fieldset>
                    <fieldset>
                        <label>Todo</label>
                        <input type="radio" name="medio" value="" <?=(!$_POST || $_POST['medio'] == '')?'checked="checked"':''?>/>
                    </fieldset>
                </td>
                <td valign="middle" height="20" align="right">Fecha&nbsp;&nbsp;</td>
                <td><input type="text" name="fecha" class="date" style="width:75px;" value="<?=isset($_POST['fecha'])?$_POST['fecha']:date('d-m-Y')?>" /></td>
                <td><a class="button extra-wide colorin" style="margin:0 0 10px 135px; width:90px;" href="javascript:$('#frm_<?=$seccion?>').submit()">Buscar</a></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="3">&nbsp;</td>
            	<td valign="middle" height="20" align="right">Refresco</td>
                <td width="300">
                	<fieldset style="margin:0 0 0 20px;">
                        <label>Manual</label>
                        <input type="radio" name="refresh" value="0" <?=($_POST['refresh'] != 1)?'checked="checked"':''?> onchange="javascript:changeRefresh(this.value)"/>
                    </fieldset>
                    <fieldset>
                        <label>Autom&aacute;tico</label>
                        <input type="radio" name="refresh" value="1" <?=($_POST['refresh'] == 1)?'checked="checked"':''?> onchange="javascript:changeRefresh(this.value)"/>
                    </fieldset>
                </td>
            </tr>    
            <tr><td colspan="7">&nbsp;</td></tr>
        </table>
	</div>
    <div id="mainInformes">
		<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
			<tr class="titulo">
				<td width="20"></td>
                <td width="20">##</td>
				<td width="60">Ticket</td>
                <td width="150">M&oacute;vil</td>
				<td width="100">IMEI</td>
                <td width="80">Tecnolog&iacute;a</td>
				<td width="80">Medio</td>
				<td width="100">Lat - Lng</td>
                <td width="250">Nomenclado</td>
                <td width="150">Wifi</td>
                <td width="80">Estado</td>
				<td width="100">Fecha Recibido</td>
			</tr>	
                <?php 
				if($arr_log){
					$i = 0;
					$nuevaAlertaID = 0;
					foreach($arr_log as $item){
						$i++;
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
						$nuevaAlertaID = ($nuevaAlertaID < $item['hp_id'])?$item['hp_id']:$nuevaAlertaID;?>
					   	
                        <tr class="<?=$class?> <?=($item['hp_evento'] != 6)?'fuera-zona':''?> <?=($item['hp_id'] > (int)$_POST['nuevaAlertaID'] && !empty($_POST['nuevaAlertaID']))?'nueva-alerta':''?>">
					        <td>&nbsp;</td>
					        <td style="text-align:center"><?=$numRows=$numRows-1?></td>
                            <td style="text-align:center"><?=$item['hp_ticket']?></td>
                            <td align="left"><?=encode($item['mo_matricula'])?></td>
                            <td style="text-align:center"><?=$item['un_nro_serie']?></td>
							<td style="text-align:center"><?=$item['hp_tecnologia']?></td>
							<td style="text-align:center"><?=($item['hp_medio'] === 1)?'SMS':'GPRS'?></td>
							<td><?=$item['hp_latitud']?>,<?=$item['hp_longitud']?></td>
                            <td><?=encode($item['hp_nomenclado'])?></td>
                            <td><?=$item['hp_wifi_name']?><br /><?=$item['hp_wifi_mac']?></td>
							<td style="text-align:center"><?=($item['hp_evento'] == 6)?'Dentro de Zona':'Fuera de Zona'?></td>
                            <td style="text-align:center"><?=date('d-m-Y H:i',strtotime($item['hp_fecha_recibido']))?></td>
						</tr>
					<?php }?>
				<?php }
				else{?>
					<tr class="filaPar">
						<td width="100%" colspan="12" style="text-align:center"><?=($_POST)?$lang->message->sin_resultados:'Seleccione la opci&oacute;n a filtrar'?></td>
					</tr>
				<?php }?>
			</table>
		<div>
        <input type="hidden" name="nuevaAlertaID" value="<?=(int)$nuevaAlertaID?>" />
</form>

<script language="javascript">
	var timer;
	if(<?=(int)$_POST['refresh']?> == 1){
		timer = setTimeout("$('#frm_<?=$seccion?>').submit()",5000);
	}
	
	function changeRefresh(valor){
		if(valor == 1){
			timer = setTimeout("$('#frm_<?=$seccion?>').submit()",5000);
		}
		else{
			clearTimeout(timer);	
		}	
	}
	
$(function() {	
	//$(".date").datepicker({});
	$(".date").live("focusin", function() { 
       $(this).datepicker({
           /* onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
                var fecha = fecha.replace('/','-');
				$(this).val(fecha);
            }*/
        });
    });
});
</script>