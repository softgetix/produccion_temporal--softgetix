<script type='text/javascript' src='js/boxes.js'></script> 
<?php if(empty($popup)){?>
<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>
<?php } 
	$botonera_old = "";
?>
<div id="main" <?php if(isset($popup)){if($popup>0){$botonera_old = "_old";?>class="sinColIzq"<?php } }?>>
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
    <div class="mainBoxLICabezera">
		<h1><?=$lang->system->abm_grupoMoviles?></h1>
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
		<input name="hidId" id="hidId" type="hidden" value="<?php echo @$id; ?>" />
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<input name="hidMovilesSerializados" id="hidMovilesSerializados" type="hidden" />
		<?php require_once 'includes/botoneraABMs.php'?>
	</div><!-- fin. mainBoxLICabezera -->
    <?php 
		switch($operacion){
			case 'altaAsignacion':
			case 'modificarAsignacion':
				require_once 'abmGrupoMoviles/alta_mod.php'; 	
			break;
			default:
				require_once 'abmGrupoMoviles/'.$operacion.'.php'; 	
			break;	
	}?>
	</form>
</div>
