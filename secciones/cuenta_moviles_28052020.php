<?php 
switch($operacion){
	case 'modificar':?>
    	<div id="botonesABM" style="width:100%;">
        	<a id="botonVolver" href="boot.php?c=<?=$seccion?>&solapa=<?=$solapa?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
        </div>
		<span class="clear"></span>
		<?php require_once 'includes/interfazGraficaABMs.php';?>
		<?php if(tienePerfil(19)){?>
        <fieldset>
			<legend>Configuraci&oacute;n Equipo</legend>
			<table width="100%" >
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
	include('includes/botoneraABMs.php');
	?>
    <table width="100%" height="100%">
        <thead>
            <tr>
                <td><span class="campo1"><?=$lang->system->matricula?></span></td>
                <?php if(!tienePerfil(array(9,10,11,12))){?>
                <td><center><span class="campo1"><?=$lang->system->marca?>/<?=$lang->system->modelo?></span></center></td>
                <?php }?>
                <td><center><span class="campo1"><?=$lang->system->tipo_movil?></span></center></td>
                <td><span class="campo1"><?=$lang->system->cliente?></span></td>
                <td><center><span class="campo1"><?=$lang->system->ultimo_reporte_recibido?></span></center></td>
                <td class="td-last"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
            </tr>
        </thead>
        <tbody>
        <?php if($arrEntidades){
            foreach($arrEntidades as $i => $item){
                $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                <tr class="<?=$class?> <?=((count($arrEntidades) - 1)==$i)?'tr-last':''?>  <?=$item['mo_borrado']?'row_inactivo':''?>">
 					<td>
						<?php if(tienePerfil(array(5,9,13,19)) && !$item['mo_borrado']){?>
                            <input type="hidden" name="chkId[]" id="chk_<?=$item['mo_id']?>" value="<?=$item['mo_id']?>"/>
                            <a href="javascript:enviarModificacion('modificar',<?=$item['mo_id']?>)"><?=$item['mo_matricula']?></a>
                        <?php }
                        else{echo $item['mo_matricula'];}?>
                    </td>
                    <?php if(!tienePerfil(array(9,10,11,12))){?>
                    <td>
                    	<center>
						<?=(trim($item['mo_marca']) == true && trim($item['mo_modelo']) == true)?trim($item['mo_marca'].' / '.$item['mo_modelo']):trim($item['mo_marca'].' '.$item['mo_modelo'])?>
                        </center>
					</td>
                    <?php }?>
                    <td><center><?=$lang->system->$item['tv_nombre']?$lang->system->$item['tv_nombre']->__toString():$item['tv_nombre']?></center></td>
                    <td><?=$item['cl_razonSocial']?></td>
                    <td><center><?=formatearFecha($arrEntidades[$i]['sh_fechaRecepcion'])?></center></td>
                    <td class="no_padding td-last">
                    	<center>
                        	<?php if($item['mo_borrado']){?>
                            	<span><?=$lang->system->movil_inactivo?></span>	
                            <?php }elseif(tienePerfil(array(5,9,13,19))){?>
                            <a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=(int)$item['mo_id']?>)"><img src="imagenes/cerrar.png" /></a>
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