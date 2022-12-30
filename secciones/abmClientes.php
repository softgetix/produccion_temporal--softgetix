<div id="colIzq">
	<?php require_once('includes/datosColIzqAbm.php')?>
</div>
<div id="main">
	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
   	<div class="mainBoxLICabezera">
   		<h1><?=$lang->system->abm_clientes?></h1>
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
        <input name="hidId" id="hidId" type="hidden" value="<?=$id?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch ($operacion){
			case 'listar':
			require_once 'includes/botoneraABMs.php';			
		?>
	</div><!-- fin mainBoxLICabezera -->
		<div id="mainBoxLI">
			<table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
			      <tr class="titulo">
					<td width="4%" align="center"></td>
			        <td width="26%" align="left"><?=$lang->system->razon_social?></td>				
					<td width="16%" align="center"><?=$lang->system->telefono?></td>				
					<td width="20%" align="center"><?=$lang->system->email?></td>				
					<td width="20%" align="center"><?=$lang->system->tipo_cliente?></td>
					<td width="4%" align="center"><?=$lang->system->habilitado?></td>
				  </tr>
                  <?php if($arrEntidades){
				  				  
			      	for($i=0;$i < count($arrEntidades) && $arrEntidades;$i++) {
			        	$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
			        	switch($arrEntidades[$i]['cl_habilitado']){
			        		case 0:$imagen="cruz.png";break;	
			        		case 1:$imagen="ok.png";break;
			        		case 2:$imagen="alerta.png";break;
			        	}?>
					   	<tr class="<?=$class?>">
					    	<?php if($_SESSION['idTipoEmpresa'] == 3 && $arrEntidades[$i]['cl_tipo'] != 1){?>
                            <td>&nbsp;</td>
                            <td ><?=trim($arrEntidades[$i]['cl_razonSocial'])?></td>
                            <?php }
							else{?>
                            <td align="center">
                             	<?php if(tienePerfil(array(5,9,13,19))){?>
                                <input type="checkbox" name="chkId[]" id="chk_<?=$arrEntidades[$i]['cl_id']?>" value="<?=$arrEntidades[$i]['cl_id']?>"/>
								<?php }?>
                            </td>	
							<td>
                            	<?php if(tienePerfil(array(5,9,13,19))){?>
                             	<a href="javascript: enviarModificacion('modificar',<?=$arrEntidades[$i]['cl_id']?>)">
									<?=$arrEntidades[$i]['cl_razonSocial']?>
                                </a>
                                <?php }else{echo $arrEntidades[$i]['cl_razonSocial'];}?>
                            </td>
                            <?php }?>
                            <td align="center"><?=$arrEntidades[$i]['cl_cuit']?></td>
					        <td align="center"><?=$arrEntidades[$i]['cl_email']?></td>
							<?php switch($arrEntidades[$i]['cl_tipo']){
									case 1: $valorTipo = 'Agente'; break;
									case 2: $valorTipo = 'Cliente'; break;
									case 3: $valorTipo = 'Localizar-T'; break;
									default: $valorTipo = $lang->system->otro; break;
							}?>							 
					        <td align="center"><?=$valorTipo?></td>	
			
            		<?php if(tienePerfil(17)){ ?>							 
					         <td align="center"><?=generarCodigoValidacion(escapear_string($arrEntidades[$i]['cl_email']))?></td>
							 <td align="center"><?=($arrEntidades[$i]['cantidadUsuarios']>0)?$lang->system->si:$lang->system->no?></td>	
							 <td align="center"><?=($arrEntidades[$i]['cantidadMoviles']>0)?$arrEntidades[$i]['cantidadMoviles']:0?></td>
							 <td align="center"><?=($arrEntidades[$i]['us_ultimo_acceso'] != NULL)?$lang->system->si:$lang->system->no?></td>
					<?php } ?>
							
					<td align="center"><img src="imagenes/<?=$imagen;?>"/></td>
				</tr>
				<?php }
					include('secciones/footer_LI.php');
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
		case 'modificar':
			require_once 'includes/botoneraABMs.php';
		?>			
	</div><!-- fin mainBoxLICabezera -->
    <hr />        
		<div id="mainBoxAM">
        	<input name="hidMensaje" id="hidMensaje" type="hidden" value="<?=($mensaje) ? $mensaje:"";?>" >
            <?php if($_SESSION['idTipoEmpresa'] == 1 || $_SESSION['idTipoEmpresa'] == 3) { ?>
				<input type="hidden" name="cmbTipo" id="cmbTipo" value=<?=isset($arrEntidades[0]['cl_tipo'])?$arrEntidades[0]['cl_tipo']:($_SESSION['idTipoEmpresa'] == 3 ? 1 : 2) ?> />
            <?php } ?>
			<?php if(tienePerfil(19)){?>		
           	<fieldset>
				<legend>Configuraci&oacute;n Localizar-T</legend>
				<table width="100%" >
					<tr>
                        <td align="right" height="20" valign="middle">Paquete&nbsp;&nbsp;</td>
                        <td style="text-align:left;" width="80%">
                            <select name="cmbPaquete" style="width:304px;">
                                <option value=""><?=$lang->system->seleccione?></option>
                                <?php foreach($arrGrupoPerfil as $item){?>
                                    <option value="<?=$item['pe_id']?>" <?=(($item['pe_id']==$arrEntidades[0]['cl_paquete'])?'selected':'')?>><?=encode($item['pe_nombre'])?></option>	
                                <?php }?>
                            </select>&nbsp;*
                        </td>
					</tr>
                    <tr>
                        <td align="right" height="20" valign="middle">Cant. posible de Dadores a crear&nbsp;&nbsp;</td>
                        <td>
                            <input type="text" name="txtDadores" style="width:300px;" value="<?=empty($arrEntidades[0]['cl_cant_dadores'])?1:$arrEntidades[0]['cl_cant_dadores']?>" />&nbsp;*
                        </td>
					</tr>
                    <td align="right" height="20" valign="middle"><?=$lang->system->url_autorizada?>&nbsp;&nbsp;</td>
                    <td><input name="txtUrlAutorizada" id="txtUrlAutorizada" value="<?=$arrEntidades[0]['cl_urlAutorizada']?>" style="width:300px;" size="255" type="text">&nbsp;</td>

				</table>
			</fieldset>
            <br/>
			<?php }?>
	
			<?php require_once 'includes/interfazGraficaABMs.php'?>
            <br/>
			<fieldset>
				<legend><?=$lang->system->habilitado?></legend>
				<table width="100%" >
					<tr>
						<td width="150" align="right" ><?=$lang->system->habilitado?></td>
						<td><input type="radio" name="radHabilitado" value="1" <?=($arrEntidades[0]['cl_habilitado']==1)?'checked="checked" ':''?> /><?=$lang->system->si?></td>
						<td><input type="radio" name="radHabilitado" value="0" <?=($arrEntidades[0]['cl_habilitado']==0)?'checked="checked"':''?> /><?=$lang->system->no?></td>
						<td><input type="radio" name="radHabilitado" value="2" <?=($arrEntidades[0]['cl_habilitado']==2)?'checked="checked"':''?> /><?=$lang->system->alerta_falta_pago?></td>
					</tr>
				</table>
            </fieldset>
            <?php if(tienePerfil(19)){
				$idiomaDefecto = trim($arrEntidades[0]['pr_idioma']).'_'.trim($arrEntidades[0]['pr_region']);
				$idiomaDefinido = trim($arrEntidades[0]['cl_idioma_definida']);
				$selectItem = !empty($idiomaDefinido)?$idiomaDefinido:(!empty($idiomaDefecto)?$idiomaDefecto:'');
			?>
            <br/>
            <fieldset>
				<legend>Idioma del Agente</legend>
				<table width="100%" >
					<tr>
						<td width="150" align="right" >Idioma&nbsp;&nbsp;</td>
                        <td>
                        	<select style="width:304px;" id="cmbRegion" name="cmbRegion">
                            	<option value=""><?=$lang->system->seleccione?></option>
                            	<?php foreach($arrIdiomas as $item){?>
									<option value="<?=$item?>" <?=(($selectItem == $item)?'selected="selected"':'')?> ><?=$item?></option>
								<?php }?>
                            </select>
						<td>
					</tr>
				</table>
            </fieldset>
            <?php }?>
		</div><!-- fin. #mainBoxAM -->
		<?php
		break;
		}?>
	</form>
