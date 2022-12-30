<form name="frm_<?=$seccion_job?>" id="frm_<?=$seccion_job?>" action="?c=<?=$seccion_job?>" method="post">

<div class="mainBoxLICabezera">
<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion_job?>" />   
<input name="action" type="hidden" value="<?=$_REQUEST['action']?>" />   
                            
<?php require_once 'includes/botoneraABMs.php';?>     
</div>	
<hr />
<div id="popup-content">
	<div style="padding:10px 20px;">
        <p>Seleccione una de las siguientes opciones para programar el cotejamiento de los puntos de entrega que no se hayan detectado ingresos y/o egresos con el hist&oacute;rico de movimientos del veh&iacute;culo asigando para cada caso.</p>
        <p>Una vez finalizado el procesamiento de los datos, se le informar&aacute; mediante un correo electr&oacute;nico el resultado el proceso solicitado.</p>
    	<br />
    	<strong>Programar el an&aacute;lisis retroactivo para:</strong>
    	<div style="margin:10px 0 0 30px;">
        	<?php foreach($arrAux as $item){?>
				<span class="clear" style="height:3px;"></span>
                <input type="hidden" name="desde_<?=$item['value']?>" value="<?=$item['desde']?>" />
                <input type="hidden" name="hasta_<?=$item['value']?>" value="<?=$item['hasta']?>" />
                <input type="radio" name="procesar" id="<?=$item['value']?>" value="<?=$item['value']?>"  class="float_l" />
                <label for="<?=$item['value']?>" class="float_l" style="line-height:20px; margin-left:4px;"><?=$item['option']?> (<?=formatearFecha($item['desde'],'date')?> al <?=formatearFecha($item['hasta'],'date')?>)</label>
			<?php }?>
        </div>
        <span class="clear"></span>
        <br />
        <center>
        	<a class="button colorin" href="javascript:enviar('procesarViajes');">PROGRAMAR</a>   
        </center>
    </div>
</div>
