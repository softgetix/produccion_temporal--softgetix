<div id="main"  class="sinColIzq">
    <div class="solapas gum clear">
        <?php include('includes/navbarSolapas.php');?>
    	<div class="contenido clear">
    <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="POST">
	<div class="mainBoxLICabezera">
		<!--<h1>Administraci&oacute;n de Informes</h1>-->
		<input name="hidOperacion" id="hidOperacion" type="hidden" value="<?=$operacion?>" />
		<input name="hidId" id="hidId" type="hidden" value="<?=isset($id)?$id:''?>" />
		<input name="hidSeccion" id="hidSeccion" type="hidden" value="<?=$seccion;?>" />
		<?php
		switch($operacion){
			case 'listar':?>
        </div><!-- fin. mainBoxLICabezera --> 
    
         	<table width="100%" height="100%">
			<thead>
				<tr>
                	<td><span class="campo1"><?=$lang->system->nombre_informe?></span></td>
                    <td><span class="campo1"><?=$lang->system->categoria?></span></td>
                    <td><center><span class="campo1"><?=$lang->system->tipo_envio?></span></center></td>
                    <td><center><span class="campo1"><?=$lang->system->ultimo_envio?></span></center></td>
                    <td class="td-last"><center><span class="campo1"><?=$lang->system->activar_informe?></span></center></td>
				</tr>
			</thead>
            <tbody>
            	<?php $i = 0;
				if($arrInformes){
					foreach($arrInformes as $item){
						$i++;
						$class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
						<tr class="<?=$class?> <?=(count($arrInformes) == $i)?'tr-last':''?>">
							<td>
								<input type="hidden" name="chkId[]" id="chk_<?=$item['ip_id']?>" value="<?=$item['ip_id']?>"/>
								<?=encode($item['ip_nombre'])?>
                            </td>
							<td><?=$item['ipc_nombre']?></td>
							<td><center><?=$lang->system->$item['ite_nombre']?$lang->system->$item['ite_nombre']:$item['ite_nombre']?></center></td>
							<td><center><?=formatearFecha($item['in_fecha_ultimo_envio'])?></center></td>
							<td class="td-last">
								<center>
									<a href="javascript:;" onclick="javascript:enviarModificacion('editarInforme',<?=$item['ip_id']?>)">
										<img src="imagenes/<?=$item['ipc_activo']?'pork-on.png':'pork-off.png'?>" />
									</a>
								</center>
							</td>
						</tr><?php }
					}
					else{?>
						<tr class="tr-last">
							<td class="td-last" colspan="5"><center><?=$lang->message->sin_resultados?></center></td>
						</tr>
					<?php }?>
			</tbody>
            </table>
	
    
    
            <?php 
			break;
			case 'alta':
			case 'modificar':
				require_once 'includes/botoneraABMs.php';
			?>
	</div><!-- fin. mainBoxLICabezera -->   
        <fieldset>
        <div id="mainBoxAM">
        <div style="width:450px; margin:auto">
        <br />
        <strong style="font-size:16px;"><?=encode($informe['ip_nombre'])?></strong>
        <p style="margin-top:8px;"><?=encode($informe['ip_descripcion'])?></p>
        <br /><br />
        <table width="100%">
            <tbody>
            <tr>
            	<td><strong style="line-height:20px"><?=$lang->system->destinatarios?>:</strong></td>
            </tr>
			<?php foreach($arrUsuarios as $item){?>
            <tr>
                <td style="text-align:left;" width="80%">
                	<input type="checkbox" name="checkEnviarA[]"  value="<?=$item['us_id']?>" class="float_l" <?=in_array($item['us_id'],$informeUsuarios)?'checked':''?>/>
                    <span class="float_l" style="line-height:20px;">
                        	<?=trim((!empty($item['us_nombre'])?$item['us_nombre']:'')
								.(!empty($item['us_apellido'])?' '.$item['us_apellido']:'')
								.(' ['.$item['us_nombreUsuario'].']'))?>
                    </span>
                    <span class="clear"></span>
                </td>
            </tr>
            <?php }?>
            <tr><td>&nbsp;</td></tr>
            <tr>
				<td style="text-align:left;height: 35px;">
                  	<center>
                    	<a href="javascript:;" onclick="javascript:enviarModificacion('activarInforme',<?=$id?>)" class="button colorin">
                        	<?=$informe['ipc_activo']?$lang->botonera->guardar_cambios:$lang->botonera->activar_informe?>
                        </a>
                        <?php if($informe['ipc_activo']){?>
                        <a href="javascript:;" onclick="javascript:enviarModificacion('desactivarInforme',<?=$id?>)" class="button">
                        	<?=$lang->botonera->desactivar_informe?>
                        </a>
                        <?php }?>
                    </center>
                </td>
            </tr>
			</tbody>
        </table>
        </div>
    </div><!-- fin. mainBoxLICabezera -->   
        </fieldset>
		<?php
		break;	
		}
		?>
	</form>
        </div>
    </div>
</div>