<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>

<div id="main">
   <div class="mainBoxLICabezera">
   <h1><?=$lang->system->abm_referencias_trafico?></h1>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
   	<input name="hidId" id="hidId" type="hidden" value="<?=@$id?>" />
   	<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
   	<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
<?php
	switch ($operacion){
		case 'listar':
			require_once 'includes/botoneraABMs.php';
?>
			
		<div id="mainBoxLI">
			<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros">
			      <tr class="titulo">
			         <td width="20px"></td>			         
					 <td width="110px" align="center"><b><?=$lang->system->tipo_camino?></b></td>
					 <td width="110px" align="center"><b><?=$lang->system->tipo_movil?></b></td>
					 <td width="110px" align="center"><b><?=$lang->system->vel_min_congestionado?></b></td>
					 <td width="110px"><b><?=$lang->system->vel_max_congestionado?></b></td>
			      </tr>
			</table>
			<div style="overflow:scroll; height:500px;">
				<table class="widefat">
			
<?php
			      if($arrEntidades){
			      	for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			        		$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
?>
					   	<tr class="<?=$class?>">
					         <td width="20px"><input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['rt_id']?>" value="<?=$arrEntidades[$i]['rt_id']?>"/></td>
					         <td width="110px" align="center"><a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['rt_id']?>)"><?=$arrEntidades[$i]['tc_descripcion']?></a></td>
					         <td width="110px" align="center"><?=$arrEntidades[$i]['tv_nombre']?></td>
							 <td width="110px" align="center"><?=$arrEntidades[$i]['rt_value_min']?></td>
							 <td width="110px" align="center"><?=$arrEntidades[$i]['rt_value_max']?></td>
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
			</div>
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