<?php 
switch($operacion){
	case 'alta':
	case 'modificar': ?>
        <div id="botonesABM">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
        <?php require_once 'includes/interfazGraficaABMs.php'?>
        <input type="hidden" name="cmbClientes" id="cmbClientes" value="<?=$_SESSION["idEmpresa"]?>" />
		<br/>
			
		<!---------Ver combo Moviles ------->
		<?php if(tienePerfil(array(5,6,9,10,13,14,19))){ ?>
		<fieldset>
			<legend><?=$lang->system->movil_disponible?></legend>
            <table width="100%">
                <tr>
                    <td width="20%" valign="middle" height="20" align="right">M&oacute;vil&nbsp;&nbsp;</td>
                    <td width="80%" style="text-align:left;">
                        <select id="cmbMovilAsignado" style="width:304px;" name="cmbMovilAsignado">
                            <option value="0"><?=$lang->system->seleccione?></option>
                            <?php foreach($arrMovilesCombo as $movil){ ?>
							<option value="<?=$movil['mo_id']?>" <?=((int)$movilAsoc==(int)$movil['mo_id'])?'selected':''?>><?=$movil['mo_matricula']?></option>
							<?php } ?>
                        </select>
                    </td>
                    <td></td>					
                </tr>
            </table>
        </fieldset>    
		<?php } ?>
        
        <!-- -->
        <br />
        <center>
        	<a href="javascript:;" onclick="javascript: $('#cmbEventosAsignados option').attr('selected', 'selected'); enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
       	</center>
        <br />
        <script type="text/javascript">
			function cargarMoviles(idEmpresa){
				getMoviles('cmbMovilAsignado', idEmpresa);
			}
		</script>
        <?php if(!count($arrMovilesCombo)){?><script> if($('#cmbCliente').val()){cargarMoviles($('#cmbCliente').val());}</script> <?php }?>
   	<?php break;
	default:?>
    <?php 
	//$tipoBotonera = 'LI-NewItem'; 
        $tipoBotonera = 'LI-NewItem-Export';
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
               	<td><span class="campo1"><?=$lang->system->nombre_apellido?></span></td>
                <td><center><span class="campo1"><?=$lang->system->telefono?></span></center></td>
                <td><span class="campo1"><?=$lang->system->empresa?></span></td>
                <td><center><span class="campo1"><?=$lang->system->moviles_asignados?></span></center></td>
                <td class="td-last"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?> <?=$item['co_borrado']?'row_inactivo':''?>">
                	<td>
                        <?php if(!tienePerfil(array(7,11))){?>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['co_id']?>" value="<?=$item['co_id']?>"/>
                            <a href="javascript: enviarModificacion('modificar',<?=$item['co_id']?>)">
							 	<?=($item['co_nombre'].' '.$item['co_apellido'].'(DNI:'.$item['co_dni'].')')?>
							</a>
                        <?php }
						else{ 
							echo ($item['co_nombre'].' '.$item['co_apellido'].'(DNI:'.$item['CO_dni'].')');
						}?>
					</td>
					<td><?=$item['co_telefono'].$item['co_Estado_app']?></td>
					<td><?=($item['razon_social'])?></td>
                    <td><center><?=($item['movil_1'].((!empty($item['movil_1']) && !empty($item['movil_2']))?', ':'').$item['movil_2'])?></center></td>
					<td class="no_padding td-last">
                    	<center>
                        	<?php if(!tienePerfil(array(7,11))){?>
                            <a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['co_id']?>)"><img src="imagenes/cerrar.png" /></a>
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