<div style="height:100%" class="contenido clear"> 
	
    <?php $objInformes->vistaListadoMoviles($arrMovilesUsuario);?>
	
	<fieldset style="width:350px; margin:20px 0 0 50px;">
    	<?php 
			$msg = $lang->message->msj_export_informes_avanzado; 
			$msg = str_replace('[SOLAPA]', $lang->system->correos_enviados, $msg);
			$msg = str_replace('[FECHA_INI]', '<strong id="msg_fecha_desde">'.$fecha.'</strong>', $msg);
			$msg = str_replace('[FECHA_FIN]', '<strong id="msg_fecha_hasta">'.$fecha.'</strong>', $msg);
		?>
        <p><?=$msg?>.</p>
        <br /><br />
    	<a href="javascript:;" onclick="javascript:generarInforme('export_mails_enviados_xls')" style="width:200px;" class="button_xls exp_excel">
			<?=$lang->botonera->exportar_excel?>
		</a>
    </fieldset>	
        
   	<span class="clear"></span>
</div><!-- fin. contenido -->