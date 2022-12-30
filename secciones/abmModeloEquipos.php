<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>
<div id="main">
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   <div class="mainBoxLICabezera">
       <h1><?=$lang->system->abm_modelo_equipos?></h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
        <input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch ($operacion){
			case 'listar':
				require_once 'includes/botoneraABMs.php';
		?>
	</div><!-- fin. mainBoxLICabezera -->        
	<div id="mainBoxLI">		
		<table class="widefat">
			<tr class="titulo">
				<td width="20px"></td>
			    <td width="150px"><?=$lang->system->nombre?></td>
			    <td width="150px"><?=$lang->system->marca?></td>
			    <td width="150px"><?=$lang->system->descripcion?></td>
				<td width="150px"><?=$lang->system->usuario?></td>
				<td width="150px"><?=$lang->system->usuario_mc?></td>
			</tr>
            <?php if($arrEntidades){
				for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			    	$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
					<tr class="<?=$class?>">
                        <td>
                            <input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['mo_id']?>" value="<?=$arrEntidades[$i]['mo_id']?>"/>
                        </td>
                        <td>
                            <a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['mo_id']?>)">
                                <?=htmlspecialchars($arrEntidades[$i]['mo_nombre'], ENT_QUOTES) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($arrEntidades[$i]['me_nombre'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($arrEntidades[$i]['mo_descripcion'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($arrEntidades[$i]['us_nombreUsuario'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($arrEntidades[$i]['mo_usuario_db'], ENT_QUOTES) ?></td>
					</tr>
				<?php }
			}
			else{?>
				<tr class="filaPar">
					<td colspan="6" align="center"><i><?=$lang->message->sin_resultados?></i></td>
				</tr>
			<?php }?>
		</table>
	</div><!-- fin. #mainBoxLI -->
		<?php
	   	break;
		case 'alta':
			require_once 'includes/botoneraABMs.php';
		?>			
	</div><!-- fin. mainBoxLICabezera -->  
	<hr />
		<div id="mainBoxAM">
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
			<?php require_once 'includes/interfazGraficaABMs.php'?>
		</div><!-- fin. #mainBoxAM -->
		<?php
		break;
	   	case 'modificar':
			require_once 'includes/botoneraABMs.php';
		?>			
	</div><!-- fin. mainBoxLICabezera -->  
	<hr />
		<div id="mainBoxAM">
		<?php require_once 'includes/interfazGraficaABMs.php'?>
		</div><!-- fin. #mainBoxAM -->
		<?php
	   	break;
	}?>
	</form>
</div>