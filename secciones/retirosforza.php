<div id="main" class="sinColIzq">
    <div class="mainBoxLICabecera">
        <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
           	<script type="text/javascript">
				arrLang['guardar_datos_error'] = '<?=$lang->message->error->guardar_datos_error?>';
				arrLang['procesar_datos_error'] = '<?=$lang->message->error->procesar_datos?>';
				arrLang['movil_recomendado'] = '<?=$lang->system->movil_recomendado?>';
				arrLang['otros_moviles'] = '<?=$lang->system->otros_moviles?>';
				arrLang['avanzada'] = '<?=$lang->system->avanzada?>';
				arrLang['msj_viajes_motivo_cambio'] = '<?=$lang->system->motivos_cambio?>';
				arrLang['msj_baja_viaje'] = '<?=$lang->message->msj_baja_viaje?>';
				arrLang['asignar_movil'] = '<?=$lang->system->asignar_movil?>';
			</script>
			
            <!---->
			<?php if($operacion == 'listar'){?>
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
					google.load('visualization', '1', {packages: ['corechart']});
			</script>		
            <?php } ?>
            <!---->
            
            <div class="esp">
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidId" id="hidId" type="hidden" value="<?=isset($_POST['hidId'])?$_POST['hidId']:'';?>" />
                <input name="hidSolapa" id="hidSolapa" type="hidden" value="<?=isset($_POST['hidSolapa'])?$_POST['hidSolapa']:'';?>" />
                <input type="hidden" name="id_motivo_cambio" id="id_motivo_cambio"/>
            </div>
            <?php
            $type = 'retiros';
            require_once "forza/{$operacion}.php";
            ?>
        </form>
    </div>
</div>