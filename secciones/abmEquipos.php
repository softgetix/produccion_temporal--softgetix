<?php if (!$popup) { ?>
	<div id="colIzq">
		<?php require_once('includes/datosColIzqAbm.php')?>
	</div> 
<?php }
else{ // Defino array de palabras para msg de error de alta popup?>
	<script type="text/javascript">
    	arrLang['info_error'] = '<?=$lang->message->info_error?>';
	</script>	
<?php } ?>
<div id="main" <?php if($popup>0){?> class="sinColIzq" <?php }?>>
   <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   <div class="mainBoxLICabezera">
       <h1><?=$lang->system->abm_equipos?></h1>
       <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?php if (isset($_GET['action'])) { ?><?=($_GET['action'] == "popup") ? "guardarA" : ""?> <?php  } ?>" />
        <input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
        <input type="hidden" name="HidPopUp" id="HidPopUp" value="<?php if(isset($_GET['action']))echo "popup";?>" />
		<?php
		switch ($operacion){
			case 'listar':
				$esAbmEquipo = true;
				require_once 'includes/botoneraABMs.php';?>
   </div><!-- fin. mainBoxLICabezera -->             
		<div id="mainBoxLI">
        	<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
            	<tr class="titulo">
					<td width="4%"></td>
					<td width="25%" align="left"><?=$lang->system->identificador?></td>
                    <td width="20%" align="center"><?=$lang->system->marca?></td>
                    <td width="16%" align="center"><?=$lang->system->simcard?></td>
                    <td width="20%" align="center"><?=$lang->system->agente?></td>
                    <td width="10%" align="center"><?=$lang->system->telefono?></td>
                    <td width="10%" align="center">&nbsp;</td>
                </tr>
                <?php if($arrEntidades){
			      	for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			        	$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
				 	<tr class="<?=$class?>">
						<td>
                        	<?php if(tienePerfil(array(5,9,13,19))){?>
                            <input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['un_id']?>" value="<?=$arrEntidades[$i]['un_id']?>"/>
                        	<?php }?>
                        </td>
				        <td>
                        	<?php if(tienePerfil(array(5,9,13,19))){?>
                            <a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['un_id']?>)">
								<?=$arrEntidades[$i]['un_mostrarComo']?>
                            </a>
                            <?php }else{ echo $arrEntidades[$i]['un_mostrarComo'];}?>
                        </td>
				        <td align="center"><?=$arrEntidades[$i]['me_nombre']?></td>
				        <td align="center"><?=$arrEntidades[$i]['ug_simcard']?></td>
				        <td align="center"><?=$arrEntidades[$i]['cl_razonSocial']?></td>
				        <td align="center"><?=$arrEntidades[$i]['ug_telefono']?></td>
                        <td align="center">
                        	<?php 
							if($objPerfil->validarSeccion('abmEquiposTelemetria') && $arrEntidades[$i]['un_mod_id'] == 17){?>
                            <a href="javascript:mostrarPopup('boot.php?c=abmEquiposTelemetria&action=popup&idEquipo=<?=$arrEntidades[$i]['un_id']?>',600,250)" title="Telemetr&iacute;a">
                            	<img src="imagenes/raster/black/configuracion_16x16.png" border="0" />
                            </a>
                            <?php }?>
                        </td>
					</tr>
				<?php }
				include('secciones/footer_LI.php');
			}
			else{?>
				<tr class="filaPar">
					<td colspan="7" align="center"><i><?=$lang->message->sin_resultados?></i></td>
				</tr>
			<?php }?>
		</table>
	</div><!-- fin. #mainBoxLI -->
		<?php
	   	break;
		case 'alta':
			require_once 'includes/botoneraABMs.php';?>	
	</div><!-- fin. mainBoxLICabezera -->             		
	<hr />
		<div id="mainBoxAM" <?php if (!$popup) {?>style="overflow:scroll; height:485px;"<?php }?>>
			<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
        	<?php require_once 'includes/interfazGraficaABMs.php';?>
			<br>
			<fieldset>
				<legend><?=$lang->system->entradas?></legend>
			    <table width="100%">
				<?php for($i=0;$i < CantidadEntradasXEquipo && CantidadEntradasXEquipo;$i++){?>
					<tr>
						<td align="right" valign="middle" height="20"><?=($i+1)?>&nbsp;&nbsp;</td>
						<td style="text-align:left;" width="80%">
							<select name="cmbEntrada<?=$i?>" id="cmbEntrada<?=$i?>" style="width:304px;">
								<option value=0><?=$lang->system->seleccione?></option>	
								<?php for($j=0; $j < count($arrEntradas) && $arrEntradas; $j++){?>										
									<option value="<?=$arrEntradas[$j]["id"]?>"><?=decode($arrEntradas[$j]["dato"])?></option>
								<?php }?>
							</select>	
						</td>
					</tr>
				<?php }?>
				</table>			
			</fieldset>
            <?php if ($popup){?><br /><?php }?>
		</div><!-- fin. #mainBoxAM -->
		<?php
		break;
	   	case 'modificar':
			require_once 'includes/botoneraABMs.php';
		?>	
	</div><!-- fin. mainBoxLICabezera -->                     		
	<hr>
		<div id="mainBoxAM">
			<?php require_once 'includes/interfazGraficaABMs.php';?>
			<br />
            <fieldset>
                <legend><?=$lang->system->entradas?></legend>
                <table width="100%">
                <?php for($i=0;$i < CantidadEntradasXEquipo && CantidadEntradasXEquipo;$i++){
                    $numeroEntrada = $arrEntradasEquipo[$i]["ee_numeroEntrada"];
                    $idEntrada = $arrEntradasEquipo[$i]["ee_id_entrada"];
                    $cont = $i+1;
                ?>
                    <tr>
                        <td align="right" valign="middle" height="20"><?='('.$cont.') '.$numeroEntrada;?>&nbsp;&nbsp;</td>
                        <td style="text-align:left;" width="80%">
                            <select name="cmbEntrada<?=$cont?>" id="cmbEntrada<?=$cont?>" style="width:304px;">
                                <option value=0><?=$lang->system->seleccione?></option>	
                                <?php for($j=0; $j < count($arrEntradas) && $arrEntradas; $j++){?>										
                                <option value="<?=$arrEntradas[$j]["id"]?>" <?=($arrEntradas[$j]["id"]==$idEntrada)? "selected":"";?>><?=decode($arrEntradas[$j]["dato"])?></option>
                                        <?php }?>
                            </select>	
                        </td>
                    </tr>
                <?php }?>
                </table>			
			</fieldset>
		</div><!-- fin #mainBoxAM -->
		<script>
			obtenerModelos(<?=$arrEntidades[0]["un_me_id"]?>,<?=$arrEntidades[0]["un_mod_id"]?>)
		</script>
		<?php
	   	break;
	}?>
	</form>
</div>
