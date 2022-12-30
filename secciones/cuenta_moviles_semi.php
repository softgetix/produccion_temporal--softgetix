<?php  if(isset($popup) && $popup == true){  ?>
    
    <form name="frm_cuenta" id="frm_cuenta" action="?c=cuenta&solapa=moviles_semi" method="post">
	<input name="hidSeccion" id="hidSeccion" type="hidden" value="cuenta" />
    <input name="hidOperacion" id="hidOperacion" type="hidden" />

    <input name="hidMessage" id="hidMessage" type="hidden" />
    <input name="hidNumber" id="hidNumber" type="hidden" />
    <input name="hidTitle" id="hidTitle" type="hidden" />
    <input name="hidPath" id="hidPath" type="hidden" />
    <input name="hidMessagetype" id="hidMessagetype" type="hidden" />
    <input name="hidIdViaje" id="hidIdViaje" type="hidden" />
    <input name="hidAdicional" id="hidAdicional" type="hidden" />

    <input name="hidId" type="hidden" value="<?=$id?>" />
    <input name="hidAction"type="hidden" value="<?=!empty($action) ? $action : 'popupPostChangeUser'?>"  />

    <div class="solapas gum clear">
        <a title="Cerrar" class="float_r" href="javascript: window.parent.cerrarPopup();">
            <span class="sprite eliminar"></span>
        </a>
        <span class="clear" style="margin-bottom:4px;"></span>
        <div class="contenido clear" style="max-height:310px; overflow-x:auto;">
            <?php  if($vista == 'newconfig'){?>
                <center><div style="padding:10px" <?=(!$status) ?'class="error"':'class="ok"'?> ><?=$message?></div></center>
                <?php if(isset($status) && $status == true){
                    echo '<script>setTimeout(function(){parent.window.location.reload(true);},1500);</script>';
                    exit;    
                }?>
                <center>
                    <select name="transportista" style="width:304px;">
                        <option value=""><?=$lang->system->transportista?></option>
                            <?php foreach($cboTransportista as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($_POST['transportista'] == $item['id']){?>selected="selected"<?php }?>><?=$item['dato']?></option>
							<?php }?>
                    </select>
                    <br/><br/>
                    <label style="text-align:left; width:300px; display: block; line-height: 16px;">Tractor:</label>
                    <input type="text" name="tractor" style="width:300px;" class="only_number_and_char" value="<?=$_POST['tractor']?>"/>
                    <br/><br/>
                    <label style="text-align:left; width:300px; display: block; line-height: 16px;">Semi:</label>
                    <input type="text" name="semi" style="width:300px;" class="only_number_and_char" value="<?=$_POST['semi']?>"/>
                    <br/><br/>
                    <select name="configuracion" style="width:304px;">
                        <option value="">Configuración</option>
                            <?php foreach($cboConfigracion as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($_POST['configuracion'] == $item['id']){?>selected="selected"<?php }?>><?=$item['configuration_description']?></option>
							<?php }?>
                    </select>
                    <br/><br/>
                    <select name="carga_bruta" style="width:304px;">
                        <option value="">Carga Bruta</option>
                            <?php foreach($cboCargaBruta as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($_POST['carga_bruta'] == $item['id']){?>selected="selected"<?php }?>><?=$item['load_description']?></option>
							<?php }?>
                    </select>
                    <br/><br/>
                    <select name="tara" style="width:304px;">
                        <option value="">Tara</option>
                            <?php foreach($cboTara as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($_POST['tara'] == $item['id']){?>selected="selected"<?php }?>><?=$item['tara_description']?></option>
							<?php }?>
                    </select>
                <center>
                <br/><br/>
                <center><a href="javascript:enviar('popup');" class="button colorin">Crear</a></center>
            <?php }?>    
        </div>
    </div>
    </form>          
<?php }
else{
switch($operacion){
	case 'modificar':?>
    	<div id="botonesABM" style="width:100%;">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
		<fieldset>
			<legend>Configuraci&oacute;n Equipo</legend>
			<table width="100%" >
                <tr>
					<td align="right" height="20"><?=$lang->system->transportista?>&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="tara" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboTransportista as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($arrEntidades['mo_id_cliente_facturar'] == $item['id']){?>selected="selected"<?php }?>><?=$item['dato']?></option>
							<?php }?>
                        </select>
                    </td>
				</tr>
				<tr>
					<td align="right" height="20">Tractor&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="tractor" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboTractor as $item){?>
                                    <option value="<?=$item['vehicle_id']?>" <?php if($arrEntidades['ms_mo_id'] == $item['vehicle_id']){?>selected="selected"<?php }?>><?=$item['vehicle']?></option>
                            <?php }?>
                        </select>
                    </td>
				</tr>
                <tr>
					<td align="right" height="20">Semi&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="semi" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboSemi as $item){?>
                                    <option value="<?=$item['second_vehicle_id']?>" <?php if($arrEntidades['ms_semi_mo_id'] == $item['second_vehicle_id']){?>selected="selected"<?php }?>><?=$item['second_vehicle']?></option>
                            <?php }?>
                        </select>
                    </td>
				</tr>
                <tr>
					<td align="right" height="20">Configuración&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="configuracion" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboConfigracion as $item){?>
                                    <option value="<?=$item['id']?>" <?php if($arrEntidades['ms_vc_id'] == $item['id']){?>selected="selected"<?php }?>><?=$item['configuration_description']?></option>
                            <?php }?>
                        </select>
                    </td>
				</tr>
                <tr>
					<td align="right" height="20">Carga Bruta&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="carga_bruta" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboCargaBruta as $item){?>
                                    <option value="<?=$item['id']?>" <?php if($arrEntidades['ms_vcb_id'] == $item['id']){?>selected="selected"<?php }?>><?=$item['load_description']?></option>
                            <?php }?>
                        </select>
                    </td>
				</tr>
                <tr>
					<td align="right" height="20">Tara&nbsp;&nbsp;</td>
					<td style="text-align:left;" width="80%">
                        <select name="tara" style="width:304px;">
                            <option value=""><?=$lang->system->seleccione?></option>
                            <?php foreach($cboTara as $item){?>
                            	<option value="<?=$item['id']?>" <?php if($arrEntidades['ms_vt_id'] == $item['id']){?>selected="selected"<?php }?>><?=$item['tara_description']?></option>
							<?php }?>
                        </select>
                    </td>
				</tr>
			</table>
		</fieldset>
		<!-- -->
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript: enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
       	</center>
        <br />
    <?php break;
	default:?>
    <?php 
    $tipoBotonera = 'LI-Export';
    $btn_action = "javascript:mostrarPopup('boot.php?c=cuenta&solapa=moviles_semi&action=newConfig&',380,360);";
    $btn_name = 'Crear Patente';
	include('includes/botoneraABMs.php');
    ?>
    <table width="100%" height="100%">
        <thead>
            <tr>
                <td><span class="campo1">Tractor</span></td>
                <td><span class="campo1">Semi</span></td>
                <td><span class="campo1"><?=$lang->system->transportista?></span></td>
                <td><span class="campo1">Configuración</span></td>
                <td><span class="campo1">Carga Bruta</span></td>
                <td><span class="campo1">Tara</span></td>
                <td class="td-last"><center><span class="campo1"><? //=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>">
 					<td>
                        <input type="hidden" name="chkId[]" id="chk_<?=$item['ms_id']?>" value="<?=$item['ms_id']?>"/>
                        <a href="javascript:enviarModificacion('modificar',<?=$item['ms_id']?>)"><?=$item['tractor']?></a>
                    </td>
                    <td><?=$item['semi']?></td>
                    <td><?=$item['transportista']?></td>
                    <td><?=$item['configuracion']?></td>
                    <td><?=$item['carga_bruta']?></td>
                    <td><?=$item['tara']?></td>
                    <td class="no_padding td-last">
                    	<center>
                        	<a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=(int)$item['ms_id']?>)"><img src="imagenes/cerrar.png" /></a>
						</center>
					</td>
                </tr>
            <?php } 
				$cantRegistros = count($arrEntidades);
				include('secciones/footer_LI.php');
            }
            else{?>
                <tr class="tr-last">
                    <td class="td-last" colspan="7"><center><?=$lang->message->sin_resultados?></center></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
	<?php break;
}}?>