<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>

<div id="main">
   <div class="mainBoxLICabezera">
   <h1><?="Administraci&oacute;n de motivos de cambios en viajes"?></h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<?php
	switch ($operacion){
		case 'listar':
			require_once 'includes/botoneraABMs.php';
?>						
		</div>
			<div id="mainBoxLI">
			   <table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
					<tr class="titulo">
						<td width="4%"></td>
						<td width="96%" align="left"><b><?=$lang->system->descripcion?></b></td>			        
					</tr>
					<?php
			      	if($arrEntidades){
						for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			        		$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
					   	<tr class="<?=$class?>">
					        <td><input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['vmc_id']?>" value="<?=$arrEntidades[$i]['vmc_id']?>"/></td>
					        <td><a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['vmc_id']?>)"><?=$arrEntidades[$i]['vmc_descripcion']?></a></td>
					    </tr>
					<?php }
					}
					else{?>
					   	<tr class="filaPar">
					         <td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
					    </tr>
					<?php }?>
			   </table>
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
				<?php require_once 'includes/interfazGraficaABMs.php'?>
			</div>
<?php
		   break;
	   	case 'baja':
		break;
	   	case 'modificar':
			require_once 'includes/botoneraABMs.php';?>			
			</div>
			<hr>
			<div id="mainBoxAM">
				<?php require_once 'includes/interfazGraficaABMs.php'?>
			</div>
<?php
	   	break;
}
?>
	</form>
</div>