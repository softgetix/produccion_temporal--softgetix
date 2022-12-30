<div id="main" class="sinColIzq">
    <div class="mainBoxLICabecera">
        <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
           	<script type="text/javascript">
				arrLang['guardar_datos_error'] = '<?=$lang->message->error->guardar_datos_error?>';
				arrLang['procesar_datos_error'] = '<?=$lang->message->error->procesar_datos?>';
				arrLang['movil_recomendado'] = '<?=$lang->system->movil_recomendado?>';
				arrLang['otros_moviles'] = '<?=$lang->system->otros_moviles?>';
				arrLang['avanzada'] = '<?=$lang->system->avanzada?>';
				arrLang['asignar_conductor_movil'] = '<?=$lang->system->asignar_conductor_movil?>';
				arrLang['msj_viajes_motivo_cambio'] = '<?=$lang->system->motivos_cambio?>';
				arrLang['msj_baja_viaje'] = '<?=$lang->message->msj_baja_viaje?>';
			</script>
			
            <div class="esp">
                <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion?>" />
                <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
                <input name="hidId" id="hidId" type="hidden" value="<?=isset($_POST['hidId'])?$_POST['hidId']:'';?>" />
                <input name="hidSolapa" id="hidSolapa" type="hidden" value="<?=isset($_POST['hidSolapa'])?$_POST['hidSolapa']:'';?>" />
            </div>
            <?php 
                $banAgente = array(12481,17925);
                if(($operacion == 'alta' || $operacion == 'modificar') && in_array($_SESSION['idAgente'], $banAgente)){
                    require_once "viajes/alta_MSC.php";
                }
                else{
                    require_once "viajes/{$operacion}.php";
                }
            ?>
        </form>
    </div>
</div>