</div>
<?
	switch ($operacion){
		case 'alta':
		case 'modificar':?>
		<script language="javascript">
			$(document).ready(function(){
				<?php if($restringirAltaDador){?>
					$("select#cmbTipoCliente option[value='1']").attr('disabled','disabled');
				<?php }?>
				
				$('#cmbTipoCliente').change(function() {
					var valor = $(this).val();
					if(valor==4){     
						$('#cmbTipoCliente').after('<div id="borrarFlete"><br>->Flete contratado a: <select name="cmbTrasportistaFlete" id="cmbTrasportistaFlete" style="width:196px;"><?
						require_once 'clases/clsClientes.php';
						$client=new Cliente($objSQLServer);
						$arrClientesFlete=$client->obtenerClientesFletes($_SESSION['idEmpresa']);
						foreach($arrClientesFlete as $cli){
							$option='<option value="'.$cli['cl_id'].'" ';
							if($cli['cl_id']==$arrEntidades[0]['cl_id_fletero']){
								$option.= 'selected=selected'; 
							}
							$option.='>'.$cli['cl_razonSocial'].'</option>';
							echo $option;
						}?></select></div>'); 
					}
					else{
						$('#borrarFlete').empty();
					}
				});
				
			});
			
			if('<?=$arrEntidades[0]['cl_pr_id']?>' != ''){
				getProvincia('cmbProvincia', '<?=$arrEntidades[0]['cl_pai_id']?>', '<?=$arrEntidades[0]['cl_pr_id']?>');
			}
		</script>
	<?php 
		break; 
	}
	
if($operacion == 'modificar' && $arrEntidades[0]['cl_id_fletero']!=0){ ?>
     <script language="javascript">
     $(document).ready(function(){
		$('#cmbTipoCliente').trigger('change');
     });
	</script>	
<?php }?>