<?php if (!$popup) { ?>
	<div id="colIzq">
		<?php require_once('includes/datosColIzqAbm.php'); ?>
	</div> 
<?php } 
	$botonera_old = "";
?>
<div id="main" <?php if (isset($popup)) { if ($popup>0) { $botonera_old = "_old"; ?> class="sinColIzq" <?php } } ?>>
	<form enctype="multipart/form-data" name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
    <div class="mainBoxLICabezera">
		<h1><?=$lang->menu->abmMoviles?></h1>
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=($_GET['action'] == "popup")?"guardarM":$operacion?>" />
		<input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
		<input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<input type="hidden" name="HidPopUp" id="HidPopUp" value="<?php if(isset($_GET['action']))echo "popup";?>" />
		<?php
        switch ($operacion){
            case 'listar':
                require_once 'includes/botoneraABMs.php';
        ?>
    </div><!-- fin. mainBoxLICabezera -->            
		<div id="mainBoxLI">
            <table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
                <tr class="titulo">
                    <td width="5%" >&nbsp;</td>
                    <td width="15%" align="left"><?=$lang->system->identificador?></td>
                    <td width="15%" align="left"><?=$lang->system->matricula?></td>
                    <td width="10%" align="center"><?=$lang->system->marca?>/<?=$lang->system->modelo?></td>
                   	<td width="8%" align="center"><?=$lang->system->tipo_movil?></td>
                    <td width="20%" align="left"><?=$lang->system->cliente?></td>
                    <td width="15%" align="center"><?=$lang->system->unidad?></td>
                    <td width="10%" align="center"><?=$lang->system->ultimo_reporte_recibido?></td>
                    <td width="2%" >&nbsp;</td>
                </tr>
				<?php if($arrEntidades){
					for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
				?>
                <tr class="<?=$class?>">
					<td align="center">
                        <?php if(tienePerfil(array(5,9,13,19))){?>
                        <input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['mo_id']?>" value="<?=$arrEntidades[$i]['mo_id']?>"/>
                    	<?php }?>
                    </td>
                    <td>
                    	<?php if(tienePerfil(array(5,9,13,19))){?>
                    	<a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['mo_id']?>)"><?=$arrEntidades[$i]['mo_identificador']?></a>
                    	<?php }else{ echo $arrEntidades[$i]['mo_identificador'];}?>
                    </td>
                    <td><?=$arrEntidades[$i]['mo_matricula']?></td>
                    <td align="center">
						<?php if(trim($arrEntidades[$i]['mo_marca']) == true && trim($arrEntidades[$i]['mo_modelo']) == true){
							echo $arrEntidades[$i]['mo_marca'].' / '.$arrEntidades[$i]['mo_modelo'];
						}
						else{
							echo trim($arrEntidades[$i]['mo_marca'].' '.$arrEntidades[$i]['mo_modelo']);	
						}
						?>
					</td>
                    <td align="center"><?=$lang->system->$arrEntidades[$i]['tv_nombre']?$lang->system->$arrEntidades[$i]['tv_nombre']->__toString():$arrEntidades[$i]['tv_nombre']?></td>
                    <td><?=$arrEntidades[$i]['cl_razonSocial']?> 
                    	<?php if(!empty($arrEntidades[$i]['cl_email'])){ echo '<br>'.$arrEntidades[$i]['cl_email'];}?>
                    </td>
                    <td align="center"><?=$arrEntidades[$i]['un_mostrarComo']?></td>
                    <td align="center"><?=formatearFecha($arrEntidades[$i]['sh_fechaRecepcion'])?></td>
                    <td>&nbsp;</td>
                </tr>
			<?php }
				$colspan = 9;
				include('secciones/footer_LI.php');
			}
			else{?>
				<tr class="filaPar">
					<td colspan="9" align="center"><i><?=$lang->message->sin_resultados?></i></td>
				</tr>
			<?php }?>
		</table>
	</div><!-- fin. #mainBoxLI -->	
	<?php
	break;
	case 'alta':
		require_once 'includes/botoneraABMs.php';?>
    </div><!-- fin. mainBoxLICabezera -->       
        <hr>
		<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
		<div id="mainBoxAM">
			<?php require_once 'includes/interfazGraficaABMs.php';?>
			<table align="center" width="100%">
				<tr>
            		<td valign="middle" align="right" height="20"><?=$lang->system->fecha_activacion?>&nbsp;&nbsp</td>
                	<td style="text-align:left;" width="80%">
                		<input type="text" name="txtActivacion" id="txtActivacion" style="width:300px;" size="15">
                	</td>
				</tr>
				<?php if(!isset($_GET['action'])) {?>
            	<tr>
                    <td align="right" height="20"><?=$lang->system->equipo_instalado?>&nbsp;&nbsp;</td>
                    <td style="text-align:left;" width="80%">
                        <select name="equipo_instalado" id="equipo_instalado" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($equipos as $item){?>
                                <option value="<?=$item['un_id']?>" ><?=$item['equipo']?></option>
                            <?php }?>
                        </select>
                    </td>
				</tr>
                <tr>
                    <td align="right" height="20">&nbsp;&nbsp;&nbsp;<?=$lang->system->imagen?></td>
                    <td style="text-align:left;" width="80%"><input type="file" name="foto2" /></td>
                </tr>
			<?php }?>
		</table>
        <input type="hidden" name="cmbDistribuidor" id="cmbDistribuidor" value="<?php echo $_SESSION["idEmpresa"]?>" />
	</div><!-- fin. #mainBoxAM -->
	<?php
	break;
	case 'modificar':
		require_once 'includes/botoneraABMs.php';?>
	</div><!-- fin. mainBoxLICabezera -->       
        <hr>
		<div id="mainBoxAM">
		<script>
			<?php if (isset($recargarAlCerrar) && $recargarAlCerrar === true): ?>
			var recargar = true;
			<?php else: ?>
			var recargar = false;
			<?php endif; ?>
		</script>
		<?php require_once 'includes/interfazGraficaABMs.php';
		if(!isset($_GET['action'])) {?>
			<br />
            <fieldset>
			<legend>Configuraci&oacute;n Conductor</legend><br />
            <table align="center" width="100%">
				<tr>
					<td valign="middle" height="20" align="right"><?=$lang->system->conductor?></td>
					<td width="80%" style="text-align:left;">
						<select id="cmbConductor" style="width:304px;" name="cmbConductor">
                            <option value=""><?=$lang->system->seleccione?></option>
                        </select>
                </tr>
            </table>
            </fieldset>
			<?php if(tienePerfil(19)){?>
                <fieldset>
                <legend>Configuraci&oacute;n Equipo</legend>
                <table align="center" width="100%">
                    <tr>
                        <td align="right" height="20"><?=$lang->system->equipo_instalado?>&nbsp;&nbsp;</td>
                        <td style="text-align:left;" width="80%">
                            <select name="equipo_instalado" id="equipo_instalado" style="width:304px;">
                                <option value=""><?=$lang->system->seleccione?></option>
                                <?php foreach($equipos as $item){?>
                                    <option value="<?=$item['un_id']?>" <?php if($item['un_mo_id'] == $id){?>selected="selected"<?php }?>><?=$item['equipo']?></option>
                                <?php }?>
                            </select>
                            <input type="hidden" name="equipo_viejo" id="equipo_viejo" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <?php }?>
        <?php } ?>
		<input type="hidden" name="cmbDistribuidor" id="cmbDistribuidor" value="<?=$_SESSION["idEmpresa"]?>" />
        <input type="hidden" id="hidden_cliente_factuar" value="<?=$arrEntidades[0]['mo_id_cliente_facturar']?>" />
        <input type="hidden" id="hidden_conductor" value="<?=$arrEntidades[0]['mo_co_id_primario']?>" />
        </div><!-- fin. #mainBoxAM -->
		<?php
		break;
	}?>
	</form>
</div>


<?php 