<?php 
switch($operacion){
	case 'alta':
	case 'modificar':?>
        <div id="botonesABM" style="width:100%;">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
        
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

				 <td align="right" height="20" valign="middle">Administra Pallets?</td>
		<td><input name="txtPallets" id="txtPallets" value="<?=$arrEntidades[0]['cl_pallets']?>" style="width:300px;" size="255" type="text">&nbsp;</td>


			</table>
		</fieldset>
        <br/>
        <?php }?>	
        	
		<?php require_once 'includes/interfazGraficaABMs.php'?>
        
        <?php if(tienePerfil(19)){?> 
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
        
        <?php
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
        <br />
        <fieldset>
        	<table>
				<tr>
                	<td style="text-align:left !important;"><?=$lang->system->lista_eventos?></td>
                    <td>&nbsp;</td>
                    <td style="text-align:left !important;"><?=$lang->system->eventos_asignados?></td>
				</tr>
				<tr>
					<td>
						<select size="20" name="cmbEventos" multiple="multiple" id="cmbEventos"  style="width: 300px;">
						<?php if($arrEventos){
							foreach($arrEventos as $item){ ?>
                            	<option value="<?=$item['id']?>"><?=$item['dato']?></option>
                        	<?php }
						}?>
						</select>
					</td>
					<td  align="center" style="vertical-align: middle">
						<input name="B1" class="texto" onclick="javaScript:Move(document.getElementById('cmbEventos'), document.getElementById('cmbEventosAsignados'));" value="&gt;&gt;" type="button" /><br><br>
						<input name="B2" class="texto" onclick="javaScript:Move(document.getElementById('cmbEventosAsignados'), document.getElementById('cmbEventos'));" value="&lt;&lt;" type="button" />
					</td>
					<td>
						<select size="20" name="cmbEventosAsignados[]" multiple="multiple" id="cmbEventosAsignados" style="width: 300px;">
						<?php if($arrMovilesUsuario){
							foreach($arrMovilesUsuario as $item){ ?>
								<option value="<?=$item['id']?>"><?=$item['dato']?></option>
							<?php }
						}?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>  
        <?php }?>
        
        <!-- -->
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript: $('#cmbEventosAsignados option').attr('selected', 'selected'); enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
       	</center>
        <br />
        <script language="javascript">
		 $(document).ready(function(){
			if(document.getElementById('cmbPais').value > 0){	
				getProvincia('cmbProvincia', document.getElementById('cmbPais').value, '<?=$arrEntidades[0]['cl_pr_id']?>');
			}
		 });
		</script>	
	<?php break;
	default:?>
    <?php 
	$lang->botonera->agregar_nuevo = $lang->botonera->agregar_clientes?$lang->botonera->agregar_clientes:$lang->botonera->agregar_nuevo;
	//$tipoBotonera = 'LI-NewItem'; 
        $tipoBotonera = 'LI-NewItem-Export';
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
               	<td><span class="campo1"><?=$lang->system->razon_social?></span></td>
                <td><span class="campo1"><?=$lang->system->telefono?></span></td>
                <td><span class="campo1"><?=$lang->system->direccion?></span></td>
                <?php if(!tienePerfil(array(9,10,11,12))){?>
                <td><center><span class="campo1"><?=$lang->system->habilitado?></span></center></td>
                <?php }?>
                <td class="td-last"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?> <?=$item['cl_borrado']?'row_inactivo':''?>">
                   
                   <?php if($_SESSION['idTipoEmpresa'] == 3 && $item['cl_tipo'] != 1){?>
                   <td ><?=trim($item['cl_razonSocial'])?></td>
                   <?php }
					else{?>
                    	<td>
                        <?php if(tienePerfil(array(5,9,13,19,27)) && !$item['cl_borrado']){?>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['cl_id']?>" value="<?=$item['cl_id']?>"/>
                            <a href="javascript: enviarModificacion('modificar',<?=$item['cl_id']?>)">
								<?=$item['cl_razonSocial']?>
                           </a>
                        <?php }
						else{
							echo $item['cl_razonSocial'];
						}?>
                       </td>
					<?php }?>
                    <td><?=$item['cl_telefono']?></td>
					<td><?=trim($item['cl_direccion'].' '.$item['cl_direccion_nro'])?></td>
                    <?php if(!tienePerfil(array(9,10,11,12))){?>
                    <td><center><?=$item['cl_habilitado']?$lang->system->si:$lang->system->no?></center></td>
                    <?php }?>
					<td class="no_padding td-last">
                    	<center>
                        	<?php if($item['cl_borrado']){?>
                            	<span><?=$lang->system->cliente_inactivo?></span>	
                            <?php }elseif(tienePerfil(array(5,9,13,19,27))){?>
                            <a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['cl_id']?>)"><img src="imagenes/cerrar.png" /></a>
                            <?php }?>
						</center>
                	</td>
                </tr>
            <?php } 
				 $cantRegistros = count($arrEntidades);
				 include('secciones/footer_LI.php');
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
	<?php break;
}?>