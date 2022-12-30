<div id="main" class="sinColIzq">
<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?><?=($action=='popup')?'&action=popup':''?>" method="post">
	<input type="hidden" name="idCliente" value="<?=(int)$_REQUEST['idCliente']?>"/>
    <input type="hidden" name="idMovil" id="idMovil" value=""/>
    <input type="hidden" name="estado" id="estado" value=""/>
    <input type="hidden" name="id_servicio" id="id_servicio" value=""/>
    
    <?php if($_SESSION['idAgente'] == 9048){ //inicio.EstiloVista (se implementa nueva vista para FiberCorp, y se deja igual para ADT)?>
    <div class="solapas gum clear">
    	<?php /*?>
		<div id="botonesABM" style="margin-bottom:10px;">
    		<a id="botonVolver" href="boot.php?c=<?=$seccion?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
		</div>
		<?php /**/?>
    	<div style="height:100%" class="contenido clear">
        <table width="100%" height="100%">
            <thead>
                <tr>
                	<td><span class="campo1"><?=$lang->system->movil?></span></td>
                    <td><center><span class="campo1"><?=$lang->system->fecha_alta?></span></center></td>
                    <td><center><span class="campo1"><?=$lang->system->fecha_ultimo_reporte?></span></center></td>
                    <td class="td-last"><center><span class="campo1"><?=$lang->system->estado?></span></center></td>
                </tr>
                </thead>
            <tbody>
            <?php if($arr_moviles){
                foreach($arr_moviles as $i => $item){
                    $class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                    <tr class="<?=$class?> <?=((count($arr_moviles) - 1)==$i)?'tr-last':''?>">
                        <td>
                            <?php if(!$item['mo_borrado'] && (date('Y-m-d',strtotime($item['sh_fechaRecepcion'])) > date('Y-m-d',strtotime('-7 day',strtotime(getFechaServer('Y-m-d'))))) && $_SESSION['idAgente'] != 9048){?>
                            <a href="secciones/abmAllInOneRastreo.php?idMovil=<?=$item['mo_id']?>&referer=true" target="_self"><?=decode($item['mo_matricula'])?></a>
                            <?php }
                            else{ echo decode($item['mo_matricula']);}?>
                        </td>
                        <td><center><?=date('d-m-Y H:i',strtotime($item['mo_fecha_creacion']))?>hs</center></td>
                        <td><center><?=date('d-m-Y H:i',strtotime($item['sh_fechaRecepcion'])).'hs'?></center></td>
                        <td class="no_padding td-last"><center>
                            <?php if(!$item['mo_borrado']){?>
                            <a class="button colorRed" href="javascript:;" onclick="javascript:cambiarEstado(<?=$item['mo_id']?>,1)">
                            	<?=$lang->system->eliminar_licencia?>
                            </a>
                            <?php }
							else{ echo $lang->system->licencia_eliminada;}
							?>
                            </center>
                    	</td>
                    </tr>
                <?php } 
                }
                else{?>
                    <tr class="tr-last">
                        <td class="td-last" colspan="5"><center><?=$lang->system->msg_no_tiene_moviles?></center></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
        </div>
    </div>
    <?php }
	else{//else.EstiloVista?>
    <!-- -->
    <div class="mainBoxLICabezera">
		<h1><?=$lang->system->title_moviles_cuenta?></h1>
		<?php if($action=='popup'){ ?>				
               <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />                               
			   <?php require_once 'includes/botoneraABMs.php';?>  
               <?php if($mensaje){ ?>
			   <div style="background:#F7F8E0;color:red;width:100%;height:15px;padding:5px;"><?=$mensaje?></div>	
				<?php } ?>   
		<?php } ?>		
	</div><!-- fin. mainBoxLICabezera -->
	<div id="mainBoxLI">
        <br />
        <table cellpadding="0" cellspacing="0" border="0" class="widefat" id="listadoRegistros" width="100%">
        <tr class="titulo">
        	<td><?=$lang->system->movil?></td>
            <td><?=$lang->system->fecha_alta?></td>
            <td><?=$lang->system->estado?></td>
            <td><?=$lang->system->fecha_ultimo_reporte?></td>
            <!--
            <td>P&aacute;nico con atajo</td>
            <td>P&aacute;nico en app</td>
            <td>P&aacute;nico con retardo</td>
            <td>Emergencia m&eacute;dica</td>
            <td>Incendio</td>
            <td>ADT te acompa&ntilde;a</td>
            <td>Acceso al sistema</td>
            -->
    	</tr>
        <?php 
		if($arr_moviles){
			foreach($arr_moviles as $item){
				$class = ($class == 'filaImpar')?'filaPar':'filaImpar';
			?>			
			<tr class="<?=$class?>">
				<td align="center">
                	<?php if(!$item['mo_borrado'] && (date('Y-m-d',strtotime($item['sh_fechaRecepcion'])) > date('Y-m-d',strtotime('-7 day',strtotime(getFechaServer('Y-m-d')))))){?>
					<a href="secciones/abmAllInOneRastreo.php?idMovil=<?=$item['mo_id']?>&referer=true" target="_self"><?=decode($item['mo_matricula'])?></a>
                    <?php }
					else{ echo decode($item['mo_matricula']);}?>
                </td>
				<td  align="center"><?=date('d-m-Y H:i',strtotime($item['mo_fecha_creacion']))?>hs</td>
                <td  align="center">
                	<a href="javascript:cambiarEstado(<?=$item['mo_id']?>,<?=!$item['mo_borrado']?1:0?>)" title="<?=!$item['mo_borrado']?$lang->system->desactivar:$lang->system->activar?>">
                    	<img src="imagenes/<?=!$item['mo_borrado']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                </td>
                <td  align="center">
                	<?=date('d-m-Y H:i',strtotime($item['sh_fechaRecepcion'])).'hs'?>
                </td>
                <?php /*
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                    <a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['panic_atajo']?0:1?>,1)" title="<?=$item['panic_atajo']?'Desactivar':'Activar'?> atajo de p&aacute;nico">
                    	<img src="imagenes/<?=$item['panic_atajo']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                	<a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['panic_app']?0:1?>,4)" title="<?=$item['panic_app']?'Desactivar':'Activar'?> p&aacute;nico en app">
                    	<img src="imagenes/<?=$item['panic_app']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                    <a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['panic_retardo']?0:1?>,5)" title="<?=$item['panic_retardo']?'Desactivar':'Activar'?> p&aacute;nico con retardo">
                    	<img src="imagenes/<?=$item['panic_retardo']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                	<a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['medica']?0:1?>,6)" title="<?=$item['medica']?'Desactivar':'Activar'?> botonera de emergencia m&eacute;dica">
                    	<img src="imagenes/<?=$item['medica']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                	<a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['incendio']?0:1?>,7)" title="<?=$item['incendio']?'Desactivar':'Activar'?> botonera de incendio">
                    	<img src="imagenes/<?=$item['incendio']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                	<a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['adt_acompana']?0:1?>,8)" title="<?=$item['adt_acompana']?'Desactivar':'Activar'?> ADT te acompa&ntilde;a">
                    	<img src="imagenes/<?=$item['adt_acompana']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                <td  align="center">
                	<?php if(!$item['mo_borrado']){?>
                	<a href="javascript:setService(<?=$item['mo_id']?>,<?=$item['acceso_sistema']?0:1?>,9)" title="<?=$item['acceso_sistema']?'Desactivar':'Activar'?> acceso al sistema">
                    	<img src="imagenes/<?=$item['acceso_sistema']?'resp_ok.png':'cruz.png'?>" />
                    </a>
                    <?php }?>
                </td>
                */?>
			</tr>
			<?php }
		}
		else{?>
		<tr class="filaPar">
        	<td colspan="10" align="center"><?=$lang->system->msg_no_tiene_moviles?></td>
        </tr>
		<?php }?>
        </table>
    </div>
    <?php } //fin.EstiloVista?>
</form>
</div>
<script language="javascript">
function cambiarEstado(idMovil, estado){
	$('#idMovil').val(idMovil);
	$('#estado').val(estado);
	if(estado == 1){
		var resp = confirm('<?=$lang->system->msg_eliminar_licencia?>');
		if(resp){
			$('#frm_abmAllInOneMoviles').submit();			
		}	
	}
	else{
		$('#frm_abmAllInOneMoviles').submit();	
	}
}

function setService(idMovil, estado, id_servicio){
	$('#idMovil').val(idMovil);
	$('#estado').val(estado);
	$('#id_servicio').val(id_servicio);
	$('#frm_abmAllInOneMoviles').submit();	
}
</script>