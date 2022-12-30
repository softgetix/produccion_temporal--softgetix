<?
if (!isset($sinColIzq)){ ?>
	<div id="colIzq">
		<? require_once('includes/datosColIzqAbm.php')?>
	</div>
<? } ?>
<script>
	arrLang['cant'] = '<?=$lang->system->cant?>';
	arrLang['max'] = '<?=$lang->system->max?>';
	arrLang['moviles'] = '<?=$lang->system->moviles?>';
	arrLang['geocercas'] = '<?=$lang->system->geocercas?>';
	arrLang['eventos'] = '<?=$lang->system->eventos?>';
	
	arrLang['alertas_txt_msg1'] = '<?=$lang->system->alertas_txt_msg1?>';
	arrLang['alertas_txt_msg2'] = '<?=$lang->system->alertas_txt_msg2?>';
	arrLang['alertas_txt_msg3'] = '<?=$lang->system->alertas_txt_msg3?>';
	arrLang['alertas_txt_msg4'] = '<?=$lang->system->alertas_txt_msg4?>';
	arrLang['alertas_txt_msg5'] = '<?=$lang->system->alertas_txt_msg5?>';
	arrLang['alertas_txt_msg6'] = '<?=$lang->system->alertas_txt_msg6?>';
	arrLang['alertas_txt_msg7'] = '<?=$lang->system->alertas_txt_msg7?>';
	arrLang['alertas_txt_msg8'] = '<?=$lang->system->alertas_txt_msg8?>';
	arrLang['alertas_txt_msg9'] = '<?=$lang->system->alertas_txt_msg9?>';
	arrLang['alertas_txt_msg10'] = '<?=$lang->system->alertas_txt_msg10?>';
	arrLang['alertas_txt_msg11'] = '<?=$lang->system->alertas_txt_msg11?>';
		
	arrLang['alertas_txt35'] = '<?=$lang->system->alertas_txt35?>';
	arrLang['alertas_txt37'] = '<?=$lang->system->alertas_txt37?>';
	arrLang['campo_nombre'] = '<?=str_replace('([NOMBRE_CAMPO])',$lang->system->nombre,$lang->message->interfaz_generica->msj_completar)?>'; 
		
	arrLang['prueba_ok'] = '<?=$lang->message->ok->pruebas?>'; 
	arrLang['prueba_error'] = '<?=$lang->message->error->pruebas?>'; 
</script>
<div id="main">
	<div class="mainBoxLICabecera">
    <h1><?=$lang->system->abm_reglas_alertas?></h1>
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" style="height:100%;">
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion;?>" />
        <input id="hidId" type="hidden" value="" name="hidId">
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <input type="hidden" id="idTipoEmpresa" value="<?=$_SESSION['idTipoEmpresa']?>" />
		<?php require_once "alertasxgeocerca/{$operacion}.php"; ?>
	</form>
	</div>
</div>
