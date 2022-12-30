<div id="main" class="sinColIzq">
	<div class="solapas gum clear">
    	<form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post" enctype="multipart/form-data">
          	<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
			<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		    <input name="hidId" id="hidId" type="hidden" value="<?=(int)$id?>" />    
            
            <div style="height:100%" class="contenido clear"> 
			<?php switch($operacion){
				case 'alta':
				case 'modificar':?>
                
                <div id="botonesABM">
                    <a id="botonVolver" href="boot.php?c=<?=$seccion?>"><img alt="" src="imagenes/botonVolver.png"><?=$lang->botonera->volver?></a>
                </div>
                <span class="clear"></span>
                <!-- -->
				
				<fieldset>
					<legend>Configuraci&oacute;n</legend>
					<table width="100%" >
						<tr>
							<td align="right" height="20" valign="middle">Tipo de Viaje&nbsp;&nbsp;</td>
							<td style="text-align:left;" width="80%">
								<select name="tipoViaje" style="width:304px;">
									<option value=""><?=$lang->system->seleccione?></option>
									<?php foreach($data['arrTipoViaje'] as $item){?>
										<option value="<?=$item['vt_id']?>" <?=(($item['vt_id']==$saved['tipoViaje'])?'selected':'')?>><?=encode($item['vt_nombre'])?></option>	
									<?php }?>
								</select>&nbsp;*
							</td>
						</tr>
						<tr>
							<td align="right" height="20" valign="middle">Origen&nbsp;&nbsp;</td>
							<td style="text-align:left;">
								<select name="origen" style="width:304px;">
									<option value=""><?=$lang->system->seleccione?></option>
									<?php foreach($data['arrOrigen'] as $item){?>
										<option value="<?=$item['re_id']?>" <?=(($item['re_id']==$saved['origen'])?'selected':'')?>><?=encode($item['re_nombre'])?></option>	
									<?php }?>
								</select>&nbsp;*
							</td>
						</tr>
						<tr>
							<td align="right" height="20" valign="middle">Frontera Destino&nbsp;&nbsp;</td>
							<td style="text-align:left;">
								<select name="destino" style="width:304px;">
									<option value=""><?=$lang->system->seleccione?></option>
									<?php foreach($data['arrDestino'] as $item){?>
										<option value="<?=$item['re_id']?>" <?=(($item['re_id']==$saved['destino'])?'selected':'')?>><?=encode($item['re_nombre'])?></option>	
									<?php }?>
								</select>&nbsp;*
							</td>
						</tr>
					</table>
				</fieldset>
				<br/>
				<fieldset>
					<legend>Rutas</legend>
					<table>
						<tr>
							<td style="text-align:left !important;">Rutas disponibles</td>
							<td>&nbsp;</td>
							<td style="text-align:left !important;">Rutas asociadas</td>
						</tr>
						<tr>
							<td> 
								<select name="cmbRutasDisponibles" multiple="multiple" id="cmbRutasDisponibles"  style="width: 300px; height:400px;">
								<?php if($data['arrRutasDisp']){
									foreach($data['arrRutasDisp'] as $item){ ?>
										<option value="<?=$item["re_id"]?>"><?=encode($item["re_nombre"])?></option>
									<?php }
								}?>
								</select>
							</td>
							<td  align="center" style="vertical-align: middle">
								<input name="B1" class="texto" onclick="javaScript:Move(document.getElementById('cmbRutasDisponibles'), document.getElementById('cmbRutasAsociadas'));" value="&gt;&gt;" type="button" /><br><br>
								<input name="B2" class="texto" onclick="javaScript:Move(document.getElementById('cmbRutasAsociadas'), document.getElementById('cmbRutasDisponibles'));" value="&lt;&lt;" type="button" />
							</td>
							<td>
								<select name="cmbRutasAsociadas[]" multiple="multiple" id="cmbRutasAsociadas" style="width: 300px; height:400px;">
								<?php if($data['arrRutasAsoc']){
									foreach($data['arrRutasAsoc'] as $item){ ?>
										<option value="<?=$item["re_id"]?>"><?=encode($item["re_nombre"])?></option>
										<?php }
									}
									?>
								</select>
							</td>
						</tr>
					</table>
				</fieldset>

				<!-- -->
				<br />
				<center>
					<a href="javascript:;" onclick="javascript: $('#cmbRutasAsociadas option').attr('selected', 'selected'); enviar('<?=($operacion=='alta')?'guardarA':'guardarM'?>')"  class="button colorin" style="width:173px;"><?=$lang->botonera->guardar?></a>
				</center>
				<br />
				
                <?php 	
				break;
				default:
				$tipoBotonera = 'LI-NewItem'; 
				include('includes/botoneraABMs.php');
				?>
                
                <table width="100%" height="100%">
                <thead>
                	<tr>
                        <td><span class="campo1">Tipo Viaje</span></td>
                        <td><span class="campo1">Origen</span></td>
						<td><span class="campo1">Destino</span></td>
                        <td class="td-last" width="60"><center><span class="campo1"><?=$lang->botonera->eliminar?></span></center></td>
                    </tr>
				</thead>
                <tbody>
                <?php if($arrListado){
                	foreach($arrListado as $i => $item){
                    	$class = ($i % 2 == 0)?'filaPar':'filaImpar';?>
                        <tr class="<?=$class?> <?=((count($arrListado) - 1)==$i)?'tr-last':''?>">
                        <td>
                        	<input type="hidden" name="chkId[]" id="chk_<?=$item['oea_id']?>" value="<?=$item['oea_id']?>"/>
							<a href="javascript: enviarModificacion('modificar',<?=$item['oea_id']?>)"><?=encode($item['vt_nombre'])?></a>
                        </td>
                        <td><?=$item['origen']?></td>
						<td><?=$item['destino']?></td>
                        <td class="no_padding td-last">
							<center><a href="javascript:;" onclick="javascript:enviarModificacion('baja',<?=$item['oea_id']?>)"><img src="imagenes/cerrar.png" /></a></center>
                        </td>
                    </tr><?php }
					}
					else{?>
						<tr class="tr-last">
                    	<td class="td-last" colspan="6"><center><?=$lang->message->sin_resultados?></center></td>
						</tr>
					<?php }
				?></table><?php
                break;
			}?>
            	<span class="clear"></span>
			</div><!-- fin. contenido--> 
        </form>  
	</div> <!-- fin. solapas-->   
</div>
