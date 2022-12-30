<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php');?>
</div>
<div id="main">
   <div class="mainBoxLICabezera">
   <h1>Administraci&oacute;n de Perfiles</h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?php echo @$id; ?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
	<?php
	if($operacion == 'alta' || $operacion == 'modificar'){
		require_once "{$seccion}/alta.php"; 	
	}
	else{
		require_once "{$seccion}/{$operacion}.php";
	}
	?>
	</form>
</div>
