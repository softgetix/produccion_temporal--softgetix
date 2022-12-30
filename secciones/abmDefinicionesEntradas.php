
<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>

<div id="main">
   <div class="mainBoxLICabezera">
   <h1>Definici&oacute;n de Entradas</h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?php if (isset($id)) { echo $id; } ?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<?php
	switch ($operacion){
		case 'listar':
			require_once 'includes/botoneraABMs.php';
?>
			
			<table class="widefat">
			      <tr class="titulo">
			         <td width="20px"></td>
			         <td align="center"><?=$lang->system->descripcion?></td>
			      </tr>
			</table>
			<div id="mainBoxLI">
			   <table class="widefat">
<?php
			      if($arrEntidades){
			      	for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			        		$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
?>
					   	<tr class="<?=$class?>">
					         <td width="20px"><input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['en_id']?>" value="<?=$arrEntidades[$i]['en_id']?>"/></td>
					         <td align="center"><a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['en_id']?>)"><?=$arrEntidades[$i]['en_descripcion']?></a></td>
					      </tr>
<?php
						}
					}else{
?>
					   	<tr class="filaPar">
					         <td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
					      </tr>
<?php					
					}
?>
			   </table>
			   <hr>
			</div>
			
<?php
	   	break;
		case 'alta':
			require_once 'includes/botoneraABMs.php';
			
?>			
			</div>
			<hr>
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
			<div id="mainBoxAM">
<?php
				require_once 'includes/interfazGraficaABMs.php';
?>
			</div>
<?php
		   break;
	   case 'baja':

	   	break;
	   case 'modificar':
			require_once 'includes/botoneraABMs.php';
?>			
			</div>
			<hr>
			<div id="mainBoxAM">
<?php
				require_once 'includes/interfazGraficaABMs.php';
?>
			</div>
<?php
	   	break;
}
?>
	</form>
</div>